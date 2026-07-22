<?php
session_start();
if (!isset($_SESSION['adm_id'])) {
    header("Location: admin/adm_login.php");
    exit();
}
include 'db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Cluster</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css">
    <link href="admin/css/style.css" rel="stylesheet">
</head>
<body>
<?php

// Initialize Clusters
$clusters = [
    'feedback' => ['Positive' => 0, 'Negative' => 0, 'Neutral' => 0],
    'gpa' => ['Poor (1.0 - 4.9)' => 0, 'Average (5.0 - 7.9)' => 0, 'Excellent (8.0 - 10.0)' => 0],
    'perc' => ['Poor (0% - 49%)' => 0, 'Average (50% - 74%)' => 0, 'Excellent (75% - 100%)' => 0],
    'gender' => [],
    'grade' => [],
    'disabled' => [],
    'marital' => [],
    'dependent' => []
];

// --- 1. FEEDBACK CLUSTERING (Sentiment Analysis) ---
$pos_words = ['good', 'great', 'excellent', 'awesome', 'nice', 'best', 'love', 'perfect', 'amazing', 'helpful'];
$neg_words = ['bad', 'poor', 'worst', 'terrible', 'issue', 'problem', 'hate', 'awful', 'slow', 'hard'];

$feed_q = $conn->query("SELECT f.feedback_text FROM feedback f INNER JOIN student_master s ON f.stu_id = s.stu_id");
$total_feedback = 0;
if ($feed_q) {
    while ($row = $feed_q->fetch_assoc()) {
        $text = strtolower($row['feedback_text']);
        $pos = 0;
        $neg = 0;
        foreach ($pos_words as $w)
            if (strpos($text, $w) !== false)
                $pos++;
        foreach ($neg_words as $w)
            if (strpos($text, $w) !== false)
                $neg++;

        if ($pos > $neg)
            $clusters['feedback']['Positive']++;
        elseif ($neg > $pos)
            $clusters['feedback']['Negative']++;
        else
            $clusters['feedback']['Neutral']++;

        $total_feedback++;
    }
}

// --- 2. STUDENT DATA CLUSTERING ---
$stu_q = $conn->query("SELECT stu_gpa, stu_perc, stu_gender, stu_disabled, stu_marital, stu_dependent, stu_grade FROM student_master");
$total_students = 0;
if ($stu_q) {
    while ($row = $stu_q->fetch_assoc()) {
        $total_students++;

        // GPA Clustering
        $gpa = (float) $row['stu_gpa'];
        if ($gpa >= 8.0)
            $clusters['gpa']['Excellent (8.0 - 10.0)']++;
        elseif ($gpa >= 5.0)
            $clusters['gpa']['Average (5.0 - 7.9)']++;
        else
            $clusters['gpa']['Poor (1.0 - 4.9)']++;

        // Percentage Clustering
        $perc = (float) $row['stu_perc'];
        if ($perc >= 75.0)
            $clusters['perc']['Excellent (75% - 100%)']++;
        elseif ($perc >= 50.0)
            $clusters['perc']['Average (50% - 74%)']++;
        else
            $clusters['perc']['Poor (0% - 49%)']++;

        // Categorical Clustering
        $gender = empty($row['stu_gender']) ? 'Unknown' : $row['stu_gender'];
        $clusters['gender'][$gender] = ($clusters['gender'][$gender] ?? 0) + 1;

        $grade = empty($row['stu_grade']) ? 'Unknown' : $row['stu_grade'];
        $clusters['grade'][$grade] = ($clusters['grade'][$grade] ?? 0) + 1;

        $disabled = empty($row['stu_disabled']) ? 'Unknown' : $row['stu_disabled'];
        $clusters['disabled'][$disabled] = ($clusters['disabled'][$disabled] ?? 0) + 1;

        $marital = empty($row['stu_marital']) ? 'Unknown' : $row['stu_marital'];
        $clusters['marital'][$marital] = ($clusters['marital'][$marital] ?? 0) + 1;

        $dependent = empty($row['stu_dependent']) ? 'Unknown' : $row['stu_dependent'];
        $clusters['dependent'][$dependent] = ($clusters['dependent'][$dependent] ?? 0) + 1;
    }
}

// Helper to render bars
function renderClusterCard($title, $icon, $data, $total, $colorClass)
{
    echo '<div class="col-md-6 col-lg-4 mb-4">';
    echo '<div class="card h-100 shadow-sm border-0 rounded-4 card-hover">';
    echo '<div class="card-header bg-white border-bottom-0 pt-4 pb-2">';
    echo '<h5 class="fw-bold text-dark"><i class="bi ' . $icon . ' text-' . $colorClass . ' me-2"></i>' . $title . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';

    // Sort descending for categorical, keep original for sequential
    if (!in_array($title, ['GPA Clusters', 'Percentage Clusters', 'Feedback Sentiment'])) {
        arsort($data);
    }

    foreach ($data as $label => $count) {
        $percent = $total > 0 ? round(($count / $total) * 100, 1) : 0;
        echo '<div class="mb-3">';
        echo '<div class="d-flex justify-content-between small fw-bold mb-1">';
        echo '<span>' . htmlspecialchars($label) . '</span>';
        echo '<span class="text-muted">' . $count . ' (' . $percent . '%)</span>';
        echo '</div>';
        echo '<div class="progress" style="height: 8px;">';
        echo '<div class="progress-bar bg-' . $colorClass . '" role="progressbar" style="width: ' . $percent . '%" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100"></div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div></div></div>';
}
?>

<link href="admin/css/style.css" rel="stylesheet">
<style>
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .page-title-gradient {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <?php include 'admin/sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4 bg-light min-vh-100">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="fw-bold page-title-gradient mb-0"><i
                            class="bi bi-pie-chart-fill me-2 text-primary"></i>Clustering Analysis Dashboard</h2>
                    <p class="text-muted mt-2 mb-0">AI-driven segmentation of student demographic and academic profiles.
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary fs-6 p-2 px-3 rounded-pill shadow-sm"><i
                            class="bi bi-people-fill me-2"></i>Total Students:
                        <?php echo $total_students; ?>
                    </span>
                    <span class="badge bg-success fs-6 p-2 px-3 rounded-pill shadow-sm ms-2"><i
                            class="bi bi-chat-dots-fill me-2"></i>Total Feedback:
                        <?php echo $total_feedback; ?>
                    </span>
                </div>
            </div>

            <div class="row">
                <?php
                renderClusterCard('GPA Clusters', 'bi-mortarboard-fill', $clusters['gpa'], $total_students, 'primary');
                renderClusterCard('Percentage Clusters', 'bi-percent', $clusters['perc'], $total_students, 'success');
                renderClusterCard('Feedback Sentiment', 'bi-emoji-smile-fill', $clusters['feedback'], $total_feedback, 'warning');

                renderClusterCard('Gender Distribution', 'bi-gender-ambiguous', $clusters['gender'], $total_students, 'info');
                renderClusterCard('Grade Distribution', 'bi-award-fill', $clusters['grade'], $total_students, 'danger');
                renderClusterCard('Disability Status', 'bi-person-wheelchair', $clusters['disabled'], $total_students, 'secondary');

                renderClusterCard('Marital Status', 'bi-heart-fill', $clusters['marital'], $total_students, 'primary');
                renderClusterCard('Dependent Status', 'bi-people-fill', $clusters['dependent'], $total_students, 'success');
                ?>
            </div>

            <?php include 'footer.php'; ?>
        </div>
    </div>
</div>
</body>
</html>