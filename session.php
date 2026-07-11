<?php

session_start();

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Session check
if (!isset($_SESSION['stu_id'])) {
    header("Location: index.php");
    exit();
}

$stu_id = $_SESSION['stu_id'];

?>