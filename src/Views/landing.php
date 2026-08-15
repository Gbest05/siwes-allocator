<?php
$pageTitle = 'Home';
require __DIR__ . '/layouts/header.php';
require __DIR__ . '/layouts/navbar.php';
$baseUrl = (require __DIR__ . '/../../config/app.php')['base_url'];
?>

<!-- 1. HERO SECTION -->
<section class="hero-section text-white d-flex align-items-center" style="background: linear-gradient(135deg, rgba(17, 24, 39, 0.88), rgba(22, 101, 52, 0.92)), url('<?= $baseUrl ?>/<?= htmlspecialchars($settings['hero_image'] ?? 'images/hero.jpg') ?>') center/cover no-repeat;">
    <div class="container position-relative z-2">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <div class="badge bg-emerald text-white px-3 py-2 rounded-pill fw-bold text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="fa-solid fa-graduation-cap me-1"></i> <?= htmlspecialchars($settings['hero_badge'] ?? 'Digital SIWES Management Platform') ?>
                </div>
                <h1 class="hero-title mb-4">
                    <?= htmlspecialchars($settings['hero_title'] ?? 'Simplifying SIWES Placement and Allocation') ?>
                </h1>
                <p class="hero-subtitle mb-4">
                    <?= htmlspecialchars($settings['hero_description'] ?? 'A smart digital platform for managing student SIWES registration, company placement, allocation, and monitoring efficiently.') ?>
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= $baseUrl ?>/index.php?route=register" class="btn btn-green btn-lg px-4 py-3">
                        <i class="fa-solid fa-user-plus me-2"></i> Get Started
                    </a>
                    <a href="<?= $baseUrl ?>/index.php?route=login" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Student Login
                    </a>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="card-custom p-4 bg-white text-dark-charcoal shadow-lg border-0">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                        <h5 class="fw-bold mb-0 text-dark-green"><i class="fa-solid fa-diagram-project me-2 text-primary-green"></i> Smart Allocation Match</h5>
                        <span class="badge bg-light-green fw-bold">Active Engine</span>
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark fs-7">Interswitch Group Hub</span>
                            <span class="match-score-badge">95% Match</span>
                        </div>
                        <small class="text-muted d-block mb-2">Computer Science &bull; Fintech & Cyber Security</small>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary-green" role="progressbar" style="width: 95%"></div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark fs-7">Technovate Solutions</span>
                            <span class="match-score-badge">90% Match</span>
                        </div>
                        <small class="text-muted d-block mb-2">Software Development &bull; Lagos Island</small>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary-green" role="progressbar" style="width: 90%"></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 text-muted fs-7 mt-2">
                        <i class="fa-solid fa-circle-check text-primary-green"></i>
                        <span>Automated compatibility calculation based on 4 criteria metrics</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. ANIMATED STATISTICS SECTION -->
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <h2 class="display-5 fw-extrabold text-primary-green mb-1 counter-val" data-target="<?= $totalStudents ?? 420 ?>">0</h2>
                    <p class="text-secondary fw-semibold mb-0">Registered Students</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <h2 class="display-5 fw-extrabold text-dark-green mb-1 counter-val" data-target="<?= $totalCompanies ?? 85 ?>">0</h2>
                    <p class="text-secondary fw-semibold mb-0">Partner Companies</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <h2 class="display-5 fw-extrabold text-primary-green mb-1 counter-val" data-target="<?= $totalAllocated ?? 360 ?>">0</h2>
                    <p class="text-secondary fw-semibold mb-0">Students Allocated</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <h2 class="display-5 fw-extrabold text-warning mb-1 counter-val" data-target="<?= $pendingApps ?? 24 ?>">0</h2>
                    <p class="text-secondary fw-semibold mb-0">Pending Applications</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. ABOUT SIWES SECTION -->
<section id="about" class="py-6" style="padding: 5rem 0;">
    <div class="container">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="<?= $baseUrl ?>/<?= htmlspecialchars($settings['about_image'] ?? 'images/about.jpg') ?>" alt="About SIWES Allocation" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 420px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 m-4 p-3 bg-white rounded-3 shadow-lg border-start border-4 border-success d-none d-sm-block" style="max-width: 280px;">
                        <h6 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-shield-halved me-1"></i> Paperless Workflow</h6>
                        <small class="text-muted">Digital verification and instant letter generation for coordinators.</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="badge bg-light-green text-dark-green px-3 py-2 rounded-pill fw-bold mb-3"><?= htmlspecialchars($settings['about_badge'] ?? 'About SIWES Portal') ?></div>
                <h2 class="fw-bold mb-3 text-dark-charcoal display-6"><?= htmlspecialchars($settings['about_title'] ?? 'Modernizing Industrial Training Placement') ?></h2>
                <p class="text-secondary mb-4">
                    <?= htmlspecialchars($settings['about_description_1'] ?? 'The Student Industrial Work Experience Scheme (SIWES) is a mandatory skills training program designed to bridge the gap between theoretical knowledge acquired in institutions and practical industrial work environment experience.') ?>
                </p>
                <p class="text-secondary mb-4">
                    <?= htmlspecialchars($settings['about_description_2'] ?? 'Our digital platform eliminates manual application delays, paper file loss, and allocation bias through a multi-factor smart matching engine that pairs students with partner organizations based on department relevance, preferred industry, and geographical location.') ?>
                </p>
                
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 border">
                            <i class="fa-solid fa-clock-rotate-left fs-3 text-primary-green"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Instant Matching</h6>
                                <small class="text-muted">Calculates score in seconds</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 border">
                            <i class="fa-solid fa-file-pdf fs-3 text-primary-green"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Digital Letters</h6>
                                <small class="text-muted">Official allocation PDFs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. KEY FEATURES SECTION -->
