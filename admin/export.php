<?php
include "../db.php";

// Which type of export? "students" or "scholarships"
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Search term (used by both types)
$search = isset($_GET['search']) ? $_GET['search'] : "";

if ($type === 'students') {
    $filter_campus = isset($_GET['campus']) ? $_GET['campus'] : '';
    $filter_college = isset($_GET['college']) ? $_GET['college'] : '';
    $filter_year = isset($_GET['year']) ? $_GET['year'] : '';
    $filter_course = isset($_GET['course']) ? $_GET['course'] : '';

    $sql = "SELECT * FROM `student_master` WHERE 1=1";
    
    if (!empty($search)) {
        $safe_search = $conn->real_escape_string($search);
        $sql .= " AND (`stu_fname` LIKE '%$safe_search%' 
                  OR `stu_lname` LIKE '%$safe_search%'
                  OR `stu_email` LIKE '%$safe_search%' 
                  OR `stu_enroll` LIKE '%$safe_search%')";
    }
    if (!empty($filter_campus)) {
        $sql .= " AND stu_campus = '" . $conn->real_escape_string($filter_campus) . "'";
    }
    if (!empty($filter_college)) {
        $sql .= " AND stu_college = '" . $conn->real_escape_string($filter_college) . "'";
    }
    if (!empty($filter_year)) {
        $sql .= " AND stu_year_level = '" . $conn->real_escape_string($filter_year) . "'";
    }
    if (!empty($filter_course)) {
        $sql .= " AND stu_program = '" . $conn->real_escape_string($filter_course) . "'";
    }
    
    $sql .= " ORDER BY stu_id ASC";
    $result = $conn->query($sql);
    $filename = "students_export.xls";

} elseif ($type === 'scholarships') {

    $filter_year = isset($_GET['year']) ? $_GET['year'] : '';
    $filter_name = isset($_GET['name']) ? $_GET['name'] : '';
    $filter_type = isset($_GET['type_filter']) ? $_GET['type_filter'] : '';

    $sql = "SELECT * FROM `ss_master` WHERE 1=1";
    
    if (!empty($search)) {
        $safe_search = $conn->real_escape_string($search);
        $sql .= " AND (`ss_name` LIKE '%$safe_search%' 
                  OR `ss_type` LIKE '%$safe_search%' 
                  OR `ss_year` LIKE '%$safe_search%'
                  OR `ss_amount` LIKE '%$safe_search%')";
    }
    if (!empty($filter_year)) {
        $sql .= " AND ss_year = '" . $conn->real_escape_string($filter_year) . "'";
    }
    if (!empty($filter_name)) {
        $sql .= " AND ss_name = '" . $conn->real_escape_string($filter_name) . "'";
    }
    if (!empty($filter_type)) {
        $sql .= " AND ss_type = '" . $conn->real_escape_string($filter_type) . "'";
    }
    
    $sql .= " ORDER BY ss_id ASC";
    $result = $conn->query($sql);
    $filename = "scholarships_export.xls";

} elseif ($type === 'list_names') {

    $ss_id = isset($_GET['ss_id']) ? $_GET['ss_id'] : '';
    $gender_filter = isset($_GET['gender']) ? $_GET['gender'] : '';
    $enroll_filter = isset($_GET['enroll']) ? $_GET['enroll'] : '';
    $course_filter = isset($_GET['course']) ? $_GET['course'] : '';
    $campus_filter = isset($_GET['campus']) ? $_GET['campus'] : '';
    $college_filter = isset($_GET['college']) ? $_GET['college'] : '';
    $year_filter = isset($_GET['year']) ? $_GET['year'] : '';

    if (empty($ss_id)) {
        die("Invalid scholarship ID.");
    }

    $sql = "SELECT scholarship.ss_id, student_master.stu_id, student_master.stu_enroll, student_master.stu_fname, student_master.stu_lname, student_master.stu_gender, ss_master.ss_year, student_master.stu_campus, student_master.stu_college, student_master.stu_program, scholarship.app_status 
            FROM scholarship
            INNER JOIN student_master ON scholarship.stu_id=student_master.stu_id 
            INNER JOIN ss_master ON scholarship.ss_id=ss_master.ss_id
            Where scholarship.ss_id = '".$conn->real_escape_string($ss_id)."'";

    if (!empty($enroll_filter)) {
        $sql .= " AND student_master.stu_enroll LIKE '%" . $conn->real_escape_string($enroll_filter) . "%'";
    }
    if (!empty($course_filter)) {
        $sql .= " AND student_master.stu_program = '" . $conn->real_escape_string($course_filter) . "'";
    }
    if (!empty($gender_filter)) {
        $safe_gender = $conn->real_escape_string($gender_filter);
        $sql .= " AND student_master.stu_gender = '$safe_gender'";
    }
    if (!empty($campus_filter)) {
        $sql .= " AND student_master.stu_campus = '" . $conn->real_escape_string($campus_filter) . "'";
    }
    if (!empty($college_filter)) {
        $sql .= " AND student_master.stu_college = '" . $conn->real_escape_string($college_filter) . "'";
    }
    if (!empty($year_filter)) {
        $sql .= " AND student_master.stu_year_level = '" . $conn->real_escape_string($year_filter) . "'";
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

} elseif ($type === 'list_type') {

    $ss_type = isset($_GET['ss_type']) ? $_GET['ss_type'] : '';
    $gender_filter = isset($_GET['gender']) ? $_GET['gender'] : '';
    $enroll_filter = isset($_GET['enroll']) ? $_GET['enroll'] : '';
    $course_filter = isset($_GET['course']) ? $_GET['course'] : '';
    $campus_filter = isset($_GET['campus']) ? $_GET['campus'] : '';
    $college_filter = isset($_GET['college']) ? $_GET['college'] : '';
    $year_filter = isset($_GET['year']) ? $_GET['year'] : '';

    if (empty($ss_type)) {
        die("Invalid scholarship type.");
    }

    $sql = "SELECT scholarship.ss_id, student_master.stu_id, student_master.stu_enroll, student_master.stu_fname, student_master.stu_lname, student_master.stu_gender, ss_master.ss_year, ss_master.ss_name, student_master.stu_campus, student_master.stu_college, student_master.stu_program, scholarship.app_status 
            FROM scholarship
            INNER JOIN student_master ON scholarship.stu_id=student_master.stu_id 
            INNER JOIN ss_master ON scholarship.ss_id=ss_master.ss_id
            Where ss_master.ss_type = '".$conn->real_escape_string($ss_type)."'";

    if (!empty($enroll_filter)) {
        $sql .= " AND student_master.stu_enroll LIKE '%" . $conn->real_escape_string($enroll_filter) . "%'";
    }
    if (!empty($course_filter)) {
        $sql .= " AND student_master.stu_program = '" . $conn->real_escape_string($course_filter) . "'";
    }
    if (!empty($gender_filter)) {
        $safe_gender = $conn->real_escape_string($gender_filter);
        $sql .= " AND student_master.stu_gender = '$safe_gender'";
    }
    if (!empty($campus_filter)) {
        $sql .= " AND student_master.stu_campus = '" . $conn->real_escape_string($campus_filter) . "'";
    }
    if (!empty($college_filter)) {
        $sql .= " AND student_master.stu_college = '" . $conn->real_escape_string($college_filter) . "'";
    }
    if (!empty($year_filter)) {
        $sql .= " AND student_master.stu_year_level = '" . $conn->real_escape_string($year_filter) . "'";
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
    $filename = "student_list_type_export.xls";

} elseif ($type === 'year_wise_summary') {

    $filter_year = isset($_GET['year']) ? $_GET['year'] : '';
    $filter_campus = isset($_GET['campus']) ? $_GET['campus'] : '';
    $filter_college = isset($_GET['college']) ? $_GET['college'] : '';
    $filter_course = isset($_GET['course']) ? $_GET['course'] : '';
    $filter_scholarship = isset($_GET['scholarship']) ? $_GET['scholarship'] : '';
    $filter_amount = isset($_GET['amount']) ? $_GET['amount'] : '';

    $sql = "SELECT s.stu_fname, s.stu_lname, s.stu_campus, s.stu_college, s.stu_program, 
                   sm.ss_name, sm.ss_year, sm.ss_amount, sc.app_status 
            FROM scholarship sc
            INNER JOIN student_master s ON sc.stu_id = s.stu_id
            INNER JOIN ss_master sm ON sc.ss_id = sm.ss_id
            WHERE 1=1";

    if (!empty($filter_year)) {
        $sql .= " AND sm.ss_year = '" . $conn->real_escape_string($filter_year) . "'";
    }
    if (!empty($filter_campus)) {
        $sql .= " AND s.stu_campus = '" . $conn->real_escape_string($filter_campus) . "'";
    }
    if (!empty($filter_college)) {
        $sql .= " AND s.stu_college = '" . $conn->real_escape_string($filter_college) . "'";
    }
    if (!empty($filter_course)) {
        $sql .= " AND s.stu_program = '" . $conn->real_escape_string($filter_course) . "'";
    }
    if (!empty($filter_scholarship)) {
        $sql .= " AND sm.ss_name = '" . $conn->real_escape_string($filter_scholarship) . "'";
    }
    if (!empty($filter_amount)) {
        $sql .= " AND sm.ss_amount = '" . $conn->real_escape_string($filter_amount) . "'";
    }

    $sql .= " ORDER BY sm.ss_year DESC, s.stu_fname ASC";
    $result = $conn->query($sql);
    $filename = "year_wise_summary_export.xls";

} elseif ($type === 'approvals') {

    $filter_type = isset($_GET['type_filter']) ? $_GET['type_filter'] : '';
    $filter_name = isset($_GET['name']) ? $_GET['name'] : '';
    $filter_status = isset($_GET['status']) ? $_GET['status'] : '';
    $filter_year = isset($_GET['year']) ? $_GET['year'] : '';

    $sql = "SELECT 
                s.app_id, s.app_status,
                stu.stu_fname, stu.stu_lname,
                ss.ss_name, ss.ss_type, ss.ss_amount, ss.ss_year
            FROM scholarship s
            JOIN student_master stu ON s.stu_id = stu.stu_id
            JOIN ss_master ss ON s.ss_id = ss.ss_id
            WHERE 1=1";

    if (!empty($filter_type)) {
        $sql .= " AND ss.ss_type = '" . $conn->real_escape_string($filter_type) . "'";
    }
    if (!empty($filter_name)) {
        $sql .= " AND ss.ss_name = '" . $conn->real_escape_string($filter_name) . "'";
    }
    if (!empty($filter_status)) {
        $sql .= " AND s.app_status = '" . $conn->real_escape_string($filter_status) . "'";
    }
    if (!empty($filter_year)) {
        $sql .= " AND ss.ss_year = '" . $conn->real_escape_string($filter_year) . "'";
    }

    $sql .= " ORDER BY s.app_id DESC";
    $result = $conn->query($sql);
    $filename = "applications_export.xls";

} elseif ($type === 'applied_students') {

    $ss_id_filter = isset($_GET['ss_id']) ? $_GET['ss_id'] : '';

    $sql = "SELECT s.app_status, stu.stu_fname, stu.stu_lname, stu.stu_email, stu.stu_program, ss.ss_name 
            FROM scholarship s
            JOIN student_master stu ON s.stu_id = stu.stu_id
            JOIN ss_master ss ON s.ss_id = ss.ss_id";

    if (!empty($ss_id_filter)) {
        $sql .= " WHERE s.ss_id = '" . $conn->real_escape_string($ss_id_filter) . "'";
    }

    $sql .= " ORDER BY stu.stu_fname ASC";
    $result = $conn->query($sql);
    $filename = "applied_students_export.xls";

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
            <th>Campus</th>
            <th>College</th>
            <th>Course</th>
            <th>Year Level</th>
            <th>Email</th>
            <th>Gender</th>
            <th>GPA</th>
            <th>City</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['stu_id']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_enroll']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_fname']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_lname']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_campus']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_college']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_program']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_year_level']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_email']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_gender']); ?></td>
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
            <th>Enrollment No</th>
            <th>Student Name</th>
            <th>Gender</th>
            <th>Year</th>
            <th>Campus</th>
            <th>College</th>
            <th>Course</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['stu_enroll'] ?? ''); ?></td>
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
    <?php } elseif ($type === 'list_type') { ?>
        <tr>
            <th>Enrollment No</th>
            <th>Student Name</th>
            <th>Gender</th>
            <th>Year</th>
            <th>Campus</th>
            <th>College</th>
            <th>Course</th>
            <th>Scholarship Name</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['stu_enroll'] ?? ''); ?></td>
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
            <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
            <td><?php echo htmlspecialchars($row['app_status']); ?></td>
        </tr>
        <?php } ?>
    <?php } elseif ($type === 'year_wise_summary') { ?>
        <tr>
            <th>Student Name</th>
            <th>Campus</th>
            <th>College</th>
            <th>Course</th>
            <th>Scholarship</th>
            <th>Year</th>
            <th>Amount</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_campus']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_college']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_program']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_year']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_amount']); ?></td>
            <td><?php echo htmlspecialchars($row['app_status']); ?></td>
        </tr>
        <?php } ?>
    <?php } elseif ($type === 'approvals') { ?>
        <tr>
            <th>Student Name</th>
            <th>Type of Scholarship</th>
            <th>Amount of Scholarship</th>
            <th>Name of Scholarship</th>
            <th>Year</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { 
            $student_name = trim($row['stu_fname'] . ' ' . $row['stu_lname']);
        ?>
        <tr>
            <td><?php echo htmlspecialchars($student_name); ?></td>
            <td><?php echo htmlspecialchars($row['ss_type']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_amount']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_year']); ?></td>
            <td><?php echo htmlspecialchars($row['app_status']); ?></td>
        </tr>
        <?php } ?>
    <?php } elseif ($type === 'applied_students') { ?>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Scholarship Name</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['stu_fname'] . ' ' . $row['stu_lname']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_email']); ?></td>
            <td><?php echo htmlspecialchars($row['stu_program']); ?></td>
            <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
            <td><?php echo htmlspecialchars($row['app_status']); ?></td>
        </tr>
        <?php } ?>
    <?php } ?>
</table>
