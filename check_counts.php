<?php
include "db.php";

$tables = ['scholarship', 'student_master', 'ss_master'];
foreach ($tables as $t) {
    $res = $conn->query("SELECT COUNT(*) as c FROM $t");
    $row = $res->fetch_assoc();
    echo "$t count: " . $row['c'] . "\n";
}

$res = $conn->query("SELECT s.stu_fname, s.stu_lname, s.stu_college, s.stu_program, 
               sm.ss_name, sm.ss_year, sm.ss_amount, sc.app_status 
        FROM scholarship sc
        INNER JOIN student_master s ON sc.stu_id = s.stu_id
        INNER JOIN ss_master sm ON sc.ss_id = sm.ss_id");
echo "Join count: " . $res->num_rows . "\n";
?>
