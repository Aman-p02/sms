<?php
$current_page = basename($_SERVER['PHP_SELF']);
$base_url = (basename(getcwd()) === 'admin') ? '' : 'admin/';
$logout_url = (basename(getcwd()) === 'admin') ? '../logout.php' : 'logout.php';
?>
<!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 sidebar p-0">
            <h4 class="text-center py-3 border-bottom mb-0">Admin Panel</h4>
            <a href="<?php echo $base_url; ?>adm_dashboard.php" class="<?php if($current_page == 'adm_dashboard.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Dashboard</a>
            <a href="<?php echo $base_url; ?>manage_students.php" class="<?php if($current_page == 'manage_students.php' || $current_page == 'stu_profile.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Manage Students</a>
            <a href="<?php echo $base_url; ?>add_scholarship.php" class="<?php if($current_page == 'add_scholarship.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Add Scholarship</a>
            <a href="<?php echo $base_url; ?>view_scholarships.php" class="<?php if($current_page == 'view_scholarships.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">View Scholarship</a>
            <a href="<?php echo $base_url; ?>applied_student.php" class="<?php if($current_page == 'applied_student.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Applied Students</a>
            <a href="<?php echo $base_url; ?>approve.php" class="<?php if($current_page == 'approve.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Approve / Reject</a>
            <a href="<?php echo $base_url; ?>year_wise_summary.php" class="<?php if($current_page == 'year_wise_summary.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Report</a>
            <a href="<?php echo $base_url; ?>manage_feedback.php" class="<?php if($current_page == 'manage_feedback.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Manage Feedback</a>
            <a href="<?php echo $base_url; ?>settings.php" class="<?php if($current_page == 'settings.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Change Password</a>
            <a href="<?php echo $logout_url; ?>">Logout</a>
        </div>