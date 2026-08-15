<?php
$pageTitle = 'Home';
require __DIR__ . '/layouts/header.php';
require __DIR__ . '/layouts/navbar.php';
$baseUrl = (require __DIR__ . '/../../config/app.php')['base_url'];
?>

<!-- 1. HERO SECTION -->
<section class="hero-section text-white d-flex align-items-center" style="background: var(--hero-gradient), url('<?= $baseUrl ?>/<?= htmlspecialchars($settings['hero_image'] ?? 'images/hero.jpg') ?>') center/cover no-repeat;">
    <div class="container position-relative z-2">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <div class="badge bg-emerald text-white px-3 py-2 rounded-pill fw-bold text-uppercase mb-3 d-inline-flex align-items-center gap-1" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    <i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($settings['hero_badge'] ?? 'Digital SIWES Management Platform') ?>
                </div>
                <h1 class="hero-title mb-3 mb-md-4">
                    <?= htmlspecialchars($settings['hero_title'] ?? 'Simplifying SIWES Placement and Allocation') ?>
                </h1>
                <p class="hero-subtitle mb-4">
                    <?= htmlspecialchars($settings['hero_description'] ?? 'A smart digital platform for managing student SIWES registration, company placement, allocation, and monitoring efficiently.') ?>
                </p>
                <div class="hero-btn-group">
                    <a href="<?= $baseUrl ?>/index.php?route=register" class="btn btn-green btn-lg">
                        <i class="fa-solid fa-user-plus me-2"></i> Get Started
                    </a>
                    <a href="<?= $baseUrl ?>/index.php?route=login" class="btn btn-outline-light btn-lg fw-semibold">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Student Login
                    </a>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="hero-match-card text-dark-charcoal">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                        <h5 class="fw-bold mb-0 text-dark-green d-flex align-items-center fs-6">
                            <i class="fa-solid fa-diagram-project me-2 text-primary-green"></i> Smart Allocation Match
                        </h5>
                        <span class="badge bg-light-green text-dark-green fw-bold">Active Engine</span>
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-1">
                            <span class="fw-bold text-dark fs-7">Interswitch Group Hub</span>
                            <span class="match-score-badge">95% Match</span>
                        </div>
                        <small class="text-secondary d-block mb-2 fs-8">Computer Science &bull; Fintech & Cyber Security</small>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary-green" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-1">
                            <span class="fw-bold text-dark fs-7">Technovate Solutions</span>
                            <span class="match-score-badge">90% Match</span>
                        </div>
                        <small class="text-secondary d-block mb-2 fs-8">Software Development &bull; Lagos Island</small>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary-green" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 text-muted fs-8 mt-2">
                        <i class="fa-solid fa-circle-check text-primary-green flex-shrink-0"></i>
                        <span>Automated compatibility calculation based on 4 criteria metrics</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. ANIMATED STATISTICS SECTION -->
<section class="py-4 py-md-5 bg-white border-bottom">
    <div class="container">
        <div class="row g-3 g-md-4 text-center">
            <div class="col-6 col-lg-3">
                <div class="stat-box">
                    <h2 class="stat-counter-number text-primary-green counter-val" data-target="<?= $totalStudents ?? 420 ?>">0</h2>
                    <p class="stat-counter-label">Registered Students</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-box">
                    <h2 class="stat-counter-number text-dark-green counter-val" data-target="<?= $totalCompanies ?? 85 ?>">0</h2>
                    <p class="stat-counter-label">Partner Companies</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-box">
                    <h2 class="stat-counter-number text-primary-green counter-val" data-target="<?= $totalAllocated ?? 360 ?>">0</h2>
                    <p class="stat-counter-label">Students Allocated</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-box">
                    <h2 class="stat-counter-number text-warning counter-val" data-target="<?= $pendingApps ?? 24 ?>">0</h2>
                    <p class="stat-counter-label">Pending Applications</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. ABOUT SIWES SECTION -->
