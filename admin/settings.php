<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 p-4">
            <h3>Settings</h3>

            <div class="card p-4 shadow card-custom">
                <h5>Change Admin Password</h5>
                <input type="password" class="form-control my-2" placeholder="Old Password">
                <input type="password" class="form-control my-2" placeholder="New Password">
                <button class="btn btn-primary mt-2">Update Password</button>
            </div>
        </div>

    </div>
</div>

</body>
</html>
