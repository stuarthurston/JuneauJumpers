<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Juneau Jumpers</title>
        <meta charset="utf-8">
        <meta name="author" content="Stuart Thurston">
        <meta name="description" content="Juneau Jumpers">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
        <![endif]-->

        <link rel="stylesheet" href="login_style.css" type="text/css" media="screen">
        <link rel="stylesheet" href="style.css" type="text/css" media="screen">
        
<!--        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>-->


        <?php
        //Start the session, use with session variables
        session_start();
        
        $debug = false;

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// PATH SETUP
//
//  $domain = "https://www.uvm.edu" or http://www.uvm.edu;

        $domain = "http://";
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS']) {
                $domain = "https://";
            }
        }

        $server = htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES, "UTF-8");

        $domain .= $server;

        $phpSelf = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, "UTF-8");

        $path_parts = pathinfo($phpSelf);

        if ($debug) {
            print "<p>Domain" . $domain;
            print "<p>php Self" . $phpSelf;
            print "<p>Path Parts<pre>";
            print_r($path_parts);
            print "</pre>";
        }

// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// inlcude all libraries
//
        
        require_once('lib/security.php');
      
        
        if ($path_parts['filename'] == "register") {
            include "lib/validation-functions.php";
        }
        
        require_once('../bin/myDatabase.php');



        ?>	

    </head>
    <!-- ################ body section ######################### -->

    <?php
    print '<body id="' . $path_parts['filename'] . '">';
    
    include "header.php";
    
    
    if ($_SESSION["admin"]) { //Create database in writer mode if admin
        
        $dbUserName = get_current_user() . '_writer';
        $whichPass = "w"; //flag for which one to use.
        $dbName = strtoupper(get_current_user()) . '_juneauJumpers';

        $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
        
    }else{ //Otherwise, open in reader mode
        $dbUserName = get_current_user() . '_reader';
        $whichPass = "r"; //flag for which one to use.
        $dbName = strtoupper(get_current_user()) . '_juneauJumpers';
        
        $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    }
       
    ?>