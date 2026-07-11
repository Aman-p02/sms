<?php

include 'db.php';
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

/*FETCH SIGLE RAW*/
$result = mysqli_query($conn, "SELECT * FROM student_master WHERE stu_id = '".$stu_id."'");
$row = mysqli_fetch_assoc($result);
$complete = $row['complete'];

?>
   
    
     <style>
        body {
            background-color: #f0f2f5;
        }
        .sidebar {
            min-height: 100vh;
            background: #0d6efd;
            color: #fff;
        }
        .sidebar a {
            color: white;
            padding: 12px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }
        .card-custom {
            border-radius: 12px;
        }
    </style>
      
       <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 sidebar p-0">
            <h4 class="text-center py-3 border-bottom">Student Panel</h4>

            <a href="stu_profile.php">Edit Profile</a>
            <?php 
            if($complete == "Yes")
            {
            ?>
            <a href="apply_scholarship.php">Apply Scholarship</a>
            <a href="#postScholarship">Post-Scholarship Operations</a>
            <a href="stu_changePassword.php">Change Password</a>
            <a href="stu_feedback.php">Feedback</a>
            <?php }?>
            <a href="logout.php">Logout</a>
        </div>