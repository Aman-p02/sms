<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applied Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main -->
        <div class="col-md-9 col-lg-10 p-4 content-area">

            <h3 class="mb-3">Students Applied for Scholarships</h3>

            <label>Select Scholarship</label>
            <select class="form-select mb-3">
                <option>Merit</option>
                <option>Need-based</option>
                <option>Research</option>
                <option>Minority</option>
            </select>

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>CGPA</th>
                        <th>Date Applied</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Alex</td>
                        <td>alex@gmail.com</td>
                        <td>BSCS</td>
                        <td>8.6</td>
                        <td>12-Nov-2025</td>
                    </tr>

                    <tr>
                        <td>Sheilla</td>
                        <td>sheilla@gmail.com</td>
                        <td>MSCS</td>
                        <td>9.1</td>
                        <td>13-Nov-2025</td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
</div>

</body>
</html>
