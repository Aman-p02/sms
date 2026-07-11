<?php
    include "../db.php";

    $stu_id = $_GET['stu_id'];
    $ss_id = $_GET['ss_id'];
    $ss_name = $_GET['ss_name'];
    $app_status = 'Approved';
    
    $sql = "UPDATE scholarship
            SET app_status = '".$app_status ."'
            WHERE stu_id = '".$stu_id."' AND ss_id = '".$ss_id."'";
    
    $result = $conn->query($sql);

    // Update ss_master as requested
    $sql2 = "UPDATE ss_master SET ss_status = 'Approve' WHERE ss_id = '".$ss_id."'";
    $conn->query($sql2);


    if ($result) {
           header("Location: list_names.php?ss_id=$ss_id&stu_id=$stu_id&ss_name=$ss_name");
                #echo "Student registered successfully!";
            } else {
                $message = "<div style='color:red;'>Error occurred!</div>";
                #echo "Error: " . $stmt->error;
            

        }

    exit();

?>