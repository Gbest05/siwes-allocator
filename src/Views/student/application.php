<?php
$pageTitle = 'SIWES Application & Placement Status';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Helper;

$flash = Helper::getFlash();
$isSubmitted = !empty($application['status']) && $application['status'] !== 'Draft';
$isAllocated = !empty($allocation) || ($isSubmitted && ($application['status'] ?? '') === 'Allocated');
$isApproved = ($application['status'] ?? '') === 'Approved';
$isRejected = ($application['status'] ?? '') === 'Rejected';

$currentStep = (int)($_GET['step'] ?? (!empty($application['preferred_industry']) ? 2 : 1));
$hasDocs = !empty($documents);
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

            <?php if ($isSubmitted): ?>
                <!-- ========================================================
                     VIEW SUBMITTED APPLICATION & STATUS (LOCKED / ALLOCATED)
                     ======================================================== -->

                <!-- Top Status Header & Actions -->
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <?php if ($isAllocated): ?>
                            <!-- Placement Allocated Banner -->
                            <div class="card-custom p-4 border-start border-4 border-success bg-white shadow-sm">
                                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="rounded-circle bg-light-green text-dark-green d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                            <i class="fa-solid fa-building-circle-check fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                <h4 class="fw-bold mb-0 text-dark-green">SIWES Placement Allocated & Confirmed</h4>
                                                <span class="badge bg-emerald fs-8"><i class="fa-solid fa-lock me-1"></i> Application Locked</span>
                                            </div>
                                            <p class="text-secondary fs-7 mb-0">
                                                Congratulations! Your SIWES placement has been officially confirmed and allocated to 
                                                <strong class="text-dark"><?= htmlspecialchars($allocation['company_name'] ?? 'Partner Organization') ?></strong>.
                                                Because your placement is finalized, your application is locked and cannot be modified or dropped.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                                        <button type="button" class="btn btn-outline-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#allocationNoticeModal">
                                            <i class="fa-solid fa-circle-info me-1"></i> Allocation Notice
                                        </button>
                                        <a href="<?= $baseUrl ?>/index.php?route=allocation-letter" class="btn btn-green btn-sm fw-bold">
                                            <i class="fa-solid fa-file-arrow-down me-1"></i> Placement Letter
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Application Submitted / Under Review Banner -->
                            <div class="card-custom p-4 border-start border-4 border-warning bg-white shadow-sm">
                                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                            <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                <h4 class="fw-bold mb-0 text-dark">SIWES Application Submitted & Under Review</h4>
                                                <?php if ($isApproved): ?>
                                                    <span class="badge bg-info text-dark fs-8"><i class="fa-solid fa-check me-1"></i> Approved by Supervisor</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark fs-8"><i class="fa-solid fa-hourglass-half me-1"></i> Under Review</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-secondary fs-7 mb-1">
                                                Your application and uploaded documents have been forwarded to your institutional supervisor and SIWES coordinator for matching.
                                            </p>
                                            <p class="text-muted fs-8 mb-0">
                                                <i class="fa-solid fa-circle-exclamation text-primary-green me-1"></i>
                                                <strong>Single Submission Policy:</strong> You can only drop your application once. If you need to make changes or update preferences before placement allocation, you must delete your current application below and re-submit.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                                        <button type="button" class="btn btn-outline-danger btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#deleteAppModal">
                                            <i class="fa-solid fa-trash-can me-1"></i> Delete Application & Re-apply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="row g-4">
                    <!-- Column 1: Application Details & Placement -->
                    <div class="col-lg-8">
                        <?php if ($isAllocated && !empty($allocation)): ?>
                            <!-- Partner Company Placement Card -->
                            <div class="card-custom p-4 mb-4 border border-success-subtle bg-light-green bg-opacity-25">
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                    <h5 class="fw-bold mb-0 text-dark-green">
                                        <i class="fa-solid fa-building me-2"></i> Official Placement Details
                                    </h5>
                                    <span class="badge bg-emerald fw-bold">Active Placement</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <small class="text-muted fs-8 d-block">Allocated Organization</small>
                                        <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($allocation['company_name']) ?></h5>
                                        <span class="badge bg-light text-dark border fs-8 mt-1">
                                            <?= htmlspecialchars($allocation['company_industry'] ?? 'General Industry') ?>
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted fs-8 d-block">Location & Address</small>
                                        <div class="fw-semibold text-dark fs-7"><?= htmlspecialchars($allocation['company_address'] ?? 'Official Address') ?></div>
                                        <div class="text-muted fs-8"><?= htmlspecialchars($allocation['company_city'] ?? '') ?>, <?= htmlspecialchars($allocation['company_state'] ?? '') ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted fs-8 d-block">Industry Liaison / Contact Person</small>
                                        <div class="fw-semibold text-dark fs-7"><?= htmlspecialchars($allocation['contact_person'] ?? 'Placement Coordinator') ?></div>
                                        <div class="text-muted fs-8">
                                            <i class="fa-solid fa-phone me-1 text-primary-green"></i> <?= htmlspecialchars($allocation['company_phone'] ?? 'N/A') ?> &bull;
                                            <i class="fa-solid fa-envelope me-1 text-primary-green"></i> <?= htmlspecialchars($allocation['company_email'] ?? 'N/A') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted fs-8 d-block">Matching Compatibility Score</small>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <div class="progress flex-grow-1" style="height: 10px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, max(20, (int)($allocation['compatibility_score'] ?? 80))) ?>%;"></div>
                                            </div>
                                            <span class="fw-bold text-dark fs-7"><?= (int)($allocation['compatibility_score'] ?? 80) ?>%</span>
                                        </div>
                                        <small class="text-muted fs-8">Based on department, industry relevance & location</small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Submitted SIWES Preferences -->
                        <div class="card-custom p-4 mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                <h5 class="fw-bold mb-0 text-dark-green">
                                    <i class="fa-solid fa-sliders me-2"></i> Submitted Placement Preferences
                                </h5>
                                <span class="badge bg-light text-muted border fs-8">
                                    Submitted: <?= date('M d, Y', strtotime($application['updated_at'] ?? $application['created_at'] ?? 'now')) ?>
                                </span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <small class="text-muted fs-8 d-block mb-1">Target Industry Sector</small>
                                        <div class="fw-bold text-dark fs-7">
                                            <i class="fa-solid fa-industry text-primary-green me-1"></i> <?= htmlspecialchars($application['preferred_industry'] ?? 'Not Specified') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <small class="text-muted fs-8 d-block mb-1">Target Location / State</small>
                                        <div class="fw-bold text-dark fs-7">
                                            <i class="fa-solid fa-location-dot text-primary-green me-1"></i> <?= htmlspecialchars($application['preferred_location'] ?? 'Not Specified') ?> State
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <small class="text-muted fs-8 d-block mb-1">Technical Focus & Special Skills Notes</small>
                                        <p class="text-dark fs-7 mb-0">
                                            <?= !empty($application['notes']) ? nl2br(htmlspecialchars($application['notes'])) : '<em class="text-muted">No additional notes provided.</em>' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submitted Documents List -->
                        <div class="card-custom p-4 mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                <h5 class="fw-bold mb-0 text-dark-green">
                                    <i class="fa-solid fa-folder-closed me-2"></i> Attached Verification Documents
                                </h5>
                                <span class="badge bg-light-green text-dark-green fw-bold"><?= count($documents) ?> Files Attached</span>
                            </div>

                            <?php if (empty($documents)): ?>
                                <div class="alert alert-light border text-center py-4 fs-8 text-secondary">
                                    <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> No documents attached to this submission.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle table-responsive-stack">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Document Category</th>
                                                <th>File Name</th>
                                                <th>File Size</th>
                                                <th>Upload Date</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($documents as $doc): ?>
                                                <tr>
                                                    <td data-label="Category" class="fw-bold text-dark fs-8">
                                                        <i class="fa-solid fa-file-pdf text-danger me-1"></i> <?= htmlspecialchars($doc['doc_type']) ?>
                                                    </td>
                                                    <td data-label="File Name" class="fs-8 text-truncate" style="max-width: 180px;">
                                                        <?= htmlspecialchars($doc['file_name']) ?>
                                                    </td>
                                                    <td data-label="Size" class="fs-8 text-muted"><?= round($doc['file_size'] / 1024, 1) ?> KB</td>
                                                    <td data-label="Upload Date" class="fs-8 text-muted"><?= date('M d, Y', strtotime($doc['uploaded_at'])) ?></td>
                                                    <td data-label="Status">
                                                        <span class="badge bg-light text-dark border fs-8"><?= htmlspecialchars($doc['status'] ?? 'Attached') ?></span>
                                                    </td>
                                                    <td data-label="Action" class="text-end">
                                                        <a href="<?= $baseUrl ?>/<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                                                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Column 2: Supervisor & Student Profile -->
                    <div class="col-lg-4">
                        <!-- Attached Institutional Supervisor Card -->
                        <div class="card-custom p-4 mb-4 border-top border-4 border-primary-green" id="supervisorCard">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                <h6 class="fw-bold mb-0 text-dark-green">
                                    <i class="fa-solid fa-user-tie me-2"></i> Institutional Supervisor
                                </h6>
                                <span class="badge bg-light-green text-dark-green fs-8">Assigned</span>
                            </div>

                            <?php if (!empty($supervisor['supervisor_name'])): ?>
                                <div class="text-center py-2 mb-3">
                                    <div class="rounded-circle bg-dark-green text-white d-inline-flex align-items-center justify-content-center fw-bold fs-4 mb-2" style="width: 56px; height: 56px;">
                                        <?= strtoupper(substr($supervisor['supervisor_name'], 0, 1)) ?>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($supervisor['supervisor_name']) ?></h6>
                                    <small class="text-muted d-block"><?= htmlspecialchars($supervisor['designation'] ?? 'Department SIWES Supervisor') ?></small>
                                    <span class="badge bg-light text-muted border fs-8 mt-1">
                                        <?= htmlspecialchars($supervisor['supervisor_department'] ?? 'Academic Department') ?>
                                    </span>
                                </div>

                                <div class="p-3 bg-light rounded-3 border mb-3 fs-8">
                                    <?php if (!empty($supervisor['staff_id'])): ?>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="text-muted">Staff ID:</span>
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($supervisor['staff_id']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-muted">Email:</span>
                                        <a href="mailto:<?= htmlspecialchars($supervisor['supervisor_email']) ?>" class="fw-semibold text-primary-green text-truncate" style="max-width: 170px;">
                                            <?= htmlspecialchars($supervisor['supervisor_email']) ?>
                                        </a>
                                    </div>
                                    <?php if (!empty($supervisor['supervisor_phone'])): ?>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="text-muted">Phone:</span>
                                            <a href="tel:<?= htmlspecialchars($supervisor['supervisor_phone']) ?>" class="fw-semibold text-dark">
                                                <?= htmlspecialchars($supervisor['supervisor_phone']) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="mailto:<?= htmlspecialchars($supervisor['supervisor_email']) ?>?subject=SIWES%20Inquiry%20from%20<?= urlencode($student['full_name']) ?>" class="btn btn-green btn-sm fw-bold">
                                        <i class="fa-solid fa-envelope me-1"></i> Send Email to Supervisor
                                    </a>
                                    <?php if (!empty($supervisor['supervisor_phone'])): ?>
                                        <a href="tel:<?= htmlspecialchars($supervisor['supervisor_phone']) ?>" class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-phone me-1"></i> Call Supervisor
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fa-solid fa-user-clock fs-1 text-muted mb-2"></i>
                                    <h6 class="fw-bold text-dark">Supervisor Pending Assignment</h6>
                                    <p class="text-secondary fs-8 mb-0">
                                        The SIWES coordinator will attach an institutional supervisor to your department cohort shortly.
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Student Academic Info Card -->
                        <div class="card-custom p-4 mb-4">
                            <h6 class="fw-bold mb-3 text-dark-green border-bottom pb-2">
                                <i class="fa-solid fa-id-card me-2"></i> Student Profile
                            </h6>
                            <div class="fs-8">
                                <div class="mb-2">
                                    <span class="text-muted d-block">Full Name</span>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($student['full_name']) ?></span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted d-block">Matriculation Number</span>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($student['matric_number']) ?></span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted d-block">Department & Programme</span>
                                    <span class="fw-semibold text-dark"><?= htmlspecialchars($student['department_name'] ?? 'N/A') ?></span>
                                    <small class="text-muted d-block"><?= htmlspecialchars($student['programme']) ?> &bull; <?= htmlspecialchars($student['level']) ?></small>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted d-block">Email Address</span>
                                    <span class="text-dark"><?= htmlspecialchars($student['email']) ?></span>
                                </div>
                                <div class="mb-0">
                                    <span class="text-muted d-block">Phone Number</span>
                                    <span class="text-dark"><?= htmlspecialchars($student['phone'] ?? 'N/A') ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Navigation Link -->
                        <div class="d-grid gap-2">
                            <a href="<?= $baseUrl ?>/index.php?route=dashboard" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-arrow-left me-1"></i> Back to Student Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Delete Application Confirmation Modal (Only for Unallocated Students) -->
                <?php if (!$isAllocated): ?>
                    <div class="modal fade" id="deleteAppModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold" id="deleteModalLabel">
                                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Delete Submitted SIWES Application
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p class="fs-7 text-dark mb-3">
                                        Are you sure you want to delete your submitted SIWES application?
                                    </p>
                                    <div class="alert alert-warning fs-8 mb-3">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                                        <strong>Single Submission Policy:</strong> Deleting your application will reset your submission status. You will be able to select new preferences, manage your uploaded documents, and re-submit your application before company placement is allocated.
                                    </div>
                                    <p class="fs-8 text-muted mb-0">
                                        <strong>Note:</strong> Once your supervisor allocates you to a partner company, your application will be permanently locked and cannot be deleted.
                                    </p>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                    <form action="<?= $baseUrl ?>/index.php?route=student/application/delete" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                        <button type="submit" class="btn btn-danger btn-sm fw-bold">
                                            <i class="fa-solid fa-trash-can me-1"></i> Yes, Delete & Re-apply
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Pop-up Notice Modal for Allocated Student -->
                <?php if ($isAllocated): ?>
                    <div class="modal fade" id="allocationNoticeModal" tabindex="-1" aria-labelledby="allocModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-dark-green text-white">
                                    <h5 class="modal-title fw-bold" id="allocModalLabel">
                                        <i class="fa-solid fa-lock text-warning me-2"></i> SIWES Placement Finalized
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="text-center mb-3">
                                        <div class="rounded-circle bg-light-green text-dark-green d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                                            <i class="fa-solid fa-building-circle-check fs-2"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">You Have Been Allocated!</h5>
                                        <span class="badge bg-emerald fs-7"><?= htmlspecialchars($allocation['company_name'] ?? 'Partner Organization') ?></span>
                                    </div>
                                    <div class="alert alert-warning fs-8 mb-3">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                        <strong>Application Locked:</strong> Once allocated to an organization, you cannot drop or re-submit your SIWES application.
                                    </div>
                                    <p class="fs-8 text-secondary mb-3">
                                        If you have any complaints, difficulties with your posting location, or require a placement reassignment, please contact your institutional supervisor immediately:
                                    </p>
                                    <div class="p-3 bg-light rounded border fs-8">
                                        <div class="fw-bold text-dark mb-1">
                                            <i class="fa-solid fa-user-tie text-primary-green me-1"></i> <?= htmlspecialchars($supervisor['supervisor_name'] ?? 'Assigned Institutional Supervisor') ?>
                                        </div>
                                        <div class="text-muted mb-1">
                                            <i class="fa-solid fa-id-badge text-primary-green me-1"></i> <?= htmlspecialchars($supervisor['designation'] ?? 'Department SIWES Supervisor') ?> &bull; <?= htmlspecialchars($supervisor['supervisor_department'] ?? ($student['department_name'] ?? 'SIWES Unit')) ?>
                                        </div>
                                        <?php if (!empty($supervisor['supervisor_phone'])): ?>
                                            <div class="mb-1">
                                                <i class="fa-solid fa-phone text-primary-green me-1"></i> <a href="tel:<?= htmlspecialchars($supervisor['supervisor_phone']) ?>" class="text-dark fw-semibold"><?= htmlspecialchars($supervisor['supervisor_phone']) ?></a>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($supervisor['supervisor_email'])): ?>
                                            <div>
                                                <i class="fa-solid fa-envelope text-primary-green me-1"></i> <a href="mailto:<?= htmlspecialchars($supervisor['supervisor_email']) ?>" class="text-primary-green fw-semibold"><?= htmlspecialchars($supervisor['supervisor_email']) ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <?php if (!empty($supervisor['supervisor_email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($supervisor['supervisor_email']) ?>?subject=Placement%20Inquiry%20from%20<?= urlencode($student['full_name']) ?>" class="btn btn-green btn-sm fw-bold">
                                            <i class="fa-solid fa-envelope me-1"></i> Contact Supervisor
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">I Understand</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var noticeModal = new bootstrap.Modal(document.getElementById('allocationNoticeModal'));
                            noticeModal.show();
                        });
                    </script>
                <?php endif; ?>

            <?php else: ?>
                <!-- ========================================================
                     APPLICATION WIZARD (NOT YET SUBMITTED OR DRAFT)
                     ======================================================== -->
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <!-- Workflow Progress Stepper -->
                        <div class="card-custom p-3 mb-4">
                            <div class="d-flex align-items-center justify-content-between position-relative px-2 px-md-4 py-2">
                                <div class="text-center z-1">
                                    <a href="<?= $baseUrl ?>/index.php?route=student/application&step=1" class="text-decoration-none">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold <?= $currentStep >= 1 ? 'bg-primary-green text-white' : 'bg-light text-secondary' ?>" style="width: 38px; height: 38px;">
                                            1
                                        </div>
                                        <div class="fs-8 fw-semibold mt-1 <?= $currentStep >= 1 ? 'text-dark-green' : 'text-muted' ?>">1. Preferences</div>
                                    </a>
                                </div>

                                <div class="flex-grow-1 mx-2" style="height: 3px; background-color: <?= $currentStep >= 2 ? 'var(--primary-green)' : '#E5E7EB' ?>;"></div>

                                <div class="text-center z-1">
                                    <a href="<?= $baseUrl ?>/index.php?route=student/application&step=2" class="text-decoration-none">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold <?= $currentStep >= 2 ? 'bg-primary-green text-white' : 'bg-light text-secondary' ?>" style="width: 38px; height: 38px;">
                                            2
                                        </div>
                                        <div class="fs-8 fw-semibold mt-1 <?= $currentStep >= 2 ? 'text-dark-green' : 'text-muted' ?>">2. Upload Documents</div>
                                    </a>
                                </div>

                                <div class="flex-grow-1 mx-2" style="height: 3px; background-color: <?= ($hasDocs && $currentStep >= 2) ? 'var(--primary-green)' : '#E5E7EB' ?>;"></div>

                                <div class="text-center z-1">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold <?= ($hasDocs && $currentStep >= 2) ? 'bg-emerald text-white' : 'bg-light text-secondary' ?>" style="width: 38px; height: 38px;">
                                        3
                                    </div>
                                    <div class="fs-8 fw-semibold mt-1 <?= ($hasDocs && $currentStep >= 2) ? 'text-dark-green' : 'text-muted' ?>">3. Submit to Supervisor</div>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 1: PREFERENCES FORM -->
                        <div class="card-custom p-4 p-md-5 mb-4 <?= $currentStep === 1 ? '' : 'border-dashed' ?>">
                            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light-green text-dark-green rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="fa-solid fa-file-signature fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0 text-dark-green">Step 1: SIWES Placement Preferences</h5>
                                        <p class="text-secondary fs-8 mb-0">Specify your preferred industry and target location for compatibility matching.</p>
                                    </div>
                                </div>
                                <?php if ($currentStep === 2): ?>
                                    <a href="<?= $baseUrl ?>/index.php?route=student/application&step=1" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-pen me-1"></i> Edit Preferences
                                    </a>
                                <?php endif; ?>
                            </div>

                            <form action="<?= $baseUrl ?>/index.php?route=student/application" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                <input type="hidden" name="action" value="save_preferences">

                                <div class="row g-4">
                                    <div class="col-12 col-md-6">
                                        <label for="preferred_industry" class="form-label fw-semibold fs-7">Preferred Industry Sector *</label>
                                        <select class="form-select" id="preferred_industry" name="preferred_industry" required <?= $currentStep === 2 ? 'disabled' : '' ?>>
                                            <option value="">-- Select Industry Sector --</option>
                                            <option value="Software Development & IT" <?= ($application['preferred_industry'] ?? '') === 'Software Development & IT' ? 'selected' : '' ?>>Software Development & IT</option>
                                            <option value="Fintech & Cyber Security" <?= ($application['preferred_industry'] ?? '') === 'Fintech & Cyber Security' ? 'selected' : '' ?>>Fintech & Cyber Security</option>
                                            <option value="Telecommunications & Cloud" <?= ($application['preferred_industry'] ?? '') === 'Telecommunications & Cloud' ? 'selected' : '' ?>>Telecommunications & Cloud</option>
                                            <option value="Power & Electrical Grid" <?= ($application['preferred_industry'] ?? '') === 'Power & Electrical Grid' ? 'selected' : '' ?>>Power & Electrical Grid</option>
                                            <option value="Research & Hardware Systems" <?= ($application['preferred_industry'] ?? '') === 'Research & Hardware Systems' ? 'selected' : '' ?>>Research & Hardware Systems</option>
                                            <option value="Energy & Embedded Control" <?= ($application['preferred_industry'] ?? '') === 'Energy & Embedded Control' ? 'selected' : '' ?>>Energy & Embedded Control</option>
                                        </select>
                                        <small class="text-muted fs-8">Used to calculate +30 points Industry Relevance Score</small>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="preferred_location" class="form-label fw-semibold fs-7">Preferred State / Location *</label>
                                        <select class="form-select" id="preferred_location" name="preferred_location" required <?= $currentStep === 2 ? 'disabled' : '' ?>>
                                            <option value="">-- Select Target Location --</option>
                                            <option value="Lagos" <?= ($application['preferred_location'] ?? '') === 'Lagos' ? 'selected' : '' ?>>Lagos State</option>
                                            <option value="Abuja" <?= ($application['preferred_location'] ?? '') === 'Abuja' ? 'selected' : '' ?>>FCT Abuja</option>
                                            <option value="Oyo" <?= ($application['preferred_location'] ?? '') === 'Oyo' ? 'selected' : '' ?>>Oyo State (Ibadan)</option>
                                            <option value="Rivers" <?= ($application['preferred_location'] ?? '') === 'Rivers' ? 'selected' : '' ?>>Rivers State (Port Harcourt)</option>
                                            <option value="Enugu" <?= ($application['preferred_location'] ?? '') === 'Enugu' ? 'selected' : '' ?>>Enugu State</option>
                                            <option value="Kaduna" <?= ($application['preferred_location'] ?? '') === 'Kaduna' ? 'selected' : '' ?>>Kaduna State</option>
                                        </select>
                                        <small class="text-muted fs-8">Used to calculate +20 points Geographic Proximity Score</small>
                                    </div>

                                    <div class="col-12">
                                        <label for="notes" class="form-label fw-semibold fs-7">Special Skills & Technical Focus Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Mention relevant software skills, programming languages, hardware experience or health accommodations..." <?= $currentStep === 2 ? 'readonly' : '' ?>><?= htmlspecialchars($application['notes'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <?php if ($currentStep === 1): ?>
                                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                        <a href="<?= $baseUrl ?>/index.php?route=dashboard" class="btn btn-outline-secondary">
                                            <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
                                        </a>
                                        <button type="submit" class="btn btn-green fw-bold px-4 py-2">
                                            Save & Proceed to Upload Documents <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>

                        <!-- STEP 2 & 3: DOCUMENT UPLOADS & FINAL SUBMISSION -->
                        <?php if ($currentStep === 2 || !empty($application['preferred_industry'])): ?>
                            <div class="card-custom p-4 p-md-5 mb-4">
                                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                                    <div class="bg-light-green text-dark-green rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="fa-solid fa-folder-open fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0 text-dark-green">Step 2: Upload Required SIWES Documents</h5>
                                        <p class="text-secondary fs-8 mb-0">Upload your SIWES Application Letter, Company Acceptance Letter, or Student ID Card.</p>
                                    </div>
                                </div>

                                <!-- Document Upload Box -->
                                <div class="p-3 p-md-4 bg-light rounded-3 mb-4 border">
                                    <form action="<?= $baseUrl ?>/index.php?route=student/documents" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                        <input type="hidden" name="redirect_to" value="student/application?step=2">

                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-5">
                                                <label class="form-label fw-bold fs-8">Document Category *</label>
                                                <select name="doc_type" class="form-select form-select-sm" required>
                                                    <option value="SIWES Application Letter">SIWES Application Letter</option>
                                                    <option value="Company Acceptance Letter">Company Acceptance Letter</option>
                                                    <option value="Student Identification Card">Student Identification Card</option>
                                                    <option value="SIWES Form 8 (Endorsement)">SIWES Form 8 (Endorsement)</option>
                                                    <option value="Logbook Clearance">Logbook Clearance</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label fw-bold fs-8">Select File (PDF, PNG, JPG, DOC - Max 5MB) *</label>
                                                <input type="file" name="document_file" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-green btn-sm w-100 fw-bold">
                                                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Uploaded Documents Table -->
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-circle-check text-primary-green me-2"></i> Attached Documents (<?= count($documents) ?>)</h6>
                                    <?php if (empty($documents)): ?>
                                        <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                                            <i class="fa-solid fa-triangle-exclamation fs-5 flex-shrink-0"></i>
                                            <div class="fs-8">
                                                <strong>No documents uploaded yet!</strong> Please upload at least one required document above before submitting your application.
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover align-middle border mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Document Category</th>
                                                        <th>File Name</th>
                                                        <th>Size</th>
                                                        <th>Uploaded Date</th>
                                                        <th>Status</th>
                                                        <th class="text-end">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($documents as $doc): ?>
                                                        <tr>
                                                            <td class="fw-semibold text-dark fs-8"><?= htmlspecialchars($doc['doc_type']) ?></td>
                                                            <td class="fs-8">
                                                                <a href="<?= $baseUrl ?>/<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="text-primary-green text-decoration-none">
                                                                    <i class="fa-solid fa-paperclip me-1"></i> <?= htmlspecialchars($doc['file_name']) ?>
                                                                </a>
                                                            </td>
                                                            <td class="fs-8 text-muted"><?= round($doc['file_size'] / 1024, 1) ?> KB</td>
                                                            <td class="fs-8 text-muted"><?= date('M d, Y', strtotime($doc['uploaded_at'])) ?></td>
                                                            <td>
                                                                <span class="badge bg-light text-dark border fs-8">Uploaded</span>
                                                            </td>
                                                            <td class="text-end">
                                                                <form action="<?= $baseUrl ?>/index.php?route=student/documents/delete" method="POST" class="d-inline" onsubmit="return confirm('Remove this uploaded document?');">
                                                                    <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                                    <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                                                    <input type="hidden" name="redirect_to" value="student/application?step=2">
                                                                    <button type="submit" class="btn btn-link text-danger p-0 fs-8" title="Remove Document">
                                                                        <i class="fa-solid fa-trash-can"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- STEP 3: FINAL SUBMISSION CARD -->
                                <div class="p-4 bg-light-green text-dark-green rounded-3 border border-success-subtle mb-4">
                                    <div class="d-flex align-items-start gap-3">
                                        <i class="fa-solid fa-paper-plane fs-3 text-primary-green flex-shrink-0 mt-1"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Step 3: Ready for Final Submission</h6>
                                            <p class="fs-8 mb-2">
                                                Submitting will forward your application preferences and all attached documents for review and placement allocation to:
                                            </p>
                                            <ul class="fs-8 mb-2 ps-3">
                                                <li>
                                                    <strong>Attached Institutional Supervisor:</strong> 
                                                    <?= !empty($supervisor['supervisor_name']) ? htmlspecialchars($supervisor['supervisor_name']) . " (" . htmlspecialchars($supervisor['designation'] ?? 'Supervisor') . ")" : "Assigned Department Supervisor" ?>
                                                </li>
                                                <li>
                                                    <strong>SIWES Unit Coordinator:</strong> Central SIWES Management Office
                                                </li>
                                            </ul>
                                            <div class="alert alert-warning py-2 px-3 mb-0 fs-8 text-dark">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                                <strong>Notice:</strong> You can only submit your application once. If you need to make changes before placement allocation, you will need to delete and re-submit.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form action="<?= $baseUrl ?>/index.php?route=student/application" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                    <input type="hidden" name="action" value="final_submit">
                                    <input type="hidden" name="preferred_industry" value="<?= htmlspecialchars($application['preferred_industry'] ?? '') ?>">
                                    <input type="hidden" name="preferred_location" value="<?= htmlspecialchars($application['preferred_location'] ?? '') ?>">
                                    <input type="hidden" name="notes" value="<?= htmlspecialchars($application['notes'] ?? '') ?>">

                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 border-top">
                                        <a href="<?= $baseUrl ?>/index.php?route=student/application&step=1" class="btn btn-outline-secondary">
                                            <i class="fa-solid fa-arrow-left me-1"></i> Back to Preferences
                                        </a>
                                        <button type="submit" class="btn btn-green fw-bold px-4 py-2" <?= empty($documents) ? 'disabled' : '' ?>>
                                            <i class="fa-solid fa-paper-plane me-2"></i> Submit Application to Supervisor & Coordinator
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
