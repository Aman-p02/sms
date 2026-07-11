<?php
include "../db.php";

// Fetch distinct values for filters
$years = $conn->query("SELECT DISTINCT ss_year FROM ss_master WHERE ss_year IS NOT NULL AND ss_year != '' ORDER BY ss_year DESC");
$colleges = $conn->query("SELECT DISTINCT stu_college FROM student_master WHERE stu_college IS NOT NULL AND stu_college != '' ORDER BY stu_college ASC");
$courses = $conn->query("SELECT DISTINCT stu_program FROM student_master WHERE stu_program IS NOT NULL AND stu_program != '' ORDER BY stu_program ASC");
$scholarships = $conn->query("SELECT DISTINCT ss_name FROM ss_master WHERE ss_name IS NOT NULL AND ss_name != '' ORDER BY ss_name ASC");
$amounts = $conn->query("SELECT DISTINCT ss_amount FROM ss_master WHERE ss_amount IS NOT NULL AND ss_amount > 0 ORDER BY ss_amount ASC");

// Get selected filters
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';
$filter_college = isset($_GET['college']) ? $_GET['college'] : '';
$filter_course = isset($_GET['course']) ? $_GET['course'] : '';
$filter_scholarship = isset($_GET['scholarship']) ? $_GET['scholarship'] : '';
$filter_amount = isset($_GET['amount']) ? $_GET['amount'] : '';

// Build dynamic query
$sql = "SELECT s.stu_fname, s.stu_lname, s.stu_college, s.stu_program, 
               sm.ss_name, sm.ss_year, sm.ss_amount, sc.app_status 
        FROM scholarship sc
        INNER JOIN student_master s ON sc.stu_id = s.stu_id
        INNER JOIN ss_master sm ON sc.ss_id = sm.ss_id
        WHERE 1=1";

if (!empty($filter_year)) {
    $sql .= " AND sm.ss_year = '" . $conn->real_escape_string($filter_year) . "'";
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
if (!empty($filter_amount)) {
    $sql .= " AND sm.ss_amount = '" . $conn->real_escape_string($filter_amount) . "'";
}

$sql .= " ORDER BY sm.ss_year DESC, s.stu_fname ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Year</label>
                        <select name="year" class="form-select">
                            <option value="">All Years</option>
                            <?php while ($row = $years->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['ss_year']); ?>" <?php if($filter_year == $row['ss_year']) echo 'selected'; ?>><?php echo htmlspecialchars($row['ss_year']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">College</label>
                        <select name="college" class="form-select">
                            <option value="">All Colleges</option>
                            <?php while ($row = $colleges->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['stu_college']); ?>" <?php if($filter_college == $row['stu_college']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_college']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Course</label>
                        <select name="course" class="form-select">
                            <option value="">All Courses</option>
                            <?php while ($row = $courses->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['stu_program']); ?>" <?php if($filter_course == $row['stu_program']) echo 'selected'; ?>><?php echo htmlspecialchars($row['stu_program']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Scholarship Name</label>
                        <select name="scholarship" class="form-select">
                            <option value="">All Scholarships</option>
                            <?php while ($row = $scholarships->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['ss_name']); ?>" <?php if($filter_scholarship == $row['ss_name']) echo 'selected'; ?>><?php echo htmlspecialchars($row['ss_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Amount</label>
                        <select name="amount" class="form-select">
                            <option value="">All Amounts</option>
                            <?php while ($row = $amounts->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['ss_amount']); ?>" <?php if($filter_amount == $row['ss_amount']) echo 'selected'; ?>><?php echo htmlspecialchars($row['ss_amount']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>

            <!-- Results Table -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Student Name</th>
                                <th>College</th>
                                <th>Course</th>
                                <th>Scholarship</th>
                                <th>Year</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!$result) {
                                echo "<tr><td colspan='7'>Query Error: " . $conn->error . "</td></tr>";
                                echo "<tr><td colspan='7'>SQL: " . $sql . "</td></tr>";
                            }
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
                                <td><?php echo htmlspecialchars($row['stu_college']); ?></td>
                                <td><?php echo htmlspecialchars($row['stu_program']); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_year']); ?></td>
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
                                echo "<tr><td colspan='7' class='text-center py-4'>No records found matching your filters.</td></tr>";
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
