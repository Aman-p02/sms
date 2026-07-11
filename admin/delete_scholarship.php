<?php
include "../db.php";


    $ss_id = $_GET['ss_id'];
    
    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM ss_master WHERE `ss_id` = '".$ss_id."'");

    
    if ($stmt->execute()) {
        // Success
        header("Location: view_scholarships.php?msg=deleted");
    } else {
        // Error
        header("Location: view_students.php?msg=error");
    }

    exit();

?>