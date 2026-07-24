<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 2592000);
    session_set_cookie_params(2592000);
    session_start();
}
include "db.php";

$message = "";

// Fetch latest 5 scholarships for the Notice Board
$notices = [];
$notice_query = $conn->query("SELECT ss_name, ss_start, ss_end FROM ss_master ORDER BY ss_id DESC LIMIT 5");
if ($notice_query) {
    while($row = $notice_query->fetch_assoc()) {
        $notices[] = $row;
    }
}

// Total Scholarships
$scholarship_res = $conn->query("SELECT COUNT(*) as cnt FROM ss_master");
$total_scholarships = $scholarship_res->fetch_assoc()['cnt'] ?? 0;

// Total Applications
$application_res = $conn->query("SELECT COUNT(*) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id");
$total_applications = $application_res->fetch_assoc()['cnt'] ?? 0;

// Total Applicants
$applicant_res = $conn->query("SELECT COUNT(DISTINCT s.stu_id) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id");
$total_applicants = $applicant_res->fetch_assoc()['cnt'] ?? 0;

// Approved Scholarships
$approved_ss_res = $conn->query("SELECT COUNT(*) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id WHERE s.app_status = 'Approved'");
$approved_scholarships = $approved_ss_res->fetch_assoc()['cnt'] ?? 0;

// Approved Candidates
$approved_cand_res = $conn->query("SELECT COUNT(DISTINCT s.stu_id) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id WHERE s.app_status = 'Approved'");
$approved_candidates = $approved_cand_res->fetch_assoc()['cnt'] ?? 0;

// Approved Amount
$amount_res = $conn->query("SELECT SUM(sm.ss_amount) as total_amount FROM scholarship sc INNER JOIN ss_master sm ON sc.ss_id = sm.ss_id INNER JOIN student_master stu ON sc.stu_id = stu.stu_id WHERE sc.app_status = 'Approved'");
$total_approved_amount = $amount_res->fetch_assoc()['total_amount'] ?? 0;


