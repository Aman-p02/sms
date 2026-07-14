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

$current_page = basename($_SERVER['PHP_SELF']);
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
            padding: 12px 16px;
            display: block;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.25);
            border-left: 4px solid #fff;
            font-weight: 600;
        }
        .card-custom {
            border-radius: 12px;
        }
    </style>
      
       <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 sidebar p-0">
            <h4 class="text-center py-3 border-bottom mb-0">Student Panel</h4>

            <a href="stu_dashboard.php" class="<?php if($current_page == 'stu_dashboard.php') echo 'active'; ?>">Dashboard</a>
            <a href="stu_profile.php" class="<?php if($current_page == 'stu_profile.php') echo 'active'; ?>">Edit Profile</a>
            <?php 
            if($complete == "Yes")
            {
            ?>
            <a href="apply_scholarship.php" class="<?php if($current_page == 'apply_scholarship.php') echo 'active'; ?>">Apply Scholarship</a>
            <a href="stu_post_scholarship.php" class="<?php if($current_page == 'stu_post_scholarship.php') echo 'active'; ?>">Post-Scholarship Operations</a>
            <a href="stu_changePassword.php" class="<?php if($current_page == 'stu_changePassword.php') echo 'active'; ?>">Change Password</a>
            <a href="stu_feedback.php" class="<?php if($current_page == 'stu_feedback.php') echo 'active'; ?>">Feedback</a>
            <?php }?>
            <a href="logout.php">Logout</a>
        </div>