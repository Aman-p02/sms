<?php
include "db.php";
$sql = "ALTER TABLE ss_master ADD COLUMN ss_document VARCHAR(255) NULL";
if ($conn->query($sql) === TRUE) {
    echo "Column ss_document added successfully";
} else {
    echo "Error adding column: " . $conn->error;
}
$conn->close();
?>
