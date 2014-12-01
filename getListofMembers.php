<?php
if ($_SESSION["admin"]) {
    $_SESSION['memberId']; //This array will hold all of the members Id
    $_SESSION['updateMember']; //Hold the Id of the member to be updated
    
    //Get the array of all the current members
            $memberId = array();
    if (isset($_POST["btnDelete"])) {



        //Add the selected members, to the checked array
        foreach ($_POST['deleteYN'] as $selected) {
            $checked[] = htmlentities($selected, ENT_QUOTES, "UTF-8");
        }

        $memberId = $_SESSION["memberId"];

        print_r($checked);
        
        //*^*^*^*^*^*^*^*^*^*^*^*^**^*^*^*^*^*^*^*^*^*^**^*^*^*^*^**^
        //UPDATE THE DATABASE 
        //*^*^*^*^*^*^*^*^*^*^*^*^**^*^*^*^*^*^*^*^*^*^**^*^*^*^*^**^
        try {

            //Delete from the members table
            $checkedData = array();
            $thisDatabase->db->beginTransaction();
            
            //This is used to shift everything one key forward, make sure something is in 0
            $checkedData = array_merge($checked);
            
        
            $query = "DELETE FROM tblMembers WHERE pmkMemberId in (?";
            
            
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
            header('Location: adminMembers.php');
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "There was a problem with accpeting your data please contact us directly.";
        }
    }
    
    //If it is trying to be updated
    if (isset($_POST["btnUpdate"])) {
        //Get the ID of the member to be updated       
        $updateMembers = htmlentities(($_POST["updateMem"]), ENT_QUOTES, "UTF-8");

        $_SESSION['updateMember'] = $updateMembers;
        header('Location: adminMembers.php');
    }


    //If neither delete, or update is being used
    else {




//Grab all of the information from the members table
        try {
            $thisDatabase->db->beginTransaction();

//Select everything from the members table
            $query = "SELECT ";
            $query .=" fldFirstName as 'First Name',";
            $query .=" fldLastName as 'Last Name',";
            $query .=" fldAge as Age,";
            $query .=" fldPosition as Position,";
            $query .=" fldBio as Biography,";
            $query .=" fldEmail as Email,";
            $query .=" fldPhone as 'Phone Number',";
            $query .=" fldImg as Image";
            $query .=" FROM tblMembers ORDER BY fldFirstName, fldLastName";
            $results = $thisDatabase->Select($query);

            $dataEntered = $thisDatabase->db->commit();



            //Get the memberIds
            $thisDatabase->db->beginTransaction();
            $query = "SELECT pmkMemberId from tblMembers ORDER BY fldFirstName, fldLastName";
            $memberIdResult = $thisDatabase->Select($query);
            $dataEntered = $thisDatabase->db->commit();


            //Make sure the array is empty to start with
            $_SESSION["memberId"] = array();

            
            //Take the ID array, and reformat it
            foreach ($memberIdResult as $row) {
                /* display the data, the array is both associative and index so we are
                 *  skipping the index otherwise records are doubled up by using the 'is_int' */
                foreach ($row as $field => $value) {
                    if (!is_int($field)) {
                        $_SESSION["memberId"][] = $value;
                    }
                }
            }

            //remove the possibilties of any duplicates
            $_SESSION['memberId'] = array_unique($_SESSION['memberId']);
            //Make sure the array elements are in order (key goes 0->n, +1)
            $_SESSION['memberId'] = array_values($_SESSION['memberId']);

            $memberId = $_SESSION["memberId"];
            
            ?>
            <form action="<?php print $phpSelf; ?>" method="post" id="frmDeleteMember">

            <?php

            //Display all of the information, in a table
            print "\n<table>\n";

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
                    print "\t\t<th>Delete</th>\n";
                    print "\t\t<th>Update</th>\n";
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
                echo "\t<td><input type='checkbox' value='$memberId[$i]' name='deleteYN[]'></td>\n";
                echo "\t<td><input type='radio' name='updateMem' value='$memberId[$i]'></td>\n</tr>\n";
                $i++;
            }
            print "</table>\n";
            ?>

                <fieldset class="button">
                    <input type="submit" id="btnDelete" name="btnDelete" value="Delete" tabindex="900" class="button">
                    <input type="submit" id="btnUpdate" name="btnUpdate" value="Update" tabindex="1000" class="button">
                </fieldset> <!-- ends buttons -->
            </form>
            <?php
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "There was a problem with accpeting your data please contact us directly.";
        }
    }
}//End if admin
else {
    print"ACCESS DENIED";
}
?>
