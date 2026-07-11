<?php
include "db.php";
$res = $conn->query("DESCRIBE ss_master");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>
