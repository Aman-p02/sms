<?php
// ml/predict.php

require_once 'config.php';
require_once 'preprocessing.php';

require_once 'models/LogisticRegression.php';
require_once 'models/DecisionTree.php';
require_once 'models/RandomForest.php';
require_once 'models/SVM.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelName = $_POST['model'] ?? 'LR';
    
    // Prepare input row based on config
    $inputRow = [];
    foreach ($ML_FEATURES as $feature) {
        $inputRow[$feature] = $_POST[$feature] ?? '';
    }
    
    // Load preprocessor mapping
    $preprocessor = new Preprocessor();
    $preprocessor->load();
    if (empty($preprocessor->mapping)) {
        echo json_encode(["status" => "error", "message" => "Models have not been trained yet. Please retrain models first."]);
        exit;
    }
    
    // Encode input using the SAME mapping used during training
    $X = $preprocessor->transform([$inputRow]);
    
    // Load the selected model
    $modelFile = __DIR__ . '/saved_models/' . $modelName . '.dat';
    if (!file_exists($modelFile)) {
        echo json_encode(["status" => "error", "message" => "Model file not found. Please retrain models."]);
        exit;
    }
    
    $model = unserialize(file_get_contents($modelFile));
    if (!$model) {
        echo json_encode(["status" => "error", "message" => "Failed to load model from disk."]);
        exit;
    }
    
    // Get prediction and probability
    $prediction = $model->predict($X)[0];
    $probability = $model->predictProba($X)[0];
    
    // If prediction is 0, confidence in that decision is (1 - prob)
    $confidence = ($prediction == 1) ? $probability : (1 - $probability);
    $confidencePct = round($confidence * 100, 2);
    
    echo json_encode([
        "status" => "success", 
        "prediction" => $prediction, 
        "confidence" => $confidencePct
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
