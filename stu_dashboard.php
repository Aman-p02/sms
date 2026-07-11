<?php
require_once 'session.php';
include "db.php";

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Session check
if (!isset($_SESSION['stu_id'])) {
    header("Location: index.php");
    exit();
}

$stu_id = $_SESSION['stu_id'];
$stu_fname = $_SESSION['stu_fname'];

?>


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
                    <?php $photo_editable = true; include 'stu_photo_widget.php'; ?>
                    <h2 class="mb-0">Welcome, <?php echo htmlspecialchars($stu_fname); ?></h2>
                </div>
                <button class="btn btn-primary">Profile</button>
            </div>

            <?php if (isset($_GET['photo_success'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Profile photo updated successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } elseif (isset($_GET['photo_error'])) {
                $errors = [
                    'size' => 'Image must be smaller than 5MB.',
                    'type' => 'Only JPG, PNG, and WEBP images are allowed.',
                    'upload' => 'Something went wrong while saving the photo. Please try again.',
                    'none' => 'No file was selected.'
                ];
                $msg = $errors[$_GET['photo_error']] ?? 'Upload failed. Please try again.';
            ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <!-- DASHBOARD CARDS -->
            <div class="row g-4 mb-4">

                <div class="col-md-4">
                    <div class="card shadow card-custom">
                        <div class="card-body text-center">
                            <h5>Edit Profile</h5>
                            <p>Update personal details & documents</p>
                            <a href="#editProfile" class="btn btn-primary btn-sm">Open</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow card-custom">
                        <div class="card-body text-center">
                            <h5>Apply Scholarship</h5>
                            <p>Submit applications for available schemes</p>
                            <a href="#applyScholarship" class="btn btn-success btn-sm">Apply</a>
                        </div>
                    </div>
                </div>


            </div>

            <!-- SECTION: EDIT PROFILE -->
            <div id="editProfile" class="mt-5">
                <h4>Edit Profile</h4>
                <div class="card p-3 shadow card-custom">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Upload Documents</label>
                                <input type="file" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary mt-3">Update Profile</button>
                    </form>
                </div>
            </div>

            <!-- SECTION: CHANGE PASSWORD -->
            <div id="changePassword" class="mt-5">
                <h4>Change Password</h4>
                <div class="card p-3 shadow card-custom">
                    <form>
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control mb-2">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control mb-2">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control mb-2">
                        <button class="btn btn-danger mt-2">Change Password</button>
                    </form>
                </div>
            </div>

            <!-- SECTION: APPLY SCHOLARSHIP -->
            <div id="applyScholarship" class="mt-5">
                <h4>Apply for Scholarship</h4>
                <div class="card p-3 shadow card-custom">
                    <form>
                        <label>Select Scholarship</label>
                        <select class="form-select mb-3">
                            <option>Merit-based Scholarship</option>
                            <option>Need-based Scholarship</option>
                            <option>Minority Scholarship</option>
                            <option>Research Grant</option>
                        </select>

                        <label>Upload Required Documents</label>
                        <input type="file" class="form-control mb-3">

                        <button class="btn btn-success">Submit Application</button>
                    </form>
                </div>
            </div>

            <!-- SECTION: POST-SCHOLARSHIP -->
            <div id="postScholarship" class="mt-5 mb-5">
                <h4>Post-Scholarship Operations</h4>
                <div class="card p-3 shadow card-custom">
                    <p>You may upload utilization certificates, progress reports, feedback, internship proof, etc.</p>
                    <form>
                        <label>Upload Document</label>
                        <input type="file" class="form-control mb-3">
                        <button class="btn btn-info">Submit</button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
