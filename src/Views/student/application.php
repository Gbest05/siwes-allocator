<?php
$pageTitle = 'SIWES Application & Document Submission';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Helper;

$flash = Helper::getFlash();
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
                                    <p class="text-secondary fs-8 mb-0">Upload your SIWES Acceptance Letter, Request Letter, or Student ID Card.</p>
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
                                        <h6 class="fw-bold mb-1">Ready for Submission</h6>
                                        <p class="fs-8 mb-2">
                                            Submitting will forward your application preferences and all attached documents for review to:
                                        </p>
                                        <ul class="fs-8 mb-0 ps-3">
                                            <li>
                                                <strong>Attached Institutional Supervisor:</strong> 
                                                <?= !empty($supervisor['supervisor_name']) ? htmlspecialchars($supervisor['supervisor_name']) . " (" . htmlspecialchars($supervisor['designation'] ?? 'Supervisor') . ")" : "Assigned Department Supervisor" ?>
                                            </li>
                                            <li>
                                                <strong>SIWES Unit Coordinator:</strong> Central SIWES Management Office
                                            </li>
                                        </ul>
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
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
