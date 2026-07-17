<?php
require_once 'session.php';
include "../db.php";

$filter_year = isset($_GET['year']) ? $_GET['year'] : '';
$filter_campus = isset($_GET['campus']) ? $_GET['campus'] : '';
$filter_college = isset($_GET['college']) ? $_GET['college'] : '';
$filter_course = isset($_GET['course']) ? $_GET['course'] : '';
$filter_scholarship = isset($_GET['scholarship']) ? $_GET['scholarship'] : '';

// Fetch distinct values for filters
$years = $conn->query("SELECT DISTINCT ss_year FROM ss_master WHERE ss_year IS NOT NULL AND ss_year != '' ORDER BY ss_year DESC");
$campuses = $conn->query("SELECT campus_name as stu_campus FROM campus ORDER BY campus_name ASC");
$colleges = $conn->query("SELECT college_name as stu_college FROM college ORDER BY college_name ASC");
$courses = $conn->query("SELECT prog_name as stu_program FROM program ORDER BY prog_name ASC");
$scholarships = $conn->query("SELECT DISTINCT ss_name FROM ss_master WHERE ss_name IS NOT NULL AND ss_name != '' ORDER BY ss_name ASC");

$sql = "SELECT 
            sm.ss_year AS Year,
            COUNT(DISTINCT sm.ss_id) AS Total_Scholarships,
            COUNT(DISTINCT s.stu_id) AS Total_Beneficiaries,
            SUM(sm.ss_amount) AS Total_Approved_Amount
        FROM scholarship s
        JOIN ss_master sm ON s.ss_id = sm.ss_id
        JOIN student_master stu ON s.stu_id = stu.stu_id
        WHERE s.app_status = 'Approved'";

if (!empty($filter_year)) {
    $sql .= " AND sm.ss_year = '" . $conn->real_escape_string($filter_year) . "'";
}
if (!empty($filter_campus)) {
    $sql .= " AND stu.stu_campus = '" . $conn->real_escape_string($filter_campus) . "'";
}
if (!empty($filter_college)) {
    $sql .= " AND stu.stu_college = '" . $conn->real_escape_string($filter_college) . "'";
}
if (!empty($filter_course)) {
    $sql .= " AND stu.stu_program = '" . $conn->real_escape_string($filter_course) . "'";
}
if (!empty($filter_scholarship)) {
    $sql .= " AND sm.ss_name = '" . $conn->real_escape_string($filter_scholarship) . "'";
}

$order_by = isset($_GET['sort']) ? $_GET['sort'] : 'sm.ss_year';
$order = isset($_GET['order']) && $_GET['order'] == 'ASC' ? 'ASC' : 'DESC';

$valid_columns = ['sm.ss_year', 'Total_Scholarships', 'Total_Beneficiaries', 'Total_Approved_Amount'];
if (!in_array($order_by, $valid_columns)) $order_by = 'sm.ss_year';

$sql .= " GROUP BY sm.ss_year ORDER BY $order_by $order";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Year-Wise Approved Summary</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4 content-area">
            <h3 class="mb-4 text-primary fw-bold">Year-Wise Approved Scholarship Summary</h3>

            <!-- Filter Form -->
            <div class="card p-3 mb-4 shadow-sm">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md">
                        <label class="form-label fw-bold">Course</label>
                        <select name="course" class="form-select">
                            <option value="">All Courses</option>
                            <?php while ($row = $courses->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['stu_program']); ?>" <?php if($filter_course == $row['stu_program']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_program']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md">
                        <label class="form-label fw-bold">Year</label>
                        <select name="year" class="form-select">
                            <option value="">All Years</option>
                            <?php while ($row = $years->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['ss_year']); ?>" <?php if($filter_year == $row['ss_year']) echo 'selected'; ?>><?php echo htmlspecialchars($row['ss_year']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md">
                        <label class="form-label fw-bold">Campus</label>
                        <select name="campus" class="form-select">
                            <option value="">All Campuses</option>
                            <?php while ($row = $campuses->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['stu_campus']); ?>" <?php if($filter_campus == $row['stu_campus']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_campus']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md">
                        <label class="form-label fw-bold">College</label>
                        <select name="college" class="form-select">
                            <option value="">All Colleges</option>
                            <?php while ($row = $colleges->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['stu_college']); ?>" <?php if($filter_college == $row['stu_college']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_college']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md">
                        <label class="form-label fw-bold">Scholarship</label>
                        <select name="scholarship" class="form-select">
                            <option value="">All Scholarships</option>
                            <?php while ($row = $scholarships->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['ss_name']); ?>" <?php if($filter_scholarship == $row['ss_name']) echo 'selected'; ?>><?php echo htmlspecialchars($row['ss_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-12 d-flex gap-2 justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">Filter</button>
                        <a href="approved_summary.php" class="btn btn-secondary px-4">Reset</a>
                        <a href="export.php?type=approved_summary&year=<?php echo urlencode($filter_year); ?>&campus=<?php echo urlencode($filter_campus); ?>&college=<?php echo urlencode($filter_college); ?>&course=<?php echo urlencode($filter_course); ?>&scholarship=<?php echo urlencode($filter_scholarship); ?>" class="btn btn-success px-4" title="Export to Excel">Export Summary</a>
                    </div>
                </form>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-striped table-hover table-bordered mb-0 align-middle">
                        <thead class="table-dark">
                            <?php 
                            $next_order = ($order == 'ASC') ? 'DESC' : 'ASC'; 
                            $base_url = "?year=" . urlencode($filter_year) . "&campus=" . urlencode($filter_campus) . "&college=" . urlencode($filter_college) . "&course=" . urlencode($filter_course) . "&scholarship=" . urlencode($filter_scholarship);
                            ?>
                            <tr>
                                <th class="text-center"><a href="<?php echo $base_url; ?>&sort=sm.ss_year&order=<?php echo ($order_by == 'sm.ss_year') ? $next_order : 'DESC'; ?>">Year</a></th>
                                <th class="text-center"><a href="<?php echo $base_url; ?>&sort=Total_Scholarships&order=<?php echo ($order_by == 'Total_Scholarships') ? $next_order : 'DESC'; ?>">No. of Scholarships</a></th>
                                <th class="text-center"><a href="<?php echo $base_url; ?>&sort=Total_Beneficiaries&order=<?php echo ($order_by == 'Total_Beneficiaries') ? $next_order : 'DESC'; ?>">No. of Beneficiaries</a></th>
                                <th class="text-end"><a href="<?php echo $base_url; ?>&sort=Total_Approved_Amount&order=<?php echo ($order_by == 'Total_Approved_Amount') ? $next_order : 'DESC'; ?>">Approved Amount for the Year</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?php echo htmlspecialchars($row['Year']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($row['Total_Scholarships']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($row['Total_Beneficiaries']); ?></td>
                                        <td class="text-end fw-semibold text-success">
                                            <?php echo number_format($row['Total_Approved_Amount'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No approved scholarships found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <a href="export.php?type=approved_summary" class="btn btn-success px-4" title="Export to Excel">Export Summary</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
