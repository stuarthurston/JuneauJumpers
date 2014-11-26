<?php

//Grab all of the information from the user table
try {
    $thisDatabase->db->beginTransaction();

//Select everything from the members table
    $query = "SELECT fldFirstName as 'First Name',";
    $query .=" fldLastName as 'Last Name',";
    $query .=" fldUsername as Username,";
    $query .=" fldEmail as Email,";
    $query .=" CASE WHEN fldAdmin = 1 THEN 'Yes' ELSE 'No' END AS fldAdmin";
    $query .=" FROM tblUsers ORDER BY fldFirstName, fldLastName";
    $results = $thisDatabase->Select($query);
    $dataEntered = $thisDatabase->db->commit();


    //Get the userIds
    $thisDatabase->db->beginTransaction();
    $query = "SELECT pmkUserId from tblUsers ORDER BY fldFirstName, fldLastName";
    $userIdResult = $thisDatabase->Select($query);
    $dataEntered = $thisDatabase->db->commit();

    //Get the user admin status (fldAdmin)
    $thisDatabase->db->beginTransaction();
    $query = "SELECT fldAdmin from tblUsers ORDER BY fldFirstName, fldLastName";
    $adminStatusResult = $thisDatabase->Select($query);
    $dataEntered = $thisDatabase->db->commit();


    //Initalize the array that will hold the user Id and admin status
    $userId = array();
    $adminStatus = array();

    

    //Take the ID array, and reformat it
    foreach ($userIdResult as $row) {
        /* display the data, the array is both associative and index so we are
         *  skipping the index otherwise records are doubled up by using the 'is_int' */
        foreach ($row as $field => $value) {
            if (!is_int($field)) {
                $userId[] = $value;
            }
        }
    }

    //Take the Admin array, and reformat it
    foreach ($adminStatusResult as $row) {
        /* display the data, the array is both associative and index so we are
         *  skipping the index otherwise records are doubled up by using the 'is_int' */
        foreach ($row as $field => $value) {
            if (!is_int($field)) {
                $adminStatus[] = $value;
            }
        }
    }


    
//Reverse the order of the ids, used to match up the ID with the correct user
//$userIdReversed = array_reverse($userId);
    $userIdReversed = $userId;


//Reverse the order of the admins, used to match up the ID with the correct user
//$adminStatusReversed = array_reverse($adminStatus);
    $adminStatusReversed = $adminStatus;

    
    
    //Display all of the information, in a table
    print "<table>\n";

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
            print "\t\t<th>Admin</th>\n";
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
        echo "\t<td><input type='hidden' value='0' name='admin" . $userIdReversed[$i] . "'>";
        echo "<input type='checkbox' value='1' name='admin" . $userIdReversed[$i] . "' ";
        if ($adminStatusReversed[$i] == 1) {
            echo 'checked';
        };
        echo "></td>\n</tr>\n";
        $i++;
    }
    print "</table>\n";
    
    
    
} catch (PDOExecption $e) {
    $thisDatabase->db->rollback();
    print "There was a problem with accpeting your data please contact us directly.";
}
?>
