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
                    <div class="col-md-7">
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
                    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
                        <button type="button" class="btn btn-outline-light fw-bold" data-bs-toggle="modal" data-bs-target="#modalBroadcastMessage">
                            <i class="fa-solid fa-bullhorn me-1"></i> Send Notice to All
                        </button>
                        <?php if ($stats['pending_placement'] > 0): ?>
                            <button type="button" class="btn btn-warning fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalAutoAssignAll">
                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto-Assign (<?= $stats['pending_placement'] ?>)
                            </button>
                        <?php endif; ?>
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
                            <small class="text-muted fs-8">In your supervision cohort</small>
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
                            Review submitted applications, inspect uploaded documents, and approve or allocate company placements.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($stats['pending_placement'] > 0): ?>
                            <button type="button" class="btn btn-sm btn-outline-success fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAutoAssignAll">
                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto-Match Unplaced
                            </button>
                        <?php endif; ?>
                        <span class="badge bg-light-green text-dark-green fw-bold px-3 py-2 fs-7">
                            <?= count($assignedStudents) ?> Total Assigned
                        </span>
                    </div>
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
                                    <th>Application & Docs</th>
                                    <th>Placement Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignedStudents as $idx => $st): ?>
                                    <?php 
                                        $docsCount = isset($studentDocs[$st['id']]) ? count($studentDocs[$st['id']]) : 0;
                                        $appStatus = $st['application_status'] ?? 'Not Submitted';
                                    ?>
                                    <tr>
                                        <td data-label="#" class="text-muted fs-8 fw-semibold"><?= $idx + 1 ?></td>
                                        <td data-label="Student Details">
                                            <div class="fw-bold text-dark fs-7"><?= htmlspecialchars($st['full_name']) ?></div>
                                            <span class="badge bg-light text-dark border fs-8"><?= htmlspecialchars($st['matric_number']) ?></span>
                                            <div class="text-muted fs-8 mt-1">
                                                <i class="fa-solid fa-envelope me-1 text-primary-green"></i> <?= htmlspecialchars($st['email']) ?>
                                            </div>
                                        </td>
                                        <td data-label="Department & Level">
                                            <div class="fw-semibold text-dark fs-7"><?= htmlspecialchars($st['dept_name'] ?? 'General') ?></div>
                                            <small class="text-muted fs-8"><?= htmlspecialchars($st['programme']) ?> &bull; <?= htmlspecialchars($st['level']) ?></small>
                                        </td>
                                        <td data-label="Application & Docs">
                                            <div>
                                                <?php if ($appStatus === 'Approved'): ?>
                                                    <span class="badge bg-success fs-8"><i class="fa-solid fa-circle-check me-1"></i> Approved</span>
                                                <?php elseif ($appStatus === 'Submitted'): ?>
                                                    <span class="badge bg-info text-dark fs-8"><i class="fa-solid fa-paper-plane me-1"></i> Submitted</span>
                                                <?php elseif ($appStatus === 'Allocated'): ?>
                                                    <span class="badge bg-emerald fs-8"><i class="fa-solid fa-building-circle-check me-1"></i> Allocated</span>
                                                <?php elseif ($appStatus === 'Rejected'): ?>
                                                    <span class="badge bg-danger fs-8"><i class="fa-solid fa-circle-xmark me-1"></i> Rejected</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary fs-8"><i class="fa-solid fa-clock me-1"></i> <?= htmlspecialchars($appStatus) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mt-1">
                                                <?php if ($docsCount > 0): ?>
                                                    <span class="badge bg-light text-dark border fs-8">
                                                        <i class="fa-solid fa-file-lines text-primary-green me-1"></i> <?= $docsCount ?> Document<?= $docsCount > 1 ? 's' : '' ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border fs-8">
                                                        <i class="fa-solid fa-file-circle-exclamation text-warning me-1"></i> No Docs
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td data-label="Placement Status">
                                            <?php if (!empty($st['company_name'])): ?>
                                                <div class="fw-bold text-dark fs-7">
                                                    <i class="fa-solid fa-building text-primary-green me-1"></i> <?= htmlspecialchars($st['company_name']) ?>
                                                </div>
                                                <small class="text-muted fs-8 d-block">
                                                    <?= htmlspecialchars($st['company_city'] ?? '') ?>, <?= htmlspecialchars($st['company_state'] ?? '') ?>
                                                </small>
                                                <?php if (!empty($st['compatibility_score'])): ?>
                                                    <span class="badge bg-light-green text-dark-green fs-8 mt-1">
                                                        <?= (int)$st['compatibility_score'] ?>% Match
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark fs-8">
                                                    <i class="fa-solid fa-clock me-1"></i> Awaiting Placement
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Actions" class="text-end">
                                            <button type="button" class="btn btn-outline-success btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalStudent<?= $st['id'] ?>">
                                                <i class="fa-solid fa-sliders me-1"></i> Review & Assign
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Student Profile, Documents & Placement Action Modal -->
                                    <div class="modal fade" id="modalStudent<?= $st['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $st['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-dark-green text-white">
                                                    <div>
                                                        <h5 class="modal-title fw-bold" id="modalLabel<?= $st['id'] ?>">
                                                            <i class="fa-solid fa-user-graduate me-2"></i> <?= htmlspecialchars($st['full_name']) ?>
                                                        </h5>
                                                        <small class="text-white-50"><?= htmlspecialchars($st['matric_number']) ?> &bull; <?= htmlspecialchars($st['dept_name'] ?? 'General') ?> (<?= htmlspecialchars($st['programme']) ?> &bull; <?= htmlspecialchars($st['level']) ?>)</small>
                                                    </div>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">

                                                    <!-- Student Info & Contacts -->
                                                    <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                                                        <div class="col-sm-6">
                                                            <small class="text-muted d-block fs-8">Email Address</small>
                                                            <a href="mailto:<?= htmlspecialchars($st['email']) ?>" class="fw-semibold text-dark fs-7 text-decoration-none">
                                                                <i class="fa-solid fa-envelope text-primary-green me-1"></i> <?= htmlspecialchars($st['email']) ?>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-muted d-block fs-8">Phone Number</small>
                                                            <span class="fw-semibold text-dark fs-7">
                                                                <i class="fa-solid fa-phone text-primary-green me-1"></i> <?= htmlspecialchars($st['phone'] ?? 'N/A') ?>
                                                            </span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-muted d-block fs-8">Preferred Industry Sector</small>
                                                            <span class="fw-bold text-dark fs-7">
                                                                <i class="fa-solid fa-industry text-primary-green me-1"></i> <?= htmlspecialchars($st['preferred_industry'] ?? 'Not Specified') ?>
                                                            </span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <small class="text-muted d-block fs-8">Preferred Placement Location</small>
                                                            <span class="fw-bold text-dark fs-7">
                                                                <i class="fa-solid fa-location-dot text-primary-green me-1"></i> <?= htmlspecialchars($st['preferred_location'] ?? 'Not Specified') ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Section: Uploaded Documents Review -->
                                                    <div class="mb-4">
                                                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                                            <h6 class="fw-bold text-dark-green mb-0">
                                                                <i class="fa-solid fa-folder-closed me-2"></i> Uploaded Documents
                                                            </h6>
                                                            <span class="badge bg-light text-dark border"><?= $docsCount ?> Attached</span>
                                                        </div>

                                                        <?php if (empty($studentDocs[$st['id']])): ?>
                                                            <div class="alert alert-light border py-3 text-center fs-8 text-secondary">
                                                                <i class="fa-solid fa-info-circle text-warning me-1"></i>
                                                                The student has not uploaded any required documents (acceptance letters, CV, IT clearance) yet.
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="list-group">
                                                                <?php foreach ($studentDocs[$st['id']] as $doc): ?>
                                                                    <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3">
                                                                        <div class="d-flex align-items-center gap-3">
                                                                            <div class="bg-light-green text-dark-green rounded p-2">
                                                                                <i class="fa-solid fa-file-pdf fs-5"></i>
                                                                            </div>
                                                                            <div>
                                                                                <div class="fw-bold text-dark fs-7"><?= htmlspecialchars($doc['doc_type'] ?? 'Uploaded Document') ?></div>
                                                                                <small class="text-muted fs-8">
                                                                                    <?= htmlspecialchars($doc['file_name']) ?> &bull; 
                                                                                    <?= round(($doc['file_size'] ?? 0) / 1024, 1) ?> KB &bull;
                                                                                    Uploaded <?= date('M d, Y', strtotime($doc['uploaded_at'])) ?>
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                        <a href="<?= $baseUrl ?>/<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-success fw-semibold">
                                                                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View / Download
                                                                        </a>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Section: Review Application Status -->
                                                    <div class="mb-4 p-3 border rounded-3 bg-white">
                                                        <h6 class="fw-bold text-dark-green border-bottom pb-2 mb-3">
                                                            <i class="fa-solid fa-clipboard-check me-2"></i> Review Application
                                                        </h6>
                                                        <form action="<?= $baseUrl ?>/index.php?route=supervisor/review-application" method="POST">
                                                            <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                            <input type="hidden" name="student_id" value="<?= $st['id'] ?>">
                                                            <input type="hidden" name="application_id" value="<?= $st['app_id'] ?? 0 ?>">
                                                            <input type="hidden" name="redirect_to" value="supervisor/dashboard">

                                                            <div class="row g-2 align-items-end">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-semibold fs-8 mb-1">Application Decision</label>
                                                                    <select name="action" class="form-select form-select-sm" required>
                                                                        <option value="Approved" <?= ($appStatus === 'Approved' || $appStatus === 'Allocated') ? 'selected' : '' ?>>Approve Application</option>
                                                                        <option value="Rejected" <?= ($appStatus === 'Rejected') ? 'selected' : '' ?>>Reject / Request Re-submission</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <label class="form-label fw-semibold fs-8 mb-1">Supervisor Review Note</label>
                                                                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. Documents verified and approved" value="<?= htmlspecialchars($st['app_notes'] ?? '') ?>">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <button type="submit" class="btn btn-green btn-sm w-100 fw-bold">
                                                                        <i class="fa-solid fa-check-double me-1"></i> Submit Review
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <!-- Section: Company Placement Allocation -->
                                                    <div class="p-3 border rounded-3 bg-white">
                                                        <h6 class="fw-bold text-dark-green border-bottom pb-2 mb-3">
                                                            <i class="fa-solid fa-building me-2"></i> Company Placement Allocation
                                                        </h6>

                                                        <?php if (!empty($st['company_name'])): ?>
                                                            <div class="alert alert-success d-flex align-items-center justify-content-between p-3 mb-3">
                                                                <div>
                                                                    <div class="fw-bold fs-7">
                                                                        <i class="fa-solid fa-circle-check me-1"></i> Currently Placed at <?= htmlspecialchars($st['company_name']) ?>
                                                                    </div>
                                                                    <small class="d-block text-muted">
                                                                        Location: <?= htmlspecialchars($st['company_city'] ?? '') ?>, <?= htmlspecialchars($st['company_state'] ?? '') ?> &bull; 
                                                                        Industry: <?= htmlspecialchars($st['company_industry'] ?? 'N/A') ?> &bull;
                                                                        Contact: <?= htmlspecialchars($st['contact_person'] ?? 'N/A') ?> (<?= htmlspecialchars($st['company_phone'] ?? 'N/A') ?>)
                                                                    </small>
                                                                    <span class="badge bg-light-green text-dark-green mt-1">
                                                                        Compatibility Score: <?= (int)$st['compatibility_score'] ?>%
                                                                    </span>
                                                                </div>
                                                                <span class="badge bg-emerald px-2 py-1">Active</span>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="alert alert-warning py-2 px-3 fs-8 mb-3">
                                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                                                This student does not have an active company placement. You can assign placement manually or trigger automatic matching below.
                                                            </div>
                                                        <?php endif; ?>

                                                        <!-- Placement Actions Tabs / Options -->
                                                        <div class="row g-3">
                                                            <!-- Option A: Automatic Matching -->
                                                            <div class="col-md-6 border-end">
                                                                <div class="p-2">
                                                                    <span class="fw-bold text-dark fs-7 d-block mb-1">
                                                                        <i class="fa-solid fa-wand-magic-sparkles text-primary-green me-1"></i> Option 1: Automatic Matching
                                                                    </span>
                                                                    <p class="text-secondary fs-8 mb-3">
                                                                        Smart-matches student's department, preferred industry, and location to the best available partner organization.
                                                                    </p>
                                                                    <form action="<?= $baseUrl ?>/index.php?route=supervisor/auto-assign-placements" method="POST">
                                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                                        <input type="hidden" name="student_id" value="<?= $st['id'] ?>">
                                                                        <input type="hidden" name="redirect_to" value="supervisor/dashboard">
                                                                        <button type="submit" class="btn btn-outline-success btn-sm w-100 fw-bold">
                                                                            <i class="fa-solid fa-bolt me-1"></i> Auto-Match Student Now
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                            <!-- Option B: Manual Company Selection -->
                                                            <div class="col-md-6">
                                                                <div class="p-2">
                                                                    <span class="fw-bold text-dark fs-7 d-block mb-1">
                                                                        <i class="fa-solid fa-hand-pointer text-primary-green me-1"></i> Option 2: Manual Selection
                                                                    </span>
                                                                    <p class="text-secondary fs-8 mb-2">
                                                                        Manually choose from active partner companies with open capacity.
                                                                    </p>
                                                                    <form action="<?= $baseUrl ?>/index.php?route=supervisor/allocate-placement" method="POST">
                                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                                        <input type="hidden" name="student_id" value="<?= $st['id'] ?>">
                                                                        <input type="hidden" name="redirect_to" value="supervisor/dashboard">

                                                                        <div class="mb-2">
                                                                            <select name="company_id" class="form-select form-select-sm" required>
                                                                                <option value="">-- Choose Partner Company --</option>
                                                                                <?php foreach ($companies as $comp): ?>
                                                                                    <option value="<?= $comp['id'] ?>" <?= (!empty($st['current_company_id']) && (int)$st['current_company_id'] === (int)$comp['id']) ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($comp['name']) ?> (<?= htmlspecialchars($comp['state']) ?> &bull; <?= $comp['available_slots'] ?> slots)
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                        <button type="submit" class="btn btn-green btn-sm w-100 fw-bold">
                                                                            <i class="fa-solid fa-check me-1"></i> <?= !empty($st['company_name']) ? 'Reassign Placement' : 'Assign Selected Company' ?>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <!-- Section: Send Direct Notification Message to Student -->
                                                    <div class="p-3 border rounded-3 bg-white mt-3">
                                                        <h6 class="fw-bold text-dark-green border-bottom pb-2 mb-3">
                                                            <i class="fa-solid fa-comment-dots me-2"></i> Send Notification Message to <?= htmlspecialchars($st['full_name']) ?>
                                                        </h6>
                                                        <form action="<?= $baseUrl ?>/index.php?route=supervisor/send-notification" method="POST">
                                                            <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                            <input type="hidden" name="student_id" value="<?= $st['id'] ?>">
                                                            <input type="hidden" name="redirect_to" value="supervisor/dashboard">

                                                            <div class="row g-2">
                                                                <div class="col-md-7">
                                                                    <label class="form-label fw-semibold fs-8 mb-1">Message Subject / Title *</label>
                                                                    <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. SIWES Document Clarification Required" required>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <label class="form-label fw-semibold fs-8 mb-1">Notice Urgency</label>
                                                                    <select name="type" class="form-select form-select-sm">
                                                                        <option value="info">Information / General</option>
                                                                        <option value="warning">Important / Warning</option>
                                                                        <option value="success">Success / Confirmation</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label fw-semibold fs-8 mb-1">Message Content *</label>
                                                                    <textarea name="message" class="form-control form-control-sm" rows="3" placeholder="Enter instructions, feedback on documents, or placement guidance..." required></textarea>
                                                                </div>
                                                                <div class="col-12 text-end">
                                                                    <button type="submit" class="btn btn-green btn-sm fw-bold">
                                                                        <i class="fa-solid fa-paper-plane me-1"></i> Send Notification Message
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>

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

