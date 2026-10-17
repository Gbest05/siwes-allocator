<?php
$pageTitle = 'Supervisor Dashboard';
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

            <!-- 1. Supervisor Profile Banner -->
            <div class="card-custom p-4 bg-dark-green text-white mb-4 position-relative overflow-hidden">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge bg-emerald mb-2 fw-bold text-uppercase">
                            <i class="fa-solid fa-id-badge me-1"></i> <?= htmlspecialchars($supervisor['designation'] ?? 'Institutional SIWES Supervisor') ?>
                        </span>
                        <h3 class="fw-bold mb-1 text-white">Welcome, <?= htmlspecialchars($supervisor['full_name']) ?></h3>
                        <p class="text-white-50 mb-0 fs-7">
                            Staff ID: <strong><?= htmlspecialchars($supervisor['staff_id'] ?? 'N/A') ?></strong> &bull;
                            Department: <strong><?= htmlspecialchars($supervisor['department_name'] ?? 'General Academic Roster') ?></strong> &bull;
                            Email: <strong><?= htmlspecialchars($supervisor['email']) ?></strong>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="<?= $baseUrl ?>/index.php?route=supervisor/students" class="btn btn-light text-dark-green fw-bold">
                            <i class="fa-solid fa-users me-1"></i> Full Student Roster
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Supervisor Metrics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Total Assigned Students</span>
                            <h3 class="fw-bold mb-0 text-dark-green"><?= $stats['total_assigned'] ?></h3>
                            <small class="text-muted fs-8">Assigned by Coordinator</small>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-users-viewfinder"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Placement Confirmed</span>
                            <h3 class="fw-bold mb-0 text-emerald"><?= $stats['total_placed'] ?></h3>
                            <small class="text-muted fs-8">Active at Partner Companies</small>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-building-circle-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Pending Placement</span>
                            <h3 class="fw-bold mb-0 text-warning"><?= $stats['pending_placement'] ?></h3>
                            <small class="text-muted fs-8">Awaiting Company Match</small>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Departments</span>
                            <h3 class="fw-bold mb-0 text-dark-charcoal"><?= $stats['distinct_depts'] ?></h3>
                            <small class="text-muted fs-8">Represented in your roster</small>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-sitemap"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Assigned Students Roster Table -->
            <div class="card-custom p-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark-green">
                            <i class="fa-solid fa-user-graduate me-2"></i> Students Assigned to You
                        </h4>
                        <p class="text-secondary fs-7 mb-0">
                            Monitor logbooks, industrial attachment locations, and student training progress.
                        </p>
                    </div>
                    <span class="badge bg-light-green text-dark-green fw-bold px-3 py-2 align-self-start align-self-md-auto fs-7">
                        <?= count($assignedStudents) ?> Total Assigned
                    </span>
                </div>

                <?php if (empty($assignedStudents)): ?>
                    <div class="text-center py-5">
                        <div class="text-secondary mb-3">
                            <i class="fa-solid fa-folder-open fs-1 text-warning"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No Students Assigned Yet</h5>
                        <p class="text-secondary fs-7 max-w-500 mx-auto mb-0">
                            The SIWES coordinator has not assigned any students to your supervision roster yet. Once students are allocated to you manually or via automatic distribution, they will appear here with their placement details.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-responsive-stack">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student Details</th>
                                    <th>Department & Level</th>
                                    <th>Contact Info</th>
                                    <th>Assigned Company / Placement</th>
                                    <th>Assigned Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignedStudents as $idx => $st): ?>
                                    <tr>
                                        <td data-label="#" class="text-muted fs-8 fw-semibold"><?= $idx + 1 ?></td>
                                        <td data-label="Student Details">
                                            <div class="fw-bold text-dark fs-7"><?= htmlspecialchars($st['full_name']) ?></div>
                                            <span class="badge bg-light text-dark border fs-8"><?= htmlspecialchars($st['matric_number']) ?></span>
                                        </td>
                                        <td data-label="Department & Level">
                                            <div class="fw-semibold text-dark fs-7"><?= htmlspecialchars($st['dept_name'] ?? 'General') ?></div>
                                            <small class="text-muted fs-8"><?= htmlspecialchars($st['programme']) ?> &bull; <?= htmlspecialchars($st['level']) ?></small>
                                        </td>
                                        <td data-label="Contact Info">
                                            <div>
                                                <a href="mailto:<?= htmlspecialchars($st['email']) ?>" class="text-dark-charcoal text-decoration-none fs-8 d-block text-truncate" style="max-width: 170px;">
                                                    <i class="fa-solid fa-envelope text-primary-green me-1"></i> <?= htmlspecialchars($st['email']) ?>
                                                </a>
                                                <?php if (!empty($st['phone'])): ?>
                                                    <a href="tel:<?= htmlspecialchars($st['phone']) ?>" class="text-secondary text-decoration-none fs-8">
                                                        <i class="fa-solid fa-phone text-primary-green me-1"></i> <?= htmlspecialchars($st['phone']) ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td data-label="Assigned Company">
                                            <?php if (!empty($st['company_name'])): ?>
                                                <div class="fw-bold text-dark fs-7">
                                                    <i class="fa-solid fa-building text-primary-green me-1"></i> <?= htmlspecialchars($st['company_name']) ?>
                                                </div>
                                                <small class="text-muted fs-8 d-block">
                                                    <?= htmlspecialchars($st['company_city'] ?? '') ?>, <?= htmlspecialchars($st['company_state'] ?? '') ?>
                                                </small>
                                                <span class="badge bg-emerald fs-8 mt-1">Confirmed Placement</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark fs-8">
                                                    <i class="fa-solid fa-clock me-1"></i> Awaiting Placement
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Assigned Date">
                                            <div class="fs-8 text-secondary">
                                                <?= !empty($st['supervisor_assigned_at']) ? date('M d, Y', strtotime($st['supervisor_assigned_at'])) : 'Active' ?>
                                            </div>
                                        </td>
                                        <td data-label="Actions" class="text-end">
                                            <button type="button" class="btn btn-outline-success btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalStudent<?= $st['id'] ?>">
                                                <i class="fa-solid fa-eye me-1"></i> View Details
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Student Profile & Placement Modal -->
                                    <div class="modal fade" id="modalStudent<?= $st['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $st['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-dark-green text-white">
                                                    <h5 class="modal-title fw-bold" id="modalLabel<?= $st['id'] ?>">
                                                        <i class="fa-solid fa-user-graduate me-2"></i> <?= htmlspecialchars($st['full_name']) ?> — Student Profile
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-sm-6">
                                                            <small class="text-secondary d-block">Matriculation Number</small>
                                                            <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($st['matric_number']) ?></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-secondary d-block">Academic Department</small>
                                                            <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($st['dept_name'] ?? 'N/A') ?></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-secondary d-block">Programme & Level</small>
                                                            <span class="fw-semibold text-dark fs-7"><?= htmlspecialchars($st['programme']) ?> &bull; <?= htmlspecialchars($st['level']) ?></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-secondary d-block">Student Phone & Email</small>
                                                            <span class="fw-semibold text-dark fs-7"><?= htmlspecialchars($st['phone'] ?? 'N/A') ?> &bull; <?= htmlspecialchars($st['email']) ?></span>
                                                        </div>
                                                    </div>

                                                    <h6 class="fw-bold text-dark-green border-bottom pb-2 mb-3">
                                                        <i class="fa-solid fa-building me-2"></i> Company Placement Information
                                                    </h6>
                                                    <?php if (!empty($st['company_name'])): ?>
                                                        <div class="row g-3">
                                                            <div class="col-sm-6">
                                                                <small class="text-secondary d-block">Company Name</small>
                                                                <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($st['company_name']) ?></span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <small class="text-secondary d-block">Industry Sector</small>
                                                                <span class="fw-semibold text-dark fs-7"><?= htmlspecialchars($st['company_industry'] ?? 'N/A') ?></span>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <small class="text-secondary d-block">Location / Address</small>
                                                                <span class="fw-semibold text-dark fs-7"><?= htmlspecialchars($st['company_city'] ?? '') ?>, <?= htmlspecialchars($st['company_state'] ?? '') ?></span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <small class="text-secondary d-block">Industry Contact Person</small>
                                                                <span class="fw-semibold text-dark fs-7"><?= htmlspecialchars($st['contact_person'] ?? 'N/A') ?> (<?= htmlspecialchars($st['company_phone'] ?? 'N/A') ?>)</span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <small class="text-secondary d-block">Attachment Duration</small>
                                                                <span class="fw-semibold text-dark fs-7">
                                                                    <?= !empty($st['start_date']) ? date('M Y', strtotime($st['start_date'])) . ' — ' . date('M Y', strtotime($st['end_date'])) : 'Standard 6-Month Period' ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="alert alert-light border">
                                                            <i class="fa-solid fa-info-circle text-warning me-2"></i>
                                                            This student has not yet been matched to an industrial placement company. The coordinator will finalize placement matching.
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <a href="mailto:<?= htmlspecialchars($st['email']) ?>" class="btn btn-outline-success btn-sm">
                                                        <i class="fa-solid fa-envelope me-1"></i> Send Email
                                                    </a>
                                                    <?php if (!empty($st['phone'])): ?>
                                                        <a href="tel:<?= htmlspecialchars($st['phone']) ?>" class="btn btn-outline-primary btn-sm">
                                                            <i class="fa-solid fa-phone me-1"></i> Call Student
                                                        </a>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
