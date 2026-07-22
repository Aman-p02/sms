<?php
// ml/metrics.php

// Calculate Classification Metrics
function calculateMetrics($actual, $predicted, $probabilities = null) {
    $tp = 0; $tn = 0; $fp = 0; $fn = 0;
    for ($i = 0; $i < count($actual); $i++) {
        if ($actual[$i] == 1 && $predicted[$i] == 1) $tp++;
        if ($actual[$i] == 0 && $predicted[$i] == 0) $tn++;
        if ($actual[$i] == 0 && $predicted[$i] == 1) $fp++;
        if ($actual[$i] == 1 && $predicted[$i] == 0) $fn++;
    }
    
    $accuracy = ($tp + $tn) / max(1, count($actual));
    $precision = $tp / max(1, ($tp + $fp));
    $recall = $tp / max(1, ($tp + $fn));
    $f1 = 2 * ($precision * $recall) / max(0.0001, ($precision + $recall));
    
    // Calculate AUC if continuous probabilities are available
    $auc = 0.5; // Random guess baseline
    if ($probabilities !== null) {
        $auc = calculateAUC($actual, $probabilities);
    }
    
    return [
        'Accuracy' => round($accuracy, 4),
        'Precision' => round($precision, 4),
        'Recall' => round($recall, 4),
        'F1-Score' => round($f1, 4),
        'AUC' => round($auc, 4)
    ];
}

// AUC (Area Under ROC Curve) using the Mann-Whitney U test equivalence
function calculateAUC($actual, $probs) {
    $pos = [];
    $neg = [];
    for ($i = 0; $i < count($actual); $i++) {
        if ($actual[$i] == 1) {
            $pos[] = $probs[$i];
        } else {
            $neg[] = $probs[$i];
        }
    }
    
    if (count($pos) == 0 || count($neg) == 0) return 0.5;
    
    $concordant = 0;
    foreach ($pos as $p) {
        foreach ($neg as $n) {
            if ($p > $n) {
                $concordant += 1;
            } else if ($p == $n) {
                $concordant += 0.5;
            }
        }
    }
    return $concordant / (count($pos) * count($neg));
}
?>
