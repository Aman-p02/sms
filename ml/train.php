<?php
// ml/train.php

// Increase execution time as manual ML training can take some seconds
ini_set('max_execution_time', 300);

require_once __DIR__ . '/../db.php';
require_once 'config.php';
require_once 'preprocessing.php';
require_once 'split.php';
require_once 'metrics.php';

require_once 'models/LogisticRegression.php';
require_once 'models/DecisionTree.php';
require_once 'models/RandomForest.php';
require_once 'models/SVM.php';

header('Content-Type: application/json');

// Fetch raw data
$rawData = [];
$labels = [];

// Join student_master with scholarship to determine label
// If student is in scholarship table with app_status = 'Approved', label = 1, else 0.
$query = "
    SELECT sm.*, 
           IF(s.app_status = 'Approved', 1, 0) as target_label
    FROM student_master sm
    LEFT JOIN scholarship s ON sm.stu_id = s.stu_id
";

$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rawData[] = $row;
        $labels[] = (int)$row['target_label'];
    }
}

if (count($rawData) == 0) {
    echo json_encode(["status" => "error", "message" => "No data found in database."]);
    exit;
}

// 1. Preprocess Data
$preprocessor = new Preprocessor();
$preprocessor->fit($rawData, $ML_FEATURES);
$X = $preprocessor->transform($rawData);

// 2. Train/Test Split (70-30)
$split = trainTestSplit($X, $labels, 0.3, 42);
$X_train = $split['trainX'];
$y_train = $split['trainY'];
$X_test = $split['testX'];
$y_test = $split['testY'];

$metrics = [];
$modelsDir = __DIR__ . '/saved_models';
if (!is_dir($modelsDir)) {
    mkdir($modelsDir, 0777, true);
}

// 3. Train Models and Calculate Metrics
// ---------------------------------------------------------
// Logistic Regression
$lr = new LogisticRegression(0.01, 1000);
$lr->fit($X_train, $y_train);
file_put_contents($modelsDir . '/LR.dat', serialize($lr));
$lr_preds = $lr->predict($X_test);
$lr_probs = $lr->predictProba($X_test);
$metrics['LR'] = calculateMetrics($y_test, $lr_preds, $lr_probs);

// Decision Tree
$dt = new DecisionTree(5, 2);
$dt->fit($X_train, $y_train);
file_put_contents($modelsDir . '/DT.dat', serialize($dt));
$dt_preds = $dt->predict($X_test);
$dt_probs = $dt->predictProba($X_test);
$metrics['DT'] = calculateMetrics($y_test, $dt_preds, $dt_probs);

// Random Forest (Using 5 trees for performance in pure PHP)
$rfc = new RandomForest(5, 5, 2); 
$rfc->fit($X_train, $y_train);
file_put_contents($modelsDir . '/RFC.dat', serialize($rfc));
$rfc_preds = $rfc->predict($X_test);
$rfc_probs = $rfc->predictProba($X_test);
$metrics['RFC'] = calculateMetrics($y_test, $rfc_preds, $rfc_probs);

// SVM
$svm = new SVM(0.001, 0.01, 1000);
$svm->fit($X_train, $y_train);
file_put_contents($modelsDir . '/SVM.dat', serialize($svm));
$svm_preds = $svm->predict($X_test);
$svm_probs = $svm->predictProba($X_test);
$metrics['SVM'] = calculateMetrics($y_test, $svm_preds, $svm_probs);

// Save metrics
file_put_contents(__DIR__ . '/metrics.json', json_encode($metrics, JSON_PRETTY_PRINT));

echo json_encode(["status" => "success", "message" => "Models trained successfully.", "metrics" => $metrics]);
?>
