<?php
session_start();

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Session check
if (!isset($_SESSION['stu_id'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

if (!isset($_SESSION['stu_id'])) {
    header("Location: index.php");
    exit();
}

$stu_id = $_SESSION['stu_id'];
$stu_fname = $_SESSION['stu_fname'];
$message = "";

if (isset($_POST['change_password'])) {
    
    $oldPass = $_POST['old_password'];
    $newPass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    // Fetch current password
    $stmt = $conn->prepare("SELECT `stu_pass` FROM `student_master` WHERE `stu_id` = '".$stu_id."' ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Verify old password
    if ($oldPass !== $user['stu_pass']) {
        $message = "<div style='color:red;'>Old password is incorrect!</div>";
    } 
    elseif ($newPass !== $confirm) {
        $message = "<div style='color:red;'>Passwords do not match!</div>";
    } 
    else {
        $update = $conn->prepare("UPDATE `student_master` SET stu_pass = '".$newPass."' WHERE `stu_id`= '".$stu_id."' ");

        if ($update->execute()) {
            $message = "<div style='color:green;'>Password updated successfully!</div>";
        } else {
            $message = "<div style='color:red;'>Update failed!</div>";
        }

        $update->close();
    }

    $stmt->close();
}
?>


<!------------------------------------------------------------------>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Scholarship Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 </head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'stu_sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4">

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <?php $photo_editable = false; include 'stu_photo_widget.php'; ?>
                    <h2 class="mb-0">Welcome, <?php echo htmlspecialchars($stu_fname); ?></h2>
                </div>
                <button class="btn btn-primary">Profile</button>
            </div>

                        
            <!-- SECTION: CHANGE PASSWORD -->
            <div id="changePassword" class="mt-5">
                <h4>Change Password</h4>
                <div class="card p-3 shadow card-custom">
                    <form method="post">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control mb-2" name="old_password">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control mb-2" name="new_password">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control mb-2" name="confirm_password">
                        
                        <button class="btn btn-danger mt-2" name="change_password">Change Password</button>
                    </form>
                    <?php if (!empty($message)) echo $message; ?>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
