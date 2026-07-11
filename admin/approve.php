<?php
require_once 'session.php';
include "../db.php";

// Handle Approve / Reject
if (isset($_GET['action']) && isset($_GET['app_id'])) {
    $action = $_GET['action'];
    $app_id = intval($_GET['app_id']);
    
    if ($action == 'approve') {
        $conn->query("UPDATE scholarship SET app_status = 'Approved' WHERE app_id = $app_id");
    } elseif ($action == 'reject') {
        $conn->query("UPDATE scholarship SET app_status = 'Rejected' WHERE app_id = $app_id");
    }
    
    header("Location: approve.php");
    exit();
}

$sql = "SELECT 
            s.app_id, 
            stu.stu_fname, stu.stu_lname,
            ss.ss_name, ss.ss_type, ss.ss_amount, ss.ss_id,
            (SELECT COUNT(*) FROM scholarship WHERE ss_id = ss.ss_id AND app_status = 'Approved') AS total_beneficiary
        FROM scholarship s
        JOIN student_master stu ON s.stu_id = stu.stu_id
        JOIN ss_master ss ON s.ss_id = ss.ss_id
        WHERE s.app_status = 'Applied'
        ORDER BY s.app_id DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve / Reject Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4 content-area">

            <h3 class="mb-3">Approve or Reject Applications</h3>

            <table class="table table-striped table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Student</th>
                        <th>Type of Scholarship</th>
                        <th>Amount of Scholarship</th>
                        <th>Name of Scholarship</th>
                        <th>Total Amount</th>
                        <th>Total Beneficiary</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php 
                                $total_beneficiary = $row['total_beneficiary'];
                                $total_amount = $total_beneficiary * $row['ss_amount'];
                                $student_name = trim($row['stu_fname'] . ' ' . $row['stu_lname']);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student_name); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_type']); ?></td>
                                <td><?php echo number_format($row['ss_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
                                <td><?php echo number_format($total_amount, 2); ?></td>
                                <td><?php echo $total_beneficiary; ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="approve.php?action=approve&app_id=<?php echo $row['app_id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this application?');">Approve</a>
                                        <a href="approve.php?action=reject&app_id=<?php echo $row['app_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this application?');">Reject</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No pending applications found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>
