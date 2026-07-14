<?php
require_once 'session.php';
include "../db.php";

function getShortCollegeName($name) {
    if (empty($name)) return '';
    $parts = explode(' - ', $name);
    return trim($parts[0]);
}

function getShortCampusName($name) {
    if (empty($name)) return '';
    return trim(str_replace('NEMSU', '', $name));
}

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

$ss_id_filter = isset($_GET['ss_id']) ? $_GET['ss_id'] : '';
$gender_filter = isset($_GET['gender_filter']) ? $_GET['gender_filter'] : '';
$campus_filter = isset($_GET['campus_filter']) ? $_GET['campus_filter'] : '';
$college_filter = isset($_GET['college_filter']) ? $_GET['college_filter'] : '';
$course_filter = isset($_GET['course_filter']) ? $_GET['course_filter'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$courses = $conn->query("SELECT prog_name as stu_program FROM program ORDER BY prog_name ASC");
$campuses = $conn->query("SELECT campus_name as stu_campus FROM campus ORDER BY campus_name ASC");
$colleges = $conn->query("SELECT college_name as stu_college FROM college ORDER BY college_name ASC");

// Fetch all active scholarships for the dropdown
$scholarships = $conn->query("SELECT ss_id, ss_name, ss_year FROM ss_master ORDER BY ss_name ASC, ss_year DESC");

// Fetch applied students based on filter
$sql = "SELECT s.app_id, s.app_status, stu.stu_fname, stu.stu_lname, stu.stu_enroll, stu.stu_campus, stu.stu_college, stu.stu_gender, stu.stu_program, ss.ss_name 
        FROM scholarship s
        JOIN student_master stu ON s.stu_id = stu.stu_id
        JOIN ss_master ss ON s.ss_id = ss.ss_id
        WHERE 1=1";

if (!empty($ss_id_filter)) {
    $sql .= " AND s.ss_id = '" . $conn->real_escape_string($ss_id_filter) . "'";
}
if (!empty($gender_filter)) {
    $sql .= " AND stu.stu_gender = '" . $conn->real_escape_string($gender_filter) . "'";
}
if (!empty($campus_filter)) {
    $sql .= " AND stu.stu_campus = '" . $conn->real_escape_string($campus_filter) . "'";
}
if (!empty($college_filter)) {
    $sql .= " AND stu.stu_college = '" . $conn->real_escape_string($college_filter) . "'";
}
if (!empty($course_filter)) {
    $sql .= " AND stu.stu_program = '" . $conn->real_escape_string($course_filter) . "'";
}
if (!empty($search)) {
    $safe_search = $conn->real_escape_string($search);
    $sql .= " AND (stu.stu_fname LIKE '%$safe_search%' 
                OR stu.stu_lname LIKE '%$safe_search%'
                OR stu.stu_enroll LIKE '%$safe_search%')";
}

$order_by = isset($_GET['sort']) ? $_GET['sort'] : 'stu.stu_fname';
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';

$valid_columns = ['s.app_id', 'stu.stu_fname', 'stu.stu_enroll', 'stu.stu_gender', 'stu.stu_campus', 'stu.stu_college', 'stu.stu_program', 'ss.ss_name', 's.app_status'];
if (!in_array($order_by, $valid_columns)) $order_by = 'stu.stu_fname';

