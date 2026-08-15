<?php
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Auth;
use App\Core\Helper;

$siteSettings = Helper::getAllSettings();
?>
<nav class="navbar navbar-expand-lg navbar-siwes sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $baseUrl ?>/index.php?route=home">
            <?php if (!empty($siteSettings['site_logo'])): ?>
                <img src="<?= $baseUrl ?>/<?= htmlspecialchars($siteSettings['site_logo']) ?>" alt="Logo" style="height: 38px; max-width: 140px; object-fit: contain;">
            <?php else: ?>
                <div class="bg-primary-green text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-graduation-cap fs-5"></i>
                </div>
            <?php endif; ?>
            <span><?= htmlspecialchars($siteSettings['site_name'] ?? 'SIWES Allocator') ?></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars fs-4 text-dark-green"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                <li class="nav-item"><a class="nav-link px-3" href="<?= $baseUrl ?>/index.php?route=home">Home</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#how-it-works">How It Works</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#why-us">Why Us</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (Auth::check()): ?>
                    <a href="<?= $baseUrl ?>/index.php?route=dashboard" class="btn btn-green">
                        <i class="fa-solid fa-gauge me-1"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= $baseUrl ?>/index.php?route=login" class="btn btn-outline-green">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Student Login
                    </a>
                    <a href="<?= $baseUrl ?>/index.php?route=register" class="btn btn-green">
                        <i class="fa-solid fa-user-plus me-1"></i> Get Started
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
