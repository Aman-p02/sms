<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Scholarship Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 </head>


<?php
    
session_start();
include "db.php";

// Protect page
if (!isset($_SESSION['stu_id'])) {
    header("Location: index.php");
    exit();
}
$stu_id = $_SESSION['stu_id'];
$stu_enroll = $_SESSION['stu_enroll'];
$stu_fname = $_SESSION['stu_fname'];

/*To fill values in form*/
$stmt = $conn->prepare("SELECT * FROM `student_master` WHERE `stu_id` = '". $stu_id ."' ");
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
    

/*FETCH CAMPUS*/
$sql1 = "Select * from campus";
$result1 = mysqli_query($conn, $sql1);
    
/*FETCH COLLEGES*/
$sql2 = "Select * from college";
$result2 = mysqli_query($conn, $sql2);
    
/*FETCH PROGRAMS*/
$sql3 = "Select * from program";
$result3 = mysqli_query($conn, $sql3);

/*$fileName = $_FILES['stu_cor']['name'];
$fileTmp  = $_FILES['stu_cor']['tmp_name'];
echo $fileName;
echo $fileTmp;*/
    
/*Update form values*/
if (isset($_POST['update_profile'])) {
    $stu_fname          = trim($_POST['stu_fname']);
    $stu_lname          = trim($_POST['stu_lname']);    
    $stu_ext            = trim($_POST['stu_ext']); 
    $stu_mname          = trim($_POST['stu_mname']);    
    $stu_gender         = $_POST['stu_gender'];        
    $stu_dob            = $_POST['stu_dob'];
    $stu_email          = $_POST['stu_email'];
    $stu_contact        = $_POST['stu_contact'];
    
    
    $stu_campus         = $_POST['stu_campus'];
    $stu_college        = $_POST['stu_college'];
    $stu_program        = $_POST['stu_program'];
    $stu_units          = $_POST['stu_units'];
    $stu_year_level     = $_POST['stu_year_level'];
    $stu_grade          = $_POST['stu_grade'];
    $stu_gpa            = $_POST['stu_gpa'];
    $stu_adm_year       = $_POST['stu_adm_year'];
    $stu_sem            = $_POST['stu_sem'];
    
    
    $father_lname       = $_POST['father_lname'];
    $father_gname       = $_POST['father_gname'];
    $father_mname       = $_POST['father_mname'];
    $mother_lname       = $_POST['mother_lname'];
    $mother_gname       = $_POST['mother_gname'];
    $mother_mname       = $_POST['mother_mname'];
    
    
    $stu_dswd           = $_POST['stu_dswd'];
    $stu_house          = $_POST['stu_house'];
    $stu_bci            = $_POST['stu_bci'];
    $stu_amount         = $_POST['stu_amount'];   
    $stu_disabled       = $_POST['stu_disabled'];
    $stu_marital        = $_POST['stu_marital'];
    $stu_dependent      = $_POST['stu_dependent'];
    $stu_inmate         = $_POST['stu_inmate'];
    $stu_rebel          = $_POST['stu_rebel'];
    
        
    $stu_street         = $_POST['stu_street'];
    $stu_barangay       = $_POST['stu_barangay'];
    $stu_city           = $_POST['stu_city'];
    $stu_province       = $_POST['stu_province'];
    $stu_zip            = $_POST['stu_zip'];
    $stu_perc           = $_POST['stu_perc'];
    

    /*$stu_cor            = $_POST['stu_cor'];
    $stu_disability     = $_POST['stu_disability'];
    $stu_profilepic     = $_POST['stu_profilepic'];*/
    
    if ($stu_fname == '' or
	$stu_lname == '' or
	$stu_ext == '' or
	$stu_mname == '' or
	$stu_gender == '' or
	$stu_dob == '' or
	$stu_email == '' or
	$stu_contact == '' or
	$stu_campus == '' or
	$stu_college == '' or
	$stu_program == '' or
	$stu_units == '' or
	$stu_year_level == '' or
	$stu_grade == '' or
	$stu_gpa == '' or
	$stu_adm_year == '' or
	$stu_sem == '' or
	$father_lname == '' or
	$father_gname == '' or
	$father_mname == '' or
	$mother_lname == '' or
	$mother_gname == '' or
	$mother_mname == '' or
	$stu_dswd == '' or
	$stu_house == '' or
	$stu_bci == '' or
	$stu_amount == '' or
	$stu_disabled == '' or
	$stu_marital == '' or
	$stu_dependent == '' or
	$stu_inmate == '' or
	$stu_rebel == '' or
	$stu_street == '' or
	$stu_barangay == '' or
	$stu_city == '' or
	$stu_province == '' or
	$stu_zip == '' or
	$stu_perc == ''){
        $complete = 'No';
    }
    else{
        $complete = 'Yes';
    }
    /*echo $complete;*/
        
    $sql4 = "UPDATE `student_master` SET 
        `stu_fname` = '". $stu_fname ."', 
        `stu_mname` = '". $stu_mname ."', 
        `stu_lname` = '". $stu_lname ."', 
        `stu_ext` = '". $stu_ext ."', 
        `stu_gender` = '".$stu_gender."', 
        `stu_email` = '".$stu_email."', 
        `stu_contact` = '".$stu_contact."', 
        `stu_dob` = '".$stu_dob."', 
        
        `stu_campus` = '".$stu_campus."', 
        `stu_college` = '".$stu_college."', 
        `stu_program` = '".$stu_program."', 
        `stu_adm_year` = '".$stu_adm_year."', 
        `stu_year_level` = '".$stu_year_level."', 
        `stu_sem` = '".$stu_sem."', 
        `stu_grade` = '".$stu_grade."', 
        `stu_gpa` = '".$stu_gpa."', 
        `stu_units` = '".$stu_units."', 
        
        `father_lname` = '".$father_lname."', 
        `father_gname` = '".$father_gname."', 
        `father_mname` = '".$father_mname."',
        `mother_lname` = '".$mother_lname."', 
        `mother_gname` = '".$mother_gname."', 
        `mother_mname` = '".$mother_lname."', 
        
        `stu_dswd` = '".$stu_dswd."', 
        `stu_house` = '".$stu_house."', 
        `stu_bci` = '".$stu_bci."', 
        `stu_amount` = '".$stu_amount."', 
        `stu_marital` = '".$stu_marital."', 
        `stu_dependent` = '".$stu_dependent."', 
        `stu_inmate` = '".$stu_inmate."', 
        `stu_rebel` = '".$stu_rebel."',
        `stu_disabled` = '".$stu_disabled."',
        
        `stu_street`= '".$stu_street."',
        `stu_barangay`= '".$stu_barangay."',
        `stu_city`= '".$stu_city."',
        `stu_province`= '".$stu_province."',
        `stu_zip`= '".$stu_zip."',
        `stu_perc`= '".$stu_perc."', 
        
        `complete`= '".$complete."' 
        
        WHERE stu_id = '". $stu_id ."'";
    
    /*echo $sql4;*/
    $result4 = mysqli_query($conn, $sql4);
    if ($result4) {
        $message = "<div style='color:green;'>Profile updated successfully!</div>";
    } else {
        $message = "<div style='color:red;'>Update failed!</div>";
    }
    /*$update->close();*/
    
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
    
?>


<?php
/*FETCH SIGLE RAW*/
$result5 = mysqli_query($conn, "SELECT * FROM student_master WHERE stu_id = '".$stu_id."'");
$row5 = mysqli_fetch_assoc($result5);
$complete = $row5['complete'];    
?>

<!-------------- BODY ----------------->

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'stu_sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4">

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <?php $photo_editable = true; include 'stu_photo_widget.php'; ?>
                    <h2 class="fw-bold mb-0">Welcome, <?php echo htmlspecialchars($stu_fname); ?></h2>
                </div>
            </div>

            <?php if (isset($_GET['photo_success'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Profile photo updated successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } elseif (isset($_GET['photo_error'])) {
                $errors = [
                    'size' => 'Image must be smaller than 5MB.',
                    'type' => 'Only JPG, PNG, and WEBP images are allowed.',
                    'upload' => 'Something went wrong while saving the photo. Please try again.',
                    'none' => 'No file was selected.'
                ];
                $msg = $errors[$_GET['photo_error']] ?? 'Upload failed. Please try again.';
            ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>
            <?php
                if($complete == 'No')
                {
                    echo '<div style="color: red;">Your Profile is Incomplete. You can not apply for any scholarship. To enable other options, fill all the details as asked </div>';
                }
            ?>
            <!-- SECTION: EDIT PROFILE -->
            <div id="editProfile" class="mt-5">
                
                <!-- NEW STUDENT DETAILS SECTION -->
                <div class="card p-4 shadow card-custom mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center border-end">
                            <h6 class="mb-3 text-muted fw-bold">Student Photo</h6>
                            <?php 
                            $__photo_path = (!empty($user['stu_profilepic']) && file_exists("uploads/profile_photos/" . $user['stu_profilepic'])) 
                                ? "uploads/profile_photos/" . $user['stu_profilepic'] 
                                : null;
                            if ($__photo_path) { ?>
                                <img src="<?php echo htmlspecialchars($__photo_path); ?>?v=<?php echo time(); ?>" alt="Student Photo" class="img-fluid border" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php } else { ?>
                                <div class="border d-flex align-items-center justify-content-center mx-auto bg-light text-muted" style="width: 150px; height: 150px; font-size: 14px;">
                                    No Photo
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-9 ps-md-4 mt-3 mt-md-0">
                            <h5 class="mb-3 fw-bold border-bottom pb-2">Student Details</h5>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-muted">Enrollment No:</div>
                                <div class="col-sm-9 fw-semibold"><?php echo htmlspecialchars($user['stu_enroll'] ?? '-'); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-muted">Full Name:</div>
                                <div class="col-sm-9 fw-semibold"><?php echo htmlspecialchars(trim(($user['stu_fname'] ?? '') . ' ' . ($user['stu_mname'] ?? '') . ' ' . ($user['stu_lname'] ?? ''))); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-muted">Email:</div>
                                <div class="col-sm-9 fw-semibold"><?php echo htmlspecialchars($user['stu_email'] ?? '-'); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3 text-muted">Campus / College:</div>
                                <div class="col-sm-9 fw-semibold"><?php echo htmlspecialchars(($user['stu_campus'] ?? '-') . ' - ' . ($user['stu_college'] ?? '-')); ?></div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-sm-3 text-muted">Course:</div>
                                <div class="col-sm-9 fw-semibold"><?php echo htmlspecialchars($user['stu_program'] ?? '-'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-4 shadow card-custom">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                           <h5 class="mb-3 border-bottom pb-2">Personal Details</h5>
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Enrollment No.</label>
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
                                <label class="col-sm-4 col-form-label">Middel Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_mname" value="<?php echo $user['stu_mname']; ?>">
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
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Date of Birth</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" name = "stu_dob" value="<?php echo $user['stu_dob']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!----------------------------------------------->
                            <h5 class="mb-3">Academic Information</h5>                            
                            
                            
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Campus</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_campus">
                                     <?php
                                        if ($user['stu_campus'] == '')
                                        {
                                        ?>
                                      <option value="">--- Select Campus ---</option>
                                      <?php 
                                        }
                                        
                                        $stored_value = $user['stu_campus'];
                                        while ($row1 = mysqli_fetch_assoc($result1)) 
                                        {
                                            $option_value = $row1['campus_name'];
                                            $selected = ($option_value == $stored_value) ? "selected" : "";
                                            echo "<option value='$option_value' $selected>$option_value</option>";
                                        }
                                        ?>
                                        
                                    </select>
                                </div>
                                </div>
                            </div>
                            
                                                   
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">College</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_college">
                                     <?php
                                        if ($user['stu_college'] == '')
                                        {
                                        ?>
                                      <option value="">--- Select College ---</option>
                                      <?php 
                                        }
                                        
                                        $stored_value = $user['stu_college'];
                                        while ($row2 = mysqli_fetch_assoc($result2)) 
                                        {
                                            $option_value = $row2['college_name'];
                                            $selected = ($option_value == $stored_value) ? "selected" : "";
                                            echo "<option value='$option_value' $selected>$option_value</option>";
                                        
                                        }
                                        ?>
                                        
                                    </select>
                                </div>
                                </div>
                            </div>
    
                           
                           <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">College</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_program">
                                     <?php
                                        if ($user['stu_program'] == '')
                                        {
                                        ?>
                                          <option value="">--- Select College ---</option>
                                      <?php 
                                        }
                                        
                                        $stored_value = $user['stu_program'];
                                        while ($row3 = mysqli_fetch_assoc($result3)) 
                                        {
                                            $option_value = $row3['prog_name'];
                                            $selected = ($option_value == $stored_value) ? "selected" : "";
                                            echo "<option value='$option_value' $selected>$option_value</option>";
                                        }
                                        ?>
                                        
                                    </select>
                                </div>
                                </div>
                            </div>
                            
                            
                            
                            
                             <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Admission Year</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_adm_year" value="<?php echo $user['stu_adm_year']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Year Level</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_year_level">
                                       <option <?php if($user['stu_year_level']=="") echo "selected"; ?>> --- Select Year Level --- </option>
                                        <option value = "1" <?php if($user['stu_year_level']=="1") echo "selected"; ?>>1</option>
                                        <option  value = "2" <?php if($user['stu_year_level']=="2") echo "selected"; ?>>2</option>
                                        <option  value = "3" <?php if($user['stu_year_level']=="3") echo "selected"; ?>>3</option>
                                        <option  value = "4" <?php if($user['stu_year_level']=="4") echo "selected"; ?>>4</option>
                                    </select>
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
                                <label class="col-sm-4 col-form-label">Number of Units Enrolled</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_units" value="<?php echo $user['stu_units']; ?>">
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
                            
                            <!---------------------------------------------->
                            <hr>
                            <h5 class="mb-3">Family Information</h5>
                            <h6>Father's Name</h6>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Last Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "father_lname"  value="<?php echo $user['father_lname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Given Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "father_gname"  value="<?php echo $user['father_gname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Middle Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "father_mname"  value="<?php echo $user['father_mname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <h6>Mother's Maiden Name</h6>
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Last Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "mother_lname"  value="<?php echo $user['mother_lname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Given Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "mother_gname"  value="<?php echo $user['mother_gname']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Middle Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "mother_mname"  value="<?php echo $user['mother_mname']; ?>">
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
                                    <input type="text" class="form-control" name = "stu_dswd"  value="<?php echo $user['stu_dswd']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">DSWD House No</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_house"  value="<?php echo $user['stu_house']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">BCI</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_bci"  value="<?php echo $user['stu_bci']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Encode Amount</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" name = "stu_amount"  value="<?php echo $user['stu_amount']; ?>">
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
                            
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Disabled</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_disabled" >
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
                            
                            <hr>
                           <!------------------------------------------------------->
                            <h5 class="mb-3">Permanent Address</h5>
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Street</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_street"   value="<?php echo $user['stu_street']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Barangay</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_barangay"  value="<?php echo $user['stu_barangay']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Town/City</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_city"  value="<?php echo $user['stu_city']; ?>">
                                </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Province</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_province"  value="<?php echo $user['stu_province']; ?>">
                                </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                               <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Zip</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_zip"  value="<?php echo $user['stu_zip']; ?>">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