<section id="about" class="section-padding">
    <div class="container">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="<?= $baseUrl ?>/<?= htmlspecialchars($settings['about_image'] ?? 'images/about.jpg') ?>" alt="About SIWES Allocation" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 420px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 m-3 m-md-4 p-3 bg-white rounded-3 shadow-lg border-start border-4 border-success d-none d-sm-block" style="max-width: 280px;">
                        <h6 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-shield-halved me-1"></i> Paperless Workflow</h6>
                        <small class="text-secondary fs-8">Digital verification and instant letter generation for coordinators.</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="badge bg-light-green text-dark-green px-3 py-2 rounded-pill fw-bold mb-3 d-inline-block"><?= htmlspecialchars($settings['about_badge'] ?? 'About SIWES Portal') ?></div>
                <h2 class="section-title mb-3 text-dark-charcoal"><?= htmlspecialchars($settings['about_title'] ?? 'Modernizing Industrial Training Placement') ?></h2>
                <p class="text-secondary mb-3 fs-7">
                    <?= htmlspecialchars($settings['about_description_1'] ?? 'The Student Industrial Work Experience Scheme (SIWES) is a mandatory skills training program designed to bridge the gap between theoretical knowledge acquired in institutions and practical industrial work environment experience.') ?>
                </p>
                <p class="text-secondary mb-4 fs-7">
                    <?= htmlspecialchars($settings['about_description_2'] ?? 'Our digital platform eliminates manual application delays, paper file loss, and allocation bias through a multi-factor smart matching engine that pairs students with partner organizations based on department relevance, preferred industry, and geographical location.') ?>
                </p>
                
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 border h-100 shadow-sm">
                            <i class="fa-solid fa-clock-rotate-left fs-3 text-primary-green flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark fs-7">Instant Matching</h6>
                                <small class="text-muted fs-8">Calculates score in seconds</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 border h-100 shadow-sm">
                            <i class="fa-solid fa-file-pdf fs-3 text-primary-green flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark fs-7">Digital Letters</h6>
                                <small class="text-muted fs-8">Official allocation PDFs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. KEY FEATURES SECTION -->
<section id="features" class="section-padding bg-light">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <div class="badge bg-light-green text-dark-green px-3 py-2 rounded-pill fw-bold mb-2">Platform Features</div>
            <h2 class="section-title">Comprehensive SIWES Management Suite</h2>
            <p class="section-subtitle">Designed specifically for polytechnic and university industrial training units.</p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom feature-card">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h5 class="fw-bold mb-2 fs-6">Student Registration</h5>
                    <p class="text-secondary fs-7 mb-0">Seamless self-service student profile creation, matric verification, department mapping, and password security.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom feature-card">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <h5 class="fw-bold mb-2 fs-6">SIWES Application</h5>
                    <p class="text-secondary fs-7 mb-0">Students submit placement requests detailing preferred industry domain, target city/state, and special interest notes.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom feature-card">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <h5 class="fw-bold mb-2 fs-6">Company Management</h5>
                    <p class="text-secondary fs-7 mb-0">Centralized database of partner companies, industry categories, contact personnel, and real-time available capacity slots.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom feature-card">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-diagram-project"></i>
                    </div>
                    <h5 class="fw-bold mb-2 fs-6">Smart Student Allocation</h5>
                    <p class="text-secondary fs-7 mb-0">Intelligent 100-point score recommendation algorithm evaluating department, industry, location, and slot availability.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom feature-card">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <h5 class="fw-bold mb-2 fs-6">Document Management</h5>
                    <p class="text-secondary fs-7 mb-0">Secure upload and review of application letters, student identity cards, logbooks, and supervisor reports.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom feature-card">
                    <div class="stat-icon bg-light-green text-dark-green mb-3">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                    <h5 class="fw-bold mb-2 fs-6">Reports & Analytics</h5>
                    <p class="text-secondary fs-7 mb-0">Interactive Chart.js visualizations, department distribution reports, allocation summaries, and CSV dataset downloads.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. HOW IT WORKS SECTION -->
