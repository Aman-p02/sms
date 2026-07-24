<?php
$current_page = basename($_SERVER['PHP_SELF']);
$base_url = '/SMS/admin/';
$logout_url = '/SMS/logout.php';
$prediction_url = '/SMS/prediction.php';
$outlier_url = '/SMS/outlier.php';
$cluster_url = '/SMS/cluster.php';
?>
<style>
/* Force sticky behavior bypassing CSS cache */
.sidebar {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: 0 !important;
    height: 100vh !important;
    overflow-y: auto !important;
    z-index: 1020 !important;
}
/* Hide scrollbar for a cleaner UI */
.sidebar::-webkit-scrollbar { width: 5px; }
.sidebar::-webkit-scrollbar-thumb { background: #495057; border-radius: 10px; }
</style>
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
            <a href="<?php echo $prediction_url; ?>" class="<?php if($current_page == 'prediction.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Prediction</a>
            <a href="<?php echo $outlier_url; ?>" class="<?php if($current_page == 'outlier.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Outlier Detection</a>
            <a href="<?php echo $cluster_url; ?>" class="<?php if($current_page == 'cluster.php') echo 'active bg-primary text-white border-start border-4 border-info'; ?>">Clustering Analysis</a>
            <a href="<?php echo $logout_url; ?>">Logout</a>
        </div>
        
        <?php 
        // Ensure Chatbot is loaded on all admin pages
        $chatbot_path = $_SERVER['DOCUMENT_ROOT'] . '/SMS/chatbot_widget.php';
        if(file_exists($chatbot_path)) {
            include_once $chatbot_path; 
        }
        ?>