<?php
require_once 'session.php';
include "../db.php";

function getShortCourseName($fullName) {
    $map = [
        'BACHELOR OF SCIENCE IN CIVIL ENGINEERING' => 'BS (CE)',
        'BACHELOR OF SCIENCE IN COMPUTER SCIENCE MAJOR IN C' => 'BS (CS)',
        'BACHELOR OF ARTS MAJOR IN ECONOMICS' => 'BA (E)',
        'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJ' => 'BS (BA)',
        'IT' => 'IT'
    ];
    $upper = strtoupper(trim($fullName));
    if (isset($map[$upper])) {
        return $map[$upper];
    }
    
    $prefix = '';
    $name = $upper;
    if (strpos($name, 'BACHELOR OF SCIENCE IN') !== false) {
        $prefix = 'BS';
        $name = str_replace('BACHELOR OF SCIENCE IN', '', $name);
    } elseif (strpos($name, 'BACHELOR OF ARTS IN') !== false) {
        $prefix = 'BA';
        $name = str_replace('BACHELOR OF ARTS IN', '', $name);
    } elseif (strpos($name, 'BACHELOR OF ARTS MAJOR IN') !== false) {
        $prefix = 'BA';
        $name = str_replace('BACHELOR OF ARTS MAJOR IN', '', $name);
    }
    
    if ($prefix !== '') {
        $words = explode(' ', $name);
        $acronym = '';
        $skipWords = ['AND', 'IN', 'OF', 'THE', 'MAJOR'];
        foreach ($words as $w) {
            $w = trim($w);
            if (empty($w)) continue;
            if (in_array($w, $skipWords)) continue;
            $acronym .= $w[0];
        }
        return $prefix . ' (' . $acronym . ')';
    }
    
    return $fullName;
}

$ss_id = isset($_GET['ss_id']) ? $_GET['ss_id'] : '';
$ss_name = isset($_GET['ss_name']) ? $_GET['ss_name'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$gender_filter = isset($_GET['gender_filter']) ? $_GET['gender_filter'] : '';

if (empty($ss_id)) {
    header("Location: view_scholarships.php");
    exit();
}

/*INNER JOIN*/

$sql = "SELECT scholarship.ss_id, student_master.stu_enroll, student_master.stu_id, student_master.stu_fname, student_master.stu_lname, student_master.stu_gender, ss_master.ss_year, student_master.stu_campus, student_master.stu_college, student_master.stu_program, scholarship.app_status 
FROM scholarship
INNER JOIN student_master ON scholarship.stu_id=student_master.stu_id 
INNER JOIN ss_master ON scholarship.ss_id=ss_master.ss_id
Where scholarship.ss_id = '".$conn->real_escape_string($ss_id)."'";

if (!empty($gender_filter)) {
    $safe_gender = $conn->real_escape_string($gender_filter);
    $sql .= " AND student_master.stu_gender = '$safe_gender'";
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

            <h2 class="fw-bold mb-4">Student List for <?php echo $ss_name; ?></h2>

            
            <!------------- DISPLAY STUDENTS -------------------->
<form method="GET" class="mb-3">
    <div class="row">
        <input type="hidden" name="ss_id" value="<?php echo htmlspecialchars($ss_id); ?>">
        <input type="hidden" name="ss_name" value="<?php echo htmlspecialchars($ss_name); ?>">
        <div class="col-md-2">
            <select name="gender_filter" class="form-select">
                <option value="">All Genders</option>
                <option value="M" <?php if($gender_filter == 'M') echo 'selected'; ?>>Male</option>
                <option value="F" <?php if($gender_filter == 'F') echo 'selected'; ?>>Female</option>
                <option value="O" <?php if($gender_filter == 'O') echo 'selected'; ?>>Other</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search others..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="export.php?type=list_names&ss_id=<?php echo urlencode($ss_id); ?>&search=<?php echo urlencode($search); ?>&gender=<?php echo urlencode($gender_filter); ?>" class="btn btn-success ms-2">Export to Excel</a>
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
            <th>Branch</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['stu_enroll']); ?></td>
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
            <td>
                <?php
                if ($row['app_status'] == 'Applied')
                { ?>
                    <a href="approve_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo urlencode($ss_name); ?>" class="btn btn-secondary btn-sm">Action</a>     
                <?php
                }
                else if($row['app_status'] == 'Approved')
                { ?>
                    <a href="reject_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo urlencode($ss_name); ?>" class="btn btn-success btn-sm">Approve</a>     
                <?php
                }
                else
                { ?>
                    <a href="reset_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo urlencode($ss_name); ?>" class="btn btn-danger btn-sm">Reject</a>
                <?php
                }
                ?>
              
               
            </td>
        </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
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
