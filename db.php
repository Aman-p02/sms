<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "sms";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Auto-maintenance: Expire scholarships that have passed their end date
$conn->query("UPDATE ss_master SET ss_status = 'inactive' WHERE ss_end < CURDATE() AND ss_status = 'active'");
?>