<section id="features" class="py-6 bg-light" style="padding: 5rem 0;">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <div class="badge bg-light-green text-dark-green px-3 py-2 rounded-pill fw-bold mb-2">Platform Features</div>
            <h2 class="fw-bold display-6">Comprehensive SIWES Management Suite</h2>
            <p class="text-secondary">Designed specifically for polytechnic and university industrial training units.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-custom p-4 h-100">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Student Registration</h5>
                    <p class="text-secondary fs-7 mb-0">Seamless self-service student profile creation, matric verification, department mapping, and password security.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom p-4 h-100">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <h5 class="fw-bold mb-2">SIWES Application</h5>
                    <p class="text-secondary fs-7 mb-0">Students submit placement requests detailing preferred industry domain, target city/state, and special interest notes.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom p-4 h-100">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Company Management</h5>
                    <p class="text-secondary fs-7 mb-0">Centralized database of partner companies, industry categories, contact personnel, and real-time available capacity slots.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom p-4 h-100">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-diagram-project"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Smart Student Allocation</h5>
                    <p class="text-secondary fs-7 mb-0">Intelligent 100-point score recommendation algorithm evaluating department, industry, location, and slot availability.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom p-4 h-100">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Document Management</h5>
                    <p class="text-secondary fs-7 mb-0">Secure upload and review of application letters, student identity cards, logbooks, and supervisor reports.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom p-4 h-100">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Reports & Analytics</h5>
                    <p class="text-secondary fs-7 mb-0">Interactive Chart.js visualizations, department distribution reports, allocation summaries, and CSV dataset downloads.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. HOW IT WORKS SECTION -->
<section id="how-it-works" class="py-6 bg-white" style="padding: 5rem 0;">
    <div class="container">
        <div class="text-center mb-5">
            <div class="badge bg-light-green text-dark-green px-3 py-2 rounded-pill fw-bold mb-2">Simple Process</div>
            <h2 class="fw-bold display-6">How The Allocation Engine Works</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="p-3">
                    <div class="rounded-circle bg-dark-green text-white mx-auto d-flex align-items-center justify-content-center fw-bold fs-4 mb-3" style="width: 64px; height: 64px;">01</div>
                    <h5 class="fw-bold text-dark">Register</h5>
                    <p class="text-secondary fs-7">Student creates an account with matriculation details and level.</p>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="p-3">
                    <div class="rounded-circle bg-primary-green text-white mx-auto d-flex align-items-center justify-content-center fw-bold fs-4 mb-3" style="width: 64px; height: 64px;">02</div>
                    <h5 class="fw-bold text-dark">Apply</h5>
                    <p class="text-secondary fs-7">Submit SIWES application indicating preferred industry & location.</p>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="p-3">
                    <div class="rounded-circle bg-emerald text-white mx-auto d-flex align-items-center justify-content-center fw-bold fs-4 mb-3" style="width: 64px; height: 64px;">03</div>
                    <h5 class="fw-bold text-dark">Allocation</h5>
                    <p class="text-secondary fs-7">Smart engine calculates match score and coordinator confirms placement.</p>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="p-3">
                    <div class="rounded-circle bg-dark-charcoal text-white mx-auto d-flex align-items-center justify-content-center fw-bold fs-4 mb-3" style="width: 64px; height: 64px;">04</div>
                    <h5 class="fw-bold text-dark">Track</h5>
                    <p class="text-secondary fs-7">Download official allocation letter and monitor placement progress.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6. WHY USE OUR SYSTEM -->
