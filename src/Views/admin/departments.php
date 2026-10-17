<?php
$pageTitle = 'Academic Structure & Registration Controls';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Helper;

$flash = Helper::getFlash();
$activeTab = $activeTab ?? ($_GET['tab'] ?? 'departments');
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

            <!-- Page Header Card -->
            <div class="card-custom p-4 mb-4 bg-white border">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark-green">
                            <i class="fa-solid fa-sliders me-2"></i> Academic Structure & Registration Fields Control
                        </h4>
                        <p class="text-secondary fs-7 mb-0">
                            Manage which departments, academic programmes, and levels appear for new students on the public registration page.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2 fs-7">
                            <i class="fa-solid fa-graduation-cap text-primary-green me-1"></i> Registration Form Settings
                        </span>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <ul class="nav nav-pills mt-4 border-top pt-3 gap-2" id="academicTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'departments' ? 'active bg-green text-white fw-bold' : 'text-dark-charcoal fw-semibold' ?>" id="depts-tab" data-bs-toggle="pill" data-bs-target="#depts-content" type="button" role="tab">
                            <i class="fa-solid fa-sitemap me-2"></i> Departments (<?= count($departments) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'programmes' ? 'active bg-green text-white fw-bold' : 'text-dark-charcoal fw-semibold' ?>" id="programmes-tab" data-bs-toggle="pill" data-bs-target="#programmes-content" type="button" role="tab">
                            <i class="fa-solid fa-award me-2"></i> Programmes (<?= count($programmes) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'levels' ? 'active bg-green text-white fw-bold' : 'text-dark-charcoal fw-semibold' ?>" id="levels-tab" data-bs-toggle="pill" data-bs-target="#levels-content" type="button" role="tab">
                            <i class="fa-solid fa-layer-group me-2"></i> Academic Levels (<?= count($levels) ?>)
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Contents -->
            <div class="tab-content" id="academicTabsContent">

                <!-- ========================================== -->
                <!-- TAB 1: ACADEMIC DEPARTMENTS -->
                <!-- ========================================== -->
                <div class="tab-pane fade <?= $activeTab === 'departments' ? 'show active' : '' ?>" id="depts-content" role="tabpanel">
                    <div class="card-custom p-4 mb-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark-green">
                                    <i class="fa-solid fa-sitemap me-2"></i> Academic Departments Directory
                                </h5>
                                <p class="text-secondary fs-8 mb-0">
                                    Departments active on the registration page are selectable by registering students.
                                </p>
                            </div>
                            <button type="button" class="btn btn-green btn-sm fw-bold align-self-start align-self-md-auto" data-bs-toggle="modal" data-bs-target="#modalAddDept">
                                <i class="fa-solid fa-plus me-1"></i> Add New Department
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-responsive-stack">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Department Name</th>
                                        <th>Description</th>
                                        <th>Enrolled Students</th>
                                        <th>Registration Visibility</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($departments as $idx => $dept): ?>
                                        <tr>
                                            <td data-label="#" class="text-muted fs-8 fw-semibold"><?= $idx + 1 ?></td>
                                            <td data-label="Code">
                                                <span class="badge bg-dark-green text-white fw-bold"><?= htmlspecialchars($dept['code']) ?></span>
                                            </td>
                                            <td data-label="Department Name" class="fw-bold text-dark fs-7">
                                                <?= htmlspecialchars($dept['name']) ?>
                                            </td>
                                            <td data-label="Description" class="text-secondary fs-8 max-w-300">
                                                <?= htmlspecialchars($dept['description'] ?? 'No description') ?>
                                            </td>
                                            <td data-label="Enrolled Students">
                                                <span class="badge bg-light text-dark border fs-8">
                                                    <?= (int)$dept['student_count'] ?> Enrolled
                                                </span>
                                            </td>
                                            <td data-label="Registration Visibility">
                                                <?php if ((int)$dept['is_active'] === 1): ?>
                                                    <span class="badge bg-emerald fs-8">
                                                        <i class="fa-solid fa-eye me-1"></i> Visible on Register
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary fs-8">
                                                        <i class="fa-solid fa-eye-slash me-1"></i> Hidden
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Actions" class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <!-- Toggle Visibility -->
                                                    <form action="<?= $baseUrl ?>/index.php?route=admin/departments/toggle" method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="id" value="<?= $dept['id'] ?>">
                                                        <button type="submit" class="btn btn-sm <?= (int)$dept['is_active'] === 1 ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= (int)$dept['is_active'] === 1 ? 'Hide from student registration' : 'Show on student registration' ?>">
                                                            <i class="fa-solid <?= (int)$dept['is_active'] === 1 ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Edit Button -->
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditDept<?= $dept['id'] ?>" title="Edit department">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form action="<?= $baseUrl ?>/index.php?route=admin/departments/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete department \'<?= htmlspecialchars($dept['name']) ?>\'?');">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="id" value="<?= $dept['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete department">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Department Modal -->
                                        <div class="modal fade" id="modalEditDept<?= $dept['id'] ?>" tabindex="-1" aria-labelledby="labelEditDept<?= $dept['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-dark-green text-white">
                                                        <h5 class="modal-title fw-bold" id="labelEditDept<?= $dept['id'] ?>">
                                                            <i class="fa-solid fa-pen-to-square me-2"></i> Edit Department
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?= $baseUrl ?>/index.php?route=admin/departments/update" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="id" value="<?= $dept['id'] ?>">

                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold fs-8 mb-1">Department Name *</label>
                                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($dept['name']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold fs-8 mb-1">Department Code *</label>
                                                                <input type="text" name="code" class="form-control text-uppercase" value="<?= htmlspecialchars($dept['code']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold fs-8 mb-1">Description</label>
                                                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($dept['description'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="activeDept<?= $dept['id'] ?>" value="1" <?= (int)$dept['is_active'] === 1 ? 'checked' : '' ?>>
                                                                <label class="form-check-label fw-semibold fs-8" for="activeDept<?= $dept['id'] ?>">
                                                                    Visible on Student Registration Page
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-green btn-sm fw-bold">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TAB 2: ACADEMIC PROGRAMMES -->
                <!-- ========================================== -->
                <div class="tab-pane fade <?= $activeTab === 'programmes' ? 'show active' : '' ?>" id="programmes-content" role="tabpanel">
                    <div class="card-custom p-4 mb-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark-green">
                                    <i class="fa-solid fa-award me-2"></i> Academic Programmes Directory
                                </h5>
                                <p class="text-secondary fs-8 mb-0">
                                    Control which degree or diploma programmes (e.g. ND, HND, BSc, BEng) students can select.
                                </p>
                            </div>
                            <button type="button" class="btn btn-green btn-sm fw-bold align-self-start align-self-md-auto" data-bs-toggle="modal" data-bs-target="#modalAddProg">
                                <i class="fa-solid fa-plus me-1"></i> Add New Programme
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-responsive-stack">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Programme Code</th>
                                        <th>Full Programme Name</th>
                                        <th>Enrolled Students</th>
                                        <th>Registration Visibility</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($programmes as $idx => $prog): ?>
                                        <tr>
                                            <td data-label="#" class="text-muted fs-8 fw-semibold"><?= $idx + 1 ?></td>
                                            <td data-label="Code">
                                                <span class="badge bg-dark-green text-white fw-bold"><?= htmlspecialchars($prog['code']) ?></span>
                                            </td>
                                            <td data-label="Programme Name" class="fw-bold text-dark fs-7">
                                                <?= htmlspecialchars($prog['name']) ?>
                                            </td>
                                            <td data-label="Enrolled Students">
                                                <span class="badge bg-light text-dark border fs-8">
                                                    <?= (int)$prog['student_count'] ?> Enrolled
                                                </span>
                                            </td>
                                            <td data-label="Registration Visibility">
                                                <?php if ((int)$prog['is_active'] === 1): ?>
                                                    <span class="badge bg-emerald fs-8">
                                                        <i class="fa-solid fa-eye me-1"></i> Visible on Register
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary fs-8">
                                                        <i class="fa-solid fa-eye-slash me-1"></i> Hidden
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Actions" class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <!-- Toggle Visibility -->
                                                    <form action="<?= $baseUrl ?>/index.php?route=admin/programmes/toggle" method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="id" value="<?= $prog['id'] ?>">
                                                        <button type="submit" class="btn btn-sm <?= (int)$prog['is_active'] === 1 ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= (int)$prog['is_active'] === 1 ? 'Hide from student registration' : 'Show on student registration' ?>">
                                                            <i class="fa-solid <?= (int)$prog['is_active'] === 1 ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Edit Button -->
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditProg<?= $prog['id'] ?>" title="Edit programme">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form action="<?= $baseUrl ?>/index.php?route=admin/programmes/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete programme \'<?= htmlspecialchars($prog['name']) ?>\'?');">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="id" value="<?= $prog['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete programme">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Programme Modal -->
                                        <div class="modal fade" id="modalEditProg<?= $prog['id'] ?>" tabindex="-1" aria-labelledby="labelEditProg<?= $prog['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-dark-green text-white">
                                                        <h5 class="modal-title fw-bold" id="labelEditProg<?= $prog['id'] ?>">
                                                            <i class="fa-solid fa-pen-to-square me-2"></i> Edit Programme
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?= $baseUrl ?>/index.php?route=admin/programmes/update" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="id" value="<?= $prog['id'] ?>">

                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold fs-8 mb-1">Programme Code *</label>
                                                                <input type="text" name="code" class="form-control text-uppercase" value="<?= htmlspecialchars($prog['code']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold fs-8 mb-1">Full Programme Name *</label>
                                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($prog['name']) ?>" required>
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="activeProg<?= $prog['id'] ?>" value="1" <?= (int)$prog['is_active'] === 1 ? 'checked' : '' ?>>
                                                                <label class="form-check-label fw-semibold fs-8" for="activeProg<?= $prog['id'] ?>">
                                                                    Visible on Student Registration Page
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-green btn-sm fw-bold">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TAB 3: ACADEMIC LEVELS -->
                <!-- ========================================== -->
                <div class="tab-pane fade <?= $activeTab === 'levels' ? 'show active' : '' ?>" id="levels-content" role="tabpanel">
                    <div class="card-custom p-4 mb-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark-green">
                                    <i class="fa-solid fa-layer-group me-2"></i> Academic Levels Directory
                                </h5>
                                <p class="text-secondary fs-8 mb-0">
                                    Choose which class levels (e.g. ND1, ND2, HND1, 200L, 300L, 400L) appear on registration.
                                </p>
                            </div>
                            <button type="button" class="btn btn-green btn-sm fw-bold align-self-start align-self-md-auto" data-bs-toggle="modal" data-bs-target="#modalAddLevel">
                                <i class="fa-solid fa-plus me-1"></i> Add New Level
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-responsive-stack">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Level Code</th>
                                        <th>Full Level Name</th>
                                        <th>Enrolled Students</th>
                                        <th>Registration Visibility</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($levels as $idx => $lvl): ?>
                                        <tr>
                                            <td data-label="#" class="text-muted fs-8 fw-semibold"><?= $idx + 1 ?></td>
                                            <td data-label="Code">
                                                <span class="badge bg-dark-green text-white fw-bold"><?= htmlspecialchars($lvl['code']) ?></span>
                                            </td>
                                            <td data-label="Level Name" class="fw-bold text-dark fs-7">
                                                <?= htmlspecialchars($lvl['name']) ?>
                                            </td>
                                            <td data-label="Enrolled Students">
                                                <span class="badge bg-light text-dark border fs-8">
                                                    <?= (int)$lvl['student_count'] ?> Enrolled
                                                </span>
                                            </td>
                                            <td data-label="Registration Visibility">
                                                <?php if ((int)$lvl['is_active'] === 1): ?>
                                                    <span class="badge bg-emerald fs-8">
                                                        <i class="fa-solid fa-eye me-1"></i> Visible on Register
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary fs-8">
                                                        <i class="fa-solid fa-eye-slash me-1"></i> Hidden
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Actions" class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <!-- Toggle Visibility -->
                                                    <form action="<?= $baseUrl ?>/index.php?route=admin/levels/toggle" method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="id" value="<?= $lvl['id'] ?>">
                                                        <button type="submit" class="btn btn-sm <?= (int)$lvl['is_active'] === 1 ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= (int)$lvl['is_active'] === 1 ? 'Hide from student registration' : 'Show on student registration' ?>">
                                                            <i class="fa-solid <?= (int)$lvl['is_active'] === 1 ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Edit Button -->
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditLvl<?= $lvl['id'] ?>" title="Edit level">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form action="<?= $baseUrl ?>/index.php?route=admin/levels/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete level \'<?= htmlspecialchars($lvl['name']) ?>\'?');">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="id" value="<?= $lvl['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete level">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Level Modal -->
                                        <div class="modal fade" id="modalEditLvl<?= $lvl['id'] ?>" tabindex="-1" aria-labelledby="labelEditLvl<?= $lvl['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-dark-green text-white">
                                                        <h5 class="modal-title fw-bold" id="labelEditLvl<?= $lvl['id'] ?>">
                                                            <i class="fa-solid fa-pen-to-square me-2"></i> Edit Academic Level
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?= $baseUrl ?>/index.php?route=admin/levels/update" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                        <input type="hidden" name="id" value="<?= $lvl['id'] ?>">

                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold fs-8 mb-1">Level Code *</label>
                                                                <input type="text" name="code" class="form-control text-uppercase" value="<?= htmlspecialchars($lvl['code']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold fs-8 mb-1">Full Level Name *</label>
                                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($lvl['name']) ?>" required>
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="activeLvl<?= $lvl['id'] ?>" value="1" <?= (int)$lvl['is_active'] === 1 ? 'checked' : '' ?>>
                                                                <label class="form-check-label fw-semibold fs-8" for="activeLvl<?= $lvl['id'] ?>">
                                                                    Visible on Student Registration Page
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-green btn-sm fw-bold">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD DEPARTMENT -->
<!-- ========================================== -->
<div class="modal fade" id="modalAddDept" tabindex="-1" aria-labelledby="labelAddDept" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold" id="labelAddDept">
                    <i class="fa-solid fa-plus me-2"></i> Add Academic Department
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/index.php?route=admin/departments/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8 mb-1">Department Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Mechanical Engineering" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8 mb-1">Department Code *</label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="e.g. MECH" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8 mb-1">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief outline of discipline"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="addDeptActive" value="1" checked>
                        <label class="form-check-label fw-semibold fs-8" for="addDeptActive">
                            Visible on Student Registration Page immediately
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green btn-sm fw-bold">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD PROGRAMME -->
<!-- ========================================== -->
<div class="modal fade" id="modalAddProg" tabindex="-1" aria-labelledby="labelAddProg" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold" id="labelAddProg">
                    <i class="fa-solid fa-plus me-2"></i> Add Academic Programme
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/index.php?route=admin/programmes/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8 mb-1">Programme Code *</label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="e.g. BEng" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8 mb-1">Full Programme Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Bachelor of Engineering (B.Eng)" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="addProgActive" value="1" checked>
                        <label class="form-check-label fw-semibold fs-8" for="addProgActive">
                            Visible on Student Registration Page immediately
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green btn-sm fw-bold">Create Programme</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD LEVEL -->
<!-- ========================================== -->
<div class="modal fade" id="modalAddLevel" tabindex="-1" aria-labelledby="labelAddLevel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold" id="labelAddLevel">
                    <i class="fa-solid fa-plus me-2"></i> Add Academic Level
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/index.php?route=admin/levels/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8 mb-1">Level Code *</label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="e.g. 500L" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8 mb-1">Full Level Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. 500 Level (Final Year)" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="addLevelActive" value="1" checked>
                        <label class="form-check-label fw-semibold fs-8" for="addLevelActive">
                            Visible on Student Registration Page immediately
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green btn-sm fw-bold">Create Level</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
