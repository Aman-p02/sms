<?php
    include "../db.php";

    $stu_id = $_GET['stu_id'];
    $ss_id = $_GET['ss_id'];
    $ss_name = $_GET['ss_name'];
    $app_status = 'Applied';
    
    $sql = "UPDATE scholarship
            SET app_status = '".$app_status ."'
            WHERE stu_id = '".$stu_id."' AND ss_id = '".$ss_id."'";
    
    $result = $conn->query($sql);

    // Update ss_master as requested
    $sql2 = "UPDATE ss_master SET ss_status = 'Action' WHERE ss_id = '".$ss_id."'";
    $conn->query($sql2);


    if ($result) {
           header("Location: list_names.php?ss_id=$ss_id&stu_id=$stu_id&ss_name=" . urlencode($ss_name));
    } else {
        $message = "<div style='color:red;'>Error occurred!</div>";
    }

    exit();
?>
