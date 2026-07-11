<?php
include "../db.php";

// Which type of export? "students" or "scholarships"
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Search term (used by both types)
$search = isset($_GET['search']) ? $_GET['search'] : "";

if ($type === 'students') {

    $sql = "SELECT * FROM `student_master` 
            WHERE `stu_fname` LIKE '%$search%' 
            OR `stu_email` LIKE '%$search%' 
            OR `stu_enroll` LIKE '%$search%'
            ORDER BY stu_id ASC";
    $result = $conn->query($sql);
    $filename = "students_export.xls";

} elseif ($type === 'scholarships') {

    $sql = "SELECT * FROM `ss_master` 
            WHERE `ss_name` LIKE '%$search%' 
            OR `ss_type` LIKE '%$search%' 
            OR `ss_year` LIKE '%$search%'
            OR `ss_amount` LIKE '%$search%'
            ORDER BY ss_id ASC";
    $result = $conn->query($sql);
    $filename = "scholarships_export.xls";

} elseif ($type === 'list_names') {

    $ss_id = isset($_GET['ss_id']) ? $_GET['ss_id'] : '';
    $gender_filter = isset($_GET['gender']) ? $_GET['gender'] : '';
    if (empty($ss_id)) {
        die("Invalid scholarship ID.");
    }

    $sql = "SELECT scholarship.ss_id, student_master.stu_id, student_master.stu_fname, student_master.stu_lname, student_master.stu_gender, ss_master.ss_year, student_master.stu_campus, student_master.stu_college, student_master.stu_program, scholarship.app_status 
            FROM scholarship
            INNER JOIN student_master ON scholarship.stu_id=student_master.stu_id 
            INNER JOIN ss_master ON scholarship.ss_id=ss_master.ss_id
            Where scholarship.ss_id = '".$conn->real_escape_string($ss_id)."'";

    if (!empty($gender_filter)) {
        $safe_gender = $conn->real_escape_string($gender_filter);
        $sql .= " AND student_master.stu_gender = '$safe_gender'";
    }

    if (!empty($search)) {
        $safe_search = $conn->real_escape_string($search);
        $sql .= " AND (student_master.stu_fname LIKE '%$safe_search%' 
                    OR student_master.stu_lname LIKE '%$safe_search%'
                    OR ss_master.ss_year LIKE '%$safe_search%'
                    OR student_master.stu_campus LIKE '%$safe_search%'
                    OR student_master.stu_college LIKE '%$safe_search%'
                    OR student_master.stu_program LIKE '%$safe_search%')";
    }
    $result = $conn->query($sql);
    $filename = "student_list_export.xls";

} else {
    // No valid type given
    die("Invalid export type.");
}

// Force download as Excel file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename);
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border="1">
    <?php if ($type === 'students') { ?>
        <tr>
            <th>ID</th>
            <th>Enrollment No</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Year Level</th>
            <th>GPA</th>
            <th>City</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['stu_id']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_enroll']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_fname']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_lname']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_email']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_gender']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_year_level']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_gpa']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_city']); ?></td>
        </tr>
        <?php } ?>

    <?php } elseif ($type === 'scholarships') { ?>
        <tr>
            <th>Year</th>
            <th>Name</th>
            <th>Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Amount</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['ss_year']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_type']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_start']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_end']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_amount']); ?></td>
        </tr>
        <?php } ?>
    <?php } elseif ($type === 'list_names') { ?>
        <tr>
            <th>Student Name</th>
            <th>Gender</th>
            <th>Year</th>
            <th>Campus</th>
            <th>College</th>
            <th>Branch</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
            <td><?php 
                if ($row['stu_gender'] == 'M') echo 'Male';
                elseif ($row['stu_gender'] == 'F') echo 'Female';
                elseif ($row['stu_gender'] == 'O') echo 'Other';
                else echo htmlspecialchars($row['stu_gender']); 
            ?></td>
            <td><?php echo htmlspecialchars($row['ss_year']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_campus']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_college']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_program']); ?></td>
            <td><?php echo htmlspecialchars($row['app_status']); ?></td>
        </tr>
        <?php } ?>
    <?php } ?>
</table>
