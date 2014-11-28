<?php
if ($_SESSION["admin"]) {
    $_SESSION['userId']; //This will hold the value of all the memebrs in the table



//Initalize the array that will hold the user Id and admin status
    $userId = array(); //Same as the session variable above
    $adminStatus = array();
    $checked = array(); //This holds the ID for the members that were 'checked'

    if (isset($_POST["btnSet"])) {



        //Add the selected members, to the checked array
        foreach ($_POST['adminYN'] as $selected) {
            $checked[] = htmlentities($selected, ENT_QUOTES, "UTF-8");
        }


        //Reassign the userId variable.
        $userId = $_SESSION["userId"];

        //This compared the userIds taken the database, and those that were checked
        //from the form. Determine which ones are unchecked.

        $unchecked = array_diff($userId, $checked);

//    print"Checked";
//    print "<pre>";
//    print_r($checked);
//    print "</pre>";
//
//    print"unChecked";
//    print "<pre>";
//    print_r($unchecked);
//    print "</pre>";
        //*^*^*^*^*^*^*^*^*^*^*^*^**^*^*^*^*^*^*^*^*^*^**^*^*^*^*^**^
        //UPDATE THE DATABASE 
        //*^*^*^*^*^*^*^*^*^*^*^*^**^*^*^*^*^*^*^*^*^*^**^*^*^*^*^**^
        try {

            //Update the admin status for the people who are LOSING admin status
            $uncheckedData = array();
            $thisDatabase->db->beginTransaction();

            //This is used to shift everything one key forward, make sure something is in 0
            $uncheckedData = array_merge($unchecked);

            $query = "UPDATE tblUsers set fldAdmin= 0 WHERE pmkUserId in (?";

            //Add the correct number of question marks depending on the size of the array.
            $i = 0;
            while ($i < count($uncheckedData) - 1) {
                $query .= ",?";
                $i++;
            }
            $query.= ")";

            $results = $thisDatabase->update($query, $uncheckedData);
            $dataEntered = $thisDatabase->db->commit();

            //Update the admin status for the people who are GAINING admin status
            $checkedData = array();
            $thisDatabase->db->beginTransaction();

            $checkedData = array_merge($checked);

            $query = "UPDATE tblUsers set fldAdmin= 1 WHERE pmkUserId in (?";

            //Add the correct number of question marks depending on the size of the array.
            $i = 0;
            while ($i < count($checkedData) - 1) {
                $query .= ",?";
                $i++;
            }
            $query.= ")";

            $results = $thisDatabase->update($query, $checkedData);
            $dataEntered = $thisDatabase->db->commit();

//Once the changes have been made, reload the page
            header('Location: adminEditPrivs.php');
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "There was a problem with accpeting your data please contact us directly.";
        }
    } else {

//Grab all of the information from the user table
        try {

            $thisDatabase->db->beginTransaction();

//Select everything from the members table
            $query = "SELECT fldFirstName as 'First Name',";
            $query .=" fldLastName as 'Last Name',";
            $query .=" fldUsername as Username,";
            $query .=" fldEmail as Email,";
            $query .=" CASE WHEN fldAdmin = 1 THEN 'Yes' ELSE 'No' END AS fldAdmin";
            $query .=" FROM tblUsers ORDER BY fldFirstName, fldLastName";
            $results = $thisDatabase->Select($query);
            $dataEntered = $thisDatabase->db->commit();


            //Get the userIds
            $thisDatabase->db->beginTransaction();
            $query = "SELECT pmkUserId from tblUsers ORDER BY fldFirstName, fldLastName";
            $userIdResult = $thisDatabase->Select($query);
            $dataEntered = $thisDatabase->db->commit();

            //Get the user admin status (fldAdmin) (see if they are already an admin)
            $thisDatabase->db->beginTransaction();
            $query = "SELECT fldAdmin from tblUsers ORDER BY fldFirstName, fldLastName";
            $adminStatusResult = $thisDatabase->Select($query);
            $dataEntered = $thisDatabase->db->commit();




            //Make sure the array is empty to start with
            $_SESSION["userId"] = array();

            //Take the ID array, and reformat it
            foreach ($userIdResult as $row) {
                /* display the data, the array is both associative and index so we are
                 *  skipping the index otherwise records are doubled up by using the 'is_int' */
                foreach ($row as $field => $value) {
                    if (!is_int($field)) {
                        $_SESSION["userId"][] = $value;
                    }
                }
            }

            //remove the possibilties of any duplicates
            $_SESSION['userId'] = array_unique($_SESSION['userId']);
            //Make sure the array elements are in order (key goes 0->n)
            $_SESSION['userId'] = array_values($_SESSION['userId']);

            //Take the Adminstatus array, and reformat it
            foreach ($adminStatusResult as $row) {
                /* display the data, the array is both associative and index so we are
                 *  skipping the index otherwise records are doubled up by using the 'is_int' */
                foreach ($row as $field => $value) {
                    if (!is_int($field)) {
                        $adminStatus[] = $value;
                    }
                }
            }



            //At one point these were used to reverse the order of the arrays, It is no longer needed
            //But I kept the same names
            $userIdReversed = $_SESSION["userId"];
            $adminStatusReversed = $adminStatus;
            
            ?>

            <form action="<?php print $phpSelf; ?>" method="post" id="frmAddAdmin">

            <?php
            //Display all of the information, in a table
            print "<table>\n";

            $firstTime = true;
            $i = 0;

            /* since it is associative array display the field names */
            foreach ($results as $row) {
                if ($firstTime) {
                    print "<thead>\n\t<tr>\n";
                    $keys = array_keys($row);
                    foreach ($keys as $key) {
                        if (!is_int($key)) {
                            print "\t\t<th>" . $key . "</th>\n";
                        }
                    }
                    print "\t\t<th>Admin</th>\n";
                    print "\t</tr>\n</thead>\n";
                    $firstTime = false;
                }

                /* display the data, the array is both associative and index so we are
                 *  skipping the index otherwise records are doubled up */
                print "<tr>\n";
                foreach ($row as $field => $value) {
                    if (!is_int($field)) {
                        print "\t<td>" . $value . "</td>\n";
                    }
                }
                echo "\t<td><input type='checkbox' value='$userIdReversed[$i]' name='adminYN[]' ";


                if ($adminStatusReversed[$i] == 1) {
                    echo 'checked';
                };
                echo "></td>\n</tr>\n";
                $i++;
            }
            print "</table>\n";
            ?>


                <fieldset class="button">
                    <input type="submit" id="btnSet" name="btnSet" value="Set" tabindex="900" class="button">
                </fieldset> <!-- ends buttons -->
            </form>

            <?php
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "There was a problem with accpeting your data please contact us directly.";
        }
    } //End of else, btn pushed
}//End of, if ADMIN
else {
    print"ACCESS DENIED";
}
?>
