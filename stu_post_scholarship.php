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

// Ensure notifications table exists
$conn->query("CREATE TABLE IF NOT EXISTS `admin_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$message = "";
if (isset($_SESSION['upload_msg'])) {
    $message = $_SESSION['upload_msg'];
    unset($_SESSION['upload_msg']);
}

// Fetch approved scholarships for this student
$approved_scholarships = [];
$stmt_app = $conn->prepare("SELECT sm.ss_id, sm.ss_name FROM scholarship s JOIN ss_master sm ON s.ss_id = sm.ss_id WHERE s.stu_id = ? AND s.app_status = 'Approved'");
$stmt_app->bind_param("i", $stu_id);
$stmt_app->execute();
$res_app = $stmt_app->get_result();
while ($row = $res_app->fetch_assoc()) {
    $approved_scholarships[] = $row;
}
$stmt_app->close();

// Handle File Upload
if (isset($_POST['upload_doc'])) {
    if (isset($_FILES['post_doc']) && $_FILES['post_doc']['error'] == 0) {
        $doc_name = $_POST['doc_name'] ?? 'Document';
        $doc_name_safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $doc_name); // Sanitize document name
        $selected_ss = $_POST['scholarship_id'] ?? '';
        
        $ss_name = "Unknown Scholarship";
        foreach ($approved_scholarships as $ss) {
            if ($ss['ss_id'] == $selected_ss) {
                $ss_name = $ss['ss_name'];
                break;
            }
        }

        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        $fileName = basename($_FILES['post_doc']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowedTypes)) {
            if ($_FILES['post_doc']['size'] < 5000000) { // 5MB limit
                $upload_dir = "uploads/post_scholarship/";
                $current_year = date("Y");
                
                // Get short form of scholarship name
                $words = explode(' ', str_replace(['/', '-', '&', '(', ')'], ' ', strtoupper(trim($ss_name))));
                $skip = ['AND', 'IN', 'OF', 'THE', 'FOR', 'TO', 'A'];
                $ss_name_short = '';
                foreach($words as $w) {
                    $w = trim($w);
                    if(empty($w) || in_array($w, $skip)) continue;
                    $ss_name_short .= $w[0];
                }
                if (empty($ss_name_short)) {
                    $ss_name_short = substr(preg_replace('/[^a-zA-Z0-9]/', '', $ss_name), 0, 5);
                }

                // Create unique filename: Enrollment_ScholarshipShort_Year_Timestamp.ext
                $newFileName = $stu_enroll . "_" . $ss_name_short . "_" . $current_year . "_" . time() . "." . $fileExt;
                $target_path = $upload_dir . $newFileName;

                if (move_uploaded_file($_FILES['post_doc']['tmp_name'], $target_path)) {
                    // Add Admin Notification
                    $notif_msg = "Student " . $stu_fname . " (Enrollment: " . $stu_enroll . ") uploaded a new document '" . $doc_name . "' for scholarship '" . $ss_name . "'.";
                    $notif_link = "../uploads/post_scholarship/" . $newFileName;
                    $stmt_notif = $conn->prepare("INSERT INTO admin_notifications (message, link) VALUES (?, ?)");
                    $stmt_notif->bind_param("ss", $notif_msg, $notif_link);
                    $stmt_notif->execute();
                    $stmt_notif->close();

                    $_SESSION['upload_msg'] = "<div class='alert alert-success alert-dismissible fade show shadow-sm' role='alert'>
                                    <i class='bi bi-check-circle-fill me-2'></i> Document uploaded successfully!
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                </div>";
                    header("Location: stu_post_scholarship.php");
                    exit();
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
$uploaded_files = glob($upload_dir . "*_" . $stu_enroll . "_*.*");

// Also try the old format just in case
$old_format_files = glob($upload_dir . $stu_enroll . "_*.*");
if ($old_format_files) {
    $uploaded_files = array_merge($uploaded_files, $old_format_files);
}
$uploaded_files = array_unique($uploaded_files);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-Scholarship Operations - SMS</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css">
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
                                    <label class="form-label fw-semibold">Select Scholarship</label>
                                    <select class="form-select" name="scholarship_id" required>
                                        <option value="" disabled selected>-- Select Approved Scholarship --</option>
                                        <?php foreach ($approved_scholarships as $ss): ?>
                                            <option value="<?php echo $ss['ss_id']; ?>"><?php echo htmlspecialchars($ss['ss_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Document Name / Type</label>
                                    <input type="text" class="form-control" name="doc_name" required>
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
                                <button type="submit" name="upload_doc" class="btn btn-primary w-100 fw-bold py-2" <?php if(empty($approved_scholarships)) echo 'disabled'; ?>><i class="bi bi-upload me-2"></i> Upload Document</button>
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
                                                if (!is_numeric($timestamp)) {
                                                    $parts[] = $timestamp;
                                                    $timestamp = filemtime($file);
                                                }
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

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
