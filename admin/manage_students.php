<?php
require_once 'session.php';
include "../db.php";

// Fetch distinct values for filters
$campuses = $conn->query("SELECT DISTINCT stu_campus FROM student_master WHERE stu_campus IS NOT NULL AND stu_campus != '' ORDER BY stu_campus ASC");
$colleges = $conn->query("SELECT DISTINCT stu_college FROM student_master WHERE stu_college IS NOT NULL AND stu_college != '' ORDER BY stu_college ASC");
$years = $conn->query("SELECT DISTINCT stu_year_level FROM student_master WHERE stu_year_level IS NOT NULL AND stu_year_level != '' ORDER BY stu_year_level ASC");
$courses = $conn->query("SELECT DISTINCT stu_program FROM student_master WHERE stu_program IS NOT NULL AND stu_program != '' ORDER BY stu_program ASC");

// Filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_campus = isset($_GET['campus']) ? $_GET['campus'] : '';
$filter_college = isset($_GET['college']) ? $_GET['college'] : '';
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';
$filter_course = isset($_GET['course']) ? $_GET['course'] : '';

// Sorting
$order_by = isset($_GET['sort']) ? $_GET['sort'] : "stu_id";
$order = isset($_GET['order']) ? $_GET['order'] : "ASC";

// Query
$sql = "SELECT * FROM `student_master` WHERE 1=1";

if (!empty($search)) {
    $safe_search = $conn->real_escape_string($search);
    $sql .= " AND (`stu_fname` LIKE '%$safe_search%' 
              OR `stu_lname` LIKE '%$safe_search%'
              OR `stu_email` LIKE '%$safe_search%' 
              OR `stu_enroll` LIKE '%$safe_search%')";
}
if (!empty($filter_campus)) {
    $sql .= " AND stu_campus = '" . $conn->real_escape_string($filter_campus) . "'";
}
if (!empty($filter_college)) {
    $sql .= " AND stu_college = '" . $conn->real_escape_string($filter_college) . "'";
}
if (!empty($filter_year)) {
    $sql .= " AND stu_year_level = '" . $conn->real_escape_string($filter_year) . "'";
}
if (!empty($filter_course)) {
    $sql .= " AND stu_program = '" . $conn->real_escape_string($filter_course) . "'";
}

$sql .= " ORDER BY $order_by $order";
$result = $conn->query($sql);

$export_link = "export.php?type=students&search=".urlencode($search)."&campus=".urlencode($filter_campus)."&college=".urlencode($filter_college)."&year=".urlencode($filter_year)."&course=".urlencode($filter_course);
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

            <h2 class="fw-bold mb-4">Manage Students</h2>

           
            
            
            <!------------- DISPLAY STUDENTS -------------------->
            <!------------- DISPLAY STUDENTS -------------------->
<form method="GET" class="mb-3">
    <div class="row g-2 mb-2">
        <div class="col-md-2">
            <select name="campus" class="form-select">
                <option value="">All Campuses</option>
                <?php while($row = $campuses->fetch_assoc()){ ?>
                    <option value="<?php echo htmlspecialchars($row['stu_campus']); ?>" <?php if($filter_campus==$row['stu_campus']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_campus']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="college" class="form-select">
                <option value="">All Colleges</option>
                <?php while($row = $colleges->fetch_assoc()){ ?>
                    <option value="<?php echo htmlspecialchars($row['stu_college']); ?>" <?php if($filter_college==$row['stu_college']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_college']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="year" class="form-select">
                <option value="">All Years</option>
                <?php while($row = $years->fetch_assoc()){ ?>
                    <option value="<?php echo htmlspecialchars($row['stu_year_level']); ?>" <?php if($filter_year==$row['stu_year_level']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_year_level']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-5">
            <select name="course" class="form-select">
                <option value="">All Courses</option>
                <?php while($row = $courses->fetch_assoc()){ ?>
                    <option value="<?php echo htmlspecialchars($row['stu_program']); ?>" <?php if($filter_course==$row['stu_program']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_program']); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row g-2">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search name, email, enroll..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="manage_students.php" class="btn btn-secondary w-100">Reset</a>
        </div>
        <div class="col-md-2">
            <a href="<?php echo htmlspecialchars($export_link); ?>" class="btn btn-success w-100">Export</a>
        </div>
    </div>
</form>

<!-- Table -->
<div class="table-responsive">
<table class="table table-bordered table-striped table-hover align-middle">
    <thead class="table-dark">
        <tr>
            <th><a href="?sort=stu_id&order=ASC">ID</a></th>
            <th><a href="?sort=stu_enroll&order=ASC">Enrollment No</a></th>
            <th><a href="?sort=stu_fname&order=ASC">Name</a></th>
            <th><a href="?sort=stu_campus&order=ASC">Campus</a></th>
            <th><a href="?sort=stu_college&order=ASC">College</a></th>
            <th><a href="?sort=stu_program&order=ASC">Course</a></th>
            <th><a href="?sort=stu_year_level&order=ASC">Year</a></th>
            <th><a href="?sort=stu_email&order=ASC">Email</a></th>
            <th><a href="?sort=stu_city&order=ASC">City</a></th>
            <th style="min-width: 150px;">Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo $row['stu_id']; ?></td>
            <td><?php echo $row['stu_enroll']; ?></td>
            <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_campus']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_college']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_program']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_year_level']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_email']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_city']); ?></td>
            <td>
                <div class="d-flex gap-1 flex-wrap">
                    <!-- Edit -->
                <a href="edit_student.php?stu_id=<?php echo $row['stu_id']; ?>&stu_enroll=<?php echo $row['stu_enroll']; ?>" class="btn btn-warning btn-sm">Edit</a>

                <!-- Delete -->
                <a href="delete_student.php?stu_id=<?php echo $row['stu_id']; ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure?')">Delete</a>

                <!-- Block -->
                <?php if ($row['stu_status'] == 'active') { ?>
                    <a href="block_student.php?stu_id=<?php echo $row['stu_id']; ?>" 
                       class="btn btn-secondary btn-sm">Block</a>
                    <?php } else { ?>
                    <a href="block_student.php?stu_id=<?php echo $row['stu_id']; ?>" 
                       class="btn btn-success btn-sm">
                       Unblock
                    </a>
                <?php } ?>
                </div>
            </td>
        </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='10' class='text-center'>No records found</td></tr>";
        }
        ?>
    </tbody>
</table>
</div>
        
        <!---------- DISPLAY ENDS --------------->

        </div>
    </div>
</div>

</body>
</html>
