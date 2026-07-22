<?php
// ml/models/RandomForest.php

require_once __DIR__ . '/DecisionTree.php';

class RandomForest {
    private $nTrees;
    private $maxDepth;
    private $minSamplesSplit;
    private $trees = [];

    public function __construct($nTrees = 10, $maxDepth = 5, $minSamplesSplit = 2) {
        $this->nTrees = $nTrees;
        $this->maxDepth = $maxDepth;
        $this->minSamplesSplit = $minSamplesSplit;
    }

    public function fit($X, $y) {
        $n_samples = count($X);
        for ($i = 0; $i < $this->nTrees; $i++) {
            // Bootstrap sampling
            $sampleX = [];
            $sampleY = [];
            for ($j = 0; $j < $n_samples; $j++) {
                $idx = mt_rand(0, $n_samples - 1);
                $sampleX[] = $X[$idx];
                $sampleY[] = $y[$idx];
            }
            
            $tree = new DecisionTree($this->maxDepth, $this->minSamplesSplit);
            $tree->fit($sampleX, $sampleY);
            $this->trees[] = $tree;
        }
    }

    public function predictProba($X) {
        $probs = [];
        $n_samples = count($X);
        
        for ($i = 0; $i < $n_samples; $i++) {
            $sum = 0;
            foreach ($this->trees as $tree) {
                // Predict returns array of predictions for the batch
                // We just pass single row wrapped in array
                $pred = $tree->predict([$X[$i]]);
                $sum += $pred[0];
            }
            $probs[] = $sum / $this->nTrees;
        }
        
        return $probs;
    }

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
