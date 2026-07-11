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

$ss_type = isset($_GET['ss_type']) ? $_GET['ss_type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$gender_filter = isset($_GET['gender_filter']) ? $_GET['gender_filter'] : '';
$enroll_filter = isset($_GET['enroll_filter']) ? $_GET['enroll_filter'] : '';
$course_filter = isset($_GET['course_filter']) ? $_GET['course_filter'] : '';
$campus_filter = isset($_GET['campus_filter']) ? $_GET['campus_filter'] : '';
$college_filter = isset($_GET['college_filter']) ? $_GET['college_filter'] : '';
$year_filter = isset($_GET['year_filter']) ? $_GET['year_filter'] : '';

$courses = $conn->query("SELECT prog_name as stu_program FROM program ORDER BY prog_name ASC");
$campuses = $conn->query("SELECT campus_name as stu_campus FROM campus ORDER BY campus_name ASC");
$colleges = $conn->query("SELECT college_name as stu_college FROM college ORDER BY college_name ASC");
$years = $conn->query("SELECT DISTINCT stu_year_level FROM student_master WHERE stu_year_level IS NOT NULL AND stu_year_level != '' ORDER BY stu_year_level ASC");

if (empty($ss_type)) {
    header("Location: view_scholarships.php");
    exit();
}

/*INNER JOIN*/
$sql = "SELECT scholarship.ss_id, student_master.stu_id, student_master.stu_enroll, student_master.stu_fname, student_master.stu_lname, student_master.stu_gender, ss_master.ss_year, ss_master.ss_name, student_master.stu_campus, student_master.stu_college, student_master.stu_program, scholarship.app_status 
FROM scholarship
INNER JOIN student_master ON scholarship.stu_id=student_master.stu_id 
INNER JOIN ss_master ON scholarship.ss_id=ss_master.ss_id
Where ss_master.ss_type = '".$conn->real_escape_string($ss_type)."'";

if (!empty($enroll_filter)) {
    $sql .= " AND student_master.stu_enroll LIKE '%" . $conn->real_escape_string($enroll_filter) . "%'";
}
if (!empty($course_filter)) {
    $sql .= " AND student_master.stu_program = '" . $conn->real_escape_string($course_filter) . "'";
}
if (!empty($gender_filter)) {
    $safe_gender = $conn->real_escape_string($gender_filter);
    $sql .= " AND student_master.stu_gender = '$safe_gender'";
}
if (!empty($campus_filter)) {
    $sql .= " AND student_master.stu_campus = '" . $conn->real_escape_string($campus_filter) . "'";
}
if (!empty($college_filter)) {
    $sql .= " AND student_master.stu_college = '" . $conn->real_escape_string($college_filter) . "'";
}
if (!empty($year_filter)) {
    $sql .= " AND student_master.stu_year_level = '" . $conn->real_escape_string($year_filter) . "'";
}
if (!empty($search)) {
    $safe_search = $conn->real_escape_string($search);
    $sql .= " AND (student_master.stu_fname LIKE '%$safe_search%' 
                OR student_master.stu_lname LIKE '%$safe_search%'
                OR ss_master.ss_year LIKE '%$safe_search%'
                OR student_master.stu_campus LIKE '%$safe_search%'
                OR student_master.stu_college LIKE '%$safe_search%'
                OR student_master.stu_program LIKE '%$safe_search%')";
}

$result = $conn->query($sql);
?>


<!------------------- BODY STARTS ---------------------->


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

            <h2 class="fw-bold mb-4">Student List for <?php echo htmlspecialchars($ss_type); ?></h2>

            <!------------- DISPLAY STUDENTS -------------------->
<form method="GET" class="mb-3">
    <div class="row g-2 mb-2">
        <input type="hidden" name="ss_type" value="<?php echo htmlspecialchars($ss_type); ?>">
        <div class="col-md-3">
            <select name="course_filter" class="form-select">
                <option value="">All Courses</option>
                <?php while($row = $courses->fetch_assoc()){ ?>
                    <option value="<?php echo htmlspecialchars($row['stu_program']); ?>" <?php if($course_filter==$row['stu_program']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_program']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="campus_filter" class="form-select">
                <option value="">All Campuses</option>
                <?php while($row = $campuses->fetch_assoc()){ ?>
                    <option value="<?php echo htmlspecialchars($row['stu_campus']); ?>" <?php if($campus_filter==$row['stu_campus']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_campus']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="college_filter" class="form-select">
                <option value="">All Colleges</option>
                <?php while($row = $colleges->fetch_assoc()){ ?>
                    <option value="<?php echo htmlspecialchars($row['stu_college']); ?>" <?php if($college_filter==$row['stu_college']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_college']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="year_filter" class="form-select">
                <option value="">All Years</option>
                <?php while($row = $years->fetch_assoc()){ ?>
                    <option value="<?php echo htmlspecialchars($row['stu_year_level']); ?>" <?php if($year_filter==$row['stu_year_level']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_year_level']); ?></option>
                <?php } ?>
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
    </div>
    <div class="row g-2">
        <div class="col-md-3">
            <input type="text" name="enroll_filter" class="form-control" placeholder="Enrollment No" value="<?php echo htmlspecialchars($enroll_filter); ?>">
        </div>
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search others..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-50">Filter</button>
        </div>
    </div>
</form>

<!-- Table -->
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Enrollment No</th>
            <th>Student Name</th>
            <th>Gender</th>
            <th>Year</th>
            <th>Campus</th>
            <th>College</th>
            <th>Course</th>
            <th>Scholarship Name</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['stu_enroll'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
            <td><?php 
                if ($row['stu_gender'] == 'M') echo 'Male';
                elseif ($row['stu_gender'] == 'F') echo 'Female';
                elseif ($row['stu_gender'] == 'O') echo 'Other';
                else echo htmlspecialchars($row['stu_gender']); 
            ?></td>
            <td><?php echo htmlspecialchars($row['ss_year']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_campus']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_college']); ?></td>
            <td><?php echo htmlspecialchars(getShortCourseName($row['stu_program'])); ?></td>
            <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
            <td>
                <?php
                if ($row['app_status'] == 'Applied')
                { ?>
                    <a href="approve_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo urlencode($row['ss_name']); ?>" class="btn btn-secondary btn-sm">Action</a>     
                <?php
                }
                else if($row['app_status'] == 'Approved')
                { ?>
                    <a href="reject_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo urlencode($row['ss_name']); ?>" class="btn btn-success btn-sm">Approve</a>     
                <?php
                }
                else
                { ?>
                    <a href="reset_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo urlencode($row['ss_name']); ?>" class="btn btn-danger btn-sm">Reject</a>
                <?php
                }
                ?>
            </td>
        </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
        }
        ?>
    </tbody>
</table>
        
        <!---------- DISPLAY ENDS --------------->

        </div>
    </div>
</div>

</body>
</html>
