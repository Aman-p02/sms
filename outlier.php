<?php
session_start();
if (!isset($_SESSION['adm_id'])) {
    header("Location: admin/adm_login.php");
    exit();
}
include 'db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Outlier</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css">
    <link href="admin/css/style.css" rel="stylesheet">
</head>
<body>
<?php

// Fetch all students for IQR calculation
$query = "SELECT stu_id, stu_enroll, stu_fname, stu_lname, stu_gpa, stu_perc FROM student_master";
$result = $conn->query($query);

$students = [];
$gpas = [];
$percs = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
    if (is_numeric($row['stu_gpa']))
        $gpas[] = (float) $row['stu_gpa'];
    if (is_numeric($row['stu_perc']))
        $percs[] = (float) $row['stu_perc'];
}

// Helper function to calculate IQR bounds
function getIQRBounds($arr)
{
    if (count($arr) < 4)
        return ['lower' => 0, 'upper' => 0, 'q1' => 0, 'q3' => 0, 'mean' => 0];
    sort($arr);
    $count = count($arr);
    $mean = array_sum($arr) / $count;

    // Calculate Q1 (25th percentile)
    $q1_index = ($count - 1) * 0.25;
    $q1 = $arr[floor($q1_index)] + ($q1_index - floor($q1_index)) * ($arr[ceil($q1_index)] - $arr[floor($q1_index)]);

    // Calculate Q3 (75th percentile)
    $q3_index = ($count - 1) * 0.75;
    $q3 = $arr[floor($q3_index)] + ($q3_index - floor($q3_index)) * ($arr[ceil($q3_index)] - $arr[floor($q3_index)]);

    $iqr = $q3 - $q1;
    return [
        'q1' => $q1,
        'q3' => $q3,
        'iqr' => $iqr,
        'lower' => $q1 - (1.5 * $iqr),
        'upper' => $q3 + (1.5 * $iqr),
        'mean' => $mean
    ];
}

$gpa_stats = getIQRBounds($gpas);
$perc_stats = getIQRBounds($percs);

$outliers = [];
foreach ($students as $stu) {
    $gpa = (float) $stu['stu_gpa'];
    $perc = (float) $stu['stu_perc'];

    $is_gpa_outlier = ($gpa < $gpa_stats['lower'] || $gpa > $gpa_stats['upper']);
    $is_perc_outlier = ($perc < $perc_stats['lower'] || $perc > $perc_stats['upper']);

    if ($is_gpa_outlier || $is_perc_outlier) {
        $reasons = [];
        if ($is_gpa_outlier)
            $reasons[] = "GPA ($gpa)";
        if ($is_perc_outlier)
            $reasons[] = "Percentage ($perc)";

        $stu['reasons'] = implode(" & ", $reasons);
        $stu['gpa_flag'] = $is_gpa_outlier;
        $stu['perc_flag'] = $is_perc_outlier;
        $outliers[] = $stu;
    }
}
?>
<style>
    .card-custom {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    }
</style>
<div class="container-fluid">
    <div class="row">
        <?php include 'admin/sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark"></i>Outlier Detection (IQR Method)</h2>
                <span class="badge bg-primary fs-6 p-2 rounded-pill">Total Students Analyzed:
                    <?php echo count($students); ?></span>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card card-custom shadow-sm bg-gradient-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold border-bottom border-light pb-2"><i
                                    class="bi bi-mortarboard me-2"></i>GPA Statistics</h5>
                            <div class="row mt-3 text-center">
                                <div class="col-4">
                                    <div class="small opacity-75">Mean</div>
                                    <div class="fs-4 fw-bold"><?php echo round($gpa_stats['mean'], 2); ?></div>
                                </div>
                                <div class="col-4 border-start border-end border-light">
                                    <div class="small opacity-75">Lower Bound</div>
                                    <div class="fs-4 fw-bold"><?php echo round($gpa_stats['lower'], 2); ?></div>
                                </div>
                                <div class="col-4">
                                    <div class="small opacity-75">Upper Bound</div>
                                    <div class="fs-4 fw-bold"><?php echo round($gpa_stats['upper'], 2); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-custom shadow-sm bg-gradient-warning text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold border-bottom border-light pb-2"><i
                                    class="bi bi-percent me-2"></i>Percentage Statistics</h5>
                            <div class="row mt-3 text-center">
                                <div class="col-4">
                                    <div class="small opacity-75">Mean</div>
                                    <div class="fs-4 fw-bold"><?php echo round($perc_stats['mean'], 2); ?></div>
                                </div>
                                <div class="col-4 border-start border-end border-light">
                                    <div class="small opacity-75">Lower Bound</div>
                                    <div class="fs-4 fw-bold"><?php echo round($perc_stats['lower'], 2); ?></div>
                                </div>
                                <div class="col-4">
                                    <div class="small opacity-75">Upper Bound</div>
                                    <div class="fs-4 fw-bold"><?php echo round($perc_stats['upper'], 2); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div
                    class="card-header bg-white border-bottom pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Detected
                        Outliers</h5>
                    <span class="badge bg-danger fs-6 rounded-pill"><?php echo count($outliers); ?> Anomalies
                        Found</span>
                </div>
                <div class="card-body p-0">
                    <?php if (count($outliers) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Enrollment No.</th>
                                        <th>Student Name</th>
                                        <th>GPA</th>
                                        <th>Percentage</th>
                                        <th>Anomaly Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($outliers as $out): ?>
                                        <tr>
                                            <td class="ps-4 fw-semibold text-secondary">
                                                <?php echo htmlspecialchars($out['stu_enroll']); ?></td>
                                            <td class="fw-bold">
                                                <?php echo htmlspecialchars($out['stu_fname'] . ' ' . $out['stu_lname']); ?>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge <?php echo $out['gpa_flag'] ? 'bg-danger' : 'bg-success bg-opacity-25 text-success'; ?> px-3 py-2 rounded-pill fs-6">
                                                    <?php echo htmlspecialchars($out['stu_gpa']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge <?php echo $out['perc_flag'] ? 'bg-danger' : 'bg-success bg-opacity-25 text-success'; ?> px-3 py-2 rounded-pill fs-6">
                                                    <?php echo htmlspecialchars($out['stu_perc']); ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-danger fw-bold small">
                                                    <i class="bi bi-bug me-1"></i> Out of bounds:
                                                    <?php echo htmlspecialchars($out['reasons']); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle display-1 mb-3 text-success d-block opacity-50"></i>
                            <h4>No Outliers Detected!</h4>
                            <p>All student records fall within the expected statistical range.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php include 'footer.php'; ?>
        </div>
    </div>
</div>
</body>
</html>