if (isset($_POST['submit'])) {

    $stu_enroll = trim($_POST['stu_enroll']);
    $stu_pass = $_POST['stu_pass'];

    // Fetch user
    $stmt = $conn->prepare("SELECT * FROM student_master WHERE `stu_enroll`= '".$stu_enroll."' ");
    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Verify password
        if ($stu_pass == $user['stu_pass']) {
            // Create session
            session_regenerate_id(true);
            $_SESSION['stu_id'] = $user['stu_id'];
            $_SESSION['stu_enroll'] = $user['stu_enroll'];
            $_SESSION['stu_fname'] = $user['stu_fname'];
            if (isset($_REQUEST['redirect']) && $_REQUEST['redirect'] == 'apply' && $user['complete'] == 'Yes') {
                header("Location: apply_scholarship.php");
            } else {
                header("Location: stu_profile.php");
            }
            exit();

        } else {
            $message = "<div style='color:red;'>Invalid password!</div>";
        }

    } else {
        $message = "<div style='color:red;'>User not found!</div>";
    }

    $stmt->close();
}
?>
       
    
<?php include 'header.php'; ?>
    <style>
        .hero-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e0e7ff 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(13,110,253,0.05) 0%, rgba(255,255,255,0) 70%);
            z-index: 0;
            animation: pulse-bg 15s infinite alternate;
        }
        @keyframes pulse-bg {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .notice-item {
            transition: all 0.3s ease;
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .notice-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: #c7d2fe;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white !important;
        }
        .impact-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #212529;
            position: relative;
        }
        .impact-section::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1.5" fill="rgba(0,0,0,0.05)"/></svg>');
            z-index: 0;
        }
        .impact-card {
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
            border-radius: 16px;
        }
        .impact-card:hover {
            transform: translateY(-5px);
        }
        .impact-number {
            color: #0d6efd;
        }
    </style>

    <!-- Hero & Login Section Combined -->
    <section class="hero-section py-5 border-bottom">
        <div class="container pt-5 pb-5 hero-content">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3"><i class="bi bi-mortarboard-fill me-2"></i>Scholarship Management System</h1>
                <p class="lead fw-medium text-secondary">Digitizing scholarship applications, verification, evaluation, and beneficiary management.</p>
            </div>
            
            <div class="row g-5 align-items-stretch mt-2" id="about-login">

                <!-- Left Column: About Section -->
                <div class="col-lg-6 pe-lg-5 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold text-dark mb-5">About the Scholarship Program <i class="bi bi-info-circle text-primary fs-4 ms-2"></i></h2>
                    
                    <div class="d-flex mb-5">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-rocket-takeoff text-primary fs-3"></i>
                            </div>
                        </div>
                        <div class="ms-4">
                            <h4 class="fw-bold text-dark">Efficiency & Fairness</h4>
                            <p class="text-secondary mb-0 fs-6">Digitizing application, evaluation, and disbursement processes with automated workflows to reduce manual errors and save time.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-5">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-shield-check text-success fs-3"></i>
                            </div>
                        </div>
                        <div class="ms-4">
                            <h4 class="fw-bold text-dark">Total Transparency</h4>
                            <p class="text-secondary mb-0 fs-6">Standardized scoring mechanisms minimizing human bias and supporting equitable, fair, and open decision-making.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-2">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-graph-up-arrow text-info fs-3"></i>
                            </div>
                        </div>
                        <div class="ms-4">
                            <h4 class="fw-bold text-dark">Analytics & Tracking</h4>
                            <p class="text-secondary mb-0 fs-6">Integrated document management and analytics capabilities to monitor trends, track fund utilization, and report easily.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Login Card -->
                <div class="col-lg-6 d-flex justify-content-center align-items-center">
                    <div class="card shadow-lg w-100 rounded-4 border-0 hover-lift" style="max-width: 420px;">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                    <i class="bi bi-person-fill-lock text-primary fs-1"></i>
                                </div>
                                <h3 class="fw-bold text-dark">Student Login</h3>
                            </div>
                            
                            <form method="POST" action="">
                                <?php if (isset($_GET['redirect'])): ?>
                                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect']); ?>">
                                <?php endif; ?>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control rounded-3" id="loginID" name="stu_enroll" placeholder="Enter Enrollment No." required />
                                    <label for="loginID"><i class="bi bi-person text-muted me-1"></i> Enrollment No</label>
                                </div>
                                
                                <div class="form-floating mb-4 position-relative">
                                    <input type="password" class="form-control rounded-3" id="loginPassword" name="stu_pass" placeholder="Enter your password" required />
                                    <label for="loginPassword"><i class="bi bi-key text-muted me-1"></i> Password</label>
                                    <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y toggle-password" tabindex="-1" style="z-index: 10;">
                                        <i class="bi bi-eye text-muted"></i>
                                    </button>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold mb-3 shadow-sm" name="submit">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Login
                                </button>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3 small fw-medium">
                                    <a href="#" class="text-decoration-none text-muted hover-primary" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
                                    <a href="register.php" class="text-decoration-none text-primary">Create an account</a>
                                </div>
                            </form>
                            <?php
                                if (!empty($message)) {
                                    echo "<div class='mt-3'>" . $message . "</div>";
                                }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container py-4">
            <!-- Notice Board Rectangle -->
            <?php if (!empty($notices)): ?>
            <div class="card shadow-lg border-0 mb-5 rounded-4 overflow-hidden">
                <div class="card-header text-white fw-bold fs-5 py-3" style="background: linear-gradient(90deg, #dc3545 0%, #f1aeb5 100%);">
                    <i class="bi bi-megaphone-fill me-2 text-white"></i> NOTICE BOARD - LATEST SCHOLARSHIPS
                </div>
                <div class="card-body bg-light p-4">
                    <marquee behavior="scroll" direction="up" height="380" scrollamount="3" onmouseover="this.stop();" onmouseout="this.start();">
                        <div class="pe-2">
                        <?php foreach($notices as $notice): ?>
                            <div class="notice-item">
                                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-award-fill text-warning me-2 fs-4 align-middle"></i> <?php echo htmlspecialchars($notice['ss_name']); ?></h5>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 rounded-pill"><i class="bi bi-calendar-event me-1"></i> Start: <?php echo date("d M Y", strtotime($notice['ss_start'])); ?></span>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-2 rounded-pill"><i class="bi bi-calendar-x me-1"></i> End: <?php echo date("d M Y", strtotime($notice['ss_end'])); ?></span>
                                    <a href="index.php?redirect=apply#about-login" class="btn btn-primary btn-sm fw-bold rounded-pill px-4 shadow-sm ms-auto hover-lift"><i class="bi bi-box-arrow-in-right me-1"></i> Apply</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </marquee>
                </div>
            </div>
            <?php endif; ?>

            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark display-6">Key Features</h2>
                <div class="mx-auto bg-primary rounded" style="width: 80px; height: 4px; margin-top: 10px;"></div>
            </div>
            
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift text-center p-4">
                        <div class="card-body">
                            <div class="bg-primary bg-gradient bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4 shadow-sm">
                                <i class="bi bi-laptop text-primary fs-1"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Online Application</h4>
                            <p class="card-text text-muted">Students can apply securely from anywhere with a simplified online form submission process.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift text-center p-4">
                        <div class="card-body">
                            <div class="bg-success bg-gradient bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4 shadow-sm">
                                <i class="bi bi-shield-check text-success fs-1"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Automated Verification</h4>
                            <p class="card-text text-muted">Effortless document verification and eligibility checking using integrated digital tools.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift text-center p-4">
                        <div class="card-body">
                            <div class="bg-info bg-gradient bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4 shadow-sm">
                                <i class="bi bi-cpu text-info fs-1"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Smart Evaluation</h4>
                            <p class="card-text text-muted">Intelligent scoring mechanisms ensure complete fairness and transparency in selection.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="statistics" class="py-5 impact-section">
        <div class="container position-relative z-1 py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark display-5 mb-3">Our Program's Impact</h2>
                <div class="mx-auto rounded" style="width: 80px; height: 4px; background: linear-gradient(90deg, #0d6efd, #0dcaf0);"></div>
            </div>
            
            <div class="row g-4 mb-4">
                <!-- Card 1 -->
                <div class="col-md-4">
                    <div class="impact-card p-4 h-100 text-center">
                        <div class="mb-3">
                            <i class="bi bi-award fs-1 text-primary"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-2">Total Scholarships</h5>
                        <h2 class="fw-bold impact-number display-4 mb-0" data-target="<?php echo $total_scholarships; ?>">0</h2>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-md-4">
                    <div class="impact-card p-4 h-100 text-center">
                        <div class="mb-3">
                            <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-2">Total Applications</h5>
                        <h2 class="fw-bold impact-number display-4 mb-0" data-target="<?php echo $total_applications; ?>">0</h2>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col-md-4">
                    <div class="impact-card p-4 h-100 text-center">
                        <div class="mb-3">
                            <i class="bi bi-people fs-1 text-primary"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-2">Total Applicants</h5>
                        <h2 class="fw-bold impact-number display-4 mb-0" data-target="<?php echo $total_applicants; ?>">0</h2>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Card 4 -->
                <div class="col-md-4">
                    <div class="impact-card p-4 h-100 text-center">
                        <div class="mb-3">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-2">Approved Scholarships</h5>
                        <h2 class="fw-bold impact-number display-4 mb-0" data-target="<?php echo $approved_scholarships; ?>">0</h2>
                    </div>
                </div>
                <!-- Card 5 -->
                <div class="col-md-4">
                    <div class="impact-card p-4 h-100 text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-check fs-1 text-success"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-2">Approved Candidates</h5>
                        <h2 class="fw-bold impact-number display-4 mb-0" data-target="<?php echo $approved_candidates; ?>">0</h2>
                    </div>
                </div>
                <!-- Card 6 -->
                <div class="col-md-4">
                    <div class="impact-card p-4 h-100 text-center">
                        <div class="mb-3">
                            <i class="bi bi-cash-stack fs-1 text-warning"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-2">Funds Disbursed</h5>
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="fs-2 text-muted me-1"><i class="bi bi-currency-rupee"></i></span>
                            <h2 class="fw-bold display-4 mb-0 impact-number" data-target="<?php echo $total_approved_amount; ?>">0</h2>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
    
    
    <!-- Modal -->
