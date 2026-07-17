<?php
require_once 'session.php';
include "../db.php";

// Total Scholarships
$scholarship_res = $conn->query("SELECT COUNT(*) as cnt FROM ss_master");
$total_scholarships = $scholarship_res->fetch_assoc()['cnt'] ?? 0;

// Total Applications
$application_res = $conn->query("SELECT COUNT(*) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id");
$total_applications = $application_res->fetch_assoc()['cnt'] ?? 0;

// Total Applicants
$applicant_res = $conn->query("SELECT COUNT(DISTINCT s.stu_id) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id");
$total_applicants = $applicant_res->fetch_assoc()['cnt'] ?? 0;

// Approved Scholarships
$approved_ss_res = $conn->query("SELECT COUNT(*) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id WHERE s.app_status = 'Approved'");
$approved_scholarships = $approved_ss_res->fetch_assoc()['cnt'] ?? 0;

// Approved Candidates
$approved_cand_res = $conn->query("SELECT COUNT(DISTINCT s.stu_id) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id WHERE s.app_status = 'Approved'");
$approved_candidates = $approved_cand_res->fetch_assoc()['cnt'] ?? 0;

// Approved Amount
$amount_res = $conn->query("SELECT SUM(sm.ss_amount) as total_amount FROM scholarship sc INNER JOIN ss_master sm ON sc.ss_id = sm.ss_id INNER JOIN student_master stu ON sc.stu_id = stu.stu_id WHERE sc.app_status = 'Approved'");
$total_approved_amount = $amount_res->fetch_assoc()['total_amount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <?php include 'sidebar.php'; ?>

            <!-- MAIN CONTENT -->
            <div class="col-md-9 col-lg-10 p-4 content-area">

                <h2 class="fw-bold mb-4">Admin Dashboard</h2>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <a href="view_scholarships.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Total Scholarships</h5>
                                <h3><?php echo number_format($total_scholarships); ?></h3>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-4">
                        <a href="applied_student.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Total Applications</h5>
                                <h3><?php echo number_format($total_applications); ?></h3>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="applied_student.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Total Applicants</h5>
                                <h3><?php echo number_format($total_applicants); ?></h3>
                            </div>
                        </a>
                    </div>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <a href="approve.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Approved Scholarships</h5>
                                <h3><?php echo number_format($approved_scholarships); ?></h3>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-4">
                        <a href="approve.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Approved Candidates</h5>
                                <h3><?php echo number_format($approved_candidates); ?></h3>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="approved_summary.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Approved Amount</h5>
                                <h3><?php echo number_format($total_approved_amount); ?></h3>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Notifications Section -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card shadow card-custom">
                            <div class="card-header bg-white border-bottom pt-3 pb-2 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Recent Notifications</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php
                                $notif_res = $conn->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 10");
                                if ($notif_res && $notif_res->num_rows > 0):
                                ?>
                                <ul class="list-group list-group-flush">
                                    <?php while($notif = $notif_res->fetch_assoc()): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-3 <?php echo $notif['is_read'] ? 'bg-light' : ''; ?>">
                                        <div>
                                            <i class="bi bi-bell text-primary me-2"></i>
                                            <span><?php echo htmlspecialchars($notif['message']); ?></span>
                                            <div class="text-muted small mt-1"><i class="bi bi-clock me-1"></i><?php echo date('d M Y, h:i A', strtotime($notif['created_at'])); ?></div>
                                        </div>
                                        <?php if($notif['link']): ?>
                                            <a href="<?php echo htmlspecialchars($notif['link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">View Document</a>
                                        <?php endif; ?>
                                    </li>
                                    <?php endwhile; ?>
                                </ul>
                                <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-bell-slash fs-3 mb-2 d-block"></i>
                                    <p class="mb-0">No new notifications.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>