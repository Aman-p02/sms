<?php include 'header.php'; ?>

<?php include "db.php"; ?>

<?php $message = ""; ?>

<!--INSERT IN DATABASE-->
  <?php

if (isset($_POST['submit'])) {
    
    // Read form data
    $stu_enroll         = trim($_POST['stu_enroll']);
    $stu_fname          = trim($_POST['stu_fname']);
    $stu_email          = $_POST['stu_email'];
    $stu_contact        = $_POST['stu_contact'];
    $stu_pass           = $_POST['stu_pass'];
    $stu_confirm_pass   = $_POST['stu_confirm_pass'];
    
        
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
        $stmt = $conn->prepare("INSERT INTO `student_master` (stu_enroll, stu_fname, stu_pass, stu_email, stu_contact) VALUES ('". $stu_enroll ."', '". $stu_fname ."',  '". $stu_pass ."', '". $stu_email ."', '". $stu_contact ."')");

        
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

<div class="container offset-4 col-4 mt-4">
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
                                <label class="col-sm-4 col-form-label">Enrollment No.</label>
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
