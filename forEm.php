<?php
include "top.php";

$debug = false;
error_reporting(E_All);
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}

if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    
    /* ##### Step one 
     * 
     * create your database object using the appropriate database username

    */
    require_once('../bin/myDatabase.php');

//    $searchQuery = $_GET['searchQuery']; //Get the SQL Statement that was requested
    $dbUserName = get_current_user() . '_reader';
    $whichPass = "r"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_UVM_Courses';
    

    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form
$department = "";
$courseNumber = "";
$building = "";
$startTime = "";
$professor = "";
$ZSection = "No";



//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$departmentERROR = false;
$courseNumberERROR = false;
$buildingERROR = false;
$startTimeERROR = false;
$professorERROR = false;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2a Security
    // 
    if (!securityCheck(true)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2b Sanitize (clean) data 
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.

    $department = htmlentities($_POST["txtDepartment"], ENT_QUOTES, "UTF-8");
    
    $courseNumber = htmlentities($_POST["txtCourseNumber"], ENT_QUOTES, "UTF-8");
    
    $building = htmlentities($_POST["lstBuildings"], ENT_QUOTES, "UTF-8");

    $startTime = htmlentities($_POST["txtStartTime"], ENT_QUOTES, "UTF-8");

    $professor = htmlentities($_POST["txtProfessor"], ENT_QUOTES, "UTF-8");

    $ZSection = htmlentities($_POST["radZSections"], ENT_QUOTES, "UTF-8");
 


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2c Validation
    //
    // Validation section. Check each value for possible errors, empty or
    // not what we expect. You will need an IF block for each element you will
    // check (see above section 1c and 1d). The if blocks should also be in the
    // order that the elements appear on your form so that the error messages
    // will be in the order they appear. errorMsg will be displayed on the form
    // see section 3b. The error flag ($emailERROR) will be used in section 3c.

    
    if($department !== ""){        
        if (!verifyAlpha($department)){
        $errorMsg[] = "Please Enter a valid Department Name";
        $departmentERROR = true;
        }
    }
    
    if($courseNumber !== ""){        
        if (!verifyNumeric($courseNumber)){
        $errorMsg[] = "Please Enter a valid Course Number";
        $courseNumberERROR = true;
        }
    }
    
    if($building !== ""){        
        if (!verifyAlphaNum($building)){
        $errorMsg[] = "Please Enter a valid Building Name";
        $buildingERROR = true;
        }
    }
    
    if($startTime !== ""){        
        if (!verifyTime($startTime)){
        $errorMsg[] = "Please Enter a valid Start Time";
        $startTimeERROR = true;
        }
    }
    
    if($professor !== ""){        
        if (!verifyAlpha($professor)){
        $errorMsg[] = "Please Enter a valid Professor Name";
        $professorERROR = true;
        }
    }
    
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";  
    } // end form is valid
    
} // ends if form was submitted.

//#############################################################################
//
// SECTION 3 Display Form
//
?>

<article id="main">

    <?php
    //####################################
    //
    // SECTION 3a.
    //
    // 
    // 
    // 
    // If its the first time coming to the form or there are errors we are going
    // to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit

        
        //Remove trailing whitespace        
        $department = rtrim($department);
        $courseNumber = rtrim($courseNumber);
        $building = rtrim($building);
        $startTime = rtrim($startTime);
        $professor = rtrim($professor);
        
        //If the time is between 1am and 9am, add a preceding 0 as well as trailing 0s
        if(strlen($startTime)==1){
            $startTime = "0".$startTime.":00";
        }
        
        //If the time is 10, 11, or 12, add trailing 0s
        if((strlen($startTime)==2 and $startTime[0]==1) and ($startTime[1]==0 or $startTime[1]==1 or $startTime[1]==2)) {
            $startTime = $startTime.":00";
        }
        
        //If the second character is a colon, add a preceding 0
        if (substr($startTime,1,1) == ":"){
            $startTime = "0".$startTime;
        }
        
        //This removes the last 2 characters of the time, the minutes.
        //They will be added back on to the end, if the time is between 1 and 7
        $minutes = substr($startTime,-2);
        
        //Changes times in the afternoon to 24hr time, add minutes on, to the end
        switch ($startTime) {
            case 1:
                $startTime = "13:".$minutes; 
                break;
            case 2:
                $startTime = "14:".$minutes; 
                break;
            case 3:
                $startTime = "15:".$minutes; 
                break;
            case 4:
                $startTime = "16:".$minutes; 
                break;
            case 5:
                $startTime = "17:".$minutes; 
                break;
            case 6:
                $startTime = "18:".$minutes; 
                break;
            case 7:
                $startTime = "19:".$minutes; 
                break;

          default:
            $startTime = $startTime;
        }

        
        //Add '%' to each variable
        $department = $department.'%';
        $courseNumber = $courseNumber.'%';
        $startTime = $startTime.'%';
        $professor = $professor.'%';
        
          

