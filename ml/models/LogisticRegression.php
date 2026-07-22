<?php
// ml/models/LogisticRegression.php

class LogisticRegression {
    private $weights = [];
    private $bias = 0;
    private $learningRate;
    private $epochs;

    public function __construct($learningRate = 0.01, $epochs = 1000) {
        $this->learningRate = $learningRate;
        $this->epochs = $epochs;
    }

    private function sigmoid($z) {
        // Prevent floating point overflow
        if ($z > 20) return 1.0;
        if ($z < -20) return 0.0;
        return 1.0 / (1.0 + exp(-$z));
    }

    // Train the model using Gradient Descent
    public function fit($X, $y) {
        $n_samples = count($X);
        if ($n_samples == 0) return;
        $n_features = count($X[0]);
        
        $this->weights = array_fill(0, $n_features, 0.0);
        $this->bias = 0.0;

        for ($epoch = 0; $epoch < $this->epochs; $epoch++) {
            $dw = array_fill(0, $n_features, 0.0);
            $db = 0.0;
            
            for ($i = 0; $i < $n_samples; $i++) {
                $linear_model = $this->bias;
                for ($j = 0; $j < $n_features; $j++) {
                    $linear_model += $X[$i][$j] * $this->weights[$j];
                }
                
                $y_pred = $this->sigmoid($linear_model);
                $error = $y_pred - $y[$i];
                
                for ($j = 0; $j < $n_features; $j++) {
                    $dw[$j] += $error * $X[$i][$j];
                }
                $db += $error;
            }
            
            // Update weights
            for ($j = 0; $j < $n_features; $j++) {
                $this->weights[$j] -= $this->learningRate * ($dw[$j] / $n_samples);
            }
            $this->bias -= $this->learningRate * ($db / $n_samples);
        }
    }

    // Predict continuous probabilities [0, 1]
    public function predictProba($X) {
        $probs = [];
        foreach ($X as $row) {
            $linear_model = $this->bias;
            for ($j = 0; $j < count($row); $j++) {
                $linear_model += $row[$j] * $this->weights[$j];
            }
            $probs[] = $this->sigmoid($linear_model);
        }
        return $probs;
    }

    // Predict classes (1 or 0)
    public function predict($X, $threshold = 0.5) {
        $probs = $this->predictProba($X);
        $preds = [];
        foreach ($probs as $p) {
            $preds[] = ($p >= $threshold) ? 1 : 0;
        }
        return $preds;
    }
}
?>
