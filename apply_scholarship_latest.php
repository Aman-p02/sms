<?php

include "db.php";

require_once 'session.php';

// Prevent browser caching
header( "Cache-Control: no-cache, no-store, must-revalidate" );
header( "Pragma: no-cache" );
header( "Expires: 0" );


// Session check
if ( !isset( $_SESSION['stu_id'] ) ) {
    header( "Location: index.php" );
    exit();
}

$stu_id = $_SESSION['stu_id'];

// Search
$search = "";
if ( isset( $_GET['search'] ) ) {
    $search = $_GET['search'];
}

// Sorting
$order_by = "ss_id";
$order = "ASC";

if ( isset( $_GET['sort'] ) ) {
    $order_by = $_GET['sort'];
}
if ( isset( $_GET['order'] ) ) {
    $order = $_GET['order'];
}

// Query
$sql = "SELECT * FROM `ss_master` 
        WHERE `ss_name` LIKE '%$search%' 
        OR `ss_type` LIKE '%$search%' 
        OR `ss_year` LIKE '%$search%'
        OR `ss_amount` LIKE '%$search%'
        ORDER BY $order_by $order";
/*echo $sql;
*/
$result = $conn->query( $sql );

/* UPDATE BUTTON STATUS */
$sql1 = "Select app_status from scholarship where stu_id = '".$stu_id."'";
$result1 = $conn->query( $sql1 );

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

            <?php include 'stu_sidebar.php';
?>

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
                        </div>
                    </form>

                    <!-- Table -->
                    <table class="table table-bordered table-striped">
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
if ( $result->num_rows > 0 ) {
    while ( $row = $result->fetch_assoc() ) {
        ?>
                            <tr>
                                <td><?php echo $row['ss_year'];
        ?></td>
                                <td><?php echo $row['ss_name'];
        ?></td>
                                <td><?php echo $row['ss_type'];
        ?></td>
                                <td><?php echo $row['ss_start'];
        ?></td>
                                <td><?php echo $row['ss_end'];
        ?></td>
                                <td><?php echo $row['ss_amount'];
        ?></td>

                                <td>
                                    <!-- Edit -->
                                    <?php
        $end = $row['ss_end'];
        $today = date( 'Y-m-d' );

        $row1 = $result1->fetch_assoc();

        if ( $end <= $today )  {
            ?>
                                    <a href="" class="btn btn-secondary btn-sm" disabled>Passed</a>
                                    <?php
        } else if ( $end > $today ) {

            $sql2 = "select app_status from scholarship where stu_id = '".$stu_id."' and ss_id = '".$row['ss_id']."'";
            $result2 = $conn->query( $sql2 );

            $row2 = $result2->fetch_assoc();
            if ( $row2 ) {
                if ( $row2['app_status'] == 'Applied' ) {
                    ?>
                                    <a href="" class="btn btn-info btn-sm" disabled> Applied</a>
                                    <?php
                } else if ( $row2['app_status'] == 'Approved' ) {
                    ?>
                                    <a href="" class="btn btn-success btn-sm" disabled> Approved</a>
                                    <?php
                } else {
                    ?>
                                    <a href="" class="btn btn-danger btn-sm" disabled> Rejected</a>
                                    <?php
                }

            } else {
                ?>
                                    <a href="apply.php?ss_id=<?php echo $row['ss_id'];?>&stu_id=<?php echo $stu_id;?>" class="btn btn-warning btn-sm">Apply</a>
                                    <?php
            }
        }

        ?>

                                    <!--  <a href = "edit_scholarship.php?ss_id=<?php echo $row['ss_id']; ?>" class = "btn btn-warning btn-sm">Active</a>-->

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
