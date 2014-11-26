<?php

//Grab all of the information from the user table
try {
    $thisDatabase->db->beginTransaction();

//Select everything from the members table
    $query = "SELECT fldTitle as 'Event',";
    $query .=" fldBody as 'Content',";
    $query .=" fldImg as image";
    $query .=" FROM tblEvents";
    $results = $thisDatabase->Select($query);

    $dataEntered = $thisDatabase->db->commit();

    echo "<section class=\"displayEvents\">";
    for ($row = 0; $row < count($results); $row++) {
        echo "<section class=\"event\">";
        for ($col = 0; $col < 1 + count($results); $col++) {
            if ($col == 1) {
                echo "<section class=\"eventDescription\">" . $results[$row][$col] . "</section>";
            } elseif ($col == 0) {
                echo "<h3 class = \"eventHeading\">" . $results[$row][$col] . "</h3>";
            }
        }
       echo "</section>"; 
    }
    echo "</section>";
    

    //Display all of the information, in a table
//    print "<table>";
//
//    $firstTime = true;

    /* since it is associative array display the field names */
//    foreach ($results as $row) {
//        if ($firstTime) {
//            print "<thead><tr>";
//            $keys = array_keys($row);
//            foreach ($keys as $key) {
//                if (!is_int($key)) {
//                    print "<th>" . $key . "</th>";
//                }
//            }
//            print "</tr>";
//            $firstTime = false;
//        }

        /* display the data, the array is both associative and index so we are
         *  skipping the index otherwise records are doubled up */
//        $i=2;
//        print "<tr>";
//        foreach ($row as $field => $value) {
//            if (!is_int($field)) {
//                if( $i % 3 == 0 ){
//                    print "<td><pre>" . $value . "</pre></td>";
//                    $i++;
//                }else{
//                print "<td>" . $value . "</td>";
//                $i++;
//                }
//            }
//        }
//        print "</tr>";
//    }
//    print "</table>";
} catch (PDOExecption $e) {
    $thisDatabase->db->rollback();
    print "There was a problem with accpeting your data please contact us directly.";
}
?>
