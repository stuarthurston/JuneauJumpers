<?php
include "top.php";
include "nav.php";

if ($_SESSION["admin"]) {

    //Display the list of the current events, before the form is submitted
    if (!isset($_POST["btnAdd"])) {
        print "<section class=\"displayAdmins\">";
        include "getListofEvents.php";
        print"</section>";
    }



//Connect to the database
    $yourURL = $domain . $phpSelf;

//Initalize all the variables
    $title  = '';
    $body   = '';
    $image  = '';

//This will hold the entry errors
    $titleERROR = false;
    $bodyERROR = false;
    $imageERROR = false;
    $errorMsg = array();

    if (isset($_POST["btnAdd"])) {

//Get the input from the forms, and sanitize them
        $title = htmlentities($_POST["txtTitle"], ENT_QUOTES, "UTF-8");
        $body = htmlentities($_POST["txtBody"], ENT_QUOTES, "UTF-8");
        $image = htmlentities($_POST["filImage"], ENT_QUOTES, "UTF-8");

        //DO SOME VALIDATION
        //Check to make sure certain entrys are not empty
        if ($title == "") {
            $errorMsg[] = "Please enter a Title";
            $titleERROR = true;
        }

        if ($body == "") {
            $errorMsg[] = "Please enter a Body";
            $bodyERROR = true;
        }
        


//This section passes the sanitized data through validation functions to 
//ensure that the input is in the correct format, and mark when ther is not.
        //DO SOME VALIDATION
    }

    if (isset($_POST["btnAdd"]) AND empty($errorMsg)) {
        //Insert information into members table (tblMembers)
        try {

            //See if the user has already made an account BEFORE CONTINUING
            $data = array();
            $data[] = $title;
            $data[] = $body;
            $data[] = $image;

            $thisDatabase->db->beginTransaction();
            $query = "INSERT INTO tblEvents (fldTitle, fldBody, fldImg) VALUES (?,?,?)";
            
            $results = $thisDatabase->insert($query, $data);

            $dataEntered = $thisDatabase->db->commit();

        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "There was a problem adding this user to the database, please contact Stuart.";
        }



//        print "things worked";
//        include "upload.php";
    } //END If the button is pressed and error empty
    else {


        //####################################
        //
        // SECTION 3b Error Messages
        //
        // display any error messages before we print out the form

        if ($errorMsg) {
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
    <form action="<?php print $phpSelf; ?>" method="post" id="frmMembers" enctype="multipart/form-data">

        <fieldset class="eventInput">
            <section class="titleInput">
                <label for="txtTitle">First Name</label>
                <input type="text" id="txtTitle" name="txtTitle"
                       value="<?php print $title; ?>"
                       tabindex="100" maxlength="45" placeholder="Enter your First Name"
                       <?php if ($titleERROR) print 'class="mistake"'; ?>/>
            </section>

            
            <section class="bodyInput">
                <textarea name="txtBody" wrap='soft' placeholder='This is a place holder' 
                          rows='15' cols='100' tabindex='200'></textarea>
            </section>  
<!--            <section class="bodyInput">
                <label for="txtBody">Body</label>
                <input type="text" id="txtBody" name="txtBody"
                       value="<?php print $body; ?>"
                       tabindex="200" maxlength="45" placeholder="Enter your Last Name"
                       <?php if ($bodyERROR) print 'class="mistake"'; ?>autofocus/>
            </section>-->
            
            <section class="imageInput">
                <label for="filImage">Image</label>
                <input type="file" name="filImage" id="filImage" accept="image/*">
                </section>


        </fieldset> <!-- loginInput -->

        <fieldset class="button">
            <input type="submit" id="btnAdd" name="btnAdd" value="Add" tabindex="900" class="button">
        </fieldset> <!-- ends buttons -->


    </form>



    <?php
} //If the admin is logged in
else {
    include_once"accessDenied.php";
}
?>
