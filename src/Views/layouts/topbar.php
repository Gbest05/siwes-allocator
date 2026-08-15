<?php
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Auth;
use App\Core\Database;

$db = Database::getInstance();
$userId = Auth::id();
$unreadNotifs = (int)$db->query("SELECT COUNT(*) FROM notifications WHERE user_id = {$userId} AND is_read = FALSE")->fetchColumn();
?>
<header class="app-topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light d-lg-none" id="sidebarToggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h5 class="mb-0 fw-bold text-dark-charcoal d-none d-sm-block"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h5>
    </div>

    <div class="d-flex align-items-center gap-3">
        <a href="<?= $baseUrl ?>/index.php?route=notifications" class="position-relative text-dark-charcoal me-2" title="Notifications">
            <i class="fa-solid fa-bell fs-5"></i>
            <?php if ($unreadNotifs > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                    <?= $unreadNotifs ?>
                </span>
            <?php endif; ?>
        </a>

        <div class="dropdown">
            <button class="btn btn-topbar-profile d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-circle-user fs-5 text-primary-green"></i>
                <span class="fw-semibold d-none d-md-inline user-name-label"><?= htmlspecialchars(Auth::user()['full_name'] ?? 'Account') ?></span>
                <i class="fa-solid fa-chevron-down fs-8 text-secondary ms-1"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item" href="<?= $baseUrl ?>/index.php?route=dashboard"><i class="fa-solid fa-gauge me-2 text-primary-green"></i> Dashboard</a></li>
                <li><a class="dropdown-item" href="<?= $baseUrl ?>/index.php?route=notifications"><i class="fa-solid fa-bell me-2 text-primary-green"></i> Notifications</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= $baseUrl ?>/index.php?route=logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</header>
