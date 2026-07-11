<?php include 'header.php'; ?>

<?php include "db.php"; ?>

<?php $message = ""; ?>

<!--INSERT IN DATABASE-->
  <?php

if (isset($_POST['submit'])) {
    echo "Inside Submit";
    
    // Read form data
    $stu_enroll         = trim($_POST['stu_enroll']);
    $stu_fname          = trim($_POST['stu_fname']);
    $stu_lname          = trim($_POST['stu_lname']);    
    $stu_ext            = trim($_POST['stu_ext']); 
    $stu_mname          = trim($_POST['stu_mname']);    
    $stu_gender         = trim($_POST['stu_gender']);        
    $stu_pass           = $_POST['stu_pass'];
    $stu_confirm_pass   = $_POST['stu_confirm_pass'];
    $stu_dob            = $_POST['stu_dob'];
    $stu_email          = $_POST['stu_email'];
    $stu_contact        = $_POST['stu_contact'];
    
    
    $stu_program        = $_POST['stu_program'];
    $stu_year_level     = $_POST['stu_year_level'];
    $stu_campus         = $_POST['stu_campus'];
    $stu_cor            = $_POST['stu_cor'];
    $stu_units          = $_POST['stu_units'];
    $stu_grade          = $_POST['stu_grade'];
    $stu_gpa            = $_POST['stu_gpa'];
    $stu_year           = $_POST['stu_year'];
    $stu_branch         = $_POST['stu_branch'];
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
    $stu_disability     = $_POST['stu_disability'];
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
    
    
    
    
    
        
    // Validation
    if ($stu_pass !== $stu_confirm_pass) {
        $message = "<div style='color:red;'>Passwords do not match!</div>";
        #echo "Passwords do not match!";
        #exit();
    }

    // Check duplicate email/enrollment
    $check = $conn->prepare("SELECT * FROM `student_master` WHERE `stu_enroll`= '".$stu_enroll."'");
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {        
        $message = "<div style='color:red;'>This enrollment number is already registered!</div>";
        #echo "Email or Enrollment already exists!";
    } 
    else {
        // Insert data
        $stmt = $conn->prepare("INSERT INTO `student_master` VALUES ('". $stu_enroll ."', '". $stu_fname ."', '". $stu_lname ."', '". $stu_ext ."', '". $stu_mname ."', '". $stu_gender ."', '". $stu_pass ."', '". $stu_confirm_pass ."', '". $stu_dob ."', '". $stu_email ."', '". $stu_contact ."', '". $stu_program ."', '". $stu_year_level ."', '". $stu_campus ."', '". $stu_cor ."', '". $stu_units ."', '". $stu_grade ."', '". $stu_gpa ."', '". $stu_year ."', '". $stu_branch ."', '". $stu_sem ."', '". $father_lname ."', '". $father_gname ."', '".  $father_mname ."', '". $mother_lname ."', '". $mother_gname ."', '". $mother_mname ."', '". $stu_dswd ."', '". $stu_house ."', '". $stu_bci ."', '". $stu_amount ."', '". $stu_disabled ."', '". $stu_disability ."', '". $stu_marital ."', '". $stu_dependent ."', '". $stu_inmate ."', '". $stu_rebel ."', '". $stu_street ."', '". $stu_barangay ."', '". $stu_city ."', '". $stu_province ."', '". $stu_zip ."', '". $stu_perc ."')");

        
        if ($stmt->execute()) {
            $message = "<div style='color:green;'>Registration successful!</div>";
            header("Location: index.php");
            #echo "Student registered successfully!";
        } else {
            $message = "<div style='color:red;'>Error occurred!</div>";
            #echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>

<!--------------- BODY ------------------->

<div class="container offset-2 col-8 mt-4">
    <div class="offset-0 card shadow-lg p-4 rounded-4">
        <h3 class="mb-4 text-center">Student Registration Form</h3>

        <form method="post" enctype="multipart/form-data">
            <div class="row g-4">
               
               <div>
                            <?php
                            if (!empty($message)) {
                                echo $message;
                            }
                            ?>
                </div>
                        
                        
                <!-- Personal Information Card -->
                <div class="col-12">
                    <div class="card p-3 shadow-sm rounded-3">
                        <h5 class="mb-3">Personal Information</h5>

                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Enrollment No. (Student ID)</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_enroll" placeholder="Enter enrollment number">
                                </div>
                        </div>
                           
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">First Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_fname" placeholder="Enter name">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Last Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_lname" placeholder="Enter name">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">EXT Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_ext" placeholder="Enter EXT name">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Middel Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_mname" placeholder="Enter middle name">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Gender</label>
                                <div class="col-sm-8">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stu_gender" value="M" id="male3">
                                        <label class="form-check-label" for="male3">Male</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stu_gender" value="F" id="female3">
                                        <label class="form-check-label" for="female3">Female</label>
                                    </div>
                                </div>
                            </div>
                            
                        
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Password</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" name = "stu_pass" placeholder="Enter password">
                                </div>
                            </div>

                           
                           <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Confirm Password</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" name = "stu_confirm_pass" placeholder="Confirm password">
                                </div>
                            </div>
                            
                            
                           <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Date of Birth</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" name = "stu_dob">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Email Address</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" name = "stu_email" placeholder="Enter email address">
                                </div>
                            </div>

                           <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Contact Number</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" name = "stu_contact" placeholder="Enter contact number">
                                </div>
                            </div>
                    </div>
                </div>

                <!-- Academic Information Card -->
                <div class="col-12">
                    <div class="card p-3 shadow-sm rounded-3">
                        <h5 class="mb-3">Academic Information</h5>

                       <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Program Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_program" placeholder="Enter program Name">
                                </div>
                        </div>
                           
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Year Level</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_year_level" placeholder="Enter year level">
                                </div>
                        </div>
                            
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Campus</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_campus" placeholder="Enter campus name">
                                </div>
                        </div>
                            
                            
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">COR</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control" name = "stu_cor" id= "stu_cor">
                                </div>
                        </div>

                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Number of Units Enrolled</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_units" placeholder="Enter units allocated">
                                </div>
                        </div>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Student Grade</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_grade" placeholder="Enter grade">
                                </div>
                        </div>

                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">GPA</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_gpa" placeholder="Enter GPA">
                                </div>
                        </div>

                       <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Admission Year</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_year" placeholder="Enter admission year">
                                </div>
                        </div>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Discipline / Program / Branch</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_branch" placeholder="Enter branch / program / discipline">
                                </div>
                        </div>

                       <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Current Semester</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_sem" placeholder="Enter Current Semester">
                                </div>
                        </div>                        
                        
                    </div>
                </div>

               
               
                <!-- Family Information Card -->
                <div class="col-12">
                    <div class="card p-3 shadow-sm rounded-3">
                        <h5 class="mb-3">Family Information</h5>

                        <h6>Father's Name</h6>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Last Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "father_lname" placeholder="Enter father's last name">
                                </div>
                        </div>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Given Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "father_gname" placeholder="Enter father's given name">
                                </div>
                        </div>
                        
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Middle Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "father_mname" placeholder="Enter father's middle name">
                                </div>
                        </div>
                        
                        

                        <h6>Mother's Maiden Name</h6>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Last Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "mother_lname" placeholder="Enter mother's last name">
                                </div>
                        </div>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Given Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "mother_gname" placeholder="Enter mother's given name">
                                </div>
                        </div>
                        
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Middle Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "mother_mname" placeholder="Enter mother's middle name">
                                </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Government & Status Card -->
                <div class="col-12">
                    <div class="card p-3 shadow-sm rounded-3">
                        <h5 class="mb-3">Government & Status Details</h5>

                       
                       <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">DSWD ID</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_dswd" placeholder="Enter DSWD ID">
                                </div>
                        </div>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">DSWD House No</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_house" placeholder="Enter DSWD House No">
                                </div>
                        </div>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">BCI</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name = "stu_bci" placeholder="Enter BCI">
                                </div>
                        </div>
                        
                                                
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Encode Amount</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" name = "stu_amount" placeholder="Enter Encode Amount">
                                </div>
                        </div>
                        
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Disabled</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_disabled">
                                        <option>No</option>
                                        <option>Yes</option>
                                    </select>
                                </div>
                        </div>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Disability Certificate</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control" name = "stu_disability" id="stu_disability">
                                </div>
                        </div>
                        
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Marital Status</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_marital">
                                        <option>Single</option>
                                        <option>Married</option>
                                        <option>Divorced</option>
                                        <option>Widowed</option>
                                    </select>
                                </div>
                        </div>
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Dependent</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_dependent">
                                        <option>No</option>
                                        <option>Yes</option>
                                    </select>
                                </div>
                        </div>

                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Former Inmate</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_inmate">
                                        <option>No</option>
                                        <option>Yes</option>
                                    </select>
                                </div>
                        </div>
                        
                        
                        <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Rebel Returnees</label>
                                <div class="col-sm-8">
                                    <select class="form-select mb-3" name="stu_rebel">
                                        <option>No</option>
                                        <option>Yes</option>
                                    </select>
                                </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Address Card -->
                <div class="col-md-12">
                    <div class="card p-3 shadow-sm rounded-3">
                        <h5 class="mb-3">Permanent Address</h5>

                       <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Street</label>
                                <input type="text" class="form-control" name="stu_street">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Barangay</label>
                                <input type="text" class="form-control" name="stu_barangay">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Town/City</label>
                                <input type="text" class="form-control" name="stu_city">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Province</label>
                                <input type="text" class="form-control" name="stu_province">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Zip Code</label>
                                <input type="text" class="form-control" name="stu_zip">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Percentage</label>
                                <input type="number" step="0.01" class="form-control" name="stu_perc">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5" name="submit">Submit</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
