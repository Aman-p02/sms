<?php
// ml/models/DecisionTree.php

class DecisionTree {
    private $maxDepth;
    private $minSamplesSplit;
    private $tree;

    public function __construct($maxDepth = 5, $minSamplesSplit = 2) {
        $this->maxDepth = $maxDepth;
        $this->minSamplesSplit = $minSamplesSplit;
    }

    public function fit($X, $y) {
        // Combine X and y into a single dataset for easier splitting
        $dataset = [];
        for ($i = 0; $i < count($X); $i++) {
            $row = $X[$i];
            $row[] = $y[$i]; // Append label at the end
            $dataset[] = $row;
        }
        $this->tree = $this->buildTree($dataset, 0);
    }

    private function buildTree($dataset, $depth) {
        $labels = array_column($dataset, count($dataset[0]) - 1);
        $uniqueLabels = array_unique($labels);
        
        // Stop criteria
        if (count($uniqueLabels) === 1) {
            return ['value' => array_values($uniqueLabels)[0]];
        }
        if (count($dataset) < $this->minSamplesSplit || $depth >= $this->maxDepth) {
            return ['value' => $this->majorityVote($labels)];
        }
        
        $bestSplit = $this->getBestSplit($dataset);
        
        if ($bestSplit['gini'] === INF || count($bestSplit['left']) == 0 || count($bestSplit['right']) == 0) {
            return ['value' => $this->majorityVote($labels)];
        }
        
        $leftNode = $this->buildTree($bestSplit['left'], $depth + 1);
        $rightNode = $this->buildTree($bestSplit['right'], $depth + 1);
        
        return [
            'index' => $bestSplit['index'],
            'value' => $bestSplit['value'],
            'left' => $leftNode,
            'right' => $rightNode
        ];
    }

    private function majorityVote($labels) {
        $counts = array_count_values($labels);
        arsort($counts);
        return array_key_first($counts);
    }

    private function getBestSplit($dataset) {
        $bestIndex = null;
        $bestValue = null;
        $bestGini = INF;
        $bestLeft = [];
        $bestRight = [];
        
        $n_features = count($dataset[0]) - 1;
        
        for ($index = 0; $index < $n_features; $index++) {
            // Get unique values to split on (for continuous or discrete)
            $uniqueVals = array_unique(array_column($dataset, $index));
            
            foreach ($uniqueVals as $value) {
                $groups = $this->testSplit($index, $value, $dataset);
                $gini = $this->giniIndex($groups);
                
                if ($gini < $bestGini) {
                    $bestIndex = $index;
                    $bestValue = $value;
                    $bestGini = $gini;
                    $bestLeft = $groups['left'];
                    $bestRight = $groups['right'];
                }
            }
        }
        
        return [
            'index' => $bestIndex,
            'value' => $bestValue,
            'gini' => $bestGini,
            'left' => $bestLeft,
            'right' => $bestRight
        ];
    }

    private function testSplit($index, $value, $dataset) {
        $left = [];
        $right = [];
        foreach ($dataset as $row) {
            if ($row[$index] < $value) {
                $left[] = $row;
            } else {
                $right[] = $row;
            }
        }
        return ['left' => $left, 'right' => $right];
    }

    private function giniIndex($groups) {
        $n_instances = count($groups['left']) + count($groups['right']);
        $gini = 0.0;
        
        foreach ($groups as $group) {
            $size = count($group);
            if ($size == 0) continue;
            
            $score = 0.0;
            $labels = array_column($group, count($group[0]) - 1);
            $counts = array_count_values($labels);
            
            foreach ($counts as $count) {
                $p = $count / $size;
                $score += $p * $p;
            }
            
            $gini += (1.0 - $score) * ($size / $n_instances);
        }
        
        return $gini;
    }

    public function predict($X) {
        $predictions = [];
        foreach ($X as $row) {
            $predictions[] = $this->predictRow($this->tree, $row);
        }
        return $predictions;
    }

    private function predictRow($node, $row) {
        if (isset($node['value']) && !isset($node['index'])) {
            return $node['value'];
        }
        if ($row[$node['index']] < $node['value']) {
            return $this->predictRow($node['left'], $row);
        } else {
            return $this->predictRow($node['right'], $row);
        }
    }

    public function predictProba($X) {
        // DT returns hard classes. For probability, we just return 1.0 or 0.0.
        $probs = [];
        foreach ($X as $row) {
            $pred = $this->predictRow($this->tree, $row);
            $probs[] = (float)$pred;
        }
        return $probs;
    }
}
?>