<section id="why-us" class="py-6 bg-light" style="padding: 5rem 0;">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <h2 class="fw-bold display-6 mb-4 text-dark-charcoal">Why Institutions Choose Our SIWES System</h2>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-light-green text-dark-green d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Faster Allocation Turnaround</h6>
                            <p class="text-secondary fs-7 mb-0">Reduces placement processing time from several weeks to just a few minutes.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-light-green text-dark-green d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Reduced Allocation Errors & Favoritism</h6>
                            <p class="text-secondary fs-7 mb-0">Transparent score ranking guarantees objective matching based on academic relevance.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-light-green text-dark-green d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-file-shield"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Centralized Digital Records</h6>
                            <p class="text-secondary fs-7 mb-0">All student documents, supervisor notes, and allocation histories safely stored in PostgreSQL.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <!-- Testimonials Card -->
                <div class="card-custom p-4 bg-white">
                    <h5 class="fw-bold mb-4 text-dark-green"><i class="fa-solid fa-quote-left me-2"></i> User Testimonials</h5>
                    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <p class="fst-italic text-secondary">"The smart allocation score helped me get placed at Interswitch Group in Lagos. The process was completely seamless and transparent!"</p>
                                <div class="fw-bold text-dark">— Fatima Abubakar (ND Student, Computer Science)</div>
                            </div>
                            <div class="carousel-item">
                                <p class="fst-italic text-secondary">"Managing over 400 student SIWES placements manually was overwhelming. This system handles slot tracking and department matching effortlessly."</p>
                                <div class="fw-bold text-dark">— Dr. Mrs. Amina Yusuf (SIWES Coordinator)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. CALL TO ACTION SECTION -->
<section class="py-6 text-white text-center" style="background: var(--green-gradient); padding: 5rem 0;">
    <div class="container">
        <h2 class="display-6 fw-bold mb-3"><?= htmlspecialchars($settings['cta_title'] ?? 'Ready to simplify SIWES management?') ?></h2>
        <p class="fs-5 text-white-50 max-w-600 mx-auto mb-4"><?= htmlspecialchars($settings['cta_description'] ?? 'Experience a faster, paperless, and intelligent student industrial work experience scheme.') ?></p>
        <a href="<?= $baseUrl ?>/index.php?route=register" class="btn btn-light btn-lg text-dark-green fw-bold px-5 py-3 rounded-pill shadow-lg">
            Start Your Application <i class="fa-solid fa-arrow-right ms-2"></i>
        </a>
    </div>
</section>

<!-- 8. FOOTER SECTION -->
<footer class="bg-dark-charcoal text-white pt-5 pb-4">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-5">
                <a class="navbar-brand d-flex align-items-center gap-2 text-white mb-3" href="#">
                    <?php if (!empty($settings['site_logo'])): ?>
                        <img src="<?= $baseUrl ?>/<?= htmlspecialchars($settings['site_logo']) ?>" alt="Logo" style="height: 36px; max-width: 120px; object-fit: contain;">
                    <?php else: ?>
                        <div class="bg-primary-green text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    <?php endif; ?>
                    <span class="fs-4"><?= htmlspecialchars($settings['site_name'] ?? 'SIWES Allocator') ?></span>
                </a>
                <p class="text-secondary fs-7 pe-lg-4">
                    <?= htmlspecialchars($settings['footer_description'] ?? 'An advanced enterprise management system designed for polytechnic and university SIWES units, supporting student registration, company slot management, and intelligent allocation.') ?>
                </p>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="fw-bold text-white mb-3">Quick Navigation</h6>
                <ul class="list-unstyled fs-7 text-secondary">
                    <li class="mb-2"><a href="<?= $baseUrl ?>/index.php?route=home" class="text-secondary text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="#about" class="text-secondary text-decoration-none">About SIWES</a></li>
                    <li class="mb-2"><a href="#features" class="text-secondary text-decoration-none">Features</a></li>
                    <li class="mb-2"><a href="#how-it-works" class="text-secondary text-decoration-none">How It Works</a></li>
                    <li class="mb-2"><a href="#why-us" class="text-secondary text-decoration-none">Why Us</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-4">
                <h6 class="fw-bold text-white mb-3">Contact Information</h6>
                <ul class="list-unstyled fs-7 text-secondary">
                    <li class="mb-2"><i class="fa-solid fa-building me-2 text-primary-green"></i> <?= htmlspecialchars($settings['contact_address'] ?? 'SIWES Directorate, Admin Block') ?></li>
                    <li class="mb-2"><i class="fa-solid fa-envelope me-2 text-primary-green"></i> <?= htmlspecialchars($settings['contact_email'] ?? 'siwes@institution.edu.ng') ?></li>
                    <li class="mb-2"><i class="fa-solid fa-phone me-2 text-primary-green"></i> <?= htmlspecialchars($settings['contact_phone'] ?? '+234 803 123 4567') ?></li>
                </ul>
            </div>
        </div>
        <div class="border-top border-secondary border-opacity-25 pt-4 d-flex flex-column flex-sm-row align-items-center justify-content-between text-secondary fs-7">
            <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name'] ?? 'SIWES Allocation Management System') ?>. All rights reserved.</p>
            <div class="d-flex gap-3 mt-2 mt-sm-0">
                <a href="#" class="text-secondary text-decoration-none">Privacy Policy</a>
                <a href="#" class="text-secondary text-decoration-none">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<?php require __DIR__ . '/layouts/footer.php'; ?>
