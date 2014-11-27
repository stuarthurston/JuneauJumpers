<?php
include "top.php";
include "nav.php";

if ($_SESSION["admin"]) {

    $_SESSION["eventListing"]; //Hold the names of the events
    $_SESSION["chosenEvent"]; 
    $_SESSION["memberId"]; //hold the values of the members, taken from memberstbl
    $_SESSION["alreadyPresent"]; //Holds the ids of the members that were checked (to be added to attendence)
    //
//Connect to the database
    $yourURL = $domain . $phpSelf;

//Initalize all the variables
    $title = '';


//This will hold the entry errors
    $titleERROR = false;

    $errorMsg = array();

    
    
 if (!isset($_POST["btnChoose"])) {

       
    //Get the names of all the events, store in an array
    try {

        //Get the titles of the events
        $thisDatabase->db->beginTransaction();
        $query = "Select fldTitle from tblEvents ORDER BY fldDateAdded DESC";
        $eventTitleListing = $thisDatabase->Select($query);
        $dataEntered = $thisDatabase->db->commit();
        
        //Get the Id's of the events
        $thisDatabase->db->beginTransaction();
        $query = "Select pmkEventId from tblEvents ORDER BY fldDateAdded DESC";
        $eventIdListing = $thisDatabase->Select($query);
        $dataEntered = $thisDatabase->db->commit();
        
        
       //Combine the key and the value
       //There must be a better way to do this, but it works. Read both arrays into
       //a better formatted array, and then combine the two.
        $tempEventTitle = array();
        $tempEventId = array();
        foreach ($eventTitleListing as $row) {

                foreach ($row as $i => $value) {
                    if (!is_int($i)) {
                        $tempEventTitle [] = $value;
                    }
                }
            }
            foreach ($eventIdListing as $row) {

                foreach ($row as $i => $value) {
                    if (!is_int($i)) {
                        $tempEventId [] = $value;
                    }
                }
            }
            
            $idAndTitle = array_combine($tempEventId, $tempEventTitle);
        
//        print"<pre>";
//        print_r($eventTitleListing);
//        print"*****************************";
//        print"<pre>";
        
//        print"<pre>";
//        print_r($eventIdListing);
//        print"*****************************";
//        print"<pre>";
        
//        print"<pre>";
//        print_r($idAndTitle);
//        print"*****************************";
//        print"<pre>";
//        
//            //Make sure the array is empty to start with
//            $_SESSION["eventListing"] = array();
//
//            //Take the ID array, and reformat it
//            foreach ($eventListing as $row) {
//                /* display the data, the array is both associative and index so we are
//                 *  skipping the index otherwise records are doubled up by using the 'is_int' */
//                foreach ($row as $field => $value) {
//                    if (!is_int($field)) {
//                        $_SESSION["eventListing"][] = $value;
//                    }
//                }
//            }
//
//            //remove the possibilties of any duplicates
//            $_SESSION['eventListing'] = array_unique($_SESSION['eventListing']);
//            //Make sure the array elements are in order (key goes 0->n)
//            $_SESSION['eventListing'] = array_values($_SESSION['eventListing']);
        
        ?>
        <form action="<?php print $phpSelf; ?>" method="post" id="frmChooseEvent">
        <?php
        print "\n\t<label for='eventListing'>Event</label>";
        print "\n\t<select id='eventListing' ";
        print "name='eventListing' ";
        print "tabindex='300' >";
        //Get the key and the value from the array, and read into list
        foreach ($idAndTitle as $key => $value) {
            print "\n\t\t<option ";
                
                print "value='" . $key . "'>" . $value ;
                print "</option>";
            }
       
        print "\n\t</select>";
        ?>

            <fieldset class="button">
                <input type="submit" id="btnChoose" name="btnChoose" value="Choose" tabindex="400" class="button">
            </fieldset> <!-- ends buttons -->  

        </form>
        <?php
    } catch (PDOExecption $e) {
        $thisDatabase->db->rollback();
        print "There was a problem adding this user to the database, please contact Stuart.";
    }

 } //If the button has not been pressed (event not yet chosen)

 //22222222222222222222222222222222222222222222222222222222222222222222222222222
 //#############################################################################
 //#############################################################################
 //#############################################################################
 //IF THE EVENT HAS BEEN CHOSEN, ACCESS THE MEMBERS TABLE
 //
  if (isset($_POST["btnChoose"])) {
 

//Get the information from the relationship table
try{
        //Make sure the array is empty before adding to it
        $_SESSION["chosenEvent"] = array();
              
        //Get the id of the event that was chosen
            $chosenEventId = htmlentities($_POST["eventListing"], ENT_QUOTES, "UTF-8");
             $_SESSION["chosenEvent"] = $chosenEventId;
            
            $data = array();
            $data [] = $chosenEventId;
            
            //This array is all of the members Ids who are already present at the event
            $thisDatabase->db->beginTransaction();
            
            $query = "SELECT M.pmkMemberId";
            $query .=" FROM tblEvents as E";
            $query .=" Left join tblAttends as A on E.pmkEventId = A.fnkEventId";
            $query .=" Left join tblMembers as M on A.fnkMemberId = M.pmkMemberId";
            $query .=" where E.pmkEventId = ?";
            $results = $thisDatabase->Select($query, $data);


            $dataEntered = $thisDatabase->db->commit();
            
            //Reformat the results
            $alreadyPresent = array(); //The array that says who was already marked as present
            foreach ($results as $row) {
                foreach ($row as $i => $value) {
                    if (!is_int($i)) {
                        $alreadyPresent [] = $value;
                    }
                }
            }
            
            

            
        }catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "There was a problem with accpeting your data please contact us directly.";
        
    }  
            
            
//Grab all of the information from the members table
        try {
            $thisDatabase->db->beginTransaction();

            //Select first and last name from the members table
            $query = "SELECT ";
            $query .=" fldFirstName as 'First Name',";
            $query .=" fldLastName as 'Last Name'";
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
            $_SESSION["alreadyPresent"] = array();

            
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
                    print "\t\t<th>Present</th>\n";
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
                echo "\t<td><input type='checkbox' value='$memberId[$i]' name='presentYN[]' ";

                if (in_array($memberId[$i], $alreadyPresent)) { //If the member is already associated with the event, check
                    echo "checked";
  
                }

                echo "></td>\n</tr>\n";
                $i++;
            }
            
            $_SESSION["alreadyPresent"] = $alreadyPresent;

            print "</table>\n";
            ?>

                <fieldset class="button">
                    <input type="submit" id="btnPresent" name="btnPresent" value="Present" tabindex="900" class="button">
                </fieldset> <!-- ends buttons -->
            </form>
            <?php
            
            

                   
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "There was a problem with accpeting your data please contact us directly.";
        
    }
    
    
  } //if the button (chosen) was pressed
 
  
 //3333333333333333333333333333333333333333333333333333333333333333333333333333
 //#############################################################################
 //#############################################################################
 //#############################################################################
 //IF THE EVENT HAS BEEN CHOSEN, ACCESS THE MEMBERS TABLE
 //
  if (isset($_POST["btnPresent"])) {
      $chosenEventId = htmlentities($_POST["eventListing"], ENT_QUOTES, "UTF-8");
        foreach ($_POST['presentYN'] as $selected) {
            $checked[] = htmlentities($selected, ENT_QUOTES, "UTF-8");
        }
        
//This is the list of all the members Ids
        $memberId = $_SESSION["memberId"];
        

        
//The members that were already selected as present
        $alreadyPresent = $_SESSION["alreadyPresent"];
        
        if (count($checked) < 1 ){ //If nothing is checked, then just use the list of members for that event
            $tempUnchecked = $memberId;
        }else{
        //Compare the two arrays, and determine which are unchecked
        $tempUnchecked = array_diff($memberId, $checked);
        }
        
        //Take the unchecked array, and reformat it
        foreach ($tempUnchecked as $value) {
            $unchecked[] = $value;
        }

        //Update the database
        try {
            //Get the event that was chosen
            $chosenEventId = $_SESSION["chosenEvent"];
            
       
            //Array to hold the items to be added to relationship table
            $newAttendanceAdd = array();
            
            
   //If the box is checked, do this
            $L = 0;
            foreach ($checked as $value) {
                if (!in_array($value, $alreadyPresent)) {
                    $newAttendanceAdd[] = $value; 
                    $newAttendanceAdd[] = $chosenEventId; //Add the id of the tbl, for the SQL statemtns
                    $L++;
                } else {
                    //If the checked value is already in the array, do something here
                }
            }
            
            $thisDatabase->db->beginTransaction();
            $query = "INSERT INTO tblAttends (fnkMemberId, fnkEventId) VALUES (?,?)";

            //Add the correct number of question marks depending on the size of the array.
            $i = 0;
            while ($i < $L-1) {
                $query .= ",(?,?)";
                $i++;
            }
            $Result = $thisDatabase->insert($query,$newAttendanceAdd);
            $dataEntered = $thisDatabase->db->commit();
        
            
            
            //Array to hold the items to be added to relationship table
            $newAttendanceDelete = array();
            
      //If the box is NOT checked, do this
            $k = 0;
            foreach ($unchecked as $value) {
                if (in_array($value, $alreadyPresent)) {
                    $newAttendanceDelete[] = $value; 
                    $newAttendanceDelete[] = $chosenEventId; //Add the id of the tbl, for the SQL statemtns
                    $k++;
                } else {
                    //If the unchecked value is not in already present, do this
                }
            }
            
            $thisDatabase->db->beginTransaction();
            $query = "DELETE entry FROM tblAttends AS entry 
                      CROSS JOIN ( SELECT fnkMemberId, fnkEventId FROM tblAttends ";
            $query .= "where (fnkMemberId = ? and fnkEventId = ?)";

            
            //Add the correct number of question marks depending on the size of the array.
            $i = 0;
            while ($i < $k-1) {
                $query .= " or (fnkMemberId = ? and fnkEventId = ?)";
                $i++;
            }
            $query .= " ) AS x USING (fnkMemberId, fnkEventId)";
            
            $Result = $thisDatabase->insert($query,$newAttendanceDelete);
            $dataEntered = $thisDatabase->db->commit();      
                
            
            
            
            print $query;
            print"<pre>";
            print "checked";
            print_r($checked);
            print"unchecked";
            print_r($unchecked);
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "There was a problem with accpeting your data please contact us directly.";
        }
    }

//  if (isset($_POST["btnPresent"])) {
// print "Yes";
//      
//  }
 
//    if (isset($_POST["btnAdd"]) AND empty($errorMsg)) {
//
//        //Once the button has been pressed
//
//
//
//        print "things worked";
////        include "upload.php";
//    } //END If the button is pressed and error empty
//    else {
//
//
//        //####################################
//        //
//        // SECTION 3b Error Messages
//        //
//        // display any error messages before we print out the form
//
//        if ($errorMsg) {
//            print '<div id="errors">';
//            print "<ol>\n";
//            foreach ($errorMsg as $err) {
//                print "<li>" . $err . "</li>\n";
//            }
//            print "</ol>\n";
//            print '</div>';
//        }
//    }
    ?>

    <!--Form that is used to get inputs and add member-->
<!--    <form action="<?php print $phpSelf; ?>" method="post" id="frmMembers">

        <fieldset class="eventInput">

        </fieldset>  loginInput 

        <fieldset class="button">
            <input type="submit" id="btnAdd" name="btnAdd" value="Add" tabindex="900" class="button">
        </fieldset>  ends buttons 


    </form>-->

    <?php
} //If the admin is logged in
?>
