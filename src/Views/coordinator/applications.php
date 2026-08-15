<?php
$pageTitle = 'SIWES Applications';
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
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-file-signature me-2"></i> Application Review Portal</h4>
                        <p class="text-secondary fs-7 mb-0">Review submitted student placement preferences and approve for allocation.</p>
                    </div>
                    <span class="badge bg-light-green text-dark-green fw-bold fs-7"><?= count($applications) ?> Submissions</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-responsive-stack">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Matric Number</th>
                                <th>Department</th>
                                <th>Target Industry</th>
                                <th>Target Location</th>
                                <th>Status</th>
                                <th>Submitted Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td data-label="Student Name" class="fw-bold text-dark"><?= htmlspecialchars($app['full_name']) ?></td>
                                    <td data-label="Matric Number"><span class="badge bg-light text-dark border"><?= htmlspecialchars($app['matric_number']) ?></span></td>
                                    <td data-label="Department"><?= htmlspecialchars($app['dept_name'] ?? 'N/A') ?></td>
                                    <td data-label="Target Industry"><span class="badge bg-light-green text-dark-green"><?= htmlspecialchars($app['preferred_industry']) ?></span></td>
                                    <td data-label="Target Location"><?= htmlspecialchars($app['preferred_location']) ?></td>
                                    <td data-label="Status">
                                        <span class="badge badge-<?= strtolower($app['status']) ?>"><?= htmlspecialchars($app['status']) ?></span>
                                    </td>
                                    <td data-label="Submitted Date"><?= date('M d, Y', strtotime($app['submitted_at'])) ?></td>
                                    <td data-label="Action">
                                        <button class="btn btn-sm btn-outline-green" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $app['id'] ?>">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Review
                                        </button>

                                        <!-- Review & Status Update Modal -->
                                        <div class="modal fade" id="reviewModal<?= $app['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-dark-green"><i class="fa-solid fa-user-check me-2"></i> Review SIWES Application</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?= $baseUrl ?>/index.php?route=coordinator/application-status" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="application_id" value="<?= $app['id'] ?>">

                                                        <div class="modal-body">
                                                            <div class="p-3 bg-light rounded-3 mb-3 fs-7">
                                                                <div><strong>Student:</strong> <?= htmlspecialchars($app['full_name']) ?> (<?= htmlspecialchars($app['matric_number']) ?>)</div>
                                                                <div><strong>Target Industry:</strong> <?= htmlspecialchars($app['preferred_industry']) ?></div>
                                                                <div><strong>Target Location:</strong> <?= htmlspecialchars($app['preferred_location']) ?></div>
                                                                <?php if (!empty($app['notes'])): ?>
                                                                    <div class="mt-2 text-muted"><em>"<?= htmlspecialchars($app['notes']) ?>"</em></div>
                                                                <?php endif; ?>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Application Status</label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="Approved" <?= $app['status'] === 'Approved' ? 'selected' : '' ?>>Approve (Ready for Allocation)</option>
                                                                    <option value="Under Review" <?= $app['status'] === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                                                                    <option value="Rejected" <?= $app['status'] === 'Rejected' ? 'selected' : '' ?>>Reject Application</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Coordinator Review Notes / Remarks</label>
                                                                <textarea name="notes" class="form-control" rows="3" placeholder="Add comments for the student..."><?= htmlspecialchars($app['notes'] ?? '') ?></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-green fw-bold">Save Status Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
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
