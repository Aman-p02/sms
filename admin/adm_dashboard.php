<?php
require_once 'session.php';
include "../db.php";

// Total Scholarships
$scholarship_res = $conn->query("SELECT COUNT(*) as cnt FROM ss_master");
$total_scholarships = $scholarship_res->fetch_assoc()['cnt'] ?? 0;

// Total Applicants
$applicant_res = $conn->query("SELECT COUNT(*) as cnt FROM scholarship");
$total_applicants = $applicant_res->fetch_assoc()['cnt'] ?? 0;

// Approved Candidates
$approved_res = $conn->query("SELECT COUNT(*) as cnt FROM scholarship WHERE app_status = 'Approved'");
$approved_candidates = $approved_res->fetch_assoc()['cnt'] ?? 0;

// Approved Amount
$amount_res = $conn->query("SELECT SUM(sm.ss_amount) as total_amount FROM scholarship sc INNER JOIN ss_master sm ON sc.ss_id = sm.ss_id WHERE sc.app_status = 'Approved'");
$total_approved_amount = $amount_res->fetch_assoc()['total_amount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <?php include 'sidebar.php'; ?>

            <!-- MAIN CONTENT -->
            <div class="col-md-9 col-lg-10 p-4 content-area">

                <h2 class="fw-bold mb-4">Admin Dashboard</h2>

                <div class="row g-4">
                    <div class="col-md-3">
                        <a href="view_scholarships.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Total Scholarships</h5>
                                <h3><?php echo number_format($total_scholarships); ?></h3>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="applied_student.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Total Applicants</h5>
                                <h3><?php echo number_format($total_applicants); ?></h3>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="approve.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Approved Candidates</h5>
                                <h3><?php echo number_format($approved_candidates); ?></h3>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="approve.php" class="text-decoration-none text-dark">
                            <div class="card shadow card-custom p-3 text-center">
                                <h5>Approved Amount</h5>
                                <h3><?php echo number_format($total_approved_amount); ?></h3>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>