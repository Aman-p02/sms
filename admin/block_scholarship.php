<?php
include "../db.php";

if (isset($_GET['ss_id'])) {
    $ss_id = $_GET['ss_id'];

    // Get current status
    $stmt = $conn->prepare("SELECT `ss_status` FROM `ss_master` WHERE `ss_id` = '".$ss_id."'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $new_status = ($row['ss_status'] == 'active') ? 'blocked' : 'active';
        /*echo $new_status;*/

        // Update status
        $update = $conn->prepare("UPDATE ss_master SET ss_status = '".$new_status."' WHERE `ss_id` = '".$ss_id."'");
        $update->execute();
    }

    header("Location: view_scholarships.php");
    exit();
}
?>