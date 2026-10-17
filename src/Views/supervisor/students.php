<?php
$pageTitle = 'My Assigned Students';
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

            <div class="card-custom p-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark-green">
                            <i class="fa-solid fa-user-graduate me-2"></i> Assigned Student Directory
                        </h4>
                        <p class="text-secondary fs-7 mb-0">
                            Complete list of students assigned to you by the department coordinator.
                        </p>
                    </div>
                    <span class="badge bg-light-green text-dark-green fw-bold px-3 py-2 fs-7 align-self-start align-self-md-auto">
                        <?= count($students) ?> Students Listed
                    </span>
                </div>

                <!-- Search & Filters -->
                <form method="GET" action="<?= $baseUrl ?>/index.php" class="row g-3 mb-4">
                    <input type="hidden" name="route" value="supervisor/students">
                    <div class="col-md-6 col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="q" class="form-control border-start-0" placeholder="Search by name, matric, email or company..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <select name="status" class="form-select">
                            <option value="">All Placement Statuses</option>
                            <option value="placed" <?= ($_GET['status'] ?? '') === 'placed' ? 'selected' : '' ?>>Placement Confirmed</option>
                            <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Awaiting Placement</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-green w-100 fw-semibold">Filter</button>
                        <a href="<?= $baseUrl ?>/index.php?route=supervisor/students" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-arrow-rotate-left"></i></a>
                    </div>
                </form>

                <?php if (empty($students)): ?>
                    <div class="text-center py-5">
                        <i class="fa-solid fa-user-slash fs-1 text-secondary mb-3"></i>
                        <h5 class="fw-bold text-dark">No Students Found</h5>
                        <p class="text-secondary fs-7 max-w-500 mx-auto">No student records matched your search query or filter selection.</p>
                        <a href="<?= $baseUrl ?>/index.php?route=supervisor/students" class="btn btn-outline-success btn-sm">Clear Filter</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-responsive-stack">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Matric Number</th>
                                    <th>Department</th>
                                    <th>Programme / Level</th>
                                    <th>Contact Info</th>
                                    <th>Company Placement</th>
                                    <th>Placement Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $idx => $st): ?>
                                    <tr>
                                        <td data-label="#"><?= $idx + 1 ?></td>
                                        <td data-label="Student Name" class="fw-bold text-dark"><?= htmlspecialchars($st['full_name']) ?></td>
                                        <td data-label="Matric Number">
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($st['matric_number']) ?></span>
                                        </td>
                                        <td data-label="Department"><?= htmlspecialchars($st['dept_name'] ?? 'N/A') ?></td>
                                        <td data-label="Programme / Level"><?= htmlspecialchars($st['programme']) ?> &bull; <?= htmlspecialchars($st['level']) ?></td>
                                        <td data-label="Contact Info">
                                            <div class="fs-8">
                                                <a href="mailto:<?= htmlspecialchars($st['email']) ?>" class="text-dark-charcoal text-decoration-none d-block">
                                                    <i class="fa-solid fa-envelope text-primary-green me-1"></i><?= htmlspecialchars($st['email']) ?>
                                                </a>
                                                <?php if (!empty($st['phone'])): ?>
                                                    <a href="tel:<?= htmlspecialchars($st['phone']) ?>" class="text-secondary text-decoration-none">
                                                        <i class="fa-solid fa-phone text-primary-green me-1"></i><?= htmlspecialchars($st['phone']) ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td data-label="Company Placement">
                                            <?php if (!empty($st['company_name'])): ?>
                                                <div class="fw-bold text-dark fs-7"><?= htmlspecialchars($st['company_name']) ?></div>
                                                <small class="text-muted fs-8"><?= htmlspecialchars($st['company_city'] ?? '') ?>, <?= htmlspecialchars($st['company_state'] ?? '') ?></small>
                                            <?php else: ?>
                                                <span class="text-muted fs-8">Not Allocated</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Placement Status">
                                            <?php if (!empty($st['company_name'])): ?>
                                                <span class="badge bg-emerald fs-8"><i class="fa-solid fa-check me-1"></i> Confirmed</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark fs-8"><i class="fa-solid fa-clock me-1"></i> Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
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
