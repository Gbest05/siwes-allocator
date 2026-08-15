<?php
$pageTitle = 'SIWES Allocation Suite';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Helper;

$flash = Helper::getFlash();
$extraJs = ['allocation.js'];
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

            <!-- Section 1: Unallocated Students Pool -->
            <div class="card-custom p-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-diagram-project me-2"></i> Smart SIWES Allocation Suite</h4>
                        <p class="text-secondary fs-7 mb-0">Select an approved student to trigger the multi-criteria compatibility matching engine.</p>
                    </div>
                    <span class="badge bg-light-green text-dark-green fw-bold align-self-start align-self-md-auto"><?= count($approvedApps) ?> Unallocated Pool</span>
                </div>

                <?php if (!empty($approvedApps)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-responsive-stack">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Matric Number</th>
                                    <th>Department</th>
                                    <th>Target Industry</th>
                                    <th>Target Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($approvedApps as $app): ?>
                                    <tr>
                                        <td data-label="Student Name" class="fw-bold text-dark"><?= htmlspecialchars($app['full_name']) ?></td>
                                        <td data-label="Matric Number"><span class="badge bg-light text-dark border"><?= htmlspecialchars($app['matric_number']) ?></span></td>
                                        <td data-label="Department"><?= htmlspecialchars($app['dept_name'] ?? 'N/A') ?></td>
                                        <td data-label="Target Industry"><span class="badge bg-light-green text-dark-green"><?= htmlspecialchars($app['preferred_industry']) ?></span></td>
                                        <td data-label="Target Location"><?= htmlspecialchars($app['preferred_location']) ?></td>
                                        <td data-label="Action">
                                            <button class="btn btn-sm btn-green btn-run-match" 
                                                    data-app-id="<?= $app['id'] ?>"
                                                    data-student-id="<?= $app['student_id'] ?>"
                                                    data-student-name="<?= htmlspecialchars($app['full_name']) ?>"
                                                    data-dept="<?= htmlspecialchars($app['dept_name'] ?? 'Computer Science') ?>"
                                                    data-industry="<?= htmlspecialchars($app['preferred_industry']) ?>"
                                                    data-loc="<?= htmlspecialchars($app['preferred_location']) ?>">
                                                <i class="fa-solid fa-bolt me-1"></i> Calculate Score & Allocate
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fa-solid fa-circle-check fs-1 text-success mb-2"></i>
                        <h6>No unallocated approved students in queue.</h6>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section 2: Completed Allocations Directory -->
            <div class="card-custom p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark-green"><i class="fa-solid fa-list-check me-2"></i> Active Allocations</h5>
                    <span class="badge bg-emerald fw-bold align-self-start align-self-md-auto"><?= count($allocations) ?> Placed</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-responsive-stack">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Department</th>
                                <th>Allocated Company</th>
                                <th>Location</th>
                                <th>Match Score</th>
                                <th>Status</th>
                                <th>Reassign</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allocations as $alloc): ?>
                                <tr>
                                    <td data-label="Student">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($alloc['student_name']) ?></div>
                                        <small class="text-muted fs-8"><?= htmlspecialchars($alloc['matric_number']) ?></small>
                                    </td>
                                    <td data-label="Department"><?= htmlspecialchars($alloc['dept_name'] ?? 'N/A') ?></td>
                                    <td data-label="Allocated Company" class="fw-bold text-dark-green"><?= htmlspecialchars($alloc['company_name']) ?></td>
                                    <td data-label="Location"><?= htmlspecialchars($alloc['company_city']) ?>, <?= htmlspecialchars($alloc['company_state']) ?></td>
                                    <td data-label="Match Score">
                                        <span class="match-score-badge"><?= $alloc['compatibility_score'] ?>% Match</span>
                                    </td>
                                    <td data-label="Status">
                                        <span class="badge bg-light-green text-dark-green fw-bold"><?= htmlspecialchars($alloc['status']) ?></span>
                                    </td>
                                    <td data-label="Reassign">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#reassignModal<?= $alloc['id'] ?>">
                                            <i class="fa-solid fa-arrows-rotate me-1"></i> Reassign
                                        </button>

                                        <!-- Reassign Modal -->
                                        <div class="modal fade" id="reassignModal<?= $alloc['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-dark-green"><i class="fa-solid fa-arrows-rotate me-2"></i> Reassign Student</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?= $baseUrl ?>/index.php?route=coordinator/reassign" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="allocation_id" value="<?= $alloc['id'] ?>">

                                                        <div class="modal-body">
                                                            <p class="fs-7">Reassign <strong><?= htmlspecialchars($alloc['student_name']) ?></strong> from <strong><?= htmlspecialchars($alloc['company_name']) ?></strong> to another organization.</p>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Select New Organization</label>
                                                                <select name="new_company_id" class="form-select" required>
                                                                    <option value="">-- Choose Target Company --</option>
                                                                    <?php foreach ($companies as $comp): ?>
                                                                        <?php if ($comp['id'] != $alloc['company_id']): ?>
                                                                            <option value="<?= $comp['id'] ?>">
                                                                                <?= htmlspecialchars($comp['name']) ?> (<?= htmlspecialchars($comp['state']) ?>) &bull; <?= $comp['available_slots'] ?> slots left
                                                                            </option>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-green fw-bold">Confirm Reassignment</button>
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

