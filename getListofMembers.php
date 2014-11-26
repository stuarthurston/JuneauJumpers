<?php

//Grab all of the information from the members table
try {
    $thisDatabase->db->beginTransaction();

//Select everything from the members table
    $query = "SELECT fldFirstName as 'First Name',";
    $query .=" fldLastName as 'Last Name',";
    $query .=" fldAge as Age,";
    $query .=" fldPosition as Position,";
    $query .=" fldBio as Biography,";
    $query .=" fldEmail as Email,";
    $query .=" fldPhone as 'Phone Number',";
    $query .=" fldImg as Image";
    $query .=" FROM tblMembers";
    $results = $thisDatabase->Select($query);

    $dataEntered = $thisDatabase->db->commit();


    //Display all of the information, in a table
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
} catch (PDOExecption $e) {
    $thisDatabase->db->rollback();
    print "There was a problem with accpeting your data please contact us directly.";
}
?>
