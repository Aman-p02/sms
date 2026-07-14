<?php
require_once 'session.php';
include "../db.php";

// Search
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Sorting
$order_by = "ss_end";
$order = "DESC";

if (isset($_GET['sort'])) {
    $order_by = $_GET['sort'];
}
if (isset($_GET['order'])) {
    $order = $_GET['order'];
}

// Filters
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';
$filter_name = isset($_GET['name']) ? $_GET['name'] : '';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';

// Query
$sql = "SELECT * FROM `ss_master` WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (`ss_name` LIKE '%$search%' 
              OR `ss_type` LIKE '%$search%' 
              OR `ss_year` LIKE '%$search%'
              OR `ss_amount` LIKE '%$search%')";
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

$sql .= " ORDER BY $order_by $order";
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

           
            
            
            <!------------- DISPLAY SCHOLARSHIPS -------------------->
            <form method="GET" class="mb-4 card p-3 shadow-sm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Scholarship Year</label>
                        <select name="year" class="form-select">
                            <option value="">All Years</option>
                            <?php 
                                $years = $conn->query("SELECT DISTINCT ss_year FROM ss_master WHERE ss_year IS NOT NULL AND ss_year != '' ORDER BY ss_year DESC");
                                while ($row = $years->fetch_assoc()) {
                                    $sel = ($filter_year == $row['ss_year']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($row['ss_year'])."' $sel>".htmlspecialchars($row['ss_year'])."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Scholarship Name</label>
                        <select name="name" class="form-select">
                            <option value="">All Names</option>
                            <?php 
                                $names = $conn->query("SELECT DISTINCT ss_name FROM ss_master WHERE ss_name IS NOT NULL AND ss_name != '' ORDER BY ss_name ASC");
                                while ($row = $names->fetch_assoc()) {
                                    $sel = ($filter_name == $row['ss_name']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($row['ss_name'])."' $sel>".htmlspecialchars($row['ss_name'])."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Scholarship Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <?php 
                                $types = $conn->query("SELECT DISTINCT ss_type FROM ss_master WHERE ss_type IS NOT NULL AND ss_type != '' ORDER BY ss_type ASC");
                                while ($row = $types->fetch_assoc()) {
                                    $sel = ($filter_type == $row['ss_type']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($row['ss_type'])."' $sel>".htmlspecialchars($row['ss_type'])."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-12 d-flex gap-2 justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">Filter</button>
                        <a href="view_scholarships.php" class="btn btn-secondary px-4">Reset</a>
                        <a href="export.php?type=scholarships&search=<?php echo urlencode($search); ?>&year=<?php echo urlencode($filter_year); ?>&name=<?php echo urlencode($filter_name); ?>&type_filter=<?php echo urlencode($filter_type); ?>&sort=<?php echo urlencode($order_by); ?>&order=<?php echo urlencode($order); ?>" class="btn btn-success px-4" title="Export to Excel">Export</a>
                    </div>
                </div>
            </form>

<!-- Table -->
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <?php $next_order = ($order == 'ASC') ? 'DESC' : 'ASC'; ?>
            <th><a href="?sort=ss_year&order=<?php echo ($order_by == 'ss_year') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Year</a></th>
            <th><a href="?sort=ss_name&order=<?php echo ($order_by == 'ss_name') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Name</a></th>
            <th><a href="?sort=ss_type&order=<?php echo ($order_by == 'ss_type') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Type</a></th>
            <th><a href="?sort=ss_start&order=<?php echo ($order_by == 'ss_start') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Start Date</a></th>
            <th><a href="?sort=ss_end&order=<?php echo ($order_by == 'ss_end') ? $next_order : 'DESC'; ?>" class="text-white text-decoration-none d-block">End Date</a></th>
            <th><a href="?sort=ss_amount&order=<?php echo ($order_by == 'ss_amount') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Amount</a></th>
            <th>Document</th>
            
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
            <td><a href="list_type.php?ss_type=<?php echo urlencode($row['ss_type']); ?>"><?php echo $row['ss_type']; ?></a></td>
            <td><?php echo $row['ss_start']; ?></td>
            <td><?php echo $row['ss_end']; ?></td>
            <td><?php echo $row['ss_amount']; ?></td>
            <td>
                <?php if (!empty($row['ss_document'])): ?>
                    <a href="../uploads/scholarships/<?php echo urlencode($row['ss_document']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                <?php else: ?>
                    <span class="text-muted small">-</span>
                <?php endif; ?>
            </td>
            
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
