<?php include 'header.php'; ?>
    
<?php
session_start();
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
            header("Location: stu_dashboard.php");
            
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
                            <h3 class="text-center mb-4 fw-bold">Student Login</h3>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="loginID" class="form-label">Enrollment No</label>
                                    <input type="text" class="form-control" id="loginID" name="stu_enroll" placeholder="Enter Enrollment No." />
                                </div>
                                
                                
                                <div class="mb-3">
                                    <label for="loginPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="loginPassword" name = "stu_pass" placeholder="Enter your password" />
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100" 
                                name="submit" style="margin-bottom:10px;">Login</button>
                                
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

    

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container">
            <!-- Notice Board Rectangle -->
            <?php if (!empty($notices)): ?>
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-header bg-danger text-white fw-bold fs-5">
                    📢 NOTICE BOARD - LATEST SCHOLARSHIPS
                </div>
                <div class="card-body p-0">
                    <marquee behavior="scroll" direction="up" height="200" onmouseover="this.stop();" onmouseout="this.start();" class="p-3">
                        <ul class="list-unstyled mb-0">
                        <?php foreach($notices as $notice): ?>
                            <li class="mb-4 border-bottom pb-3">
                                <h5 class="fw-bold text-primary mb-2">✨ <?php echo htmlspecialchars($notice['ss_name']); ?></h5>
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark border p-2 me-2">Start: <?php echo date("d M Y", strtotime($notice['ss_start'])); ?></span>
                                    <span class="badge bg-warning text-dark border p-2">End: <?php echo date("d M Y", strtotime($notice['ss_end'])); ?></span>
                                </div>
                                <div class="text-success fw-semibold">👉 <em>Login to Student Portal to apply online!</em></div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </marquee>
                </div>
            </div>
            <?php endif; ?>

            <h2 class="fw-bold">Key Features</h2>
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Online Application</h5>
                            <p class="card-text">Students can apply from anywhere with simplified online form submission.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Automated Verification</h5>
                            <p class="card-text">Effortless document verification and eligibility checking using digital tools.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Smart Evaluation</h5>
                            <p class="card-text">AI/ML-assisted scoring ensures fairness and transparency in selection.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Apply Section -->
    <section id="apply" class="py-5 text-center">
        <div class="container">
            <h2 class="fw-bold">Apply for Scholarship</h2>
            <p class="mt-3">Start your scholarship journey by filling out the online application form.</p>
            <a href="#" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#signinModal">Start Application</a>
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