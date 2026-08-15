<?php
$pageTitle = 'User Management';
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
                        <h4 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-user-gear me-2"></i> User Account Directory</h4>
                        <p class="text-secondary fs-7 mb-0">System roles, emails, and permissions control.</p>
                    </div>
                    <span class="badge bg-light-green text-dark-green fw-bold align-self-start align-self-md-auto"><?= count($users) ?> Accounts</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-responsive-stack">
                        <thead class="table-light">
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Assigned Role</th>
                                <th>Registration Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td data-label="Full Name" class="fw-bold text-dark"><?= htmlspecialchars($u['full_name']) ?></td>
                                    <td data-label="Email"><?= htmlspecialchars($u['email']) ?></td>
                                    <td data-label="Assigned Role">
                                        <span class="badge bg-light-green text-dark-green fw-bold text-capitalize"><?= htmlspecialchars($u['role']) ?></span>
                                    </td>
                                    <td data-label="Registration Date"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
