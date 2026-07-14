<?php require_once 'session.php';
include "../db.php";

$message = "";

if (isset($_POST['submit'])) {
    /*echo "Inside Submit";*/
    
    // Read form data
        
    
    $ss_year           = $_POST['ss_year'];
    $ss_name           = $_POST['ss_name'];
    $ss_type           = $_POST['ss_type'];
    $ss_desc           = $_POST['ss_desc'];
    $ss_start          = $_POST['ss_start'];
    $ss_end            = $_POST['ss_end'];
    $ss_amount         = $_POST['ss_amount'];    
    
    // Handle Document Upload
    $ss_document = "";
    if (isset($_FILES['ss_document']) && $_FILES['ss_document']['error'] == 0) {
        $upload_dir = "../uploads/scholarships/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES['ss_document']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['ss_document']['tmp_name'], $target_file)) {
            $ss_document = $filename;
        }
    }
        
    // Insert data
    $sql = "INSERT INTO `ss_master` (`ss_year`, `ss_name`, `ss_type`, `ss_desc`, `ss_start`, `ss_end`, `ss_amount`, `ss_document`) VALUES ('".$ss_year."', '". $ss_name ."', '". $ss_type ."', '". $ss_desc ."', '". $ss_start ."', '". $ss_end ."', '". $ss_amount ."', '".$ss_document."')";
    
    
    /*echo $sql;*/
    $result = $conn->query($sql);
    
        

    if ($result) {
        $message = "<div style='color:green;'>Scholarship added successful!</div>";
            #header("Location: add_scholarship.php");
            #echo "Student registered successfully!";
        } else {
            $message = "<div style='color:red;'>Error occurred!</div>";
            #echo "Error: " . $stmt->error;
        }

    }
    $conn->close();
?>

<!----------------- BODY STARTS -------------------->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Scholarship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'sidebar.php'; ?>

        <!-- MAIN -->
        <div class="col-md-9 col-lg-10 p-4 content-area">

            <h3 class="mb-4">Add New Scholarship</h3>

            <div class="card p-4 shadow card-custom">
                <form method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                       
                       <div class="col-md-6">
                            <label class="form-label">Scholarship Year</label>
                            <select class="form-select" name="ss_year">
                                <option>2025-26</option>
                                <option>2026-27</option>
                                <option>2027-28</option>
                            </select>
                        </div>

                        
                        
                        <div class="col-md-6">
                            <label class="form-label">Scholarship Name</label>
                            <select class="form-select" name="ss_name">
                                <option>CHED Scholarship Program (CSP)</option>
                                <option>DOST-SEI Undergraduate Scholarship</option>
                                <option>TES / Tertiary Education Subsidy (UniFAST)</option>
                                <option>Tulong Dunong Program (TDP)</option>
                                <option>SUC Tulong Dunong Program (TDP)</option>
                                <option>Scholarship Program for Coconut Farmers and their Families</option>
                                <option>Local Government Scholarships (Province/City/Municipality)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="ss_type">
                                <option>Half Merit</option>
                                <option>Full Merit</option>
                                <option>Grant</option>
                                <option>Scholarship</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" name="ss_desc"></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="ss_start">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="ss_end">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" name="ss_amount">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Attach Document (Optional)</label>
                            <input type="file" class="form-control" name="ss_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                        
                    </div>
                    <button class="btn btn-primary mt-3" name="submit">Add Scholarship</button>
                </form>
                <?php echo $message; ?>
            </div>

        </div>
    </div>
</div>

</body>
</html>