<section id="how-it-works" class="section-padding bg-white">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <div class="badge bg-light-green text-dark-green px-3 py-2 rounded-pill fw-bold mb-2">Simple Process</div>
            <h2 class="section-title">How The Allocation Engine Works</h2>
            <p class="section-subtitle">Four straightforward steps from student sign-up to confirmed placement.</p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-sm-6 col-lg-3 text-center">
                <div class="process-card">
                    <div class="process-step-num bg-dark-green text-white">01</div>
                    <h5 class="fw-bold text-dark fs-6 mb-2">Register</h5>
                    <p class="text-secondary fs-7 mb-0">Student creates an account with matriculation details and level.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 text-center">
                <div class="process-card">
                    <div class="process-step-num bg-primary-green text-white">02</div>
                    <h5 class="fw-bold text-dark fs-6 mb-2">Apply</h5>
                    <p class="text-secondary fs-7 mb-0">Submit SIWES application indicating preferred industry & location.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 text-center">
                <div class="process-card">
                    <div class="process-step-num bg-emerald text-white">03</div>
                    <h5 class="fw-bold text-dark fs-6 mb-2">Allocation</h5>
                    <p class="text-secondary fs-7 mb-0">Smart engine calculates match score and coordinator confirms placement.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 text-center">
                <div class="process-card">
                    <div class="process-step-num bg-dark-charcoal text-white">04</div>
                    <h5 class="fw-bold text-dark fs-6 mb-2">Track</h5>
                    <p class="text-secondary fs-7 mb-0">Download official allocation letter and monitor placement progress.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6. WHY USE OUR SYSTEM -->
<section id="why-us" class="section-padding bg-light">
    <div class="container">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6">
                <div class="badge bg-light-green text-dark-green px-3 py-2 rounded-pill fw-bold mb-3 d-inline-block">Why SIWES Allocator</div>
                <h2 class="section-title mb-4 text-dark-charcoal">Why Institutions Choose Our SIWES System</h2>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-light-green text-dark-green d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark fs-7">Faster Allocation Turnaround</h6>
                            <p class="text-secondary fs-7 mb-0">Reduces placement processing time from several weeks to just a few minutes.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-light-green text-dark-green d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark fs-7">Reduced Allocation Errors & Favoritism</h6>
                            <p class="text-secondary fs-7 mb-0">Transparent score ranking guarantees objective matching based on academic relevance.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-light-green text-dark-green d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="fa-solid fa-file-shield"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark fs-7">Centralized Digital Records</h6>
                            <p class="text-secondary fs-7 mb-0">All student documents, supervisor notes, and allocation histories safely stored in PostgreSQL.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <!-- Testimonials Card -->
                <div class="card-custom p-4 bg-white shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <h5 class="fw-bold mb-0 text-dark-green fs-6">
                            <i class="fa-solid fa-quote-left me-2 text-primary-green"></i> User Testimonials
                        </h5>
                        <span class="badge bg-light-green text-dark-green fw-bold">Verified Feedback</span>
                    </div>
                    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <p class="fst-italic text-secondary fs-7 mb-3">"The smart allocation score helped me get placed at Interswitch Group in Lagos. The process was completely seamless and transparent!"</p>
                                <div class="fw-bold text-dark fs-7">— Fatima Abubakar <span class="text-muted fw-normal d-block fs-8">ND Student, Computer Science</span></div>
                            </div>
                            <div class="carousel-item">
                                <p class="fst-italic text-secondary fs-7 mb-3">"Managing over 400 student SIWES placements manually was overwhelming. This system handles slot tracking and department matching effortlessly."</p>
                                <div class="fw-bold text-dark fs-7">— Dr. Mrs. Amina Yusuf <span class="text-muted fw-normal d-block fs-8">SIWES Unit Coordinator</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. CALL TO ACTION SECTION -->
<section class="section-padding text-white text-center" style="background: var(--green-gradient);">
    <div class="container">
        <h2 class="section-title text-white mb-3"><?= htmlspecialchars($settings['cta_title'] ?? 'Ready to simplify SIWES management?') ?></h2>
        <p class="fs-6 text-white-50 max-w-600 mx-auto mb-4"><?= htmlspecialchars($settings['cta_description'] ?? 'Experience a faster, paperless, and intelligent student industrial work experience scheme.') ?></p>
        <a href="<?= $baseUrl ?>/index.php?route=register" class="btn btn-light btn-cta-large text-dark-green fw-bold">
            Start Your Application <i class="fa-solid fa-arrow-right ms-2"></i>
        </a>
    </div>
