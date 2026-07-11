<?php
require_once 'session.php';
include "../db.php";

// Search
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Sorting
$order_by = "ss_id";
$order = "ASC";

if (isset($_GET['sort'])) {
    $order_by = $_GET['sort'];
}
if (isset($_GET['order'])) {
    $order = $_GET['order'];
}

// Query
$sql = "SELECT * FROM `ss_master` 
        WHERE `ss_name` LIKE '%$search%' 
        OR `ss_type` LIKE '%$search%' 
        OR `ss_year` LIKE '%$search%'
        OR `ss_amount` LIKE '%$search%'
        ORDER BY $order_by $order";
/*echo $sql;*/
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

            <h2 class="fw-bold mb-4">Manage Scholarships</h2>

           
            
            
            <!------------- DISPLAY STUDENTS -------------------->
            <form method="GET" class="row mb-3">
    <!-- Search -->
<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo $search; ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">Search</button>
        </div>
        <div class="col-md-2">
            <a href="export.php?type=scholarships&search=<?php echo urlencode($search); ?>" class="btn btn-success">Export to Excel</a>
        </div>
    </div>
</form>

<!-- Table -->
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th><a href="?sort=ss_yearorder=ASC">Year</a></th>
            <th><a href="?sort=ss_name&order=ASC">Name</a></th>
            <th><a href="?sort=ss_type&order=ASC">Type</a></th>
            <th><a href="?sort=ss_start&order=ASC">Start Date</a></th>
            <th><a href="?sort=ss_end&order=ASC">End Date</a></th>
            <th><a href="?sort=ss_amount&order=ASC">Amount</a></th>
            
            
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo $row['ss_year']; ?></td>            
            <td><a href="list_names.php?ss_id=<?php echo $row['ss_id']; ?>&ss_name=<?php echo $row['ss_name']; ?>"><?php echo $row['ss_name']; ?></a></td>
            <td><a href="list_type.php?ss_id=<?php echo $row['ss_type']; ?>"><?php echo $row['ss_type']; ?></a></td>
            <td><?php echo $row['ss_start']; ?></td>
            <td><?php echo $row['ss_end']; ?></td>
            <td><?php echo $row['ss_amount']; ?></td>
            
            <td>
                <!-- Edit -->
                <a href="edit_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>" class="btn btn-warning btn-sm">Edit</a>

                <!-- Delete -->
                <a href="delete_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure?')">Delete</a>

                <!-- Block -->
                <?php if ($row['ss_status'] == 'active') { ?>
                    <a href="block_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>" 
                       class="btn btn-secondary btn-sm">Block</a>
                    <?php } else { ?>
                    <a href="block_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>" 
                       class="btn btn-success btn-sm">
                       Unblock
                    </a>
                <?php } ?>

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