<div class="modal fade" id="signinModal" tabindex="-1" aria-labelledby="signinModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="signinModalLabel">Sign In</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form>
            <div class="mb-3">
                <label class="form-label">Email / Student ID</label>
                <input type="text" class="form-control" placeholder="Enter your email or ID">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" placeholder="Enter your password">
                    <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y toggle-password" style="z-index: 10;">
                        <i class="bi bi-eye text-muted"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>

            <div class="text-center mt-3">
                <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
                <a href="register.php">Register</a>
            </div>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Your Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <p>Please enter your registered email address. We will send a password reset link.</p>

        <form>
          <div class="mb-3">
            <label for="resetEmail" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="resetEmail" placeholder="example@gmail.com" required>
          </div>

          <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        </form>
      </div>

    </div>
  </div>
</div>

    
    <!-- Benefits Section -->
    <section id="benefits" class="py-5 bg-light">
        <div class="container">
        <h2 class="fw-bold text-center">Benefits of the Scholarship Management System</h2>
        <p class="text-center mt-3 mb-5">A modern, efficient, and transparent way to manage scholarships.</p>
            <div class="row g-4 mt-2">
            <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift p-4 text-center">
            <div class="mb-3"><i class="bi bi-file-earmark-check fs-1 text-primary"></i></div>
            <h5 class="fw-bold">Streamlined Application</h5>
            <p class="text-muted">Students can apply online anytime, reducing paperwork and long queues.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift p-4 text-center">
            <div class="mb-3"><i class="bi bi-shield-lock fs-1 text-success"></i></div>
            <h5 class="fw-bold">Automated Verification</h5>
            <p class="text-muted">Documents and eligibility are checked digitally, saving administrative time.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift p-4 text-center">
            <div class="mb-3"><i class="bi bi-eye fs-1 text-info"></i></div>
            <h5 class="fw-bold">Transparency & Fairness</h5>
            <p class="text-muted">Real‑time updates and AI‑assisted evaluation ensure fair selection.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift p-4 text-center">
            <div class="mb-3"><i class="bi bi-geo-alt fs-1 text-warning"></i></div>
            <h5 class="fw-bold">Centralized Tracking</h5>
            <p class="text-muted">Both students and administrators can monitor progress in one platform.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift p-4 text-center">
            <div class="mb-3"><i class="bi bi-cash-coin fs-1 text-danger"></i></div>
            <h5 class="fw-bold">Faster Disbursement</h5>
            <p class="text-muted">Automated workflows speed up approval and fund release.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift p-4 text-center">
            <div class="mb-3"><i class="bi bi-graph-up fs-1 text-primary"></i></div>
            <h5 class="fw-bold">Data‑Driven Reports</h5>
            <p class="text-muted">Institutions access analytics and reports for better decision‑making.</p>
            </div>
            </div>
            </div>
        </div>
    </section>
    
    
    <!-- Testimonial Section -->
    <section id="testimonials" class="py-5 bg-bright">
        <div class="container text-center">
            <h2 class="fw-bold">What Students Say</h2>
            <p class="mt-3 mb-5">Real experiences from scholarship beneficiaries.</p>
            
            <style>
                .feedback-scroll {
                    display: flex;
                    overflow-x: auto;
                    gap: 1.5rem;
                    padding-bottom: 1rem;
                    -webkit-overflow-scrolling: touch;
                }
                .feedback-scroll::-webkit-scrollbar {
                    display: none;
                }
                .feedback-card {
                    flex: 0 0 auto;
                    width: 350px;
                    text-align: left;
                    display: flex;
                    flex-direction: column;
                }
                .feedback-profile-img {
                    width: 50px !important;
                    height: 50px !important;
                    border-radius: 50%;
                    object-fit: cover;
                    margin-right: 15px;
                    flex-shrink: 0;
                }
            </style>

            <div class="feedback-scroll">
                <?php
                $feed_sql = "SELECT f.feedback_text, s.stu_fname, s.stu_lname, s.stu_program, s.stu_profilepic 
                             FROM feedback f 
                             INNER JOIN student_master s ON f.stu_id = s.stu_id 
                             WHERE f.status = 'Approved' 
                             ORDER BY f.created_at DESC";
                $feed_res = $conn->query($feed_sql);

                if ($feed_res && $feed_res->num_rows > 0) {
                    while ($feed = $feed_res->fetch_assoc()) {
                        $default_avatar = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="#e9ecef" stroke="#ced4da" stroke-width="2"><circle cx="50" cy="50" r="48"/><path d="M50 50c-11 0-20-9-20-20s9-20 20-20 20 9 20 20-9 20-20 20zm0 10c-16 0-35 8-35 25v5h70v-5c0-17-19-25-35-25z" fill="#adb5bd"/></svg>');
                        $img_src = (!empty($feed['stu_profilepic']) && file_exists("uploads/profile_photos/" . $feed['stu_profilepic'])) 
                            ? "uploads/profile_photos/" . $feed['stu_profilepic'] 
                            : $default_avatar;
                ?>
                <div class="card shadow-sm p-4 h-100 feedback-card">
                    <p class="fst-italic mb-4">“<?php echo htmlspecialchars($feed['feedback_text']); ?>”</p>
                    <div class="d-flex align-items-center mt-auto">
                        <img src="<?php echo $img_src; ?>" alt="Profile" class="feedback-profile-img">
                        <div>
                            <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($feed['stu_fname'] . ' ' . $feed['stu_lname']); ?></h6>
                            <small class="text-muted"><?php echo htmlspecialchars($feed['stu_program']); ?></small>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p class='text-muted w-100 text-center'>No feedback available yet.</p>";
                }
                ?>
            </div>
            
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const scrollContainer = document.querySelector('.feedback-scroll');
                    if(scrollContainer) {
                        let isPaused = false;
                        let scrollSpeed = 1; 
                        
                        function autoScroll() {
                            if(!isPaused) {
                                scrollContainer.scrollLeft += scrollSpeed;
                                // Reset to beginning if at the end
                                if (Math.ceil(scrollContainer.scrollLeft) >= (scrollContainer.scrollWidth - scrollContainer.clientWidth)) {
                                    scrollContainer.scrollLeft = 0;
                                }
                            }
                            requestAnimationFrame(autoScroll);
                        }
                        
                        // Pause on hover
                        scrollContainer.addEventListener('mouseenter', () => isPaused = true);
                        scrollContainer.addEventListener('mouseleave', () => isPaused = false);
                        
                        // Start auto scroll
                        requestAnimationFrame(autoScroll);
                    }
                });
            </script>
        </div>
    </section>

    <script>
        // Counter Animation for Impact Section
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll('.impact-number');
            const speed = 200; // The lower the slower

            const animateCounters = () => {
                counters.forEach(counter => {
                    const updateCount = () => {
                        const target = +counter.getAttribute('data-target');
                        if (!target || target === 0) {
                            counter.innerText = '0';
                            return;
                        }
                        const currentText = counter.innerText.replace(/,/g, '');
                        const count = +currentText || 0;

                        // Calculate increment
                        const inc = target / speed;

                        if (count < target) {
                            counter.innerText = Math.ceil(count + inc).toLocaleString();
                            setTimeout(updateCount, 10);
                        } else {
                            counter.innerText = target.toLocaleString();
                        }
                    };
                    updateCount();
                });
            };

            // Use Intersection Observer to trigger when section comes into view
            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        obs.disconnect(); // Only animate once
                    }
                });
            }, { threshold: 0.5 });

            const statsSection = document.getElementById('statistics');
            if (statsSection) observer.observe(statsSection);
        });
    </script>
  <?php include 'footer.php'; ?>