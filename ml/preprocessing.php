<?php
// ml/preprocessing.php

class Preprocessor {
    public $mapping = [];

    // Fit the encoder on the raw training dataset
    public function fit($rawData, $features) {
        $this->mapping = [];
        
        foreach ($features as $col) {
            // Check if column is numeric or categorical
            $isNumeric = true;
            $uniqueValues = [];
            foreach ($rawData as $row) {
                $val = isset($row[$col]) ? trim((string)$row[$col]) : '';
                if ($val === '') continue; // Skip empty
                if (!is_numeric($val)) {
                    $isNumeric = false;
                }
                $uniqueValues[$val] = true;
            }
            
            $uniqueKeys = array_keys($uniqueValues);
            $numUnique = count($uniqueKeys);
            
            // If less than 2 unique values but not numeric, treat as one-hot with 1 column
            if ($isNumeric && $numUnique > 2) {
                // Treat as numeric continuous
                $this->mapping[$col] = [
                    'type' => 'numeric'
                ];
            } else if ($numUnique == 2) {
                // Binary categorical (e.g., Yes/No, Male/Female) -> map to 0 and 1
                sort($uniqueKeys);
                $this->mapping[$col] = [
                    'type' => 'binary',
                    'values' => [
                        $uniqueKeys[0] => 0,
                        $uniqueKeys[1] => 1
                    ]
                ];
            } else {
                // One-Hot Encoding for 3+ categories (or 1 category)
                $this->mapping[$col] = [
                    'type' => 'onehot',
                    'values' => $uniqueKeys
                ];
            }
        }
        
        // Save mapping to file for persistent predictions
        file_put_contents(__DIR__ . '/mapping.json', json_encode($this->mapping, JSON_PRETTY_PRINT));
    }

    // Load existing mapping (used during live prediction)
    public function load() {
        if (file_exists(__DIR__ . '/mapping.json')) {
            $this->mapping = json_decode(file_get_contents(__DIR__ . '/mapping.json'), true);
        }
    }

    // Transform raw rows into fully numeric arrays based on the mapping
    public function transform($data) {
        $encodedData = [];
        foreach ($data as $row) {
            $encodedRow = [];
            foreach ($this->mapping as $col => $map) {
                $val = isset($row[$col]) ? trim((string)$row[$col]) : '';
                
                if ($map['type'] == 'numeric') {
                    $encodedRow[] = (float)$val;
                } else if ($map['type'] == 'binary') {
                    // Fallback to 0 if unseen category
                    $encodedRow[] = isset($map['values'][$val]) ? $map['values'][$val] : 0;
                } else if ($map['type'] == 'onehot') {
                    // Generate one binary column per category
                    foreach ($map['values'] as $category) {
                        $encodedRow[] = ($val === $category) ? 1 : 0;
                    }
                }
            }
            $encodedData[] = $encodedRow;
        }
        return $encodedData;
    }
}
?>
