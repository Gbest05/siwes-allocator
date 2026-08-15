<?php
$pageTitle = 'Reports & Analytics';
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
                        <h4 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-chart-column me-2"></i> Comprehensive SIWES Analytics & Reports</h4>
                        <p class="text-secondary fs-7 mb-0">Generate, view, and export student placement reports.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm fw-bold">
                            <i class="fa-solid fa-print me-1"></i> Print Report
                        </button>
                        <a href="<?= $baseUrl ?>/index.php?route=reports&export=csv" class="btn btn-green btn-sm fw-bold">
                            <i class="fa-solid fa-file-csv me-1"></i> Export Dataset (CSV)
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped align-middle table-responsive-stack">
                        <thead class="table-dark">
                            <tr>
                                <th>Matric Number</th>
                                <th>Student Name</th>
                                <th>Department</th>
                                <th>App Status</th>
                                <th>Allocated Organization</th>
                                <th>Location</th>
                                <th>Score Match</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $row): ?>
                                <tr>
                                    <td data-label="Matric Number"><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['matric_number']) ?></span></td>
                                    <td data-label="Student Name" class="fw-bold text-dark"><?= htmlspecialchars($row['student_name']) ?></td>
                                    <td data-label="Department"><?= htmlspecialchars($row['dept_name'] ?? 'N/A') ?></td>
                                    <td data-label="App Status"><span class="badge badge-<?= strtolower($row['app_status'] ?? 'Draft') ?>"><?= htmlspecialchars($row['app_status'] ?? 'None') ?></span></td>
                                    <td data-label="Allocated Organization" class="fw-semibold text-dark-green"><?= htmlspecialchars($row['company_name'] ?? 'Unallocated') ?></td>
                                    <td data-label="Location"><?= htmlspecialchars($row['company_state'] ?? 'N/A') ?></td>
                                    <td data-label="Score Match">
                                        <?php if (!empty($row['compatibility_score'])): ?>
                                            <span class="match-score-badge"><?= $row['compatibility_score'] ?>%</span>
                                        <?php else: ?>
                                            <span class="text-muted fs-8">N/A</span>
                                        <?php endif; ?>
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
