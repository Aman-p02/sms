<?php
    
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 2592000);
    session_set_cookie_params(2592000);
    session_start();
}
include "../db.php";

// Protect page
if (!isset($_GET['stu_id'])) {
    header("Location: index.php");
    exit();
}
$stu_id = $_GET['stu_id'];
$stu_enroll = $_GET['stu_enroll'];

/*Update form values*/
if (isset($_POST['update_profile'])) {
    $stu_fname          = trim($_POST['stu_fname']);
    $stu_lname          = trim($_POST['stu_lname']);    
    $stu_ext            = trim($_POST['stu_ext']); 
    $stu_mname          = trim($_POST['stu_mname']);    
    $stu_gender         = trim($_POST['stu_gender']);        
    $stu_pass           = $_POST['stu_pass'];
    $stu_dob            = $_POST['stu_dob'];
    $stu_email          = $_POST['stu_email'];
    $stu_contact        = $_POST['stu_contact'];
    
    
    $stu_program        = $_POST['stu_program'];
    $stu_year_level     = $_POST['stu_year_level'];
    #$stu_campus         = $_POST['stu_campus'];
    $stu_units          = $_POST['stu_units'];
    $stu_grade          = $_POST['stu_grade'];
    $stu_gpa            = $_POST['stu_gpa'];
    #$stu_year           = $_POST['stu_year'];
    #$stu_branch         = $_POST['stu_branch'];
    $stu_sem            = $_POST['stu_sem'];
    
    
    #$father_lname       = $_POST['father_lname'];
    #$father_gname       = $_POST['father_gname'];
    #$father_mname       = $_POST['father_mname'];
    #$mother_lname       = $_POST['mother_lname'];
    #$mother_gname       = $_POST['mother_gname'];
    #$mother_mname       = $_POST['mother_mname'];
    
    
    #$stu_dswd           = $_POST['stu_dswd'];
    #$stu_house          = $_POST['stu_house'];
    #$stu_bci            = $_POST['stu_bci'];
    #$stu_amount         = $_POST['stu_amount'];   
    #$stu_disabled       = $_POST['stu_disabled'];
    #$stu_disability     = $_POST['stu_disability'];
    $stu_marital        = $_POST['stu_marital'];
    $stu_dependent      = $_POST['stu_dependent'];
    $stu_inmate         = $_POST['stu_inmate'];
    $stu_rebel          = $_POST['stu_rebel'];
    
        
    #$stu_street         = $_POST['stu_street'];
    #$stu_barangay       = $_POST['stu_barangay'];
    #$stu_city           = $_POST['stu_city'];
    #$stu_province       = $_POST['stu_province'];
    #$stu_zip            = $_POST['stu_zip'];
    $stu_perc           = $_POST['stu_perc'];
    
    
    
    $update = $conn->prepare("UPDATE `student_master` SET 
        `stu_fname` = '". $stu_fname ."', 
        `stu_lname` = '". $stu_lname ."', 
        `stu_ext` = '". $stu_ext ."', 
        `stu_mname` = '". $stu_mname ."', 
        `stu_gender` = '".$stu_gender."', 
        `stu_dob` = '".$stu_dob."', 
        `stu_email` = '".$stu_email."', 
        `stu_contact` = '".$stu_contact."', 
        `stu_program` = '".$stu_program."', 
        `stu_year_level` = '".$stu_year_level."', 
        `stu_units` = '".$stu_units."', 
        `stu_grade` = '".$stu_grade."', 
        `stu_gpa` = '".$stu_gpa."', 
        `stu_sem` = '".$stu_sem."', 
        `stu_marital` = '".$stu_marital."', 
        `stu_dependent` = '".$stu_dependent."', 
        `stu_inmate` = '".$stu_inmate."', 
        `stu_rebel` = '".$stu_rebel."', 
        `stu_perc`= '".$stu_perc."' 
        WHERE stu_id = '". $stu_id ."'");
    

    if ($update->execute()) {
        $message = "<div style='color:green;'>Profile updated successfully!</div>";
    } else {
        $message = "<div style='color:red;'>Update failed!</div>";
    }
    $update->close();
    
    /*==================================================*/    
    /*------------ FILE UPLOAD (COR) --------------*/
    /*==================================================*/    
        $message_cor = "";
        $upload_dir = 'uploads/student/';
        $fileName = basename($_FILES["stu_cor"]["name"]);
        $fileTmp  = $_FILES["stu_cor"]["tmp_name"];
        $fileSize = $_FILES["stu_cor"]["size"];
        
        // Rename File
        $cor_path = $upload_dir.$stu_enroll."_cor.jpg";        

        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
        $fileExt = strtolower(pathinfo($cor_path, PATHINFO_EXTENSION));
        
        if (in_array($fileExt, $allowedTypes)) {
            if ($fileSize < 2000000) { // 2MB limit
                if (move_uploaded_file($fileTmp, $cor_path)) {

                    // Save path in DB
                    $stmt = $conn->prepare("UPDATE `student_master` SET `stu_cor`= '".$cor_path."' WHERE `stu_id` = '".$stu_id."' ");
                    if ($stmt->execute()) {
                        #echo "File uploaded and saved successfully!";
                    } else {
                        $message_cor = "Database error!";
                    }
                } else {
                    $message_cor =  "File upload failed!";
                }

            } else {
                $message_cor =  "File size must be less than 2MB!";
            }

        } else {
            $message_cor =  "Only JPG, JPEG, PNG, PDF allowed!";
        }
        
        /*FILE UPLOAD OVER*/
    
    /*==================================================*/    
    /*------------ FILE UPLOAD (Disability Certificate) --------------*/
    /*==================================================*/    
        $message_dc = "";
        $upload_dir = 'uploads/student/';
        $fileName = basename($_FILES["stu_disability"]["name"]);
        $fileTmp  = $_FILES["stu_disability"]["tmp_name"];
        $fileSize = $_FILES["stu_disability"]["size"];
        
        // Rename File
        $dc_path = $upload_dir.$stu_enroll."_dc.pdf";        

        $allowedTypes = ['pdf'];
        $fileExt = strtolower(pathinfo($dc_path, PATHINFO_EXTENSION));
        
        if (in_array($fileExt, $allowedTypes)) {
            if ($fileSize < 2000000) { // 2MB limit
                if (move_uploaded_file($fileTmp, $dc_path)) {

                    // Save path in DB
                    $stmt = $conn->prepare("UPDATE `student_master` SET `stu_disability`= '".$dc_path."' WHERE `stu_id` = '".$stu_id."' ");
                    if ($stmt->execute()) {
                        #echo "File uploaded and saved successfully!";
                    } else {
                        $message_dc = "Database error!";
                    }
                } else {
                    $message_dc = "File upload failed!";
                }

            } else {
                $message_dc = "File size must be less than 2MB!";
            }

        } else {
            $message_dc = "Only PDF allowed!";
        }
    
}
    
/*Fetch updated values for form*/
$stmt = $conn->prepare("SELECT * FROM `student_master` WHERE `stu_id` = '". $stu_id ."' ");
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

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

           
           <div id="editProfile" class="mt-4">
                
                <?php
                // Calculate photo path
                $photo_path = (!empty($user['stu_profilepic']) && file_exists("../uploads/profile_photos/" . $user['stu_profilepic'])) 
                    ? "../uploads/profile_photos/" . $user['stu_profilepic'] 
                    : null;
                ?>

                <div class="card p-4 shadow-sm mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center border-end">
                            <h6 class="mb-3 fw-bold">Student Photo</h6>
                            <?php if ($photo_path) { ?>
                                <img src="<?php echo $photo_path; ?>?v=<?php echo time(); ?>" alt="Student Photo" class="img-thumbnail" style="width: 160px; height: 160px; object-fit: cover;">
                            <?php } else { ?>
                                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white img-thumbnail" style="width: 160px; height: 160px; font-size: 48px;">
                                    <?php echo strtoupper(substr($user['stu_fname'], 0, 1)); ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-9 ps-md-4">
                            <h5 class="fw-bold mb-3">Student Details</h5>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-muted">Enrollment No:</div>
                                <div class="col-sm-9 fw-bold"><?php echo htmlspecialchars($user['stu_enroll']); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-muted">Full Name:</div>
                                <div class="col-sm-9 fw-bold">
                                    <?php echo htmlspecialchars(trim($user['stu_fname'] . ' ' . $user['stu_mname'] . ' ' . $user['stu_lname'] . ' ' . $user['stu_ext'])); ?>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-muted">Email:</div>
                                <div class="col-sm-9 fw-bold"><?php echo htmlspecialchars($user['stu_email']); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-muted">Campus:</div>
                                <div class="col-sm-9 fw-bold"><?php echo htmlspecialchars($user['stu_campus']); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-muted">College:</div>
                                <div class="col-sm-9 fw-bold"><?php echo htmlspecialchars($user['stu_college']); ?></div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-sm-3 text-muted">Course:</div>
                                <div class="col-sm-9 fw-bold"><?php echo htmlspecialchars($user['stu_program']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card p-3 shadow card-custom">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                           <h5 class="mb-3">Personal Information</h5>
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Enrollment No. (Student ID)</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_enroll" disabled value="<?php echo $user['stu_enroll']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">First Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_fname" value="<?php echo $user['stu_fname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Last Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_lname" value="<?php echo $user['stu_lname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">EXT Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_ext" value="<?php echo $user['stu_ext']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Middel Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_mname" value="<?php echo $user['stu_mname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Gender</label>
                                <div class="col-sm-8">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stu_gender" value="M" id="male3" <?php if($user['stu_gender']=="M") echo "checked"; ?>>
                                        <label class="form-check-label" for="male3">Male</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stu_gender" value="F" id="female3" <?php if($user['stu_gender']=="F") echo "checked"; ?>>
                                        <label class="form-check-label" for="female3">Female</label>
                                    </div>
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Password</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" name = "stu_pass" value="<?php echo $user['stu_pass']; ?>">
                                </div>
                                </div>
                            </div>
                            
                                                        
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Date of Birth</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" name = "stu_dob" value="<?php echo $user['stu_dob']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Email Address</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" name = "stu_email" value="<?php echo $user['stu_email']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Contact Number</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" name = "stu_contact" value="<?php echo $user['stu_contact']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!----------------------------------------------->
                            <h5 class="mb-3">Academic Information</h5>                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Program Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_program" value="<?php echo $user['stu_program']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Year Level</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_year_level" value="<?php echo $user['stu_year_level']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Campus</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_campus" disabled value="<?php echo $user['stu_campus']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">COR</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control" name = "stu_cor" id="stu_cor">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Number of Units Enrolled</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_units" value="<?php echo $user['stu_units']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Student Grade</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_grade" value="<?php echo $user['stu_grade']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">GPA</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_gpa" value="<?php echo $user['stu_gpa']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Admission Year</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_year" disabled value="<?php echo $user['stu_year']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Discipline / Program / Branch</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_branch" disabled value="<?php echo $user['stu_branch']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Current Semester</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_sem" value="<?php echo $user['stu_sem']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <!---------------------------------------------->
                            <hr>
                            <h5 class="mb-3">Family Information</h5>
                            <h6>Father's Name</h6>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Last Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "father_lname" disabled value="<?php echo $user['father_lname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Given Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "father_gname" disabled value="<?php echo $user['father_gname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Middle Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "father_mname" disabled value="<?php echo $user['father_mname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <h6>Mother's Maiden Name</h6>
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Last Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "mother_lname" disabled value="<?php echo $user['mother_lname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Given Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "mother_gname" disabled value="<?php echo $user['mother_gname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Middle Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "mother_mname" disabled value="<?php echo $user['mother_mname']; ?>">
                                </div>
                                </div>
                            </div>
                            <hr>
                            
                            <!------------------------------------------------------->
                            <h5 class="mb-3">Government & Status Details</h5>
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">DSWD ID</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_dswd" disabled value="<?php echo $user['stu_dswd']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">DSWD House No</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_house" disabled value="<?php echo $user['stu_house']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">BCI</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_bci" disabled value="<?php echo $user['stu_bci']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Encode Amount</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" name = "stu_amount" disabled value="<?php echo $user['stu_amount']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Disabled</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_disabled" disabled>
                                        <option <?php if($user['stu_disabled']=="N") echo "selected"; ?>>No</option>
                                        <option <?php if($user['stu_disabled']=="Y") echo "selected"; ?>>Yes</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Disability Certificate</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control" name = "stu_disability">
                                </div>
                                </div>                                
                            </div>
                            
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Marital Status</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_marital">
                                        <option <?php if($user['stu_marital']=="Single") echo "selected"; ?>>Single</option>
                                        <option <?php if($user['stu_marital']=="Married") echo "selected"; ?>>Married</option>
                                        <option <?php if($user['stu_marital']=="Divorced") echo "selected"; ?>>Divorced</option>
                                        <option <?php if($user['stu_marital']=="Widowed") echo "selected"; ?>>Widowed</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Dependent</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_dependent">
                                        <option <?php if($user['stu_dependent']=="No") echo "selected"; ?>>No</option>
                                        <option <?php if($user['stu_dependent']=="Yes") echo "selected"; ?>>Yes</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Former Inmate</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_inmate">
                                        <option <?php if($user['stu_inmate']=="No") echo "selected"; ?>>No</option>
                                        <option <?php if($user['stu_inmate']=="Yes") echo "selected"; ?>>Yes</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Rebel Returnees</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_rebel">
                                        <option <?php if($user['stu_rebel']=="No") echo "selected"; ?>>No</option>
                                        <option <?php if($user['stu_rebel']=="Yes") echo "selected"; ?>>Yes</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            <hr>
                            
                           <!------------------------------------------------------->
                            <h5 class="mb-3">Permanent Address</h5>
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Street</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_street"  disabled value="<?php echo $user['stu_street']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Barangay</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_barangay" disabled value="<?php echo $user['stu_barangay']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Town/City</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_city" disabled value="<?php echo $user['stu_city']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Province</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_province" disabled value="<?php echo $user['stu_province']; ?>">
                                </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Zip</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_zip" disabled value="<?php echo $user['stu_zip']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Percentage</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" name = "stu_perc" value="<?php echo $user['stu_perc']; ?>">
                                </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary mt-3" name="update_profile" value="update_profile">Update Profile</button>
                    </form>
                    <?php
                                if (!empty($message_pdf)) {
                                    echo $message_pdf;
                                }
                                ?>
                </div>
            </div>
           
        </div>
    </div>
</div>

</body>
</html>
