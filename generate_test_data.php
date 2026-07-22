<?php
// generate_test_data.php
include "db.php";

echo "<h1>Generating FULL Test Data for Machine Learning...</h1>";

// 1. Clean up old test data first
$conn->query("DELETE FROM scholarship WHERE stu_id IN (SELECT stu_id FROM student_master WHERE stu_enroll LIKE 'ML%')");
$conn->query("DELETE FROM student_master WHERE stu_enroll LIKE 'ML%'");

echo "<p>✓ Removed old test data.</p>";

// Ensure we have a default scholarship to attach to
$ss_res = $conn->query("SELECT ss_id FROM ss_master LIMIT 1");
if ($ss_res->num_rows == 0) {
    // Create a dummy scholarship if none exists
    $conn->query("INSERT INTO ss_master (ss_name, ss_status) VALUES ('Test Scholarship for ML', 'Active')");
    $ss_id = $conn->insert_id;
} else {
    $ss_id = $ss_res->fetch_assoc()['ss_id'];
}

// Arrays for random generation
$genders = ['Male', 'Female'];
$grades = ['A', 'B', 'C', 'D', 'E', 'F'];
$yesNo = ['Yes', 'No'];
$maritals = ['Single', 'Married', 'Divorced', 'Widowed'];
$programs = ['BS Computer Science', 'BS Information Tech', 'BA Economics', 'BS Business Admin'];
$campuses = ['Main Campus', 'North Campus', 'Bislig Campus', 'Cantilan Campus'];
$colleges = ['CITE', 'CBM', 'COE', 'CAS'];
$years = ['1', '2', '3', '4'];
$sems = ['1', '2', '3'];

$inserted_students = [];

for ($i = 1; $i <= 1000; $i++) {
    // Generate Random Data for EVERY field
    $enroll = 'ML' . rand(10000, 99999) . $i;
    $fname = 'FirstName' . $i;
    $lname = 'LastName' . $i;
    $ext = 'Jr';
    $mname = 'MidName' . $i;
    $gender = $genders[array_rand($genders)];
    $pass = 'pass123';
    
    // Generate random date between 2000 and 2005
    $dob = rand(2000, 2005) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    
    $email = "student{$i}@dummy.com";
    $contact = "9" . rand(100000000, 999999999);
    $program = $programs[array_rand($programs)];
    $year_level = $years[array_rand($years)];
    $campus = $campuses[array_rand($campuses)];
    $cor = "uploads/student/dummy_cor.jpg";
    $units = (string)rand(15, 24);
    
    // ML Specific
    $grade = $grades[array_rand($grades)];
    $gpa = round(mt_rand(10, 40) / 10, 1); 
    
    $adm_year = '2024-25';
    $college = $colleges[array_rand($colleges)];
    $sem = $sems[array_rand($sems)];
    
    $father_lname = 'FatherL';
    $father_gname = 'FatherG';
    $father_mname = 'FatherM';
    $mother_lname = 'MotherL';
    $mother_gname = 'MotherG';
    $mother_mname = 'MotherM';
    
    $dswd = 'DSWD' . rand(100, 999);
    $house = rand(1, 100) . ' Street Name';
    $bci = 'BCI' . rand(100, 999);
    $amount = (string)rand(1000, 5000);
    
    // ML Specific
    $disabled = $yesNo[array_rand($yesNo)];
    $disability = ($disabled == 'Yes') ? 'Physical Disability' : 'None';
    $marital = $maritals[array_rand($maritals)];
    $dependent = $yesNo[array_rand($yesNo)];
    
    $inmate = $yesNo[array_rand($yesNo)];
    $rebel = $yesNo[array_rand($yesNo)];
    $street = 'Dummy Street ' . $i;
    $barangay = 'Dummy Barangay';
    $city = 'Dummy City';
    $province = 'Dummy Province';
    $zip = (string)rand(1000, 9999);
    $perc = (float)rand(50, 100);
    $profilepic = "dummy_profile.jpg";
    $status = 'active';
    $complete = 'Yes';
    
    $query = "INSERT INTO student_master (
        stu_enroll, stu_fname, stu_lname, stu_ext, stu_mname, stu_gender, stu_pass, stu_dob, stu_email, stu_contact, 
        stu_program, stu_year_level, stu_campus, stu_cor, stu_units, stu_grade, stu_gpa, stu_adm_year, stu_college, stu_sem, 
        father_lname, father_gname, father_mname, mother_lname, mother_gname, mother_mname, stu_dswd, stu_house, stu_bci, stu_amount, 
        stu_disabled, stu_disability, stu_marital, stu_dependent, stu_inmate, stu_rebel, stu_street, stu_barangay, stu_city, stu_province, 
        stu_zip, stu_perc, stu_profilepic, stu_status, complete
    ) VALUES (
        '$enroll', '$fname', '$lname', '$ext', '$mname', '$gender', '$pass', '$dob', '$email', '$contact', 
        '$program', '$year_level', '$campus', '$cor', '$units', '$grade', '$gpa', '$adm_year', '$college', '$sem', 
        '$father_lname', '$father_gname', '$father_mname', '$mother_lname', '$mother_gname', '$mother_mname', '$dswd', '$house', '$bci', '$amount', 
        '$disabled', '$disability', '$marital', '$dependent', '$inmate', '$rebel', '$street', '$barangay', '$city', '$province', 
        '$zip', $perc, '$profilepic', '$status', '$complete'
    )";
              
    if ($conn->query($query)) {
        $inserted_students[] = $conn->insert_id;
    } else {
        echo "<p style='color:red;'>Error inserting student: " . $conn->error . "</p>";
    }
}

echo "<p>✓ Successfully inserted " . count($inserted_students) . " FULLY POPULATED students (No NULL fields).</p>";

// Now randomly approve 700 and reject 300
shuffle($inserted_students);

$approved_count = 700;
$approved = array_slice($inserted_students, 0, $approved_count);
$rejected = array_slice($inserted_students, $approved_count);

foreach ($approved as $stu_id) {
    $conn->query("INSERT INTO scholarship (stu_id, ss_id, app_status) VALUES ('$stu_id', '$ss_id', 'Approved')");
}

foreach ($rejected as $stu_id) {
    $conn->query("INSERT INTO scholarship (stu_id, ss_id, app_status) VALUES ('$stu_id', '$ss_id', 'Rejected')");
}

echo "<p>✓ Automation Complete:</p>";
echo "<ul>";
echo "<li><b>700</b> random students have been <b>Approved</b> for a scholarship (Label = 1).</li>";
echo "<li><b>300</b> random students have been <b>Rejected</b> (Label = 0).</li>";
echo "</ul>";

echo "<p><b>Data is ready!</b> You can now go to the Admin Panel -> Prediction tool and click <b>'Retrain Models'</b> to train the machine learning algorithm on this new dataset!</p>";
?>
