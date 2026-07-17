<?php
include "../db.php";

if (isset($_GET['stu_id'])) {
    $stu_id = intval($_GET['stu_id']); // Ensure integer to prevent injection

    // 1. Delete from feedback table
    $stmt_fb = $conn->prepare("DELETE FROM feedback WHERE `stu_id` = ?");
    $stmt_fb->bind_param("i", $stu_id);
    $stmt_fb->execute();
    $stmt_fb->close();

    // 2. Delete from scholarship table
    $stmt_sc = $conn->prepare("DELETE FROM scholarship WHERE `stu_id` = ?");
    $stmt_sc->bind_param("i", $stu_id);
    $stmt_sc->execute();
    $stmt_sc->close();

    // 3. Delete from student_master table
    $stmt = $conn->prepare("DELETE FROM student_master WHERE `stu_id` = ?");
    $stmt->bind_param("i", $stu_id);

    if ($stmt->execute()) {
        // Success
        $stmt->close();
        header("Location: manage_students.php?msg=deleted");
    } else {
        // Error
        $stmt->close();
        header("Location: view_students.php?msg=error");
    }

    exit();
}
?>