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
    <!-- Admin Login Section -->
    <section id="admin-login" class="py-5 bg-white" style="min-height: 80vh; display: flex; align-items: center;">
        <div class="container py-4">
            <div class="row justify-content-center">

                <!-- Admin Login Card -->
                <div class="col-lg-5 d-flex justify-content-center">
                    <div class="card shadow-lg w-100 rounded-4 border-0 hover-lift" style="max-width: 420px;">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                    <i class="bi bi-shield-lock-fill text-danger fs-1"></i>
                                </div>
                                <h3 class="fw-bold text-dark">Admin Login</h3>
                                <p class="text-muted small">Secure access to the management portal</p>
                            </div>
                            
                            <form method="post">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control rounded-3" id="loginID" name="adm_user" placeholder="Enter user name" required />
                                    <label for="loginID"><i class="bi bi-person-badge text-muted me-1"></i> Username</label>
                                </div>
                                
                                <div class="form-floating mb-4">
                                    <input type="password" class="form-control rounded-3" id="loginPassword" name="adm_pass" placeholder="Enter your password" required />
                                    <label for="loginPassword"><i class="bi bi-key text-muted me-1"></i> Password</label>
                                </div>
                                
                                <button type="submit" class="btn btn-danger btn-lg w-100 rounded-pill fw-bold mb-3 shadow-sm" name="submit">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Access Portal
                                </button>
                                
                                <div class="text-center mt-3 small fw-medium">
                                    <a href="/SMS/index.php" class="text-decoration-none text-muted hover-danger"><i class="bi bi-arrow-left me-1"></i> Back to Home</a>
                                </div>
                            </form>
                            <?php
                                if (!empty($message)) {
                                    echo "<div class='mt-3 text-center'>" . $message . "</div>";
                                }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
 


    
    
    

  <?php include '..\footer.php'; ?>