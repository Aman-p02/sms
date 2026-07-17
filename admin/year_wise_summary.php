<?php
require_once 'session.php';
include "../db.php";

function getShortCourseName($fullName) {
    $map = ['IT' => 'IT'];
    $upper = strtoupper(trim($fullName));
    if (isset($map[$upper])) return $map[$upper];

    $name = $upper;
    $prefix = '';
    
    if (strpos($name, 'BACHELOR OF SCIENCE IN') !== false) {
        $prefix = 'BS';
        $name = str_replace('BACHELOR OF SCIENCE IN', '', $name);
    } elseif (strpos($name, 'BACHELOR OF ARTS IN') !== false || strpos($name, 'BACHELOR OF ARTS MAJOR IN') !== false) {
        $prefix = 'BA';
        $name = str_replace(['BACHELOR OF ARTS IN', 'BACHELOR OF ARTS MAJOR IN'], '', $name);
    } elseif (strpos($name, 'BACHELOR OF ELEMENTARY EDUCATION') !== false) {
        $prefix = 'BEEd';
        $name = str_replace('BACHELOR OF ELEMENTARY EDUCATION', '', $name);
    } elseif (strpos($name, 'BACHELOR OF SECONDARY EDUCATION') !== false) {
        $prefix = 'BSEd';
        $name = str_replace('BACHELOR OF SECONDARY EDUCATION', '', $name);
    } elseif (strpos($name, 'BACHELOR OF PUBLIC ADMINISTRATION') !== false) {
        return 'BPA';
    } elseif (strpos($name, 'BACHELOR OF') !== false) {
        $prefix = 'B';
        $name = str_replace('BACHELOR OF', '', $name);
    }
    
    $words = explode(' ', str_replace(['/', '-', '&'], ' ', $name));
    $skip = ['AND', 'IN', 'OF', 'THE', 'MAJOR'];
    $acro = '';
    foreach($words as $w) {
        $w = trim($w);
        if(empty($w) || in_array($w, $skip)) continue;
        $acro .= $w[0];
    }
    
    if ($prefix !== '') {
        return empty($acro) ? $prefix : $prefix . ' (' . $acro . ')';
    }
    
    return empty($acro) ? $fullName : $acro;
}

function getShortCampusName($fullName) {
    $name = trim($fullName);
    if (stripos($name, 'NEMSU ') === 0) {
        return substr($name, 6);
    }
    return $name;
}

function getShortCollegeName($fullName) {
    $parts = explode('-', $fullName);
    return trim($parts[0]);
}

// Fetch distinct values for filters
$years = $conn->query("SELECT DISTINCT ss_year FROM ss_master WHERE ss_year IS NOT NULL AND ss_year != '' ORDER BY ss_year DESC");
$campuses = $conn->query("SELECT campus_name as stu_campus FROM campus ORDER BY campus_name ASC");
$colleges = $conn->query("SELECT college_name as stu_college FROM college ORDER BY college_name ASC");
$courses = $conn->query("SELECT prog_name as stu_program FROM program ORDER BY prog_name ASC");
$scholarships = $conn->query("SELECT DISTINCT ss_name FROM ss_master WHERE ss_name IS NOT NULL AND ss_name != '' ORDER BY ss_name ASC");

// Get selected filters
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';
$filter_campus = isset($_GET['campus']) ? $_GET['campus'] : '';
$filter_college = isset($_GET['college']) ? $_GET['college'] : '';
$filter_course = isset($_GET['course']) ? $_GET['course'] : '';
$filter_scholarship = isset($_GET['scholarship']) ? $_GET['scholarship'] : '';

