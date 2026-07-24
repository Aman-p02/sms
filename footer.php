  <!-- Footer -->
    <footer id="contact" class="bg-dark text-white pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5 mb-4 mb-md-0">
                    <h4 class="fw-bold text-warning mb-3"><i class="bi bi-mortarboard-fill me-2"></i>ScholarshipMS</h4>
                    <p class="text-secondary pe-md-4">A complete digital platform for seamless scholarship application, evaluation, and disbursement. Empowering education everywhere.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-white bg-secondary bg-opacity-25 p-2 rounded-circle hover-opacity"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white bg-secondary bg-opacity-25 p-2 rounded-circle hover-opacity"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-white bg-secondary bg-opacity-25 p-2 rounded-circle hover-opacity"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white bg-secondary bg-opacity-25 p-2 rounded-circle hover-opacity"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_PATH; ?>index.php" class="text-secondary text-decoration-none text-white-hover"><i class="bi bi-chevron-right small me-1"></i>Home</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_PATH; ?>index.php#about" class="text-secondary text-decoration-none text-white-hover"><i class="bi bi-chevron-right small me-1"></i>About</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_PATH; ?>index.php#features" class="text-secondary text-decoration-none text-white-hover"><i class="bi bi-chevron-right small me-1"></i>Features</a></li>
                        <li class="mb-2"><a href="#contact" class="text-secondary text-decoration-none text-white-hover"><i class="bi bi-chevron-right small me-1"></i>Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold mb-3">Get in Touch</h5>
                    <ul class="list-unstyled text-secondary">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="bi bi-envelope-fill text-warning me-3 fs-5"></i> 
                            <span>support@scholarshipms.com</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="bi bi-telephone-fill text-warning me-3 fs-5"></i> 
                            <span>+123 456 7890</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill text-warning me-3 fs-5"></i> 
                            <span>123 University Ave, City, Country</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="text-center border-top border-secondary pt-4 mt-4 d-flex justify-content-center align-items-center">
                <p class="mb-0 text-secondary">&copy; <?php echo date("Y"); ?> Scholarship Management System. All rights reserved.</p>
                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="btn btn-warning position-fixed bottom-0 end-0 m-4 rounded-circle shadow d-flex justify-content-center align-items-center" style="width: 50px; height: 50px; z-index: 1000;" title="Scroll to Top">
                    <i class="bi bi-arrow-up text-dark fs-5 fw-bold"></i>
                </button>
            </div>
        </div>
    </footer>

    <style>
        .hover-opacity:hover { opacity: 0.8; transition: 0.3s; }
        .text-white-hover:hover { color: white !important; transition: 0.3s; }
    </style>

    <script src="<?php echo BASE_PATH; ?>assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.toggle-password');
            if(btn) {
                const container = btn.closest('.position-relative');
                const input = container ? container.querySelector('input') : null;
                const icon = btn.querySelector('i');
                if (input && input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else if (input && input.type === 'text') {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    </script>
    
    <?php include_once 'chatbot_widget.php'; ?>
    </body>
</html>
