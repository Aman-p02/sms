<?php
    include "db.php";


if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 2592000);
    session_set_cookie_params(2592000);
    session_start();
}

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
    $ss_id = $_GET['ss_id'];
    $app_status = 'Applied';
    
    // Security Check: Verify student is not blocked
    $status_res = $conn->query("SELECT stu_status FROM student_master WHERE stu_id = '".$stu_id."'");
    $status_row = $status_res->fetch_assoc();
    if ($status_row && $status_row['stu_status'] == 'blocked') {
        header("Location: apply_scholarship.php");
        exit();
    }

    $sql = "INSERT INTO scholarship (`stu_id`, `ss_id`, `app_status`) VALUES ('".$stu_id."', '". $ss_id ."', '". $app_status ."')";

    $result = $conn->query($sql);

    if ($result) {
           header("Location: apply_scholarship.php");
                #echo "Student registered successfully!";
            } else {
                $message = "<div style='color:red;'>Error occurred!</div>";
                #echo "Error: " . $stmt->error;
            

        }

    exit();

?>