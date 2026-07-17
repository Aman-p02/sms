<?php
require_once 'session.php';

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Session check
if (!isset($_SESSION['stu_id'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

$stu_id = $_SESSION['stu_id'];
$message = "";

// Fetch student details
$stmt = $conn->prepare("SELECT stu_fname, stu_lname, stu_program, stu_college FROM student_master WHERE stu_id = ?");
$stmt->bind_param("i", $stu_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$student_name = trim($student['stu_fname'] . ' ' . $student['stu_lname']);
$student_program = $student['stu_program'];
$student_college = $student['stu_college'];
$stmt->close();

// Check if already submitted
$check_stmt = $conn->prepare("SELECT id FROM feedback WHERE stu_id = ?");
$check_stmt->bind_param("i", $stu_id);
$check_stmt->execute();
$check_res = $check_stmt->get_result();
$has_submitted = ($check_res->num_rows > 0);
$check_stmt->close();

if (isset($_POST['post_feedback']) && !$has_submitted) {
    $feedback_text = trim($_POST['feedback_text']);
    
    if (empty($feedback_text)) {
        $message = "<div class='alert alert-danger mt-3'>Feedback cannot be empty.</div>";
    } else {
        $insert = $conn->prepare("INSERT INTO feedback (stu_id, feedback_text) VALUES (?, ?)");
        $insert->bind_param("is", $stu_id, $feedback_text);
        
        if ($insert->execute()) {
            $message = "<div class='alert alert-success mt-3'>Thank you! Your feedback has been submitted to the admin.</div>";
            $has_submitted = true; // Update state after successful submission
        } else {
            $message = "<div class='alert alert-danger mt-3'>Submission failed!</div>";
        }
        $insert->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Feedback</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
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
                    <h2 class="mb-0">Welcome, <?php echo htmlspecialchars($student['stu_fname']); ?></h2>
                </div>
            </div>

            <!-- SECTION: FEEDBACK -->
            <div id="feedbackSection" class="mt-5">
                <h4>Submit Feedback</h4>
                
                <?php if (!empty($message)) echo $message; ?>

                <div class="card p-4 shadow card-custom mt-3">
                    <?php if ($has_submitted): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h3 class="mt-3 text-success fw-bold">Feedback Submitted</h3>
                            <p class="text-muted fs-5 mt-2">You have already submitted your feedback. Thank you for helping us improve!</p>
                        </div>
                    <?php else: ?>
                        <form method="post">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_name); ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">College</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_college); ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Program</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_program); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Your Feedback</label>
                                <textarea class="form-control" name="feedback_text" rows="5" required placeholder="Write your experience..."></textarea>
                            </div>
                            <button type="submit" name="post_feedback" class="btn btn-primary">Submit Feedback</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
