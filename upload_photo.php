<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 2592000);
    session_set_cookie_params(2592000);
    session_start();
}
include "db.php";

// Must be logged in
if (!isset($_SESSION['stu_id'])) {
    header("Location: index.php");
    exit();
}

$stu_id = $_SESSION['stu_id'];

// Redirect back to whichever page triggered the upload (falls back to dashboard)
$return_page = "stu_profile.php";
if (!empty($_POST['return_to'])) {
    // Only allow local .php filenames, never external URLs
    $candidate = basename($_POST['return_to']);
    if (preg_match('/^[a-zA-Z0-9_\-]+\.php$/', $candidate)) {
        $return_page = $candidate;
    }
}

if (isset($_FILES['stu_photo']) && $_FILES['stu_photo']['error'] === UPLOAD_ERR_OK) {

    $file = $_FILES['stu_photo'];

    // 1. Size check (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        header("Location: " . $return_page . "?photo_error=size");
        exit();
    }

    // 2. Validate actual file content (not just the filename extension)
    $allowed_types = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!array_key_exists($mime, $allowed_types)) {
        header("Location: " . $return_page . "?photo_error=type");
        exit();
    }

    $ext = $allowed_types[$mime];

    // 3. Build a safe, unique filename using student enrollment number
    $stu_enroll = null;
    $stmt_enroll = $conn->prepare("SELECT stu_enroll FROM student_master WHERE stu_id = ?");
    $stmt_enroll->bind_param("i", $stu_id);
    $stmt_enroll->execute();
    $stmt_enroll->bind_result($stu_enroll);
    $stmt_enroll->fetch();
    $stmt_enroll->close();
    
    // Fallback to stu_id if enrollment number is missing
    $enroll_identifier = (!empty($stu_enroll)) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $stu_enroll) : $stu_id;
    
    $new_filename = $enroll_identifier . "." . $ext;
    $upload_dir = "uploads/profile_photos/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $destination = $upload_dir . $new_filename;

    // 4. Get old photo so we can delete it after successful upload
    $old_photo = null;
    $stmt = $conn->prepare("SELECT stu_profilepic FROM student_master WHERE stu_id = ?");
    $stmt->bind_param("i", $stu_id);
    $stmt->execute();
    $stmt->bind_result($old_photo);
    $stmt->fetch();
    $stmt->close();

    // 5. Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {

        // 6. Update database with new photo filename
        $stmt = $conn->prepare("UPDATE student_master SET stu_profilepic = ? WHERE stu_id = ?");
        $stmt->bind_param("si", $new_filename, $stu_id);
        $stmt->execute();
        $stmt->close();

        // 7. Delete old photo file if it existed
        if (!empty($old_photo) && file_exists($upload_dir . $old_photo)) {
            unlink($upload_dir . $old_photo);
        }

        header("Location: " . $return_page . "?photo_success=1");
        exit();
    } else {
        header("Location: " . $return_page . "?photo_error=upload");
        exit();
    }

} else {
    header("Location: " . $return_page . "?photo_error=none");
    exit();
}
