<?php
if($_SESSION["admin"]){ //This will log the user out, if logged in
    $_SESSION["admin"] = false;
    
    header('Location: home.php');
}else{ //Otherwise, log in.
print '<aside id="loginFunction">';


    


//Send the username to the database, and seee if the account as admin privs
//if it does, set the session variable '$admin' to true. This will give the user access
//to more items.
$_SESSION["admin"] = false;

//Connect to the database
$yourURL = $domain . $phpSelf;

//Initalize all the variables
$username = '';
$password = '';

//This will hold the entry errors
$usernameERROR = false;
$passwordERROR = false;
$errorMsg = array();

//Once the button has been pressed
if (isset($_POST["btnLogin"])) {


//Get the input from the forms, and sanitize them
    $username = htmlentities($_POST["txtUsername"], ENT_QUOTES, "UTF-8");
    $username = strtolower($username);
    $password = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");

//This section passes the sanitized data through validation functions to 
//ensure that there is something in both of the fields
    if ($username == "") {
        $errorMsg[] = "Please enter your Username";
        $usernameERROR = true;
    }
    if ($password == "") {
        $errorMsg[] = "Please enter your Password";
        $passwordERROR = true;
    }


    //Section get the password and admin status from the database, for
    //the user name that was taken from the form.
    try {
        if (empty($errorMsg)) {


            $data = array();
            $data[] = $username;

            $thisDatabase->db->beginTransaction();

            //Get the users password and admin status
            $query = "Select fldPassword, fldAdmin from tblUsers where fldUsername = ?";


            $results = $thisDatabase->select($query, $data);

            //Remove the usersPassword and Admin status from the results
            $userDbPassword = $results[0]["fldPassword"];
            $userDbAdminStatus = $results[0]["fldAdmin"];


            $dataEntered = $thisDatabase->db->commit();

            //Hash the password taken from the form
            $hashPassword = sha1($password);

            //If the passwords match, set the admin status for the page to true
            if ($hashPassword == $userDbPassword) {
                if ($userDbAdminStatus == 1) { //If the admin status is 1 (yes)
                    $_SESSION["admin"] = true;
                } else {
                    $errorMsg[] = "Your password was correct, however this account does not have admin privs";
                }
            } else {
                $errorMsg[] = "The password you entered did not match the one stored, please try again or contact the sysadmin";
            }
        }
    } catch (PDOExecption $e) {
        $thisDatabase->db->rollback();
        if ($debug)
            print "Error!: " . $e->getMessage() . "</br>";
        $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
    }

    print"<h1> The button was pressed</h1>";
} //If the button is pressed








if (isset($_POST["btnLogin"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
    if ($dataEntered) {

        //%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*//
        // This is where code will go, to be displayed once the 
        // login was successful
        //
        //%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*%*//
    header('Location: home.php');
        print "<h2> THIS WORKED WELL </h2>";
    } //Close if dataEntered
} else {
    if ($errorMsg) {
        print '<div id="errors">';
        print "<ol>\n";
        foreach ($errorMsg as $err) {
            print "<li>" . $err . "</li>\n";
        }
        print "</ol>\n";
        print '</div>';
    }
    ?>
    <form action="<?php print $phpSelf; ?>" method="post" id="frmLogin">

        <fieldset class="loginInput">
            <section class="usernameInput">
                <label for="txtUsername">Username</label>
                <input type="text" id="txtUsername" name="txtUsername"
                       value=""
                       tabindex="100" maxlength="45" placeholder="Enter your username"
                       <?php if ($usernameERROR) print 'class="mistake"'; ?>autofocus/>
            </section>



            <section class="passwordInput">
                <label for="txtPassword">Password</label>
                <input type="password" id="txtPassword" name="txtPassword"
                       value=""
                       tabindex="120" maxlength="45" placeholder="Enter your Password"
                       <?php if ($passwordERROR) print 'class="mistake"'; ?>/>
            </section>

        </fieldset> <!-- loginInput -->
        
        <fieldset class="button">
            <input type="submit" id="btnLogin" name="btnLogin" value="Login" tabindex="900" class="button">
        </fieldset> <!-- ends buttons -->


    </form>

    <?php
}

print"</aside>";
}
?>


