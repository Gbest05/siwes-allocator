<?php
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Auth;
use App\Core\Helper;

$siteSettings = Helper::getAllSettings();
$currentRoute = $_GET['route'] ?? 'home';
?>
<header class="header-wrapper sticky-top">
    <nav class="navbar navbar-expand-lg navbar-siwes" aria-label="Main Navigation">
        <div class="container">
            <!-- Brand / Logo -->
            <a class="navbar-brand d-flex align-items-center" href="<?= $baseUrl ?>/index.php?route=home">
                <?php if (!empty($siteSettings['site_logo'])): ?>
                    <img src="<?= $baseUrl ?>/<?= htmlspecialchars($siteSettings['site_logo']) ?>" alt="<?= htmlspecialchars($siteSettings['site_name'] ?? 'SIWES Allocator') ?>" class="navbar-brand-logo">
                <?php else: ?>
                    <div class="navbar-brand-icon">
                        <i class="fa-solid fa-graduation-cap fs-5"></i>
                    </div>
                <?php endif; ?>
                <span class="navbar-brand-text"><?= htmlspecialchars($siteSettings['site_name'] ?? 'SIWES Allocator') ?></span>
            </a>

            <!-- Mobile Hamburger Menu Button -->
            <button class="navbar-toggler mobile-menu-toggle" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarContent" 
                    aria-controls="navbarContent" 
                    aria-expanded="false" 
                    aria-label="Toggle navigation">
                <span class="hamburger-icon-wrapper" aria-hidden="true">
                    <i class="fa-solid fa-bars menu-icon-bars"></i>
                    <i class="fa-solid fa-xmark menu-icon-close d-none"></i>
                </span>
            </button>

            <!-- Navigation Links & Action Buttons Container -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                    <li class="nav-item">
                        <a class="nav-link px-3 <?= $currentRoute === 'home' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=home#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= $baseUrl ?>/index.php?route=home#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= $baseUrl ?>/index.php?route=home#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= $baseUrl ?>/index.php?route=home#how-it-works">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= $baseUrl ?>/index.php?route=home#why-us">Why Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= $baseUrl ?>/index.php?route=home#contact">Contact</a>
                    </li>
                </ul>

                <div class="nav-auth-actions">
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
</header>
