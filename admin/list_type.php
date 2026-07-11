<?php
include "../db.php";

$ss_type = $_GET['ss_type'];

/*INNER JOIN*/

$sql = "SELECT scholarship.ss_id, student_master.stu_id, student_master.stu_fname, scholarship.app_status 
FROM scholarship
INNER JOIN student_master ON scholarship.stu_id=student_master.stu_id 
Where ss_id = '".$ss_id."'";
$result = $conn->query($sql);
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

        <?php include 'sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4 content-area">

            <h2 class="fw-bold mb-4">Student List for <?php echo $ss_name; ?></h2>

            
            <!------------- DISPLAY STUDENTS -------------------->
            <form method="GET" class="row mb-3">
    <!-- Search -->
<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">Search</button>
        </div>
    </div>
</form>

<!-- Table -->
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>SS_ID</th>
            <th>STU_ID</th>
            <th>Student Name</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo $row['ss_id']; ?></td>
            <td><?php echo $row['stu_id']; ?></td>
            <td><?php echo $row['stu_fname']; ?></td>
            <td>
                <?php
                if ($row['app_status'] == 'Applied')
                { ?>
                    <a href="approve_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo $ss_name; ?>" class="btn btn-warning btn-sm">Approve</a>     
                    
                    <a href="reject_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo $ss_name; ?>" class="btn btn-warning btn-sm">Reject</a>
                
                <?php
                }
                else if($row['app_status'] == 'Approved')
                { ?>
                    <a href="" class="btn btn-success btn-sm" disabled>Approved</a>     
                    
                    <a href="reject_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo $ss_name; ?>" class="btn btn-warning btn-sm">Reject</a>     
                    
                <?php
                }
                else
                { ?>
                    <a href="approve_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>&stu_id=<?php echo $row['stu_id']; ?>&ss_name=<?php echo $ss_name; ?>" class="btn btn-warning btn-sm">Approve</a>     
                    
                    <a href="" class="btn btn-danger btn-sm" disabled >Rejected</a>
                    
                <?php
                }
                ?>
              
               
            </td>
        </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
        }
        ?>
    </tbody>
</table>
        
        <!---------- DISPLAY ENDS --------------->

        </div>
    </div>
</div>

</body>
</html>