//        print " ";
//        print $department;
//        print " ";
//        print $courseNumber;
//        print " ";
//        print $building;
//        print " ";
//        print $startTime;
//        print " ";
//        print $professor;
//        print " g";
//        print $ZSection;
//        print " g";
        
        
       
        
        $query ="
                Select 
                    concat(fldDepartment, ' ',fldCourseNumber) AS Course, 
                    fldCourseName AS Name, 
                    fldCRN AS CRN,
                    concat(fldType, ' ', fldSection) AS Section,
                    fldMaxStudents - fldNumStudents AS 'Seats Remaining',
                    concat(fldStart,' - ', fldStop) AS Time,
                    fldDays AS Day,
                    fldBuilding AS Building,
                    fldRoom AS Room,
                    concat(fldFirstName, ' ', fldLastName) AS Profesor";
        
        $query .="
                FROM
                    tblSections,
                    tblTeachers,
                    tblCourses";
        
       $query .=" WHERE pmkNetId = fnkTeacherNetId";
       $query .=" AND pmkCourseId = fnkCourseId";
       $query .=" AND fldDepartment like ?";
       $query .=" AND fldCourseNumber like ?";
       $query .=" AND fldStart like ?";
       $query .=" AND fldLastName like ?";
       $query .=" AND fldBuilding like ?";         
                
       if ($ZSection == "No"){
                        $query .= " AND fldSection NOT like 'z%' OR 'e__' ";
                }

        
                
         $data = array($department,$courseNumber, $startTime, $professor, $building);
                
                
        $results = $thisDatabase->select($query, $data);
        
        /* ##### Step four
     * prepare output and loop through array

     *      */
//    $numberRecords = count($results);

//    print "<h2>Total Records: " . $numberRecords . "</h2>";
//    print "<h3>SQL: " . $query . "</h3>";
?>
    <script>
        function resetForm() {
            window.location.href ="https://sathurst.w3.uvm.edu/cs148/assignment5.0/form.php";
        }
    </script>
    <aside class="resetButton">
      <button  id ="btnReset" onclick="resetForm();">New Search</button>  
    </aside>
    <aside id="backToTop">
        <a href="#form">Top</a>
    </aside>
    
            <?php
            
   if( empty( $results ) )
    {
     print"<h2 class=\"noResults\">There are no resuslts for that search, try again.</h2>";
    }       
