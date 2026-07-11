<?php
include "../db.php";

if (isset($_GET['stu_id'])) {
    $stu_id = $_GET['stu_id'];
    echo $stu_id;
    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM student_master WHERE `stu_id` = '".$stu_id."'");

    if ($stmt->execute()) {
        // Success
        header("Location: manage_students.php?msg=deleted");
    } else {
        // Error
        header("Location: view_students.php?msg=error");
    }

    exit();
}
?>