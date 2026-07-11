<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent browser caching to avoid back-button after logout
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Session check
if (!isset($_SESSION['adm_id'])) {
    // If not logged in, redirect to index (home page) as requested by user
    header("Location: ../index.php");
    exit();
}
$adm_id = $_SESSION['adm_id'];
?>
