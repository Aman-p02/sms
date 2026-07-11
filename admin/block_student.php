<?php
include "../db.php";

if (isset($_GET['stu_id'])) {
    $stu_id = $_GET['stu_id'];

    // Get current status
    $stmt = $conn->prepare("SELECT `stu_status` FROM `student_master` WHERE `stu_id` = '".$stu_id."'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $new_status = ($row['stu_status'] == 'active') ? 'blocked' : 'active';
        echo $new_status;

        // Update status
        $update = $conn->prepare("UPDATE student_master SET stu_status = '".$new_status."' WHERE `stu_id` = '".$stu_id."'");
        $update->execute();
    }

    header("Location: manage_students.php");
    exit();
}
?>