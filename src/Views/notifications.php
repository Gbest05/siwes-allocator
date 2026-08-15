<?php
$pageTitle = 'Notifications Center';
require __DIR__ . '/layouts/header.php';
$baseUrl = (require __DIR__ . '/../../config/app.php')['base_url'];
use App\Core\Helper;

$flash = Helper::getFlash();
?>

<div class="app-wrapper">
    <?php require __DIR__ . '/layouts/sidebar.php'; ?>

    <div class="app-main">
        <?php require __DIR__ . '/layouts/topbar.php'; ?>

        <div class="app-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mb-4" role="alert">
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card-custom p-4 max-w-800 mx-auto">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-bell me-2"></i> Notification Center</h4>
                        <p class="text-secondary fs-7 mb-0">System alerts, application updates, and placement notifications.</p>
                    </div>
                    <span class="badge bg-light-green text-dark-green fw-bold"><?= count($notifications) ?> Messages</span>
                </div>

                <?php if (!empty($notifications)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($notifications as $n): ?>
                            <div class="list-group-item px-0 py-3 border-bottom">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle bg-light-green text-dark-green d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="fa-solid fa-circle-check fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($n['title']) ?></h6>
                                            <small class="text-muted fs-8"><?= date('M d, Y H:i', strtotime($n['created_at'])) ?></small>
                                        </div>
                                        <p class="text-secondary fs-7 mb-0"><?= htmlspecialchars($n['message']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fa-solid fa-bell-slash fs-1 text-secondary mb-3"></i>
                        <h6 class="fw-bold text-secondary">No notifications found.</h6>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>
