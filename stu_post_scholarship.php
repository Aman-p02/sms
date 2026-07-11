<?php
require_once 'session.php';
include 'db.php';

// Session check
if (!isset($_SESSION['stu_id'])) {
    header("Location: index.php");
    exit();
}

$stu_id = $_SESSION['stu_id'];
$stu_enroll = $_SESSION['stu_enroll'];
$stu_fname = $_SESSION['stu_fname'];

$message = "";

// Handle File Upload
if (isset($_POST['upload_doc'])) {
    if (isset($_FILES['post_doc']) && $_FILES['post_doc']['error'] == 0) {
        $doc_name = $_POST['doc_name'] ?? 'Document';
        $doc_name_safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $doc_name); // Sanitize document name

        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        $fileName = basename($_FILES['post_doc']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowedTypes)) {
            if ($_FILES['post_doc']['size'] < 5000000) { // 5MB limit
                $upload_dir = "uploads/post_scholarship/";
                // Create unique filename: ENROLL_SafeName_TIMESTAMP.ext
                $newFileName = $stu_enroll . "_" . $doc_name_safe . "_" . time() . "." . $fileExt;
                $target_path = $upload_dir . $newFileName;

                if (move_uploaded_file($_FILES['post_doc']['tmp_name'], $target_path)) {
                    $message = "<div class='alert alert-success alert-dismissible fade show shadow-sm' role='alert'>
                                    <i class='bi bi-check-circle-fill me-2'></i> Document uploaded successfully!
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                </div>";
                } else {
                    $message = "<div class='alert alert-danger'>Failed to move uploaded file.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>File size must be under 5MB.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Invalid file type. Only PDF, JPG, and PNG are allowed.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Please select a file to upload.</div>";
    }
}

// Fetch previously uploaded documents for this student
$upload_dir = "uploads/post_scholarship/";
$uploaded_files = glob($upload_dir . $stu_enroll . "_*.*");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-Scholarship Operations - SMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .card-custom { border-radius: 12px; border: none; }
        .upload-box {
            border: 2px dashed #0d6efd;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-box:hover {
            background-color: #e9ecef;
            border-color: #0b5ed7;
        }
        .file-icon { font-size: 3rem; color: #0d6efd; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'stu_sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4" style="background-color: #f0f2f5; min-height: 100vh;">

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <?php $photo_editable = true; include 'stu_photo_widget.php'; ?>
                    <h3 class="mb-0 fw-bold">Welcome, <?php echo htmlspecialchars($stu_fname); ?></h3>
                </div>
            </div>

            <!-- PAGE TITLE -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-primary"><i class="bi bi-folder-check me-2"></i> Post-Scholarship Operations</h4>
            </div>

            <?php echo $message; ?>

            <div class="row">
                <!-- UPLOAD SECTION -->
                <div class="col-md-5 mb-4">
                    <div class="card card-custom shadow-sm h-100">
                        <div class="card-header bg-white border-bottom pt-3 pb-2">
                            <h5 class="fw-bold mb-0">Upload New Document</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Document Name / Type</label>
                                    <input type="text" class="form-control" name="doc_name" placeholder="e.g. Sem 2 Marksheet, Fee Receipt" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Select File</label>
                                    <div class="upload-box" onclick="document.getElementById('post_doc').click();">
                                        <i class="bi bi-cloud-arrow-up file-icon"></i>
                                        <p class="mt-2 mb-0 fw-semibold text-muted">Click to browse or drag file here</p>
                                        <small class="text-muted">(Max: 5MB | PDF, JPG, PNG)</small>
                                    </div>
                                    <input type="file" id="post_doc" name="post_doc" class="d-none" required onchange="document.getElementById('file-name-display').innerText = this.files[0].name;">
                                    <div id="file-name-display" class="mt-2 text-primary fw-bold text-center"></div>
                                </div>
                                <button type="submit" name="upload_doc" class="btn btn-primary w-100 fw-bold py-2"><i class="bi bi-upload me-2"></i> Upload Document</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- HISTORY SECTION -->
                <div class="col-md-7 mb-4">
                    <div class="card card-custom shadow-sm h-100">
                        <div class="card-header bg-white border-bottom pt-3 pb-2">
                            <h5 class="fw-bold mb-0">My Uploaded Documents</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (count($uploaded_files) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Document Name</th>
                                                <th>Upload Date</th>
                                                <th class="text-end pe-4">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($uploaded_files as $file): 
                                                // Filename format: ENROLL_DocName_TIMESTAMP.ext
                                                $basename = basename($file);
                                                $parts = explode('_', pathinfo($basename, PATHINFO_FILENAME));
                                                
                                                // Extract timestamp (last part) and name
                                                $timestamp = array_pop($parts);
                                                array_shift($parts); // Remove enroll
                                                $doc_name_display = implode(' ', $parts);
                                                if (empty($doc_name_display)) $doc_name_display = "Document";
                                            ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-file-earmark-text text-primary fs-4 me-3"></i>
                                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($doc_name_display); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted small"><?php echo date("d M Y, h:i A", $timestamp); ?></span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="<?php echo htmlspecialchars($file); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x display-4 mb-3 d-block"></i>
                                    <h5>No documents uploaded yet.</h5>
                                    <p>Your uploaded post-scholarship documents will appear here.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
