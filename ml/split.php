<?php
// ml/split.php

// Perform a stratified Train/Test split
function trainTestSplit($X, $y, $testSize = 0.3, $seed = 42) {
    // Set seed for reproducibility so models don't fluctuate wildly on retraining
    mt_srand($seed);
    
    $trainX = [];
    $trainY = [];
    $testX = [];
    $testY = [];
    
    $class0 = [];
    $class1 = [];
    
    // Group indices by class to ensure we keep the ratio
    for ($i = 0; $i < count($y); $i++) {
        if ($y[$i] == 1) {
            $class1[] = $i;
        } else {
            $class0[] = $i;
        }
    }
    
    // Randomize indices within each class
    shuffle($class0);
    shuffle($class1);
    
    // Calculate how many of each class go to the test set
    $testSize0 = (int)round(count($class0) * $testSize);
    $testSize1 = (int)round(count($class1) * $testSize);
    
    $testIndices = array_merge(array_slice($class0, 0, $testSize0), array_slice($class1, 0, $testSize1));
    $trainIndices = array_merge(array_slice($class0, $testSize0), array_slice($class1, $testSize1));
    
    // Shuffle the final train and test sets to prevent order bias
    shuffle($testIndices);
    shuffle($trainIndices);
    
    foreach ($trainIndices as $idx) {
        $trainX[] = $X[$idx];
        $trainY[] = $y[$idx];
    }
    
    foreach ($testIndices as $idx) {
        $testX[] = $X[$idx];
        $testY[] = $y[$idx];
    }
    
    return [
        'trainX' => $trainX,
        'trainY' => $trainY,
        'testX' => $testX,
        'testY' => $testY
    ];
}
?>
