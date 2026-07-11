<?php
include "../db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Manage Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4 content-area">

            <h2 class="fw-bold mb-4">Manage Student Feedback</h2>

            <!-- Table -->
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Feedback</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $sql = "SELECT f.id, f.feedback_text, f.status, f.created_at, s.stu_fname, s.stu_lname 
                            FROM feedback f 
                            INNER JOIN student_master s ON f.stu_id = s.stu_id 
                            ORDER BY f.created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback_text']); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <?php 
                                if($row['status'] == 'Approved') echo "<span class='badge bg-success'>Approved</span>";
                                else if($row['status'] == 'Rejected') echo "<span class='badge bg-danger'>Rejected</span>";
                                else echo "<span class='badge bg-warning text-dark'>Pending</span>";
                            ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'Pending') { ?>
                                <a href="approve_feedback.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                                <a href="reject_feedback.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                            <?php } else { ?>
                                <a href="approve_feedback.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm <?php echo $row['status'] == 'Approved' ? 'disabled' : ''; ?>">Approve</a>
                                <a href="reject_feedback.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm <?php echo $row['status'] == 'Rejected' ? 'disabled' : ''; ?>">Reject</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No feedback found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>
