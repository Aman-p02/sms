<?php
require_once 'session.php';
include "../db.php";

// Handle Approve / Reject
if (isset($_GET['action']) && isset($_GET['app_id'])) {
    $action = $_GET['action'];
    $app_id = intval($_GET['app_id']);
    
    if ($action == 'approve') {
        $conn->query("UPDATE scholarship SET app_status = 'Approved' WHERE app_id = $app_id");
    } elseif ($action == 'reject') {
        $conn->query("UPDATE scholarship SET app_status = 'Rejected' WHERE app_id = $app_id");
    }
    
    $query_params = $_GET;
    unset($query_params['action'], $query_params['app_id']);
    $query_string = http_build_query($query_params);
    header("Location: approve.php" . ($query_string ? '?' . $query_string : ''));
    exit();
}

$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_name = isset($_GET['name']) ? $_GET['name'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';

$sql = "SELECT 
            s.app_id, s.app_status,
            stu.stu_fname, stu.stu_lname,
            ss.ss_name, ss.ss_type, ss.ss_amount, ss.ss_id, ss.ss_year,
            (SELECT COUNT(DISTINCT s2.stu_id) FROM scholarship s2 INNER JOIN student_master sm2 ON s2.stu_id = sm2.stu_id WHERE s2.ss_id = ss.ss_id AND s2.app_status = 'Approved') AS total_beneficiary
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

$order_by = isset($_GET['sort']) ? $_GET['sort'] : 's.app_id';
$order = isset($_GET['order']) && $_GET['order'] == 'ASC' ? 'ASC' : 'DESC';

$valid_columns = ['stu.stu_fname', 'ss.ss_type', 'ss.ss_amount', 'ss.ss_name', 'ss.ss_year', 's.app_status', 's.app_id'];
if (!in_array($order_by, $valid_columns)) $order_by = 's.app_id';

$sql .= " ORDER BY $order_by $order";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve / Reject Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4 content-area">

            <h3 class="mb-3">Approve or Reject Applications</h3>

            <div class="card p-3 mb-4 shadow-sm">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Year</label>
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
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Application Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Applied" <?php echo ($filter_status == 'Applied') ? 'selected' : ''; ?>>Pending</option>
                            <option value="Approved" <?php echo ($filter_status == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="Rejected" <?php echo ($filter_status == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-12 d-flex gap-2 justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">Filter</button>
                        <a href="approve.php" class="btn btn-secondary px-4">Reset</a>
                        <a href="export.php?type=approvals&type_filter=<?php echo urlencode($filter_type); ?>&name=<?php echo urlencode($filter_name); ?>&status=<?php echo urlencode($filter_status); ?>&year=<?php echo urlencode($filter_year); ?>&sort=<?php echo urlencode($order_by); ?>&order=<?php echo urlencode($order); ?>" class="btn btn-success px-4" title="Export to Excel">Export</a>
                    </div>
                </form>
            </div>

            <?php
            $rows = [];
            $approved_count = 0;
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                    if ($row['app_status'] == 'Approved') {
                        $approved_count++;
                    }
                }
            }
            $total_apps = count($rows);
            ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 text-primary fw-bold">Total Applications: <?php echo $total_apps; ?></h5>
                <h5 class="mb-0 text-success fw-bold">Approved: <?php echo $approved_count; ?></h5>
            </div>

            <table class="table table-striped table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <?php 
                        $next_order = ($order == 'ASC') ? 'DESC' : 'ASC'; 
                        $base_url = "?type=" . urlencode($filter_type) . "&name=" . urlencode($filter_name) . "&status=" . urlencode($filter_status) . "&year=" . urlencode($filter_year);
                        ?>
                        <th><a href="<?php echo $base_url; ?>&sort=stu.stu_fname&order=<?php echo ($order_by == 'stu.stu_fname') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Student</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=ss.ss_type&order=<?php echo ($order_by == 'ss.ss_type') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Type of Scholarship</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=ss.ss_amount&order=<?php echo ($order_by == 'ss.ss_amount') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Amount of Scholarship</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=ss.ss_name&order=<?php echo ($order_by == 'ss.ss_name') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Name of Scholarship</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=ss.ss_year&order=<?php echo ($order_by == 'ss.ss_year') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Year</a></th>
                        <th><a href="<?php echo $base_url; ?>&sort=s.app_status&order=<?php echo ($order_by == 's.app_status') ? $next_order : 'ASC'; ?>" class="text-white text-decoration-none d-block">Status</a></th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($total_apps > 0): ?>
                        <?php foreach ($rows as $row): ?>
                            <?php 
                                $student_name = trim($row['stu_fname'] . ' ' . $row['stu_lname']);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student_name); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_type']); ?></td>
                                <td><?php echo number_format($row['ss_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['ss_year']); ?></td>
                                <td>
                                    <?php if ($row['app_status'] == 'Applied'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php elseif ($row['app_status'] == 'Approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="approve.php?action=approve&app_id=<?php echo $row['app_id']; ?>" class="btn btn-success btn-sm <?php echo ($row['app_status'] == 'Approved') ? 'disabled' : ''; ?>" onclick="return confirm('Are you sure you want to approve this application?');">Approve</a>
                                        <a href="approve.php?action=reject&app_id=<?php echo $row['app_id']; ?>" class="btn btn-danger btn-sm <?php echo ($row['app_status'] == 'Rejected') ? 'disabled' : ''; ?>" onclick="return confirm('Are you sure you want to reject this application?');">Reject</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No applications found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>
