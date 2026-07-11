<?php
/*
    stu_photo_widget.php
    ---------------------
    Reusable "Welcome" profile photo widget.

    REQUIRES (must already be set by the including page):
        $conn       - database connection
        $stu_id     - logged-in student's ID
        $stu_fname  - student's first name (for placeholder initial)

    OPTIONAL (set before including):
        $photo_editable = true;   // allows click-to-upload (default: false = view only)
*/

if (!isset($photo_editable)) {
    $photo_editable = false;
}

$__stu_photo = null;
$stmt = $conn->prepare("SELECT stu_profilepic FROM student_master WHERE stu_id = ?");
$stmt->bind_param("i", $stu_id);
$stmt->execute();
$stmt->bind_result($__stu_photo);
$stmt->fetch();
$stmt->close();

$__photo_path = (!empty($__stu_photo) && file_exists("uploads/profile_photos/" . $__stu_photo))
    ? "uploads/profile_photos/" . $__stu_photo
    : null;
?>
<style>
    .profile-photo-wrap {
        position: relative;
        width: 60px;
        height: 60px;
    }
    .profile-photo-wrap.editable {
        cursor: pointer;
    }
    .profile-photo-wrap img,
    .profile-photo-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #0d6efd;
    }
    .profile-photo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #0d6efd;
        color: #fff;
        font-size: 24px;
        font-weight: bold;
    }
    .profile-photo-badge {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 20px;
        height: 20px;
        background-color: #0d6efd;
        color: #fff;
        border-radius: 50%;
        border: 2px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        line-height: 1;
    }
</style>

<?php if ($photo_editable) { ?>
    <!-- EDITABLE: click photo to upload a new one -->
    <form id="photoForm" action="upload_photo.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="return_to" value="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF'])); ?>">
        <label for="photoInput" class="profile-photo-wrap editable mb-0">
            <?php if ($__photo_path) { ?>
                <img src="<?php echo htmlspecialchars($__photo_path); ?>?v=<?php echo time(); ?>" alt="Profile Photo">
            <?php } else { ?>
                <div class="profile-photo-placeholder">
                    <?php echo htmlspecialchars(strtoupper(substr($stu_fname, 0, 1))); ?>
                </div>
            <?php } ?>
            <span class="profile-photo-badge">+</span>
        </label>
        <input type="file" id="photoInput" name="stu_photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="d-none" onchange="document.getElementById('photoForm').submit();">
    </form>
<?php } else { ?>
    <!-- VIEW ONLY: no click / no upload -->
    <div class="profile-photo-wrap">
        <?php if ($__photo_path) { ?>
            <img src="<?php echo htmlspecialchars($__photo_path); ?>?v=<?php echo time(); ?>" alt="Profile Photo">
        <?php } else { ?>
            <div class="profile-photo-placeholder">
                <?php echo htmlspecialchars(strtoupper(substr($stu_fname, 0, 1))); ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
