<?php
require_once 'session.php';
include "../db.php";

$ss_id_filter = isset($_GET['ss_id']) ? $_GET['ss_id'] : '';

// Fetch all active scholarships for the dropdown
$scholarships = $conn->query("SELECT ss_id, ss_name, ss_year FROM ss_master ORDER BY ss_name ASC, ss_year DESC");

// Fetch applied students based on filter
$sql = "SELECT s.app_status, stu.stu_fname, stu.stu_lname, stu.stu_email, stu.stu_program, ss.ss_name 
        FROM scholarship s
        JOIN student_master stu ON s.stu_id = stu.stu_id
        JOIN ss_master ss ON s.ss_id = ss.ss_id";

if (!empty($ss_id_filter)) {
    $sql .= " WHERE s.ss_id = '" . $conn->real_escape_string($ss_id_filter) . "'";
}

$sql .= " ORDER BY stu.stu_fname ASC";
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
                <label class="form-label fw-bold">Select Scholarship</label>
                <div class="d-flex gap-2">
                    <select name="ss_id" class="form-select w-50">
                        <option value="">All Scholarships</option>
                        <?php while($ss = $scholarships->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($ss['ss_id']); ?>" <?php echo ($ss_id_filter == $ss['ss_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ss['ss_name'] . ' (' . $ss['ss_year'] . ')'); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" class="btn btn-primary px-4">Filter</button>
                    <a href="export.php?type=applied_students&ss_id=<?php echo urlencode($ss_id_filter); ?>" class="btn btn-success px-4">Export</a>
                </div>
            </form>

            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Scholarship Name</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
                                <td><?php echo htmlspecialchars($row['stu_email']); ?></td>
                                <td><?php echo htmlspecialchars($row['stu_program']); ?></td>
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
                            <td colspan="5" class="text-center text-muted py-4">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