$filter_gender = isset($_GET['gender']) ? $_GET['gender'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Build dynamic query
$sql = "SELECT s.stu_enroll, s.stu_gender, s.stu_fname, s.stu_lname, s.stu_campus, s.stu_college, s.stu_program, 
               sm.ss_name, sm.ss_year, sm.ss_amount, sc.app_status 
        FROM scholarship sc
        INNER JOIN student_master s ON sc.stu_id = s.stu_id
        INNER JOIN ss_master sm ON sc.ss_id = sm.ss_id
        WHERE 1=1";

if (!empty($filter_year)) {
    $sql .= " AND sm.ss_year = '" . $conn->real_escape_string($filter_year) . "'";
}
if (!empty($filter_campus)) {
    $sql .= " AND s.stu_campus = '" . $conn->real_escape_string($filter_campus) . "'";
}
if (!empty($filter_college)) {
    $sql .= " AND s.stu_college = '" . $conn->real_escape_string($filter_college) . "'";
}
if (!empty($filter_course)) {
    $sql .= " AND s.stu_program = '" . $conn->real_escape_string($filter_course) . "'";
}
if (!empty($filter_scholarship)) {
    $sql .= " AND sm.ss_name = '" . $conn->real_escape_string($filter_scholarship) . "'";
}
if (!empty($filter_gender)) {
    $sql .= " AND s.stu_gender = '" . $conn->real_escape_string($filter_gender) . "'";
}
if (!empty($filter_status)) {
    $sql .= " AND sc.app_status = '" . $conn->real_escape_string($filter_status) . "'";
}
if (!empty($search_query)) {
    $safe_search = $conn->real_escape_string($search_query);
    $sql .= " AND (s.stu_fname LIKE '%$safe_search%' OR s.stu_lname LIKE '%$safe_search%' OR s.stu_enroll LIKE '%$safe_search%' OR sm.ss_name LIKE '%$safe_search%')";
}

$order_by = isset($_GET['sort']) ? $_GET['sort'] : 'sm.ss_year';
$order = isset($_GET['order']) && $_GET['order'] == 'ASC' ? 'ASC' : 'DESC';

$valid_columns = ['s.stu_fname', 's.stu_campus', 's.stu_college', 's.stu_program', 'sm.ss_name', 'sm.ss_year', 'sm.ss_amount', 'sc.app_status'];
if (!in_array($order_by, $valid_columns)) $order_by = 'sm.ss_year';

$sql .= " ORDER BY $order_by $order";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Report</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4 content-area">
            <h2 class="fw-bold mb-4">Detailed Report</h2>

            <!-- Filter Form -->
            <div class="card p-3 mb-4 shadow-sm">
                <form method="GET" class="row g-3 align-items-end">
                    
                    <div class="col-md">
                        <label class="form-label fw-bold">Course</label>
                        <select name="course" class="form-select">
                            <option value="">All</option>
                            <?php while ($row = $courses->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['stu_program']); ?>" <?php if($filter_course == $row['stu_program']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_program']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md">
                        <label class="form-label fw-bold">Year</label>
                        <select name="year" class="form-select">
                            <option value="">All</option>
                            <?php while ($row = $years->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['ss_year']); ?>" <?php if($filter_year == $row['ss_year']) echo 'selected'; ?>><?php echo htmlspecialchars($row['ss_year']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md">
                        <label class="form-label fw-bold">Campus</label>
                        <select name="campus" class="form-select">
                            <option value="">All</option>
                            <?php while ($row = $campuses->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['stu_campus']); ?>" <?php if($filter_campus == $row['stu_campus']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_campus']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md">
                        <label class="form-label fw-bold">College</label>
                        <select name="college" class="form-select">
                            <option value="">All</option>
                            <?php while ($row = $colleges->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['stu_college']); ?>" <?php if($filter_college == $row['stu_college']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_college']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md">
                        <label class="form-label fw-bold">Type</label>
                        <select name="scholarship" class="form-select">
                            <option value="">All</option>
                            <?php while ($row = $scholarships->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['ss_name']); ?>" <?php if($filter_scholarship == $row['ss_name']) echo 'selected'; ?>><?php echo htmlspecialchars($row['ss_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="col-md">
                        <label class="form-label fw-bold">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">All</option>
                            <option value="M" <?php if($filter_gender == 'M') echo 'selected'; ?>>Male</option>
                            <option value="F" <?php if($filter_gender == 'F') echo 'selected'; ?>>Female</option>
                        </select>
                    </div>
                    
                    <div class="col-md">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="Applied" <?php if($filter_status == 'Applied') echo 'selected'; ?>>Pending</option>
                            <option value="Approved" <?php if($filter_status == 'Approved') echo 'selected'; ?>>Approved</option>
                            <option value="Rejected" <?php if($filter_status == 'Rejected') echo 'selected'; ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>

                    <div class="col-md-12 d-flex gap-2 justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">Filter</button>
                        <a href="export.php?type=year_wise_summary&year=<?php echo urlencode($filter_year); ?>&campus=<?php echo urlencode($filter_campus); ?>&college=<?php echo urlencode($filter_college); ?>&course=<?php echo urlencode($filter_course); ?>&scholarship=<?php echo urlencode($filter_scholarship); ?>&gender=<?php echo urlencode($filter_gender); ?>&status=<?php echo urlencode($filter_status); ?>&search=<?php echo urlencode($search_query); ?>&sort=<?php echo urlencode($order_by); ?>&order=<?php echo urlencode($order); ?>" class="btn btn-success px-4" title="Export to Excel">Export</a>
                    </div>
                </form>
            </div>

            <!-- Results Table -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <?php 
                                $next_order = ($order == 'ASC') ? 'DESC' : 'ASC'; 
                                $base_url = "?year=" . urlencode($filter_year) . "&campus=" . urlencode($filter_campus) . "&college=" . urlencode($filter_college) . "&course=" . urlencode($filter_course) . "&scholarship=" . urlencode($filter_scholarship) . "&gender=" . urlencode($filter_gender) . "&status=" . urlencode($filter_status) . "&search=" . urlencode($search_query);
                                ?>
                                <th><a href="<?php echo $base_url; ?>&sort=s.stu_enroll&order=<?php echo ($order_by == 's.stu_enroll') ? $next_order : 'ASC'; ?>" >Enrollment</a></th>
                                <th><a href="<?php echo $base_url; ?>&sort=s.stu_fname&order=<?php echo ($order_by == 's.stu_fname') ? $next_order : 'ASC'; ?>" >Student Name</a></th>
                                <th><a href="<?php echo $base_url; ?>&sort=s.stu_gender&order=<?php echo ($order_by == 's.stu_gender') ? $next_order : 'ASC'; ?>" >Gender</a></th>
                                <th><a href="<?php echo $base_url; ?>&sort=s.stu_campus&order=<?php echo ($order_by == 's.stu_campus') ? $next_order : 'ASC'; ?>" >Campus</a></th>
                                <th><a href="<?php echo $base_url; ?>&sort=s.stu_college&order=<?php echo ($order_by == 's.stu_college') ? $next_order : 'ASC'; ?>" >College</a></th>
                                <th><a href="<?php echo $base_url; ?>&sort=s.stu_program&order=<?php echo ($order_by == 's.stu_program') ? $next_order : 'ASC'; ?>" >Course</a></th>
                                <th><a href="<?php echo $base_url; ?>&sort=sm.ss_name&order=<?php echo ($order_by == 'sm.ss_name') ? $next_order : 'ASC'; ?>" >Scholarship</a></th>
                                <th class="text-nowrap"><a href="<?php echo $base_url; ?>&sort=sm.ss_year&order=<?php echo ($order_by == 'sm.ss_year') ? $next_order : 'ASC'; ?>" >Year</a></th>
                                <th><a href="<?php echo $base_url; ?>&sort=sm.ss_amount&order=<?php echo ($order_by == 'sm.ss_amount') ? $next_order : 'ASC'; ?>" >Amount</a></th>
                                <th><a href="<?php echo $base_url; ?>&sort=sc.app_status&order=<?php echo ($order_by == 'sc.app_status') ? $next_order : 'ASC'; ?>" >Status</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!$result) {
                                echo "<tr><td colspan='8'>Query Error: " . $conn->error . "</td></tr>";
                                echo "<tr><td colspan='8'>SQL: " . $sql . "</td></tr>";
                            }
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['stu_enroll'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
                                <td><?php echo htmlspecialchars($row['stu_gender'] == 'M' ? 'Male' : ($row['stu_gender'] == 'F' ? 'Female' : $row['stu_gender'])); ?></td>
                                <td><?php echo htmlspecialchars(getShortCampusName($row['stu_campus'])); ?></td>
                                <td><?php echo htmlspecialchars(getShortCollegeName($row['stu_college'])); ?></td>
                                <td><?php echo htmlspecialchars(getShortCourseName($row['stu_program'])); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
                                <td class="text-nowrap"><?php echo htmlspecialchars($row['ss_year']); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_amount']); ?></td>
                                <td>
                                    <?php 
                                        if($row['app_status'] == 'Approved') echo "<span class='badge bg-success'>Approved</span>";
                                        else if($row['app_status'] == 'Rejected') echo "<span class='badge bg-danger'>Rejected</span>";
                                        else echo "<span class='badge bg-secondary'>".$row['app_status']."</span>";
                                    ?>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center py-4'>No records found matching your filters.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
