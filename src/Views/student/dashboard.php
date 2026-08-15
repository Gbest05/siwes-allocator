<?php
$pageTitle = 'Student Dashboard';
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
                    <i class="fa-solid fa-circle-info me-2"></i> <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Student Profile Header Banner -->
            <div class="card-custom p-4 bg-dark-green text-white mb-4 position-relative overflow-hidden">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge bg-emerald mb-2 fw-bold text-uppercase"><?= htmlspecialchars($student['programme']) ?> &bull; <?= htmlspecialchars($student['level']) ?></span>
                        <h3 class="fw-bold mb-1 text-white">Welcome, <?= htmlspecialchars($student['full_name']) ?></h3>
                        <p class="text-white-50 mb-0">
                            Matric No: <strong><?= htmlspecialchars($student['matric_number']) ?></strong> &bull; Department: <strong><?= htmlspecialchars($student['department_name'] ?? 'N/A') ?></strong>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <?php if ($allocation): ?>
                            <a href="<?= $baseUrl ?>/index.php?route=allocation-letter" class="btn btn-light text-dark-green fw-bold">
                                <i class="fa-solid fa-file-arrow-down me-1"></i> Allocation Letter
                            </a>
                        <?php else: ?>
                            <a href="<?= $baseUrl ?>/index.php?route=student/application" class="btn btn-green fw-bold">
                                <i class="fa-solid fa-paper-plane me-1"></i> Apply for SIWES
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 1. DASHBOARD STATUS CARDS -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Application Status</span>
                            <h5 class="fw-bold mb-0">
                                <?php 
                                    $appStatus = $application['status'] ?? 'Not Submitted';
                                    $appBadgeClass = match($appStatus) {
                                        'Approved' => 'text-success',
                                        'Allocated' => 'text-success',
                                        'Submitted', 'Under Review' => 'text-warning',
                                        'Rejected' => 'text-danger',
                                        default => 'text-secondary'
                                    };
                                ?>
                                <span class="<?= $appBadgeClass ?>"><?= $appStatus ?></span>
                            </h5>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Allocation Status</span>
                            <h5 class="fw-bold mb-0">
                                <?php if ($allocation): ?>
                                    <span class="text-emerald"><i class="fa-solid fa-circle-check"></i> Allocated</span>
                                <?php else: ?>
                                    <span class="text-warning"><i class="fa-solid fa-clock"></i> Pending</span>
                                <?php endif; ?>
                            </h5>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-diagram-project"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Assigned Organization</span>
                            <h6 class="fw-bold mb-0 text-truncate" style="max-width: 140px;" title="<?= htmlspecialchars($allocation['company_name'] ?? 'Not Assigned') ?>">
                                <?= htmlspecialchars($allocation['company_name'] ?? 'Unassigned') ?>
                            </h6>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-building"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">SIWES Duration</span>
                            <h5 class="fw-bold mb-0 text-dark">6 Months</h5>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. QUICK ACTIONS & ALLOCATION DETAILS CARD -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <!-- Allocation Details Card -->
                    <div class="card-custom p-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                            <h5 class="fw-bold mb-0 text-dark-green"><i class="fa-solid fa-building-circle-check me-2"></i> Official Placement Details</h5>
                            <?php if ($allocation): ?>
                                <span class="badge bg-emerald px-3 py-2 fw-bold">Active Placement</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark px-3 py-2 fw-bold">Awaiting Coordinator Allocation</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($allocation): ?>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <small class="text-secondary d-block">Company Name</small>
                                    <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($allocation['company_name']) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-secondary d-block">Industry Sector</small>
                                    <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($allocation['company_industry']) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-secondary d-block">Company Address</small>
                                    <span class="fw-semibold text-dark fs-7"><?= htmlspecialchars($allocation['company_address']) ?>, <?= htmlspecialchars($allocation['company_city']) ?>, <?= htmlspecialchars($allocation['company_state']) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-secondary d-block">Supervisor Contact</small>
                                    <span class="fw-semibold text-dark fs-7"><?= htmlspecialchars($allocation['contact_person']) ?> (<?= htmlspecialchars($allocation['company_phone']) ?>)</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-secondary d-block">Attachment Period</small>
                                    <span class="fw-semibold text-dark fs-7"><?= date('M d, Y', strtotime($allocation['start_date'])) ?> — <?= date('M d, Y', strtotime($allocation['end_date'])) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-secondary d-block">Match Score Rating</small>
                                    <span class="match-score-badge"><?= $allocation['compatibility_score'] ?>% Compatibility Match</span>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-top d-flex gap-2">
                                <a href="<?= $baseUrl ?>/index.php?route=allocation-letter" class="btn btn-green btn-sm fw-bold">
                                    <i class="fa-solid fa-download me-1"></i> Download Official Letter
                                </a>
                                <a href="<?= $baseUrl ?>/index.php?route=student/allocation" class="btn btn-outline-secondary btn-sm">
                                    View Placement History
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <div class="text-secondary mb-3"><i class="fa-solid fa-hourglass-half fs-1 text-warning"></i></div>
                                <h6 class="fw-bold">No Allocation Assigned Yet</h6>
                                <p class="text-secondary fs-7 max-w-500 mx-auto">
                                    Your application has been received. Once the SIWES coordinator completes the smart compatibility matching process, your assigned company details will appear here.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions & Notifications Column -->
                <div class="col-lg-4">
                    <div class="card-custom p-4 mb-4">
                        <h6 class="fw-bold mb-3 text-dark-green"><i class="fa-solid fa-bolt me-2"></i> Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="<?= $baseUrl ?>/index.php?route=student/application" class="btn btn-outline-green text-start">
                                <i class="fa-solid fa-pen-to-square me-2"></i> Update SIWES Preferences
                            </a>
                            <a href="<?= $baseUrl ?>/index.php?route=student/documents" class="btn btn-outline-green text-start">
                                <i class="fa-solid fa-upload me-2"></i> Upload Verification Documents
                            </a>
                            <a href="<?= $baseUrl ?>/index.php?route=notifications" class="btn btn-outline-green text-start">
                                <i class="fa-solid fa-bell me-2"></i> View System Notifications
                            </a>
                        </div>
                    </div>

                    <!-- Recent Notifications Widget -->
                    <div class="card-custom p-4">
                        <h6 class="fw-bold mb-3 text-dark-green"><i class="fa-solid fa-bell me-2"></i> Recent Alerts</h6>
                        <?php if (!empty($notifications)): ?>
                            <div class="list-group list-group-flush fs-7">
                                <?php foreach ($notifications as $notif): ?>
                                    <div class="list-group-item px-0 border-bottom py-2">
                                        <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($notif['title']) ?></div>
                                        <div class="text-secondary fs-8 mb-1"><?= htmlspecialchars($notif['message']) ?></div>
                                        <small class="text-muted" style="font-size: 0.7rem;"><?= date('M d, H:i', strtotime($notif['created_at'])) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-secondary fs-7 mb-0">No new notifications.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
