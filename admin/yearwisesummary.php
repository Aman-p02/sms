<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Year-wise Summary</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">

            <h3 class="mb-3">Year-wise Scholarship Summary</h3>

            <table class="table table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Year</th>
                        <th>Scholarship Type</th>
                        <th>Total Applicants</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>2023</td>
                        <td>Merit</td>
                        <td>520</td>
                        <td>410</td>
                        <td>110</td>
                        <td>₹42,00,000</td>
                    </tr>

                    <tr>
                        <td>2024</td>
                        <td>Research</td>
                        <td>380</td>
                        <td>260</td>
                        <td>120</td>
                        <td>₹28,50,000</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>
