<?php

include "db.php";
include 'session.php';


// Get Search criteria
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Check if student is blocked
$stu_status = 'active';
if (isset($_SESSION['stu_id'])) {
    $status_res = $conn->query("SELECT stu_status FROM student_master WHERE stu_id = '".$_SESSION['stu_id']."'");
    if ($status_row = $status_res->fetch_assoc()) {
        $stu_status = $status_row['stu_status'];
    }
}
?>
<!------------------- BODY STARTS ---------------------->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'stu_sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4 content-area">

            <h2 class="fw-bold mb-4">Manage Scholarships</h2>

           
            
            
            <!------------- DISPLAY STUDENTS -------------------->
            <!----><form method="GET" class="row mb-3">
                <!-- Display Search Box and Search Button -->
                <form method="GET" class="mb-3">
                    <div class="row">
                        
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo $search; ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <button class="btn btn-primary">Search</button>                            
                        </div>
                    </div>
                </form>
                
                
                

                <!-- Render Table -->
                <table class="table table-bordered table-striped" id="studentTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Year</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        /*$sql = "SELECT * FROM `ss_master` 
                        WHERE `ss_name` LIKE '%$search%' 
                        OR `ss_type` LIKE '%$search%' 
                        OR `ss_year` LIKE '%$search%'
                        OR `ss_amount` LIKE '%$search%'
                        ORDER BY $order_by $order";                        */
                        
                        $sql = "SELECT * FROM `ss_master` 
                        WHERE `ss_name` LIKE '%$search%' 
                        OR `ss_type` LIKE '%$search%' 
                        OR `ss_year` LIKE '%$search%'
                        OR `ss_amount` LIKE '%$search%'
                        ORDER by ss_end DESC";                        
                        
                        $result = $conn->query($sql);
                        
                       
                        
                        if ($result->num_rows > 0) 
                        {
                            while ($row = $result->fetch_assoc())
                            { 
                            ?>
                                <tr>
                                    <td><?php echo $row['ss_year']; ?></td>            
                                    <td><?php echo $row['ss_name']; ?></td>
                                    <td><?php echo $row['ss_type']; ?></td>
                                    <td><?php echo $row['ss_start']; ?></td>
                                    <td><?php echo $row['ss_end']; ?></td>
                                    <td><?php echo $row['ss_amount']; ?></td>

                                    <td>
                                        <?php 
                                            $end = $row['ss_end'];
                                            $today = date('Y-m-d');

                                            // Check if student applied
                                            $sql2 = "select app_status from scholarship where stu_id = '".$stu_id."' and ss_id = '".$row['ss_id']."'";
                                            $result2 = $conn->query($sql2);                   
                                            $row2 = $result2->fetch_assoc();

                                            if ($row2) {
                                                // Student has applied, show their status regardless of deadline
                                                if($row2['app_status'] == 'Applied') {
                                                ?>
                                                    <a href="#" class="btn btn-info btn-sm" disabled>Applied</a>
                                                <?php
                                                } else if($row2['app_status'] == 'Approved') {
                                                ?>
                                                    <a href="#" class="btn btn-success btn-sm" disabled>Approved</a>
                                                <?php
                                                } else {
                                                ?>
                                                    <a href="#" class="btn btn-danger btn-sm" disabled>Rejected</a>
                                                <?php
                                                }
                                            } else {
                                                // Student has not applied
                                                if ($end < $today || $stu_status == 'blocked') {
                                                ?>
                                                    <a href="#" class="btn btn-secondary btn-sm" disabled>Passed</a>
                                                <?php
                                                } else {
                                                ?>
                                                    <a href="apply.php?ss_id=<?php echo $row['ss_id'];?>&stu_id=<?php echo $stu_id;?>" 
                                                       class="btn btn-warning btn-sm">Apply</a>
                                                <?php
                                                }
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                               
                            } /*end while*/
                        } /*end if*/
                        else {
                            echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                
        
        <!---------- DISPLAY ENDS --------------->

        </div> <!--End cotent-area-->
        
    </div> <!--End row-->
</div> <!--End container-fluid-->

</body>
</html>
