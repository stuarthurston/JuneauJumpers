<?php   
include"top.php";
include "nav.php";

if ($_SESSION["admin"]) {
    //Get the id of the memebr to be editd
    $updateMember = $_SESSION['updateMember'];
    $updateFlag = NULL; //IF SET TO TRUE, UPDATE rather than Add
?>

<!--This section is here to display the contents of the table, before
anything is added by the form. There is another instance of this at the
bottom of the form, once the button is pressed, it will be used instead-->
<?php 
if (!isset($_POST["btnAdd"])) {
    print "<section class=\"displayMembers\">";
    include "getListofMembers.php";
    print"</section>";
}
?>





<?php

if (empty($updateMember) && !is_numeric($updateMember)) { //If the record is to be updated, set the values from the database
//Initalize variables, 
        $firstName = "";
        $lastName = "";
        $age = "";
        $position = "";
        $bio = "";
        $email = "";
        $phone = "";
        $image = "";
    }

    //If the user wants to update an entry
    elseif (is_numeric($updateMember)) {
        

        try {
            $data = array();
            $data[] = $updateMember;

            $thisDatabase->db->beginTransaction();


            $query = "SELECT fldFirstName,fldLastName, fldAge,";
            $query .=" fldPosition, fldBio, fldEmail, fldPhone, fldImg";
            $query .=" FROM tblMembers";
            $query .=" WHERE pmkMemberId = ?";

            $results = $thisDatabase->select($query, $data);


            $dataEntered = $thisDatabase->db->commit();

            //Initalize variables, 
            $firstName = $results[0]["fldFirstName"];
            $lastName = $results[0]["fldLastName"];
            $age = $results[0]["fldAge"];
            $position = $results[0]["fldPosition"];
            $bio = $results[0]["fldBio"];
            $email = $results[0]["fldEmail"];
            $phone = $results[0]["fldPhone"];
            $image = $results[0]["fldImg"];



            $updateFlag = TRUE;
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "There was a problem adding this user to the database, please contact Stuart.";
        }
    }


//Initalize ERROR variables, 
    $firstNameERROR = FALSE;
    $lastNameERROR = FALSE;
    $ageERROR = FALSE;
    $positionERROR = FALSE;
    $bioERROR = FALSE;
    $emailERROR = FALSE;
    $phoneERROR = FALSE;
    $imageERROR = FALSE;

//Array to hold the error messages
    $errorMsg = array();

//If the button was pressed
    if (isset($_POST["btnAdd"])) {
        print"1";

//Get the input from the forms, and sanitize them
        $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
        $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
        $age = htmlentities($_POST["txtAge"], ENT_QUOTES, "UTF-8");
        $position = htmlentities($_POST["lstPosition"], ENT_QUOTES, "UTF-8");
        $bio = htmlentities($_POST["txtBio"], ENT_QUOTES, "UTF-8");
        $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");
        $phone = htmlentities($_POST["txtPhone"], ENT_QUOTES, "UTF-8");
        $image = htmlentities($_FILES["filImage"]["name"], ENT_QUOTES, "UTF-8");



        //DO SOME VALIDATION
        //Check to make sure certain entrys are not empty
        if ($firstName == "") {
            $errorMsg[] = "Please enter your First Name";
            $firstNameERROR = true;
        }

        if ($lastName == "") {
            $errorMsg[] = "Please enter your Last Name";
            $lastNameERROR = true;
        }

        if ($email == "") {
            $errorMsg[] = "Please enter your Email";
            $emailERROR = true;
        }

//This section passes the sanitized data through validation functions to 
//ensure that the input is in the correct format, and mark when ther is not.
        //DO SOME VALIDATION
    }
    if (isset($_POST["btnAdd"]) AND empty($errorMsg)) {
        if ($updateFlag) {
            try {
                $thisDatabase->db->beginTransaction();

                $data = array();
                $data[] = $firstName;
                $data[] = $lastName;
                $data[] = $age;
                $data[] = $position;
                $data[] = $bio;
                $data[] = $email;
                $data[] = $phone;
                $data[] = $image;
                $data[] = $updateMember;


                //Insert everything into the members table
                $query = "update tblMembers";
                $query .=" SET fldFirstName = ?";
                $query .=" ,fldLastName = ?";
                $query .=" ,fldAge = ?";
                $query .=" ,fldPosition = ?";
                $query .=" ,fldBio = ?";
                $query .=" ,fldEmail = ?";
                $query .=" ,fldPhone = ?";
                $query .=" ,fldPhone = ?";
                $query .=" WHERE pmkMemberId = ?";
                
          
//                print"<pre>";
//                print $query;
//                print"</pre>";
//                print"---";
//                print"<pre>";
//                print_r($data);
//                print"</pre>";
                
                $results = $thisDatabase->update($query, $data);

                $dataEntered = $thisDatabase->db->commit();

                unset($_SESSION['updateMember']);
                header('Location: adminMembers.php');
                
            } catch (PDOExecption $e) {
                $thisDatabase->db->rollback();
                print "There was a problem adding this user to the database, please contact Stuart.";
            }
        } else {


            //Insert information into members table (tblMembers)
            try {

                //See if the user has already made an account BEFORE CONTINUING
                $data = array();
                $data[] = $firstName;
                $data[] = $lastName;

                $thisDatabase->db->beginTransaction();
                $query = 'SELECT fldFirstName FROM tblMembers Where fldFirstName = ? and fldLastName = ?';

                $results = $thisDatabase->select($query, $data);

                $dataEntered = $thisDatabase->db->commit();


                if (empty($results)) {
                    $alreadyExists = FALSE;
                } else {
                    $alreadyExists = TRUE;
                }

                //True means it already exists
                //False means that it is not taken
                //If the email does not exist, continue
                if (!$alreadyExists) {

                    $thisDatabase->db->beginTransaction();

                    $data = array();
                    $data[] = $firstName;
                    $data[] = $lastName;
                    $data[] = $age;
                    $data[] = $position;
                    $data[] = $bio;
                    $data[] = $email;
                    $data[] = $phone;
                    $data[] = $image;


                    //Insert everything into the members table
                    $query = "INSERT INTO tblMembers ";
                    $query .="(fldFirstName,fldLastName, fldAge";
                    $query .=", fldPosition, fldBio, fldEmail,fldPhone, fldImg)";
                    $query .=" VALUES (?,?,?,?,?,?,?,?)";

                    $results = $thisDatabase->insert($query, $data);

                    $dataEntered = $thisDatabase->db->commit();
                    $updateFlag = FALSE;
                } //If doesnt already exist
                else {
                    print "There is already a member with this name, please try again";
                }
            } catch (PDOExecption $e) {
                $thisDatabase->db->rollback();
                print "There was a problem adding this user to the database, please contact Stuart.";
            }


            //This provides an up to date listing of the table
            print "<section class=\"displayMembers\">";
            include "getListofMembers.php";
            print"</section>";

            include "upload.php";

            //Unset variables so that the form is empty after a submit
            unset($firstName, $lastName, $age, $position, $bio, $email, $phone, $image);
        }//If not an update
    } //END If the button is pressed and error empty
    else {


        //####################################
        //
        // SECTION 3b Error Messages
        //
        // display any error messages before we print out the form



        if ($errorMsg) {

            print "<section class=\"displayMembers\">";
            include "getListofMembers.php";
            print"</section>";

            print '<div id="errors">';
            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        }
    }
    ?>




<!--Form that is used to get inputs and add member-->
<section class="addMembers">
<form action="<?php print $phpSelf; ?>" method="post" id="frmMembers" enctype="multipart/form-data">

        <fieldset class="memberInput">
            <section class="firstNameInput">
                <label for="txtFirstName">First Name</label>
                <input type="text" id="txtFirstName" name="txtFirstName"
                       value="<?php print $firstName; ?>"
                       tabindex="100" maxlength="45" placeholder="Enter your First Name"
                       <?php if ($firstNameERROR) print 'class="mistake"'; ?>/>
            </section>
            
            <section class="lastNameInput">
                <label for="txtLastName">Last Name</label>
                <input type="text" id="txtLastName" name="txtLastName"
                       value="<?php print $lastName; ?>"
                       tabindex="200" maxlength="45" placeholder="Enter your Last Name"
                       <?php if ($lastNameERROR) print 'class="mistake"'; ?>/>
            </section>
            
            <section class="ageInput">
                <label for="txtAge">Age</label>
                <input type="text" id="txtAge" name="txtAge"
                       value="<?php print $age; ?>"
                       tabindex="300" maxlength="45" placeholder="Enter your Age"
                       <?php if ($ageERROR) print 'class="mistake"'; ?>/>
            </section>
            
            <section class="bioInput">
                <label for="txtBio">Biography</label>
                <input type="text" id="txtBio" name="txtBio"
                       value="<?php print $bio; ?>"
                       tabindex="400" maxlength="4500" placeholder="Enter your Biography"
                       <?php if ($bioERROR) print 'class="mistake"'; ?>/>
            </section>
            
            <section class="emailInput">
                <label for="txtEmail">Email</label>
                <input type="text" id="txtEmail" name="txtEmail"
                       value="<?php print $email; ?>"
                       tabindex="500" maxlength="45" placeholder="Enter your Email"
                       <?php if ($emailERROR) print 'class="mistake"'; ?>/>
            </section>

            <section class="phoneInput">
                <label for="txtPhone">Phone Number</label>
                <input type="text" id="txtPhone" name="txtPhone"
                       value="<?php print $phone; ?>"
                       tabindex="600" maxlength="45" placeholder="Enter your Phone Number"
                       <?php if ($phoneERROR) print 'class="mistake"'; ?>/>
            </section>
            
            <section class="imageInput">
                <label for="filImage">Image</label>
                <input type="file" name="filImage" id="filImage" accept="image/*">
                </section>

            

            <section class="positionInput">
                    <label for="lstPosition">Position</label>
                    <select name="lstPosition">
                        <option value="Jumper" <?php if($position=="Jumper") echo "selected";?> >Jumper</option>
                        <option value="Coach" <?php if($position=="Coach") echo "selected";?> >Coach</option>
                        <option value="Board Member" <?php if($position=="Board Member") echo "selected";?> >Board Member</option>
                        <option value="Other" <?php if($position=="Other") echo "selected";?> >Other</option>
                    </select>
                </section>

            
        </fieldset> <!-- loginInput -->
        
        <fieldset class="button">
            <input type="submit" id="btnAdd" name="btnAdd" value="Add" tabindex="900" class="button">
        </fieldset> <!-- ends buttons -->


    </form>
</section>


<?php
} else {
    include_once"accessDenied.php";
}
include_once"footer.php";
?>
