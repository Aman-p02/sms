<?php
include "../db.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "UPDATE feedback SET status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: manage_feedback.php");
exit();
?>
