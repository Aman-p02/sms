<?php include 'header.php'; ?>
    
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
$applicant_res = $conn->query("SELECT COUNT(*) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id");
$total_applicants = $applicant_res->fetch_assoc()['cnt'] ?? 0;

// Approved Scholarships
$approved_ss_res = $conn->query("SELECT COUNT(*) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id WHERE s.app_status = 'Approved'");
$approved_scholarships = $approved_ss_res->fetch_assoc()['cnt'] ?? 0;

// Approved Candidates
$approved_cand_res = $conn->query("SELECT COUNT(*) as cnt FROM scholarship s JOIN student_master sm ON s.stu_id = sm.stu_id JOIN ss_master ss ON s.ss_id = ss.ss_id WHERE s.app_status = 'Approved'");
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
       
    
<!------------------ BODY ---------------------->
    <!-- Hero Section -->
    <section class="py-5 text-center bg-white border-bottom">
        <div class="container pt-4 pb-3">
            <h1 class="display-4 fw-bold text-primary mb-3"><i class="bi bi-mortarboard-fill me-2"></i>Scholarship Management System</h1>
            <p class="lead fw-medium text-muted">Digitizing scholarship applications, verification, evaluation, and beneficiary management.</p>
        </div>
    </section>

    <!-- About + Login Two Column Section -->
    <section id="about-login" class="py-5 bg-white">
        <div class="container py-4">
            <div class="row g-5 align-items-center">

                <!-- Left Column: About Section -->
                <div class="col-lg-6 pe-lg-5">
                    <h2 class="fw-bold text-dark mb-4">About the Scholarship Program <i class="bi bi-info-circle text-primary fs-4 ms-2"></i></h2>
                    <p class="text-secondary lh-lg fs-5">A Scholarship Management System significantly enhances the efficiency and fairness of the scholarship lifecycle by digitizing application, evaluation, and disbursement processes. Its automated workflows reduce manual errors, improve data accuracy, and ensure timely communication with applicants.</p>
                    <p class="text-secondary lh-lg fs-5">The system also fosters transparency through standardized scoring mechanisms, minimizing human bias and supporting equitable decision-making. Furthermore, its integrated document management and analytics capabilities enable institutions to monitor trends, track fund utilization, and generate audit-ready reports.</p>
                </div>

                <!-- Right Column: Login Card -->
                <div class="col-lg-6 d-flex justify-content-center">
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
                                
                                <div class="form-floating mb-4">
                                    <input type="password" class="form-control rounded-3" id="loginPassword" name="stu_pass" placeholder="Enter your password" required />
                                    <label for="loginPassword"><i class="bi bi-key text-muted me-1"></i> Password</label>
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
                <div class="card-body p-0 bg-white">
                    <marquee behavior="scroll" direction="up" height="400" scrollamount="3" onmouseover="this.stop();" onmouseout="this.start();" class="p-4">
                        <ul class="list-unstyled mb-0">
                        <?php foreach($notices as $notice): ?>
                            <li class="mb-4 pb-4 border-bottom position-relative">
                                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-award-fill text-warning me-2"></i> <?php echo htmlspecialchars($notice['ss_name']); ?></h5>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-calendar-event me-1"></i> Start: <?php echo date("d M Y", strtotime($notice['ss_start'])); ?></span>
                                    <span class="badge bg-warning text-dark border-0 px-3 py-2 rounded-pill"><i class="bi bi-calendar-x me-1"></i> End: <?php echo date("d M Y", strtotime($notice['ss_end'])); ?></span>
                                    <a href="index.php?redirect=apply#about-login" class="btn btn-success btn-sm fw-bold rounded-pill px-3 shadow-sm ms-2"><i class="bi bi-box-arrow-in-right me-1"></i> Apply</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
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
                    <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift text-center p-3">
                        <div class="card-body">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-4">
                                <i class="bi bi-laptop text-primary fs-2"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Online Application</h4>
                            <p class="card-text text-muted">Students can apply securely from anywhere with a simplified online form submission process.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift text-center p-3">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-4">
                                <i class="bi bi-shield-check text-success fs-2"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Automated Verification</h4>
                            <p class="card-text text-muted">Effortless document verification and eligibility checking using integrated digital tools.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift text-center p-3">
                        <div class="card-body">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-4">
                                <i class="bi bi-cpu text-info fs-2"></i>
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
    <section id="statistics" class="py-5 bg-white text-center border-top border-bottom">
        <div class="container">
            <h2 class="fw-bold text-dark display-6 mb-5">Program Impact</h2>
            
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 p-4 h-100 hover-lift">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3 mx-auto">
                            <i class="bi bi-award text-primary fs-3"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-3">Total Scholarships</h5>
                        <h2 class="fw-bold text-dark display-5"><?php echo number_format($total_scholarships); ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 p-4 h-100 hover-lift">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3 mx-auto">
                            <i class="bi bi-file-earmark-text text-primary fs-3"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-3">Total Applications</h5>
                        <h2 class="fw-bold text-dark display-5"><?php echo number_format($total_applications); ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 p-4 h-100 hover-lift">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3 mx-auto">
                            <i class="bi bi-people text-primary fs-3"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-3">Total Applicants</h5>
                        <h2 class="fw-bold text-dark display-5"><?php echo number_format($total_applicants); ?></h2>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 p-4 h-100 hover-lift">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3 mx-auto">
                            <i class="bi bi-check-circle text-success fs-3"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-3">Approved Scholarships</h5>
                        <h2 class="fw-bold text-success display-5"><?php echo number_format($approved_scholarships); ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 p-4 h-100 hover-lift">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3 mx-auto">
                            <i class="bi bi-person-check text-success fs-3"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-3">Approved Candidates</h5>
                        <h2 class="fw-bold text-success display-5"><?php echo number_format($approved_candidates); ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 p-4 h-100 hover-lift">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3 mx-auto">
                            <i class="bi bi-cash-stack text-success fs-3"></i>
                        </div>
                        <h5 class="text-muted fw-bold mb-3">Total Funds Disbursed</h5>
                        <h2 class="fw-bold text-success display-5"><?php echo number_format($total_approved_amount); ?></h2>
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
                <input type="password" class="form-control" placeholder="Enter your password">
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
            <div class="row g-4">
            <div class="col-md-4">
            <div class="card h-100 shadow-sm p-4">
            <h5 class="fw-bold">✔ Streamlined Application</h5>
            <p>Students can apply online anytime, reducing paperwork and long queues.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm p-4">
            <h5 class="fw-bold">✔ Automated Verification</h5>
            <p>Documents and eligibility are checked digitally, saving administrative time.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm p-4">
            <h5 class="fw-bold">✔ Transparency & Fairness</h5>
            <p>Real‑time updates and AI‑assisted evaluation ensure fair selection.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm p-4">
            <h5 class="fw-bold">✔ Centralized Tracking</h5>
            <p>Both students and administrators can monitor progress in one platform.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm p-4">
            <h5 class="fw-bold">✔ Faster Disbursement</h5>
            <p>Automated workflows speed up approval and fund release.</p>
            </div>
            </div>
            <div class="col-md-4">
            <div class="card h-100 shadow-sm p-4">
            <h5 class="fw-bold">✔ Data‑Driven Reports</h5>
            <p>Institutions access analytics and reports for better decision‑making.</p>
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
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    object-fit: cover;
                    margin-right: 15px;
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
                        $img_src = (!empty($feed['stu_profilepic']) && file_exists("uploads/profile_photos/" . $feed['stu_profilepic'])) 
                            ? "uploads/profile_photos/" . $feed['stu_profilepic'] 
                            : "https://ui-avatars.com/api/?name=" . urlencode($feed['stu_fname'] . ' ' . $feed['stu_lname']) . "&background=0D8ABC&color=fff";
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

  <?php include 'footer.php'; ?>