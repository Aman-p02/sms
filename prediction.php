<?php
session_start();
include "db.php";
include "ml/config.php";

// Ensure only admin can access
if (!isset($_SESSION['adm_id'])) {
    header("Location: admin/adm_login.php");
    exit();
}

$metrics = [];
if (file_exists('ml/metrics.json')) {
    $metrics = json_decode(file_get_contents('ml/metrics.json'), true);
}

include 'header.php';
?>

<link href="admin/css/style.css" rel="stylesheet">

<div class="container-fluid">
    <div class="row">
        <?php 
        // We are in the root directory, but we want to include the admin sidebar.
        // It might be tricky since admin/sidebar.php uses getcwd() logic. Let's just include it.
        // Or wait, the admin sidebar relies on variables set in it. 
        include 'admin/sidebar.php'; 
        ?>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4 content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Scholarship Prediction (Machine Learning)</h2>
                <button id="btn-retrain" class="btn btn-warning fw-bold">
                    <i class="bi bi-arrow-clockwise"></i> Retrain Models
                </button>
            </div>

            <!-- SECTION A: Model Comparison Table -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Model Comparison Table (Latest Training Run)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Algorithm</th>
                                    <th>Accuracy</th>
                                    <th>Precision</th>
                                    <th>Recall</th>
                                    <th>F1-Score</th>
                                    <th>AUC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($metrics)): ?>
                                <tr>
                                    <td colspan="6" class="text-muted">No models trained yet. Click 'Retrain Models' to begin.</td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach (['LR' => 'Logistic Regression', 'SVM' => 'Support Vector Machine', 'DT' => 'Decision Tree', 'RFC' => 'Random Forest'] as $code => $name): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo $name; ?></td>
                                        <td><?php echo isset($metrics[$code]['Accuracy']) ? ($metrics[$code]['Accuracy'] * 100) . '%' : '-'; ?></td>
                                        <td><?php echo isset($metrics[$code]['Precision']) ? ($metrics[$code]['Precision'] * 100) . '%' : '-'; ?></td>
                                        <td><?php echo isset($metrics[$code]['Recall']) ? ($metrics[$code]['Recall'] * 100) . '%' : '-'; ?></td>
                                        <td><?php echo isset($metrics[$code]['F1-Score']) ? ($metrics[$code]['F1-Score'] * 100) . '%' : '-'; ?></td>
                                        <td><?php echo isset($metrics[$code]['AUC']) ? ($metrics[$code]['AUC'] * 100) . '%' : '-'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION B: Live Prediction Tool -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Live Scholarship Prediction Tool</h5>
                </div>
                <div class="card-body">
                    <form id="prediction-form">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Select Model</label>
                                <select name="model" class="form-select" required>
                                    <option value="LR">Logistic Regression</option>
                                    <option value="SVM">Support Vector Machine (Linear)</option>
                                    <option value="DT">Decision Tree</option>
                                    <option value="RFC">Random Forest Classifier</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <?php 
                            $mapping = [];
                            if (file_exists('ml/mapping.json')) {
                                $mapping = json_decode(file_get_contents('ml/mapping.json'), true);
                            }
                            foreach ($ML_FEATURES as $feature): 
                                $colMap = $mapping[$feature] ?? null;
                                $isNumeric = ($colMap && $colMap['type'] == 'numeric');
                                $options = [];
                                if ($colMap && $colMap['type'] == 'binary') {
                                    $options = array_keys($colMap['values']);
                                } else if ($colMap && $colMap['type'] == 'onehot') {
                                    $options = $colMap['values'];
                                }
                            ?>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-capitalize fw-bold"><?php echo str_replace('stu_', '', $feature); ?></label>
                                <?php if ($isNumeric): ?>
                                    <input type="text" name="<?php echo $feature; ?>" class="form-control" placeholder="Enter <?php echo $feature; ?>" required>
                                <?php else: ?>
                                    <select name="<?php echo $feature; ?>" class="form-select" required>
                                        <option value="">-- Select --</option>
                                        <?php foreach ($options as $opt): ?>
                                            <option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-success px-4 fw-bold">Predict Eligibility</button>
                    </form>
                    
                    <div id="prediction-result" class="mt-4" style="display:none;">
                        <h4 class="mb-2">Result: <span id="res-eligible" class="fw-bold"></span></h4>
                        <p class="mb-0 text-muted fs-5">Confidence / Probability: <span id="res-confidence" class="fw-bold text-dark"></span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Retrain Models
    $('#btn-retrain').click(function() {
        Swal.fire({
            title: 'Training Models...',
            text: 'Please wait, this may take a few seconds as it is training purely in PHP.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'ml/train.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Success', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred during training.', 'error');
            }
        });
    });

    // Predict
    $('#prediction-form').submit(function(e) {
        e.preventDefault();
        
        let formData = $(this).serialize();
        
        $.ajax({
            url: 'ml/predict.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#prediction-result').fadeIn();
                    if (response.prediction == 1) {
                        $('#res-eligible').text('Yes (Eligible)').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#res-eligible').text('No (Not Eligible)').removeClass('text-success').addClass('text-danger');
                    }
                    $('#res-confidence').text(response.confidence + '%');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to get prediction.', 'error');
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>
