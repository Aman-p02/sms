<?php
// ml/models/SVM.php

class SVM {
    private $weights = [];
    private $bias = 0;
    private $learningRate;
    private $lambda; // Regularization parameter
    private $epochs;

    public function __construct($learningRate = 0.001, $lambda = 0.01, $epochs = 1000) {
        $this->learningRate = $learningRate;
        $this->lambda = $lambda;
        $this->epochs = $epochs;
    }

    public function fit($X, $y) {
        $n_samples = count($X);
        if ($n_samples == 0) return;
        $n_features = count($X[0]);
        
        $this->weights = array_fill(0, $n_features, 0.0);
        $this->bias = 0.0;
        
        // SVM uses labels -1 and 1 instead of 0 and 1
        $y_svm = [];
        foreach ($y as $label) {
            $y_svm[] = ($label == 1) ? 1 : -1;
        }

        for ($epoch = 0; $epoch < $this->epochs; $epoch++) {
            for ($i = 0; $i < $n_samples; $i++) {
                $linear_model = $this->bias;
                for ($j = 0; $j < $n_features; $j++) {
                    $linear_model += $X[$i][$j] * $this->weights[$j];
                }
                
                // Hinge loss condition: y_i * (w.x_i - b) >= 1
                if ($y_svm[$i] * $linear_model >= 1) {
                    for ($j = 0; $j < $n_features; $j++) {
                        $this->weights[$j] -= $this->learningRate * (2 * $this->lambda * $this->weights[$j]);
                    }
                } else {
                    for ($j = 0; $j < $n_features; $j++) {
                        $this->weights[$j] -= $this->learningRate * (2 * $this->lambda * $this->weights[$j] - $X[$i][$j] * $y_svm[$i]);
                    }
                    $this->bias -= $this->learningRate * $y_svm[$i];
                }
            }
        }
    }

    public function predictProba($X) {
        // SVM doesn't natively output probabilities. 
        // We can approximate it using distance from hyperplane and a sigmoid.
        $probs = [];
        foreach ($X as $row) {
            $linear_model = $this->bias;
            for ($j = 0; $j < count($row); $j++) {
                $linear_model += $row[$j] * $this->weights[$j];
            }
            // Use sigmoid to scale distance to [0, 1]
            if ($linear_model > 20) $probs[] = 1.0;
            else if ($linear_model < -20) $probs[] = 0.0;
            else $probs[] = 1.0 / (1.0 + exp(-$linear_model));
        }
        return $probs;
    }

    public function predict($X) {
        $preds = [];
        foreach ($X as $row) {
            $linear_model = $this->bias;
            for ($j = 0; $j < count($row); $j++) {
                $linear_model += $row[$j] * $this->weights[$j];
            }
            $preds[] = ($linear_model >= 0) ? 1 : 0;
        }
        return $preds;
    }
}
?>
