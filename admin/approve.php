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

            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Student</th>
                        <th>Scholarship</th>
                        <th>CGPA</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Alex</td>
                        <td>Need-based Scholarship</td>
                        <td>8.5</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>
                            <button class="btn btn-success btn-sm">Approve</button>
                            <button class="btn btn-danger btn-sm">Reject</button>
                        </td>
                    </tr>

                    <tr>
                        <td>Sheilla Pacheco</td>
                        <td>Merit Scholarship</td>
                        <td>9.4</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>
                            <button class="btn btn-success btn-sm">Approve</button>
                            <button class="btn btn-danger btn-sm">Reject</button>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>
