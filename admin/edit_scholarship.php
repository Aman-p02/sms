<?php
    
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 2592000);
    session_set_cookie_params(2592000);
    session_start();
}
include "../db.php";

// Protect page
if (!isset($_SESSION['adm_id'])) {
    header("Location: index.php");
    exit();
}

$ss_id = $_GET['ss_id'];

$message = '';
/*To fill values in form*/
$stmt = $conn->prepare("SELECT * FROM `ss_master` WHERE `ss_id` = '". $ss_id ."' ");
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

    
/*Update form values*/
if (isset($_POST['submit'])) {
    $ss_year          = $_POST['ss_year'];
    $ss_name          = $_POST['ss_name'];
    $ss_type          = $_POST['ss_type'];    
    $ss_desc          = $_POST['ss_desc']; 
    $ss_start         = $_POST['ss_start'];    
    $ss_end           = $_POST['ss_end'];        
    $ss_amount        = $_POST['ss_amount'];
    

    
    $update = $conn->prepare("UPDATE `ss_master` SET 
        `ss_year`   = '". $ss_year."',
        `ss_name`   = '". $ss_name ."', 
        `ss_type`   = '". $ss_type ."', 
        `ss_desc`   = '". $ss_desc ."', 
        `ss_start`  = '". $ss_start ."', 
        `ss_end`    = '". $ss_end."', 
        `ss_amount` = '". $ss_amount."' WHERE `ss_id` = '". $ss_id ."'  " );
    

    if ($update->execute()) {
        $message = "<div style='color:green;'>Scholarship details updated successfully!</div>";
    } else {
        $message = "<div style='color:red;'>Update failed!</div>";
    }
    $update->close();
    
}
    
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

            <h2 class="fw-bold mb-4">Manage Students</h2>

           
           <form method="post">
                    <div class="row g-3">
                       
                       <div class="col-md-6">
                            <label class="form-label">Scholarship Year</label>
                            <select class="form-select" name="ss_year">
                                <option <?php if($user['ss_year']=="2025-26") echo "selected"; ?>>2025-26</option>
                                <option <?php if($user['ss_year']=="2026-27") echo "selected"; ?>>2026-27</option>
                                <option <?php if($user['ss_year']=="2027-28") echo "selected"; ?>>2027-28</option>
                                
                            </select>
                        </div>
                       
                        <div class="col-md-6">
                            <label class="form-label">Scholarship Name</label>
                            <select class="form-select" name="ss_name">
                               <option <?php if($user['ss_name']=="CHED Scholarship Program (CSP)") echo "selected"; ?>>CHED Scholarship Program (CSP)</option>
                               
                               <option <?php if($user['ss_name']=="DOST-SEI Undergraduate Scholarship") echo "selected"; ?>>DOST-SEI Undergraduate Scholarship</option>
                               
                               <option <?php if($user['ss_name']=="TES / Tertiary Education Subsidy (UniFAST)") echo "selected"; ?>>TES / Tertiary Education Subsidy (UniFAST)</option>
                                
                                <option <?php if($user['ss_name']=="Tulong Dunong Program (TDP)") echo "selected"; ?>>Tulong Dunong Program (TDP)</option>
                                
                                <option <?php if($user['ss_name']=="SUC Tulong Dunong Program (TDP)") echo "selected"; ?>>SUC Tulong Dunong Program (TDP)</option>
                                
                                
                                <option <?php if($user['ss_name']=="Scholarship Program for Coconut Farmers and their Families") echo "selected"; ?>>Scholarship Program for Coconut Farmers and their Families</option>
                                                                
                                <option <?php if($user['ss_name']=="Local Government Scholarships (Province/City/Municipality)") echo "selected"; ?>>Local Government Scholarships (Province/City/Municipality)</option>
                                
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="ss_type">
                                <option <?php if($user['ss_type']=="Half Merit") echo "selected"; ?>>Half Merit</option>
                                
                                <option <?php if($user['ss_type']=="Full Merit") echo "selected"; ?>>Full Merit</option>
                                
                                <option <?php if($user['ss_type']=="Grant") echo "selected"; ?>>Grant</option>
                                
                                <option <?php if($user['ss_type']=="Scholarship") echo "selected"; ?>>Scholarship</option>
                                
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" name="ss_desc" ><?php echo $user['ss_desc']; ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="ss_start" value="<?php echo $user['ss_start']; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="ss_end" value="<?php echo $user['ss_end']; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" name="ss_amount" value="<?php echo $user['ss_amount']; ?>">
                        </div>
                        
                        
                    </div>
                    <button class="btn btn-primary mt-3" name="submit">Update Scholarship</button>
                </form>
                <?php echo $message; ?>
           
        </div>
    </div>
</div>

</body>
</html>
