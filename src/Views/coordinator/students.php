<?php
$pageTitle = 'Student Management';
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
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-users me-2"></i> Registered Students Directory</h4>
                        <p class="text-secondary fs-7 mb-0">View, search, and manage student SIWES registration records.</p>
                    </div>
                    <a href="<?= $baseUrl ?>/index.php?route=reports&export=csv" class="btn btn-outline-green btn-sm fw-bold">
                        <i class="fa-solid fa-file-csv me-1"></i> Export CSV
                    </a>
                </div>

                <!-- Search & Department Filters -->
                <form action="<?= $baseUrl ?>/index.php" method="GET" class="row g-3 mb-4">
                    <input type="hidden" name="route" value="coordinator/students">
                    <div class="col-md-6 col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="q" class="form-control" placeholder="Search by name, matric no, or email..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <select name="dept_id" class="form-select">
                            <option value="0">-- All Departments --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>" <?= (int)($_GET['dept_id'] ?? 0) === (int)$dept['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-green w-100 fw-bold">Filter</button>
                    </div>
                </form>

                <!-- Responsive Student Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-responsive-stack">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Matric Number</th>
                                <th>Department</th>
                                <th>Programme / Level</th>
                                <th>App Status</th>
                                <th>Allocation</th>
                                <th>Assigned Company</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $row): ?>
                                    <tr>
                                        <td data-label="Student Name">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                                            <small class="text-muted fs-8"><?= htmlspecialchars($row['email']) ?></small>
                                        </td>
                                        <td data-label="Matric Number">
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['matric_number']) ?></span>
                                        </td>
                                        <td data-label="Department"><?= htmlspecialchars($row['dept_name'] ?? 'N/A') ?></td>
                                        <td data-label="Programme / Level"><?= htmlspecialchars($row['programme']) ?> &bull; <?= htmlspecialchars($row['level']) ?></td>
                                        <td data-label="App Status">
                                            <?php $appSt = $row['app_status'] ?? 'Draft'; ?>
                                            <span class="badge badge-<?= strtolower($appSt) ?>"><?= htmlspecialchars($appSt) ?></span>
                                        </td>
                                        <td data-label="Allocation">
                                            <?php if (!empty($row['company_name'])): ?>
                                                <span class="badge bg-emerald"><i class="fa-solid fa-circle-check"></i> Allocated</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock"></i> Unallocated</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Assigned Company" class="fw-semibold text-dark fs-7">
                                            <?= htmlspecialchars($row['company_name'] ?? 'Not Assigned') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-secondary">No student records found matching search filter.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
