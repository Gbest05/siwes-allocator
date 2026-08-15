<?php
$pageTitle = 'Administrative Dashboard';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Helper;

$flash = Helper::getFlash();
$extraJs = ['chart-config.js'];
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

            <!-- Stat Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Total Students</span>
                            <h4 class="fw-bold mb-0 text-dark"><?= $stats['total_students'] ?></h4>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green"><i class="fa-solid fa-users"></i></div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Total Companies</span>
                            <h4 class="fw-bold mb-0 text-dark-green"><?= $stats['total_companies'] ?></h4>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green"><i class="fa-solid fa-building"></i></div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Allocated Placements</span>
                            <h4 class="fw-bold mb-0 text-primary-green"><?= $stats['completed_alloc'] ?></h4>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green"><i class="fa-solid fa-diagram-project"></i></div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Pending Approvals</span>
                            <h4 class="fw-bold mb-0 text-warning"><?= $stats['pending_apps'] ?></h4>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green"><i class="fa-solid fa-file-signature"></i></div>
                    </div>
                </div>
            </div>

            <!-- Analytics Charts Section -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card-custom p-4 h-100">
                        <h5 class="fw-bold mb-3 text-dark-green"><i class="fa-solid fa-chart-pie me-2"></i> Students Distribution by Department</h5>
                        <div style="height: 280px;">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-custom p-4 h-100">
                        <h5 class="fw-bold mb-3 text-dark-green"><i class="fa-solid fa-chart-column me-2"></i> SIWES Application & Placement Status</h5>
                        <div style="height: 280px;">
                            <canvas id="allocationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent User Registrations Table -->
            <div class="card-custom p-4">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark-green"><i class="fa-solid fa-user-clock me-2"></i> Recent User Accounts</h5>
                    <a href="<?= $baseUrl ?>/index.php?route=admin/users" class="btn btn-outline-green btn-sm fw-bold">Manage All Users</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-responsive-stack">
                        <thead class="table-light">
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>System Role</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $usr): ?>
                                <tr>
                                    <td data-label="Full Name" class="fw-bold text-dark"><?= htmlspecialchars($usr['full_name']) ?></td>
                                    <td data-label="Email"><?= htmlspecialchars($usr['email']) ?></td>
                                    <td data-label="System Role">
                                        <span class="badge bg-light-green text-dark-green fw-bold text-capitalize"><?= htmlspecialchars($usr['role']) ?></span>
                                    </td>
                                    <td data-label="Created Date"><?= date('M d, Y', strtotime($usr['created_at'])) ?></td>
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