print "<table>";

    $firstTime = true;

    /* since it is associative array display the field names */
    foreach ($results as $row) {
        if ($firstTime) {
            print "<thead><tr>";
            $keys = array_keys($row);
            foreach ($keys as $key) {
                if (!is_int($key)) {
                    print "<th>" . $key . "</th>";
                }
            }
            print "</tr>";
            $firstTime = false;
        }
        
        /* display the data, the array is both associative and index so we are
         *  skipping the index otherwise records are doubled up */
        print "<tr>";
        foreach ($row as $field => $value) {
            if (!is_int($field)) {
                print "<td>" . $value . "</td>";
            }
        }
        print "</tr>";
    }
    print "</table>";
    
    

    } else {


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


        //####################################
        //
        // SECTION 3c html Form
        //
        /* Display the HTML form. note that the action is to this same page. $phpSelf
          is defined in top.php
          NOTE the line:

          value="<?php print $email; ?>

          this makes the form sticky by displaying either the initial default value (line 35)
          or the value they typed in (line 84)

          NOTE this line:

          <?php if($emailERROR) print 'class="mistake"'; ?>

          this prints out a css class so that we can highlight the background etc. to
          make it stand out that a mistake happened here.

         */
        

        ?>

    

        <form action="<?php print $phpSelf ;?>" method="post" id="frmRegister">

            <fieldset class="wrapper">
              

                <fieldset class="wrapperTwo">
     
                        <section class="fldInput">
                            <label for="txtDepartment">Department </label>
                            <section class="inputWrapper">    
                                <input type="text" id="txtDepartment" name="txtDepartment"
                                           value="<?php print $department; ?>"
                                           tabindex="100" maxlength="45" placeholder="Enter the Department: CS"
                                           <?php if ($departmentERROR) print 'class="mistake"'; ?>
                                           onfocus="this.select();"
                                           autofocus />
                            </section>
                        </section>
                        
                        <section class="fldInput">
                            <label for="txtCourseNumber">Course Number</label>
                            <section class="inputWrapper">    
                                <input type="text" id="txtCourseNumber" name="txtCourseNumber"
                                           value="<?php print $courseNumber; ?>"
                                           tabindex="200" maxlength="45" placeholder="Enter the Course Number: 148"
                                           <?php if ($courseNumberERROR) print 'class="mistake"'; ?>
                                           onfocus="this.select();"
                                           autofocus/>
                            </section>
                        </section>
                        
                        <section class="fldInput">
                        <?php
                         $buildingSearch = "SELECT DISTINCT fldBuilding FROM tblSections ORDER BY fldBuilding ASC";
                         $buildingList = $thisDatabase->select($buildingSearch);
                                           
                         print "<label for=\"lstBuildings\">Building </label>
                            <section class=\"inputWrapper\">
                            <select id=\"lstBuildings\"
                                    name=\"lstBuildings\"
                                    tabindex=\"300\" >";
                         //This item I have manually entered so that it will pass validation
                         
                         print "<option value=\"%\"> All Buildings</option>";
                         for ($row = 1; $row < count($buildingList); $row++) {
                              for ($col = 0; $col < 1; $col++) {
                                echo "<option value=\"".$buildingList[$row][$col]."\">".$buildingList[$row][$col]."</option>\n";
                              }
                              
                        }
                            
                            print "</select>\n ";
                          print"</section>\n";
                                                
                         ?>
                       </section>
                        <section class="fldInput">
                        <label for="txtStartTime">StartTime</label>
                        <section class="inputWrapper">
                            <input type="text" id="txtStartTime"  name="txtStartTime"
                                       value="<?php print $startTime; ?>"
                                       tabindex="400" maxlength="45" placeholder="Enter the Start Time: 8:30"
                                       <?php if ($startTimeERROR) print 'class="mistake"'; ?>
                                       onfocus="this.select();"
                                       autofocus/>
                        </section>    
                        </section>
                            
                        <section class="fldInput">
                        <label for="txtProfessor">Professor </label>
                        <section class="inputWrapper">    
                            <input type="text" id="txtProfessor" name="txtProfessor"
                                       value="<?php print $professor; ?>"
                                       tabindex="500" maxlength="45" placeholder="Enter the Professors Last Name"
                                       <?php if ($professorERROR) print 'class="mistake"'; ?>
                                       onfocus="this.select()"
                                       autofocus/>
                        </section>
                        </section>

    
                  
                    <section class="fldInput"> 
                        <section class="yesNoLabel">
                            <h2>
                                Include Z Sections?
                            </h2>    
                        </section>
                        
                        <section class="yesNo">
                            <input type="radio" 
                              id="radZSectionsYes" 
                              name="radZSections" 
                              value="Yes"
                              <?php if ($ZSection == "Yes") print 'checked' ?>
                              tabindex="600"/>
                            <label for="radZSectionsYes">Yes</label>

                            <input type="radio" 
                                id="radZSectionsNo" 
                                name="radZSections" 
                                value="No"
                                <?php if ($ZSection == "No") print 'checked' ?>
                                tabindex="700"/>
                            <label for="radZSectionsNo">No</label>
                        </section>
               
                     
                    </section>


                    
                </fieldset> <!-- ends wrapper Two -->
                
                <fieldset class="buttons">
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Search" tabindex="10000" class="button">
                </fieldset> <!-- ends buttons -->
                
            </fieldset> <!-- Ends Wrapper -->
        </form>

    <?php
    } // end body submit
    ?>

</article>

<?php include "footer.php"; ?>

</body>
</html>

