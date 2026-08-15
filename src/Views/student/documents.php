<?php
$pageTitle = 'Document Management';
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

            <div class="row g-4">
                <!-- Document Upload Form -->
                <div class="col-lg-4">
                    <div class="card-custom p-4">
                        <h5 class="fw-bold mb-3 text-dark-green"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload Verification Document</h5>
                        <form action="<?= $baseUrl ?>/index.php?route=student/documents" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                            <div class="mb-3">
                                <label for="doc_type" class="form-label fw-semibold">Document Category *</label>
                                <select class="form-select" id="doc_type" name="doc_type" required>
                                    <option value="SIWES Application Letter">SIWES Application Letter</option>
                                    <option value="Student Identification Card">Student Identification Card</option>
                                    <option value="Passport Photograph">Passport Photograph</option>
                                    <option value="Acceptance Letter">Company Acceptance Letter</option>
                                    <option value="Logbook Submission">SIWES Logbook</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="document_file" class="form-label fw-semibold">Select File (PDF, PNG, JPG, DOC) *</label>
                                <input class="form-control" type="file" id="document_file" name="document_file" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" required>
                                <small class="text-muted fs-8">Maximum file size allowed: 5 MB</small>
                            </div>

                            <button type="submit" class="btn btn-green w-100 fw-bold">
                                <i class="fa-solid fa-upload me-1"></i> Upload Document
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Document Records Table -->
                <div class="col-lg-8">
                    <div class="card-custom p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                            <h5 class="fw-bold mb-0 text-dark-green"><i class="fa-solid fa-folder-open me-2"></i> Uploaded Documents</h5>
                            <span class="badge bg-light-green text-dark-green fw-bold"><?= count($documents) ?> Files</span>
                        </div>

                        <?php if (!empty($documents)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-responsive-stack">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Document Type</th>
                                            <th>File Name</th>
                                            <th>Size</th>
                                            <th>Upload Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($documents as $doc): ?>
                                            <tr>
                                                <td data-label="Document Type" class="fw-bold text-dark"><?= htmlspecialchars($doc['doc_type']) ?></td>
                                                <td data-label="File Name" class="text-truncate" style="max-width: 180px;"><?= htmlspecialchars($doc['file_name']) ?></td>
                                                <td data-label="Size"><?= round($doc['file_size'] / 1024, 1) ?> KB</td>
                                                <td data-label="Upload Date"><?= date('M d, Y', strtotime($doc['uploaded_at'])) ?></td>
                                                <td data-label="Status">
                                                    <span class="badge badge-<?= strtolower($doc['status']) ?>"><?= htmlspecialchars($doc['status']) ?></span>
                                                </td>
                                                <td data-label="Action">
                                                    <a href="<?= $baseUrl ?>/<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-green" title="Download">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fa-solid fa-folder-open fs-1 text-secondary mb-3"></i>
                                <h6 class="fw-bold text-secondary">No documents uploaded yet.</h6>
                                <p class="text-muted fs-7">Use the form on the left to upload your SIWES letter and ID card.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
