<?php
$pageTitle = 'Coordinator Dashboard';
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

            <!-- Stat Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Total Registered</span>
                            <h4 class="fw-bold mb-0 text-dark"><?= $stats['total_students'] ?> Students</h4>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green"><i class="fa-solid fa-users"></i></div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Pending Approvals</span>
                            <h4 class="fw-bold mb-0 text-warning"><?= $stats['pending_apps'] ?> Apps</h4>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green"><i class="fa-solid fa-file-signature"></i></div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Allocated Students</span>
                            <h4 class="fw-bold mb-0 text-primary-green"><?= $stats['total_allocated'] ?> Placed</h4>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green"><i class="fa-solid fa-diagram-project"></i></div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Partner Companies</span>
                            <h4 class="fw-bold mb-0 text-dark-green"><?= $stats['total_companies'] ?> Companies</h4>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green"><i class="fa-solid fa-building"></i></div>
                    </div>
                </div>
            </div>

            <!-- Pending Allocations Suite Table -->
            <div class="card-custom p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark-green"><i class="fa-solid fa-diagram-project me-2"></i> Approved Applications Needing Allocation</h5>
                        <p class="text-secondary fs-7 mb-0">Use the smart matching score algorithm to allocate students to partner organizations.</p>
                    </div>
                    <a href="<?= $baseUrl ?>/index.php?route=coordinator/allocation" class="btn btn-green btn-sm fw-bold">
                        <i class="fa-solid fa-sliders me-1"></i> Full Allocation Suite
                    </a>
                </div>

                <?php if (!empty($pendingAllocations)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-responsive-stack">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Matric Number</th>
                                    <th>Department</th>
                                    <th>Target Industry</th>
                                    <th>Target State</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingAllocations as $item): ?>
                                    <tr>
                                        <td data-label="Student Name" class="fw-bold text-dark"><?= htmlspecialchars($item['full_name']) ?></td>
                                        <td data-label="Matric Number"><span class="badge bg-light text-dark border"><?= htmlspecialchars($item['matric_number']) ?></span></td>
                                        <td data-label="Department"><?= htmlspecialchars($item['dept_name'] ?? 'N/A') ?></td>
                                        <td data-label="Target Industry"><span class="badge bg-light-green text-dark-green"><?= htmlspecialchars($item['preferred_industry']) ?></span></td>
                                        <td data-label="Target State"><?= htmlspecialchars($item['preferred_location']) ?></td>
                                        <td data-label="Action">
                                            <a href="<?= $baseUrl ?>/index.php?route=coordinator/allocation" class="btn btn-sm btn-green">
                                                <i class="fa-solid fa-bolt me-1"></i> Run Smart Match
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fa-solid fa-circle-check fs-1 text-success mb-2"></i>
                        <h6>All approved applications have been allocated!</h6>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
