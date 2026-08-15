<?php
$pageTitle = 'Academic Departments';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Helper;

$flash = Helper::getFlash();
?>

<div class="app-wrapper">
    <?php require __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="app-main">
        <?php require __DIR__ . '/../layouts/topbar.php'; ?>

        <div class="app-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mb-4" role="alert">
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card-custom p-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-sitemap me-2"></i> Academic Departments Directory</h4>
                        <p class="text-secondary fs-7 mb-0">Institutions academic departments participating in the SIWES program.</p>
                    </div>
                    <span class="badge bg-light-green text-dark-green fw-bold align-self-start align-self-md-auto"><?= count($departments) ?> Departments</span>
                </div>

                <div class="row g-4">
                    <?php foreach ($departments as $dept): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card-custom p-4 h-100 border">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="badge bg-dark-green text-white fw-bold"><?= htmlspecialchars($dept['code']) ?></span>
                                    <span class="badge bg-light-green text-dark-green fw-bold"><?= $dept['student_count'] ?> Enrolled</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($dept['name']) ?></h5>
                                <p class="text-secondary fs-7 mb-0"><?= htmlspecialchars($dept['description'] ?? 'No description added.') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
