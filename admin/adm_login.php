<?php include '..\header.php'; ?>
    
<?php
session_start();
include "..\db.php";

$message = "";

if (isset($_POST['submit'])) {

    $adm_user = trim($_POST['adm_user']);
    $adm_pass = $_POST['adm_pass'];

    // Fetch user
    $stmt = $conn->prepare("SELECT * FROM admin_master WHERE `adm_user`= '".$adm_user."' ");
    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Verify password
        if ($adm_pass == $user['adm_pass']) {
            // Create session
            session_regenerate_id(true);
            $_SESSION['adm_id'] = $user['adm_id'];
            $_SESSION['adm_user'] = $user['adm_user'];
            
            header("Location: adm_dashboard.php");
            
            exit();

        } else {
            $message = "<div style='color:red;'>Invalid password!</div>";
        }

    } else {
        $message = "<div style='color:red;'>User not found!</div>";
    }

    $stmt->close();
    $conn->close();
}
?>
       
    
<!------------------ BODY ---------------------->
    <!-- Hero Section -->
    <section class="bg-light py-5 text-center border-bottom">
        <div class="container">
            <h1 class="display-5 fw-bold">Scholarship Management System</h1>
            <p class="lead mt-3">Digitizing scholarship applications, verification, evaluation, and beneficiary management.</p>
            <!--<a href="#apply" class="btn btn-primary btn-lg mt-3">Apply Now</a>-->
        </div>
    </section>

    <!-- About + Login Two Column Section -->
    <section id="about-login" class="py-5">
        <div class="container">
            <div class="row g-4">

                <!-- Left Column: About Section -->
                <div class="col-md-6">
                    <h2 class="fw-bold">About the Scholarship Program</h2>
                    <p class="mt-3">A Scholarship Management System significantly enhances the efficiency and fairness of the scholarship lifecycle by digitizing application, evaluation, and disbursement processes. Its automated workflows reduce manual errors, improve data accuracy, and ensure timely communication with applicants. The system also fosters transparency through standardized scoring mechanisms, minimizing human bias and supporting equitable decision-making. Furthermore, its integrated document management and analytics capabilities enable institutions to monitor trends, track fund utilization, and generate audit-ready reports. By offering secure, accessible, and cost-effective operations, a Scholarship Management System serves as a transformative tool for educational organizations, improving administrative productivity while expanding opportunities for deserving students.</p>
                </div>

                <!-- Right Column: Login Card -->
                <div class="col-md-6 d-flex justify-content-center">
                    <div class="card shadow w-100" style="max-width: 400px;">
                        <div class="card-body">
                            <h3 class="text-center mb-4 fw-bold">Admin Login</h3>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="loginID" class="form-label">User Name</label>
                                    <input type="text" class="form-control" id="loginID" name="adm_user" placeholder="Enter user name" />
                                </div>
                                
                                
                                <div class="mb-3">
                                    <label for="loginPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="loginPassword" name = "adm_pass" placeholder="Enter your password" />
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100" 
                                name="submit">Login</button>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
                                    <a href="register.php" class="text-decoration-none">Register</a>
                                </div>
                            </form>
                            <?php
                                if (!empty($message)) {
                                    echo $message;
                                }
                                ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    

    

    
 


    
    
    

  <?php include '..\footer.php'; ?>