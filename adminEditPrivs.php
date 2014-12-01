<?php
include"top.php";
include "nav.php";

if ($_SESSION["admin"]) {

//Display the list of the current admins, before the form is submitted
    if (!isset($_POST["btnAdd"])) {
        print "<section class=\"displayAdmins\">";
        include "getListofAdmins.php";       
        print"</section>";
    }


//Connect to the database
    $yourURL = $domain . $phpSelf;

//Initalize all the variables
    $firstName = '';
    $lastName = '';
    $email = '';
    $username = '';
    $password = '';
    $passwordCon = '';

//This will hold the entry errors
    $firstNameERROR = false;
    $lastNameERROR = false;
    $emailERROR = false;
    $usernameERROR = false;
    $passwordERROR = false;
    $passwordConERROR = false;
    $errorMsg = array();


//Once the button has been pressed
    if (isset($_POST["btnAdd"])) {

//Get the input from the forms, and sanitize them
        $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
        $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
        $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");
        $username = htmlentities($_POST["txtUsername"], ENT_QUOTES, "UTF-8");
        $username = strtolower($username);
        $password = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");
        $passwordRaw = $password;
        $password = sha1($password);
        $passwordCon = htmlentities($_POST["txtPasswordCon"], ENT_QUOTES, "UTF-8");
        $passwordCon = sha1($passwordCon);

        

//This section passes the sanitized data through validation functions to 
//ensure that there is something in both of the fields
        if ($firstName == "") {
            $errorMsg[] = "Please enter your First Name";
            $firstNameERROR = true;
        }

        if ($lastName == "") {
            $errorMsg[] = "Please enter your Last Name";
            $lastNameERROR = true;
        }
        if ($email == "") {
            $errorMsg[] = "Please enter your email";
            $emailERROR = true;
        }
        if ($username == "") {
            $errorMsg[] = "Please enter your Username";
            $usernameERROR = true;
        }
        if ($password !== $passwordCon) {
            $errorMsg[] = "Passwords do not match";
            $passwordERROR = true;
            $passwordConERROR = true;
        }
    }//If the button is pressed










    if (isset($_POST["btnAdd"]) AND empty($errorMsg)) { 
        //The information is now added to the database
        //the user name that was taken from the form.
        try {
            if (empty($errorMsg)) {

                if ($password == $passwordCon) {

                    $data = array();
                    $data[] = $firstName;
                    $data[] = $lastName;
                    $data[] = $email;
                    $data[] = $username;
                    $data[] = $password;
                    $data[] = 1;
                    
                    
                  
                    $thisDatabase->db->beginTransaction();

                    //Insert the new admins information into tblUsers
                    $query = "INSERT INTO tblUsers (fldFirstName, fldLastName, fldEmail, fldUsername, fldPassword, fldAdmin) VALUES (?,?,?,?,?,?)";

                    $results = $thisDatabase->select($query, $data);

                    $dataEntered = $thisDatabase->db->commit();
                    
                    
                    $to = $email; // the person who filled out the form
                    $cc = "";
                    $bcc = "";
                    $from = "Admin<noreply@juneauJumpers.com>";
                    $subject = "New Privileges";
                    
                    $message = "<p>You have been granted access to the administrator section of the website: ";
                    $message .= "<a href='https://sathurst.w3.uvm.edu/cs148/assignment7.0/home.php'>Juneau Jumpers</a></p>"; 
                    $message .= "<p>Click the 'Login' button located on the right side of the menu.</p>";
                    $message .= "<h2>Username: ".$username."</h2>";
                    $message .= "<h2>Password: ".$passwordRaw."</h2>";
                    
                    
                    
                    require_once "lib/mail-message.php";
                    
                    //Send the information to the mailing function
            $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
            
                } //End Passwords match 
                else {
                    $errorMsg[] = "The passwords did not match, please try again";
                }
            } //Error message empty
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }
        
        
        
                
        //This is an upto date of the admins
        print "<section class=\"displayAdmins\">";
        include "getListofAdmins.php";
        print"</section>";
       



    }//btn pressed and empty error
    else {
        if ($errorMsg) {
            
            print "<section class=\"displayAdmins\">";
            include "getListofAdmins.php";
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
        <section class="addAdmin">
        <form action="<?php print $phpSelf; ?>" method="post" id="frmAddAdmin">

            <fieldset class="addAdmin">

                <section class="newFirstName">
                    <label for="txtFirstName">First Name</label>
                    <input type="text" id="txtFirstName" name="txtFirstName"
                           value="<?php print $firstName; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter your FirstName"
                           <?php if ($firstNameERROR) print 'class="mistake"'; ?>/>
                </section>

                <section class="newLastName">
                    <label for="txtLastName">Last Name</label>
                    <input type="text" id="txtLastName" name="txtLastName"
                           value="<?php print $lastName; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter your LastName"
                           <?php if ($lastNameERROR) print 'class="mistake"'; ?>/>
                </section>

                <section class="newEmail">
                    <label for="txtEmail">Email</label>
                    <input type="text" id="txtEmail" name="txtEmail"
                           value="<?php print $email; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter your username"
                           <?php if ($emailERROR) print 'class="mistake"'; ?>/>
                </section>

                <section class="newUsername">
                    <label for="txtUsername">Username</label>
                    <input type="text" id="txtUsername" name="txtUsername"
                           value="<?php print $username; ?>"
                           tabindex="100" maxlength="45" placeholder="Enter your username"
                           <?php if ($usernameERROR) print 'class="mistake"'; ?>/>
                </section>



                <section class="newPassword">
                    <label for="txtPassword">Password</label>
                    <input type="password" id="txtPassword" name="txtPassword"
                           value=""
                           tabindex="120" maxlength="45" placeholder="Enter your Password"
                           <?php if ($passwordERROR) print 'class="mistake"'; ?>/>
                </section>

                <section class="newPasswordCon">
                    <label for="txtPasswordCon">Confirm Password</label>
                    <input type="password" id="txtPasswordCon" name="txtPasswordCon"
                           value=""
                           tabindex="120" maxlength="45" placeholder="Enter your Password"
                           <?php if ($passwordConERROR) print 'class="mistake"'; ?>/>
                </section>

            </fieldset> <!-- addAdmin -->

            <fieldset class="button">
                <input type="submit" id="btnAdd" name="btnAdd" value="Add" tabindex="900" class="button">
            </fieldset> <!-- ends buttons -->


        </form>
    </section>
        <?php
    


} //If the admin is logged in
else {
    include_once"accessDenied.php";
}
include_once"footer.php";

//%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*
?>
