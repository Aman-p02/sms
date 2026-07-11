<?php
require_once 'session.php';
include "../db.php";

$ss_id = isset($_GET['ss_id']) ? $_GET['ss_id'] : '';
$ss_name = isset($_GET['ss_name']) ? $_GET['ss_name'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$stu_id_filter = isset($_GET['stu_id_filter']) ? $_GET['stu_id_filter'] : '';

if (empty($ss_id)) {
    header("Location: view_scholarships.php");
    exit();
}

/*INNER JOIN*/

$sql = "SELECT scholarship.ss_id, student_master.stu_id, student_master.stu_fname, student_master.stu_lname, ss_master.ss_year, student_master.stu_campus, student_master.stu_college, student_master.stu_program, scholarship.app_status 
FROM scholarship
INNER JOIN student_master ON scholarship.stu_id=student_master.stu_id 
INNER JOIN ss_master ON scholarship.ss_id=ss_master.ss_id
Where scholarship.ss_id = '".$conn->real_escape_string($ss_id)."'";

if (!empty($stu_id_filter)) {
    $safe_stu_id = $conn->real_escape_string($stu_id_filter);
    $sql .= " AND student_master.stu_id = '$safe_stu_id'";
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
            <input type="text" name="stu_id_filter" class="form-control" placeholder="STU_ID..." value="<?php echo htmlspecialchars($stu_id_filter); ?>">
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search others..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">Search</button>
        </div>
    </div>
</form>

<!-- Table -->
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>SS_ID</th>
            <th>STU_ID</th>
            <th>Student Name</th>
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
            <td><?php echo $row['ss_id']; ?></td>
            <td><?php echo $row['stu_id']; ?></td>
            <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_year']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_campus']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_college']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_program']); ?></td>
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
            echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
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
