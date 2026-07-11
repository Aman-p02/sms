<?php
include "db.php";
$res = $conn->query("DESCRIBE student_master");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>
