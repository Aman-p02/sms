<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['adm_id'])) {
    header("Location: ../index.php");
    exit();
}
if (!isset($_GET['stu_id'])) {
    header("Location: manage_students.php");
    exit();
}
$stu_id = $_GET['stu_id'];
$stu_enroll = isset($_GET['stu_enroll']) ? $_GET['stu_enroll'] : '';
header("Location: ../stu_profile.php?stu_id=" . $stu_id . "&stu_enroll=" . urlencode($stu_enroll));
exit();
?>
