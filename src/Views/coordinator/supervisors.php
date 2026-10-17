<?php
$pageTitle = 'Assign Supervisors';
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

            <!-- 1. Header Banner & Actions -->
            <div class="card-custom p-4 mb-4">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <span class="badge bg-light-green text-dark-green fw-bold text-uppercase mb-2">
                            <i class="fa-solid fa-chalkboard-user me-1"></i> Academic Supervision Module
                        </span>
                        <h4 class="fw-bold mb-1 text-dark-green">Supervisor Management & Student Allocation</h4>
                        <p class="text-secondary fs-7 mb-0">
                            Assign students to institutional supervisors manually or use the automatic equal distribution engine.
                        </p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-green fw-bold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalAutoDistribute">
                            <i class="fa-solid fa-scale-balanced"></i> Auto Distribute Equally
                        </button>
                        <button type="button" class="btn btn-outline-success fw-bold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalAddSupervisor">
                            <i class="fa-solid fa-user-plus"></i> Add Supervisor
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. Statistics Overview Cards -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Total Supervisors</span>
                            <h3 class="fw-bold mb-0 text-dark-green"><?= $stats['total_supervisors'] ?></h3>
                            <small class="text-muted fs-8">Active faculty supervisors</small>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Total Students</span>
                            <h3 class="fw-bold mb-0 text-dark-charcoal"><?= $stats['total_students'] ?></h3>
                            <small class="text-muted fs-8">Registered for SIWES</small>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Assigned to Supervisor</span>
                            <h3 class="fw-bold mb-0 text-emerald"><?= $stats['assigned_students'] ?></h3>
                            <small class="text-muted fs-8">Have attached supervisor</small>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div>
                            <span class="text-secondary fs-7 fw-semibold d-block mb-1">Unassigned Students</span>
                            <h3 class="fw-bold mb-0 text-warning"><?= $stats['unassigned_students'] ?></h3>
                            <small class="text-muted fs-8">Pending supervisor</small>
                        </div>
                        <div class="stat-icon bg-light-green text-dark-green">
                            <i class="fa-solid fa-user-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Available Supervisors Capacity Cards -->
            <div class="card-custom p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-users-line me-2"></i> Active Supervisors Roster & Workload</h5>
                        <p class="text-secondary fs-8 mb-0">Current student distribution load across active institutional supervisors.</p>
                    </div>
                    <span class="badge bg-light text-dark border fw-bold"><?= count($supervisors) ?> Supervisors</span>
                </div>

                <div class="row g-3">
                    <?php if (empty($supervisors)): ?>
                        <div class="col-12 text-center py-4 text-muted">
                            <i class="fa-solid fa-user-xmark fs-2 mb-2 d-block"></i>
                            No supervisor accounts found. Click <strong>Add Supervisor</strong> to register your faculty supervisors.
                        </div>
                    <?php else: ?>
                        <?php foreach ($supervisors as $sup): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 border rounded-3 bg-white h-100 shadow-sm d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-light-green text-dark-green fw-bold fs-8"><?= htmlspecialchars($sup['staff_id'] ?? 'Staff') ?></span>
                                            <span class="badge bg-dark-green text-white fw-bold fs-8"><?= $sup['assigned_student_count'] ?> Students</span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?= htmlspecialchars($sup['full_name']) ?>">
                                            <?= htmlspecialchars($sup['full_name']) ?>
                                        </h6>
                                        <small class="text-secondary d-block fs-8 mb-2">
                                            <i class="fa-solid fa-sitemap me-1 text-primary-green"></i> <?= htmlspecialchars($sup['dept_name'] ?? 'General Academic Department') ?>
                                        </small>
                                        <div class="fs-8 text-muted mb-2">
                                            <div class="text-truncate"><i class="fa-solid fa-envelope me-1"></i> <?= htmlspecialchars($sup['email']) ?></div>
                                            <?php if (!empty($sup['phone'])): ?>
                                                <div><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($sup['phone']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-top mt-2 d-flex justify-content-between align-items-center">
                                        <small class="text-secondary fs-8"><?= htmlspecialchars($sup['designation'] ?? 'Supervisor') ?></small>
                                        <a href="<?= $baseUrl ?>/index.php?route=coordinator/supervisors&supervisor_id=<?= $sup['id'] ?>" class="btn btn-outline-success btn-xs fw-semibold">
                                            View Students (<?= $sup['assigned_student_count'] ?>)
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 4. Student Assignment Table & Filter Controls -->
            <div class="card-custom p-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark-green">
                            <i class="fa-solid fa-list-check me-2"></i> Student Assignment Directory
                        </h5>
                        <p class="text-secondary fs-8 mb-0">
                            Assign individual students or select multiple students for batch allocation to a supervisor.
                        </p>
                    </div>
                </div>

                <!-- Search & Filters Bar -->
                <form method="GET" action="<?= $baseUrl ?>/index.php" class="row g-2 mb-4">
                    <input type="hidden" name="route" value="coordinator/supervisors">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="q" class="form-control border-start-0" placeholder="Search by student name, matric, or email..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Assignment Statuses</option>
                            <option value="assigned" <?= ($_GET['status'] ?? '') === 'assigned' ? 'selected' : '' ?>>Assigned Only</option>
                            <option value="unassigned" <?= ($_GET['status'] ?? '') === 'unassigned' ? 'selected' : '' ?>>Unassigned Only</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="supervisor_id" class="form-select">
                            <option value="">Filter by Supervisor</option>
                            <?php foreach ($supervisors as $sup): ?>
                                <option value="<?= $sup['id'] ?>" <?= ((int)($_GET['supervisor_id'] ?? 0) === (int)$sup['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sup['full_name']) ?> (<?= $sup['assigned_student_count'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button type="submit" class="btn btn-green w-100" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                        <a href="<?= $baseUrl ?>/index.php?route=coordinator/supervisors" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                    </div>
                </form>

                <!-- Bulk Assignment Bar -->
                <form method="POST" action="<?= $baseUrl ?>/index.php?route=coordinator/assign-supervisor" id="bulkAssignForm">
                    <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                    <div class="p-3 bg-light rounded-3 mb-3 border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="selectAllStudents">
                                <label class="form-check-label fw-bold text-dark fs-7" for="selectAllStudents">Select All</label>
                            </div>
                            <span class="text-secondary fs-8" id="selectedCountText">(0 selected)</span>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0" style="min-width: 320px;">
                            <select name="supervisor_id" class="form-select form-select-sm" required>
                                <option value="">-- Choose Supervisor for Selected --</option>
                                <?php foreach ($supervisors as $sup): ?>
                                    <option value="<?= $sup['id'] ?>">
                                        <?= htmlspecialchars($sup['full_name']) ?> (<?= $sup['assigned_student_count'] ?> assigned)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-green btn-sm fw-bold text-nowrap" id="bulkSubmitBtn" disabled>
                                <i class="fa-solid fa-check-double me-1"></i> Assign Selected
                            </button>
                        </div>
                    </div>

                    <?php if (empty($students)): ?>
                        <div class="text-center py-5">
                            <i class="fa-solid fa-user-graduate fs-1 text-muted mb-3"></i>
                            <h6 class="fw-bold text-dark">No Student Records Found</h6>
                            <p class="text-secondary fs-8">No students match your filter criteria.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-responsive-stack">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>Student Details</th>
                                        <th>Department & Level</th>
                                        <th>Placement Company</th>
                                        <th>Current Assigned Supervisor</th>
                                        <th style="min-width: 260px;">Manual Assignment / Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $st): ?>
                                        <tr>
                                            <td>
                                                <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="<?= $st['id'] ?>">
                                            </td>
                                            <td data-label="Student Details">
                                                <div class="fw-bold text-dark fs-7"><?= htmlspecialchars($st['student_name']) ?></div>
                                                <span class="badge bg-light text-dark border fs-8"><?= htmlspecialchars($st['matric_number']) ?></span>
                                            </td>
                                            <td data-label="Department & Level">
                                                <div class="fw-semibold text-dark fs-7"><?= htmlspecialchars($st['dept_name'] ?? 'N/A') ?></div>
                                                <small class="text-muted fs-8"><?= htmlspecialchars($st['programme']) ?> &bull; <?= htmlspecialchars($st['level']) ?></small>
                                            </td>
                                            <td data-label="Placement Company">
                                                <?php if (!empty($st['company_name'])): ?>
                                                    <div class="fw-semibold text-dark fs-8"><i class="fa-solid fa-building text-primary-green me-1"></i> <?= htmlspecialchars($st['company_name']) ?></div>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">Pending Match</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Assigned Supervisor">
                                                <?php if (!empty($st['supervisor_name'])): ?>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="badge bg-emerald fs-8"><i class="fa-solid fa-user-check me-1"></i> <?= htmlspecialchars($st['supervisor_name']) ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark fs-8"><i class="fa-solid fa-clock me-1"></i> Unassigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Manual Assignment">
                                                <!-- Inline Single Manual Assignment Form -->
                                                <div class="d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-outline-success btn-xs fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAssignSingle<?= $st['id'] ?>">
                                                        <i class="fa-solid fa-pen-to-square me-1"></i> Change Supervisor
                                                    </button>

                                                    <?php if (!empty($st['supervisor_name'])): ?>
                                                        <button type="button" class="btn btn-outline-danger btn-xs" onclick="document.getElementById('unassignForm<?= $st['id'] ?>').submit();" title="Remove Supervisor">
                                                            <i class="fa-solid fa-user-xmark"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </form>

                <!-- Hidden individual unassign forms -->
                <?php foreach ($students as $st): ?>
                    <?php if (!empty($st['supervisor_name'])): ?>
                        <form id="unassignForm<?= $st['id'] ?>" method="POST" action="<?= $baseUrl ?>/index.php?route=coordinator/unassign-supervisor" style="display:none;">
                            <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                            <input type="hidden" name="student_id" value="<?= $st['id'] ?>">
                        </form>
                    <?php endif; ?>

                    <!-- Single Assignment Modal for Student <?= $st['id'] ?> -->
                    <div class="modal fade" id="modalAssignSingle<?= $st['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-dark-green text-white">
                                    <h6 class="modal-title fw-bold">
                                        <i class="fa-solid fa-chalkboard-user me-2"></i> Assign Supervisor for <?= htmlspecialchars($st['student_name']) ?>
                                    </h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="<?= $baseUrl ?>/index.php?route=coordinator/assign-supervisor">
                                    <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                    <input type="hidden" name="student_id" value="<?= $st['id'] ?>">
                                    <div class="modal-body p-4">
                                        <div class="mb-3 p-3 bg-light rounded-3 border">
                                            <div class="fs-7"><strong>Matric No:</strong> <?= htmlspecialchars($st['matric_number']) ?></div>
                                            <div class="fs-7"><strong>Department:</strong> <?= htmlspecialchars($st['dept_name'] ?? 'N/A') ?></div>
                                            <div class="fs-7"><strong>Current Supervisor:</strong> <?= htmlspecialchars($st['supervisor_name'] ?? 'None (Unassigned)') ?></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold fs-7">Select Institutional Supervisor *</label>
                                            <select name="supervisor_id" class="form-select" required>
                                                <option value="">-- Choose Supervisor --</option>
                                                <?php foreach ($supervisors as $sup): ?>
                                                    <option value="<?= $sup['id'] ?>" <?= ((int)($st['supervisor_user_id'] ?? 0) === (int)$sup['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($sup['full_name']) ?> (<?= $sup['assigned_student_count'] ?> students &bull; <?= htmlspecialchars($sup['dept_name'] ?? 'General') ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-muted fs-8 mt-1 d-block">
                                                Assigning will link the supervisor to this student's profile and notify both parties.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-green btn-sm fw-bold">
                                            <i class="fa-solid fa-save me-1"></i> Save Assignment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL 1: AUTOMATIC EQUAL DISTRIBUTION
     ========================================================================= -->
<div class="modal fade" id="modalAutoDistribute" tabindex="-1" aria-labelledby="autoDistributeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold" id="autoDistributeLabel">
                    <i class="fa-solid fa-scale-balanced me-2"></i> Auto-Distribute Students Equally
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= $baseUrl ?>/index.php?route=coordinator/auto-distribute-supervisors">
                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                <div class="modal-body p-4">
                    <div class="p-3 bg-light-green text-dark-green rounded-3 mb-3 border border-success-subtle">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fa-solid fa-circle-check fs-5"></i>
                            <span class="fw-bold fs-7">Equal Sharing Algorithm Active</span>
                        </div>
                        <p class="fs-8 mb-0">
                            The smart distribution engine calculates the exact ratio of students to available supervisors and shares them equally in balanced round-robin order.
                        </p>
                    </div>

                    <div class="row g-2 mb-3 text-center">
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light">
                                <span class="text-secondary fs-8 d-block">Available Students</span>
                                <span class="fw-bold text-dark fs-5"><?= $stats['total_students'] ?></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light">
                                <span class="text-secondary fs-8 d-block">Available Supervisors</span>
                                <span class="fw-bold text-dark fs-5"><?= $stats['total_supervisors'] ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($stats['total_supervisors'] > 0): ?>
                        <div class="p-2 mb-3 text-secondary fs-8 bg-light rounded text-center">
                            <i class="fa-solid fa-calculator me-1 text-primary-green"></i>
                            Expected quota: approximately <strong><?= ceil($stats['total_students'] / max(1, $stats['total_supervisors'])) ?> students</strong> per supervisor.
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold fs-7">Distribution Mode</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="mode" id="modeAll" value="all" checked>
                            <label class="form-check-label fs-7" for="modeAll">
                                <strong>Rebalance All Students Equally</strong>
                                <small class="text-muted d-block fs-8">Distributes all <?= $stats['total_students'] ?> students evenly across all <?= $stats['total_supervisors'] ?> supervisors.</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" id="modeUnassigned" value="unassigned">
                            <label class="form-check-label fs-7" for="modeUnassigned">
                                <strong>Assign Unassigned Students Only (<?= $stats['unassigned_students'] ?>)</strong>
                                <small class="text-muted d-block fs-8">Leaves existing assignments intact and shares only pending students.</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green btn-sm fw-bold">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Start Equal Distribution
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL 2: ADD NEW SUPERVISOR
     ========================================================================= -->
<div class="modal fade" id="modalAddSupervisor" tabindex="-1" aria-labelledby="addSupervisorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold" id="addSupervisorLabel">
                    <i class="fa-solid fa-user-plus me-2"></i> Register New Faculty Supervisor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= $baseUrl ?>/index.php?route=coordinator/create-supervisor">
                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold fs-7">Supervisor Full Name *</label>
                        <input type="text" name="full_name" class="form-control" placeholder="e.g. Dr. Mrs. Funke Adeyemi" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs-7">Official Email *</label>
                            <input type="email" name="email" class="form-control" placeholder="f.adeyemi@siwesp.edu.ng" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs-7">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="08031234567">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs-7">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">-- Choose Department --</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs-7">Staff ID Number</label>
                            <input type="text" name="staff_id" class="form-control" placeholder="e.g. SUP/COM/04">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold fs-7">Academic Designation</label>
                        <input type="text" name="designation" class="form-control" value="Senior Lecturer & SIWES Supervisor">
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <small class="text-secondary fs-8">
                            <i class="fa-solid fa-lock text-muted me-1"></i>
                            Default password will be set to: <strong>password123</strong> (supervisor can change it upon logging in).
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green btn-sm fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Create Supervisor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllStudents');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const submitBtn = document.getElementById('bulkSubmitBtn');
    const countText = document.getElementById('selectedCountText');

    function updateCount() {
        const checked = document.querySelectorAll('.student-checkbox:checked').length;
        if (countText) {
            countText.textContent = `(${checked} selected)`;
        }
        if (submitBtn) {
            submitBtn.disabled = (checked === 0);
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!cb.checked && selectAll) {
                selectAll.checked = false;
            }
            updateCount();
        });
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
