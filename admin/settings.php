<?php
require_once 'session.php';
include "../db.php";

$message = "";

if (isset($_POST['update_password'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if ($new_pass !== $confirm_pass) {
        $message = "<div class='alert alert-danger'>New password and confirm password do not match.</div>";
    } else {
        $adm_id = $_SESSION['adm_id'];
        
        // Fetch current password
        $stmt = $conn->prepare("SELECT adm_pass FROM admin_master WHERE adm_id = ?");
        $stmt->bind_param("i", $adm_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if ($old_pass === $user['adm_pass']) {
                // Update password
                $update_stmt = $conn->prepare("UPDATE admin_master SET adm_pass = ? WHERE adm_id = ?");
                $update_stmt->bind_param("si", $new_pass, $adm_id);
                if ($update_stmt->execute()) {
                    $message = "<div class='alert alert-success'>Password updated successfully!</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Failed to update password.</div>";
                }
                $update_stmt->close();
            } else {
                $message = "<div class='alert alert-danger'>Incorrect old password.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Admin user not found.</div>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4 content-area">
            <h3 class="mb-4 text-primary fw-bold">Settings</h3>

            <div class="card p-4 shadow-sm card-custom" style="max-width: 500px;">
                <h5 class="mb-4 fw-bold">Change Admin Password</h5>
                <?php echo $message; ?>
                <form method="POST">
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold text-muted">Old Password</label>
                        <input type="password" name="old_pass" class="form-control" placeholder="Enter Old Password" required>
                        <button type="button" class="btn border-0 position-absolute end-0 bottom-0 toggle-password" tabindex="-1" style="margin-bottom: 2px;">
                            <i class="bi bi-eye text-muted"></i>
                        </button>
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold text-muted">New Password</label>
                        <input type="password" name="new_pass" class="form-control" placeholder="Enter New Password" required>
                        <button type="button" class="btn border-0 position-absolute end-0 bottom-0 toggle-password" tabindex="-1" style="margin-bottom: 2px;">
                            <i class="bi bi-eye text-muted"></i>
                        </button>
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold text-muted">Confirm New Password</label>
                        <input type="password" name="confirm_pass" class="form-control" placeholder="Confirm New Password" required>
                        <button type="button" class="btn border-0 position-absolute end-0 bottom-0 toggle-password" tabindex="-1" style="margin-bottom: 2px;">
                            <i class="bi bi-eye text-muted"></i>
                        </button>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-primary px-4 mt-2">Update Password</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.toggle-password');
        if(btn) {
            const container = btn.closest('.position-relative');
            const input = container ? container.querySelector('input') : null;
            const icon = btn.querySelector('i');
            if (input && input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else if (input && input.type === 'text') {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    });
</script>
</body>
</html>