<!-- SMART MATCH ALGORITHM MODAL -->
<div class="modal fade" id="smartMatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-calculator me-2"></i> Smart Compatibility Matching Score</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Selected Student Details Summary -->
                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="row g-2 fs-7">
                        <div class="col-sm-6"><strong>Student:</strong> <span id="modalStudentName"></span></div>
                        <div class="col-sm-6"><strong>Department:</strong> <span id="modalStudentDept"></span></div>
                        <div class="col-sm-6"><strong>Preferred Industry:</strong> <span id="modalStudentIndustry" class="text-primary-green fw-bold"></span></div>
                        <div class="col-sm-6"><strong>Preferred Location:</strong> <span id="modalStudentLoc" class="text-dark fw-bold"></span></div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 text-dark-green">Recommended Partner Companies (Ranked by Compatibility Score):</h6>

                <div class="list-group">
                    <?php foreach ($companies as $comp): ?>
                        <div class="list-group-item p-3 mb-2 border rounded-3 company-match-item"
                             data-comp-industry="<?= htmlspecialchars($comp['industry']) ?>"
                             data-comp-state="<?= htmlspecialchars($comp['state']) ?>"
                             data-comp-slots="<?= $comp['available_slots'] ?>">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($comp['name']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($comp['industry']) ?> &bull; <?= htmlspecialchars($comp['city']) ?>, <?= htmlspecialchars($comp['state']) ?></small>
                                </div>
                                <div class="text-sm-end">
                                    <span class="match-score-pill badge bg-success">90% Match</span>
                                    <div class="fs-8 text-secondary mt-1">Available Slots: <strong><?= $comp['available_slots'] ?> / <?= $comp['total_capacity'] ?></strong></div>
                                </div>
                            </div>

                            <div class="progress mb-3" style="height: 6px;">
                                <div class="match-progress-bar progress-bar bg-success" style="width: 90%;"></div>
                            </div>

                            <form action="<?= $baseUrl ?>/index.php?route=coordinator/allocate" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                <input type="hidden" name="application_id" id="modalAppIdInput">
                                <input type="hidden" name="student_id" id="modalStudentIdInput">
                                <input type="hidden" name="company_id" value="<?= $comp['id'] ?>">

                                <button type="submit" class="btn btn-sm btn-green fw-bold w-100 w-sm-auto">
                                    <i class="fa-solid fa-check me-1"></i> Accept & Allocate Student Here
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
