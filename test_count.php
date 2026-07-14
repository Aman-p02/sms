<?php
include "db.php";
$q = $conn->query("SELECT s.app_status, s.stu_id, stu.stu_fname, ss.ss_name FROM scholarship s JOIN student_master stu ON s.stu_id = stu.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id WHERE s.app_status = 'Approved'");
while ($r = $q->fetch_assoc()) {
    echo $r['stu_fname'] . " - " . $r['ss_name'] . "\n";
}
?>