</section>

<!-- 8. FOOTER SECTION -->
<footer class="footer-section">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-12 col-lg-5 mb-3 mb-lg-0">
                <a class="navbar-brand d-inline-flex align-items-center gap-2 text-white mb-3" href="#">
                    <?php if (!empty($settings['site_logo'])): ?>
                        <img src="<?= $baseUrl ?>/<?= htmlspecialchars($settings['site_logo']) ?>" alt="Logo" class="navbar-brand-logo">
                    <?php else: ?>
                        <div class="navbar-brand-icon">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    <?php endif; ?>
                    <span class="text-white"><?= htmlspecialchars($settings['site_name'] ?? 'SIWES Allocator') ?></span>
                </a>
                <p class="text-secondary fs-7 pe-lg-4 mb-0">
                    <?= htmlspecialchars($settings['footer_description'] ?? 'An advanced enterprise management system designed for polytechnic and university SIWES units, supporting student registration, company slot management, and intelligent allocation.') ?>
                </p>
            </div>
            
            <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-sm-0">
                <h6 class="fw-bold text-white mb-3 fs-6">Quick Navigation</h6>
                <ul class="list-unstyled fs-7 text-secondary mb-0">
                    <li class="mb-2"><a href="<?= $baseUrl ?>/index.php?route=home" class="footer-link">Home</a></li>
                    <li class="mb-2"><a href="#about" class="footer-link">About SIWES</a></li>
                    <li class="mb-2"><a href="#features" class="footer-link">Features</a></li>
                    <li class="mb-2"><a href="#how-it-works" class="footer-link">How It Works</a></li>
                    <li class="mb-2"><a href="#why-us" class="footer-link">Why Us</a></li>
                </ul>
            </div>
            
            <div class="col-12 col-sm-6 col-lg-4">
                <h6 class="fw-bold text-white mb-3 fs-6">Contact Information</h6>
                <ul class="list-unstyled fs-7 text-secondary mb-0">
                    <li class="mb-2 footer-contact-item">
                        <i class="fa-solid fa-building me-2 text-primary-green"></i>
                        <span><?= htmlspecialchars($settings['contact_address'] ?? 'SIWES Directorate, Admin Block') ?></span>
                    </li>
                    <li class="mb-2 footer-contact-item">
                        <i class="fa-solid fa-envelope me-2 text-primary-green"></i>
                        <a href="mailto:<?= htmlspecialchars($settings['contact_email'] ?? 'siwes@institution.edu.ng') ?>" class="text-secondary text-decoration-none hover-emerald">
                            <?= htmlspecialchars($settings['contact_email'] ?? 'siwes@institution.edu.ng') ?>
                        </a>
                    </li>
                    <li class="mb-2 footer-contact-item">
                        <i class="fa-solid fa-phone me-2 text-primary-green"></i>
                        <a href="tel:<?= htmlspecialchars($settings['contact_phone'] ?? '+234 803 123 4567') ?>" class="text-secondary text-decoration-none">
                            <?= htmlspecialchars($settings['contact_phone'] ?? '+234 803 123 4567') ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-top border-secondary border-opacity-25 pt-4 d-flex flex-column flex-sm-row align-items-center justify-content-between text-secondary fs-7 gap-2">
            <p class="mb-0 text-center text-sm-start">&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name'] ?? 'SIWES Allocation Management System') ?>. All rights reserved.</p>
            <div class="d-flex gap-3 mt-2 mt-sm-0">
                <a href="#" class="footer-link fs-8">Privacy Policy</a>
                <span class="text-secondary opacity-25">&bull;</span>
                <a href="#" class="footer-link fs-8">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<?php require __DIR__ . '/layouts/footer.php'; ?>
