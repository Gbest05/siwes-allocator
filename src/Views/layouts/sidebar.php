<?php
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Auth;
use App\Core\Helper;

$role = Auth::role();
$currentRoute = $_GET['route'] ?? 'dashboard';
$siteSettings = Helper::getAllSettings();
?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="app-sidebar" id="appSidebar">
    <!-- 1. Sticky Brand / Logo Header -->
    <div class="sidebar-brand d-flex align-items-center justify-content-between">
        <a href="<?= $baseUrl ?>/index.php?route=home" class="text-white text-decoration-none d-flex align-items-center gap-2 overflow-hidden">
            <?php if (!empty($siteSettings['site_logo'])): ?>
                <img src="<?= Helper::asset($siteSettings['site_logo']) ?>" alt="Logo" style="height: 32px; max-width: 110px; object-fit: contain;">
            <?php else: ?>
                <i class="fa-solid fa-graduation-cap text-primary-green fs-4 flex-shrink-0"></i>
            <?php endif; ?>
            <span class="text-truncate fw-bold"><?= htmlspecialchars($siteSettings['site_name'] ?? 'SIWES Portal') ?></span>
        </a>
    </div>

    <!-- 2. Smoothly Scrollable Navigation Links Body -->
    <div class="sidebar-nav-scroll">
        <div class="px-3 py-2 text-uppercase text-secondary fs-7 fw-bold" style="font-size: 0.72rem; letter-spacing: 0.08em;">
            Navigation Menu
        </div>

        <nav class="nav flex-column mb-3">
            <?php if ($role === 'student'): ?>
                <a class="nav-link <?= $currentRoute === 'dashboard' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=dashboard">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a class="nav-link <?= $currentRoute === 'student/application' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=student/application">
                    <i class="fa-solid fa-file-lines"></i> SIWES Application
                </a>
                <a class="nav-link <?= $currentRoute === 'student/allocation' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=student/allocation">
                    <i class="fa-solid fa-diagram-project"></i> My Placement
                </a>
                <a class="nav-link <?= $currentRoute === 'student/documents' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=student/documents">
                    <i class="fa-solid fa-folder-open"></i> Documents
                </a>
                <a class="nav-link <?= $currentRoute === 'notifications' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=notifications">
                    <i class="fa-solid fa-bell"></i> Notifications
                </a>
            <?php elseif ($role === 'coordinator' || $role === 'admin'): ?>
                <a class="nav-link <?= str_contains($currentRoute, 'dashboard') ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=coordinator/dashboard">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a class="nav-link <?= $currentRoute === 'coordinator/students' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=coordinator/students">
                    <i class="fa-solid fa-users"></i> Registered Students
                </a>
                <a class="nav-link <?= $currentRoute === 'coordinator/applications' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=coordinator/applications">
                    <i class="fa-solid fa-file-lines"></i> Applications
                </a>
                <a class="nav-link <?= $currentRoute === 'coordinator/allocation' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=coordinator/allocation">
                    <i class="fa-solid fa-diagram-project"></i> Smart Allocation
                </a>
                <a class="nav-link <?= $currentRoute === 'coordinator/companies' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=coordinator/companies">
                    <i class="fa-solid fa-building"></i> Companies
                </a>
                <a class="nav-link <?= $currentRoute === 'reports' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=reports">
                    <i class="fa-solid fa-chart-column"></i> Reports & Analytics
                </a>

                <?php if ($role === 'admin'): ?>
                    <div class="px-3 py-2 text-uppercase text-secondary fs-7 fw-bold mt-2" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                        Administration
                    </div>
                    <a class="nav-link <?= $currentRoute === 'admin/users' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=admin/users">
                        <i class="fa-solid fa-user-gear"></i> User Management
                    </a>
                    <a class="nav-link <?= $currentRoute === 'admin/departments' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=admin/departments">
                        <i class="fa-solid fa-sitemap"></i> Departments
                    </a>
                    <a class="nav-link <?= $currentRoute === 'admin/settings' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?route=admin/settings">
                        <i class="fa-solid fa-sliders"></i> Landing Page CMS
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    </div>

    <!-- 3. Sticky User Profile & Logout Bottom Bar -->
    <div class="sidebar-footer p-3 border-top border-secondary border-opacity-25 mt-auto">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <div class="rounded-circle bg-primary-green text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 38px; height: 38px;">
                    <?= strtoupper(substr(Auth::user()['full_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="text-truncate">
                    <div class="fw-bold text-white fs-7 text-truncate"><?= htmlspecialchars(Auth::user()['full_name'] ?? '') ?></div>
                    <div class="text-secondary fs-8 text-capitalize" style="font-size: 0.75rem;"><?= Auth::role() ?></div>
                </div>
            </div>
            <a href="<?= $baseUrl ?>/index.php?route=logout" class="text-secondary hover-white fs-5 ms-2 flex-shrink-0" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </div>
</aside>
