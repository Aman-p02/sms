<?php
session_start();
include '../db.php';
require_once 'SimpleXLSX.php'; // Include SimpleXLSX

// Must be logged in as admin
if (!isset($_SESSION['adm_id'])) {
    header("Location: adm_login.php");
    exit();
}

// Handle Template Download
if (isset($_GET['download_template'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=students_template.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('stu_enroll', 'stu_fname', 'stu_lname', 'stu_email', 'stu_pass', 'stu_program', 'stu_campus', 'stu_college'));
    fclose($output);
    exit();
}

// Handle File Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csvFile'])) {
    $file = $_FILES['csvFile'];
    
    if ($file['error'] == 0) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($ext !== 'csv' && $ext !== 'xlsx') {
            header("Location: manage_students.php?msg=" . urlencode("Invalid file format. Please upload a CSV or XLSX file.") . "&type=danger");
            exit();
        }

        $expected = ['stu_enroll', 'stu_fname', 'stu_lname', 'stu_email', 'stu_pass', 'stu_program', 'stu_campus', 'stu_college'];
        
        $rows = [];
        
        if ($ext === 'csv') {
            $handle = fopen($file['tmp_name'], "r");
            if ($handle !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            } else {
                header("Location: manage_students.php?msg=" . urlencode("Could not read the CSV file.") . "&type=danger");
                exit();
            }
        } else if ($ext === 'xlsx') {
            if ( $xlsx = \Shuchkin\SimpleXLSX::parse($file['tmp_name']) ) {
                $rows = $xlsx->rows();
            } else {
                header("Location: manage_students.php?msg=" . urlencode("Error parsing XLSX file: " . \Shuchkin\SimpleXLSX::parseError()) . "&type=danger");
                exit();
            }
        }
        
        if (empty($rows)) {
            header("Location: manage_students.php?msg=" . urlencode("The uploaded file is empty.") . "&type=danger");
            exit();
        }

        $headers = array_shift($rows); // Remove and get first row
        
        $isValid = true;
        $headerMap = [];
        foreach ($expected as $col) {
            $idx = array_search(trim($col), array_map('trim', $headers));
            if ($idx === false) {
                $isValid = false;
                break;
            }
            $headerMap[$col] = $idx;
        }

        if (!$isValid) {
            header("Location: manage_students.php?msg=" . urlencode("Invalid headers. Please download and use the template.") . "&type=danger");
            exit();
        }

        $successCount = 0;
        $errorCount = 0;

        $stmt = $conn->prepare("INSERT INTO `student_master` (`stu_enroll`, `stu_fname`, `stu_lname`, `stu_email`, `stu_pass`, `stu_program`, `stu_campus`, `stu_college`, `stu_status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Active')");

        foreach ($rows as $data) {
            if (count($data) < count($expected)) continue;
            
            $enroll = trim((string)$data[$headerMap['stu_enroll']]);
            $fname = trim((string)$data[$headerMap['stu_fname']]);
            $lname = trim((string)$data[$headerMap['stu_lname']]);
            $email = trim((string)$data[$headerMap['stu_email']]);
            $pass = trim((string)$data[$headerMap['stu_pass']]);
            $prog = trim((string)$data[$headerMap['stu_program']]);
            $campus = trim((string)$data[$headerMap['stu_campus']]);
            $college = trim((string)$data[$headerMap['stu_college']]);

            if (empty($enroll) || empty($fname)) {
                $errorCount++;
                continue;
            }

            // Check if enroll or email already exists
            $check = $conn->prepare("SELECT stu_id FROM student_master WHERE stu_enroll = ? OR (stu_email = ? AND stu_email != '')");
            $check->bind_param("ss", $enroll, $email);
            $check->execute();
            $res = $check->get_result();
            
            if ($res->num_rows > 0) {
                $errorCount++;
                continue; // Skip existing
            }

            $stmt->bind_param("ssssssss", $enroll, $fname, $lname, $email, $pass, $prog, $campus, $college);
            
            if ($stmt->execute()) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $msg = "Import completed: $successCount students imported successfully.";
        if ($errorCount > 0) {
            $msg .= " ($errorCount rows skipped due to duplicates or missing data).";
        }
        
        header("Location: manage_students.php?msg=" . urlencode($msg) . "&type=success");
        exit();

    } else {
        header("Location: manage_students.php?msg=" . urlencode("Error uploading file.") . "&type=danger");
        exit();
    }
}
header("Location: manage_students.php");
?>