<!-- Modal: Auto-Assign All Unplaced Students -->
<div class="modal fade" id="modalAutoAssignAll" tabindex="-1" aria-labelledby="modalAutoAssignLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold" id="modalAutoAssignLabel">
                    <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Auto-Assign Placements
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/index.php?route=supervisor/auto-assign-placements" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                <input type="hidden" name="student_id" value="0">
                <input type="hidden" name="redirect_to" value="supervisor/dashboard">

                <div class="modal-body p-4">
                    <p class="text-dark fs-7 mb-3">
                        You have <strong><?= $stats['pending_placement'] ?></strong> assigned student(s) currently awaiting placement.
                    </p>
                    <div class="alert alert-info py-2 px-3 fs-8 mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        The smart placement engine evaluates each student's academic department, preferred industry, and geographical location against available partner company slots, allocating the highest compatibility matches automatically.
                    </div>
                    <p class="text-secondary fs-8 mb-0">
                        Both the student and the SIWES coordinator will be notified immediately upon allocation.
                    </p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green btn-sm fw-bold">
                        <i class="fa-solid fa-bolt me-1"></i> Run Auto-Placement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Broadcast Notification to All Assigned Students -->
<div class="modal fade" id="modalBroadcastMessage" tabindex="-1" aria-labelledby="modalBroadcastLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold" id="modalBroadcastLabel">
                    <i class="fa-solid fa-bullhorn me-2"></i> Broadcast Notice to All Assigned Students
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/index.php?route=supervisor/send-notification" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                <input type="hidden" name="student_id" value="0">
                <input type="hidden" name="redirect_to" value="supervisor/dashboard">

                <div class="modal-body p-4">
                    <p class="text-secondary fs-8 mb-3">
                        This message will be dispatched directly to the notification inbox of all <strong><?= count($assignedStudents) ?></strong> students in your supervision cohort.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8 mb-1">Broadcast Title *</label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. SIWES Logbook Submission & Placement Guidelines" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8 mb-1">Priority / Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="info">General Information</option>
                            <option value="warning">Important Warning / Deadline</option>
                            <option value="success">Congratulations / General Update</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold fs-8 mb-1">Broadcast Content *</label>
                        <textarea name="message" class="form-control form-control-sm" rows="4" placeholder="Enter instructions or announcements to be broadcast to all assigned students..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green btn-sm fw-bold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Broadcast
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
