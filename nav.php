<nav>
    <ol id="mainNav">
    <?php
    
    //Home Page
    if(basename($_SERVER['PHP_SELF'])=="home.php"){
        print "\t".'<li><a class="activePage" href="home.php">Home</a></li>'."\n\t\t";
    } 
    else {
        print '<li><a href="home.php">Home</a></li>'."\n\t\t";
    }
    
    //Page1
    if(basename($_SERVER['PHP_SELF'])=="schedule.php"){
        print '<li><a class="activePage" href="schedule.php">Schedule</a></li>'."\n\t\t";
    } 
    else {
        print '<li><a href="schedule.php">Schedule</a></li>'."\n\t\t";
    }
    
    //Page2
    if(basename($_SERVER['PHP_SELF'])=="jumperResources.php"){
        print '<li><a class="activePage" href="jumperResources.php">Jumper Resources</a></li>'."\n\t\t";
    } 
    else {
        print '<li><a href="jumperResources.php">Jumper Resources</a></li>'."\n\t\t";
    }
    
    //Page3
    if(basename($_SERVER['PHP_SELF'])=="photos.php"){
        print '<li><a class="activePage" href="photos.php">Photos</a></li>'."\n\t\t";
    } 
    else {
        print '<li><a href="photos.php">Photos</a></li>'."\n\t\t";
    }
    
    //Page4
    if(basename($_SERVER['PHP_SELF'])=="about.php"){
        print '<li><a class="activePage" href="about.php">About</a></li>'."\n\t\t";
    } 
    else {
        print '<li><a href="about.php">About</a></li>'."\n\t\t";
    }
       
      ?>
        
    </ol>
    
<!--    This nav is built right to left, top element gets shoved to the right-->
    <ol id="secondaryNav">
        <?php
        if (!$_SESSION["admin"]) {
            if (basename($_SERVER['PHP_SELF']) == "loginPage.php") {
                print "\t" . '<li><a class="activePage" href="loginPage.php">Log In</a></li>' . "\n\t\t";
            } else {
                print '<li><a href="loginPage.php">Log In</a></li>' . "\n\t\t";
            }
        } //End if NOT admin
        ?>

<!--    This nav is built right to left, top element gets shoved to the right-->
        <?php
        if ($_SESSION["admin"]) {
            if (basename($_SERVER['PHP_SELF']) == "loginPage.php") {
                print "\t" . '<li><a class="activePage" href="loginPage.php">Log In</a></li>' . "\n\t\t";
            } else {
                print '<li><a href="loginPage.php">Log Out</a></li>' . "\n\t\t";
            }

            if (basename($_SERVER['PHP_SELF']) == "adminMembers.php") {
                print "\t" . '<li><a class="activePage" href="adminMembers.php">Add Members</a></li>' . "\n\t\t";
            } else {
                print '<li><a href="adminMembers.php">Add Members</a></li>' . "\n\t\t";
            }
            
            if (basename($_SERVER['PHP_SELF']) == "adminEditPrivs.php") {
                print "\t" . '<li><a class="activePage" href="adminEditPrivs.php">Add Admin</a></li>' . "\n\t\t";
            } else {
                print '<li><a href="adminEditPrivs.php">Add Admin</a></li>' . "\n\t\t";
            }
            
            if (basename($_SERVER['PHP_SELF']) == "adminCreateEvent.php") {
                print "\t" . '<li><a class="activePage" href="adminCreateEvent.php">Add Event</a></li>' . "\n\t\t";
            } else {
                print '<li><a href="adminCreateEvent.php">Add Event</a></li>' . "\n\t\t";
            }
            
            if (basename($_SERVER['PHP_SELF']) == "adminAttendance.php") {
                print "\t" . '<li><a class="activePage" href="adminAttendance.php">Attendance</a></li>' . "\n\t\t";
            } else {
                print '<li><a href="adminAttendance.php">Attendance</a></li>' . "\n\t\t";
            }
        } //End if admin
        ?>
  
        
  
    </ol>
	
    <div style="clear:both;"></div>
</nav>