$sql .= " ORDER BY $order_by $order";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applied Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main -->
        <div class="col-md-9 col-lg-10 p-4 content-area">

            <h3 class="mb-3">Students Applied for Scholarships</h3>

            <form method="GET" class="mb-4">
                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <select name="ss_id" class="form-select">
                            <option value="">All Scholarships</option>
                            <?php while($ss = $scholarships->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($ss['ss_id']); ?>" <?php echo ($ss_id_filter == $ss['ss_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ss['ss_name'] . ' (' . $ss['ss_year'] . ')'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="gender_filter" class="form-select">
                            <option value="">All Genders</option>
                            <option value="M" <?php if($gender_filter == 'M') echo 'selected'; ?>>Male</option>
                            <option value="F" <?php if($gender_filter == 'F') echo 'selected'; ?>>Female</option>
                            <option value="O" <?php if($gender_filter == 'O') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="campus_filter" class="form-select">
                            <option value="">All Campuses</option>
                            <?php while($row = $campuses->fetch_assoc()){ ?>
                                <option value="<?php echo htmlspecialchars($row['stu_campus']); ?>" <?php if($campus_filter==$row['stu_campus']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_campus']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="college_filter" class="form-select">
                            <option value="">All Colleges</option>
                            <?php while($row = $colleges->fetch_assoc()){ ?>
                                <option value="<?php echo htmlspecialchars($row['stu_college']); ?>" <?php if($college_filter==$row['stu_college']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_college']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <select name="course_filter" class="form-select">
                            <option value="">All Courses</option>
                            <?php while($row = $courses->fetch_assoc()){ ?>
                                <option value="<?php echo htmlspecialchars($row['stu_program']); ?>" <?php if($course_filter==$row['stu_program']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_program']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search Name or Enrollment No..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4 d-flex gap-1">
                        <button type="submit" class="btn btn-primary w-50">Filter</button>
                        <a href="export.php?type=applied_students&ss_id=<?php echo urlencode($ss_id_filter); ?>&gender=<?php echo urlencode($gender_filter); ?>&campus=<?php echo urlencode($campus_filter); ?>&college=<?php echo urlencode($college_filter); ?>&course=<?php echo urlencode($course_filter); ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($order_by); ?>&order=<?php echo urlencode($order); ?>" class="btn btn-success w-50">Export</a>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <?php 
                        $next_order = ($order == 'ASC') ? 'DESC' : 'ASC'; 
                        $base_url = "?ss_id=" . urlencode($ss_id_filter) . "&gender_filter=" . urlencode($gender_filter) . "&campus_filter=" . urlencode($campus_filter) . "&college_filter=" . urlencode($college_filter) . "&course_filter=" . urlencode($course_filter) . "&search=" . urlencode($search);
                        ?>
                        <th>Sr. No</th>
                        <th><a href="<?php echo $base_url; ?>&sort=stu.stu_fname&order=<?php echo ($order_by == 'stu.stu_fname') ? $next_order : 'DESC'; ?>" class="text-white text-decoration-none d-block">Name</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=stu.stu_enroll&order=<?php echo ($order_by == 'stu.stu_enroll') ? $next_order : 'DESC'; ?>" class="text-white text-decoration-none d-block">Enrollment No</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=stu.stu_gender&order=<?php echo ($order_by == 'stu.stu_gender') ? $next_order : 'DESC'; ?>" class="text-white text-decoration-none d-block">Gender</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=stu.stu_campus&order=<?php echo ($order_by == 'stu.stu_campus') ? $next_order : 'DESC'; ?>" class="text-white text-decoration-none d-block">Campus</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=stu.stu_college&order=<?php echo ($order_by == 'stu.stu_college') ? $next_order : 'DESC'; ?>" class="text-white text-decoration-none d-block">College</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=stu.stu_program&order=<?php echo ($order_by == 'stu.stu_program') ? $next_order : 'DESC'; ?>" class="text-white text-decoration-none d-block">Course</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=ss.ss_name&order=<?php echo ($order_by == 'ss.ss_name') ? $next_order : 'DESC'; ?>" class="text-white text-decoration-none d-block">Scholarship Name</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=s.app_status&order=<?php echo ($order_by == 's.app_status') ? $next_order : 'DESC'; ?>" class="text-white text-decoration-none d-block">Status</a></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php $sr_no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $sr_no++; ?></td>
                                <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
                                <td><?php echo htmlspecialchars($row['stu_enroll'] ?? ''); ?></td>
                                <td><?php 
                                    if ($row['stu_gender'] == 'M') echo 'Male';
                                    elseif ($row['stu_gender'] == 'F') echo 'Female';
                                    elseif ($row['stu_gender'] == 'O') echo 'Other';
                                    else echo htmlspecialchars($row['stu_gender'] ?? ''); 
                                ?></td>
                                <td><?php echo htmlspecialchars(getShortCampusName($row['stu_campus'])); ?></td>
                                <td><?php echo htmlspecialchars(getShortCollegeName($row['stu_college'])); ?></td>
                                <td><?php echo htmlspecialchars(getShortCourseName($row['stu_program'])); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
                                <td>
                                    <?php if ($row['app_status'] == 'Applied'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php elseif ($row['app_status'] == 'Approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
