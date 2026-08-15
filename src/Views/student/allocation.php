<?php
$pageTitle = 'My SIWES Placement';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
?>

<div class="app-wrapper">
    <?php require __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="app-main">
        <?php require __DIR__ . '/../layouts/topbar.php'; ?>

        <div class="app-content">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-9">
                    <div class="card-custom p-3 p-sm-4 p-md-5">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 border-bottom pb-3">
                            <div>
                                <h4 class="fw-bold mb-1 text-dark-green fs-5"><i class="fa-solid fa-diagram-project me-2"></i> Official Allocation Status</h4>
                                <p class="text-secondary fs-7 mb-0">SIWES Placement Details & Company Assignment Card</p>
                            </div>
                            <?php if ($allocation): ?>
                                <a href="<?= $baseUrl ?>/index.php?route=allocation-letter" class="btn btn-green fw-bold align-self-start align-self-md-auto">
                                    <i class="fa-solid fa-print me-1"></i> Print Official Letter
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if ($allocation): ?>
                            <div class="p-3 p-sm-4 bg-light rounded-4 mb-4 border">
                                <div class="row g-4">
                                    <div class="col-12 col-md-6">
                                        <label class="text-secondary fs-7 d-block">Allocated Company</label>
                                        <h4 class="fw-bold text-dark-green mb-1 fs-5"><?= htmlspecialchars($allocation['company_name']) ?></h4>
                                        <span class="badge bg-light-green text-dark-green fw-bold"><?= htmlspecialchars($allocation['company_industry']) ?></span>
                                    </div>
                                    <div class="col-12 col-md-6 text-md-end">
                                        <label class="text-secondary fs-7 d-block">Compatibility Score</label>
                                        <span class="display-6 fw-extrabold text-primary-green d-block"><?= $allocation['compatibility_score'] ?>%</span>
                                        <div class="fs-8 text-secondary">Smart Matching Rank</div>
                                    </div>
                                    <div class="col-12"><hr class="my-0"></div>
                                    <div class="col-12 col-md-6">
                                        <label class="text-secondary fs-7 d-block">Company Address & Location</label>
                                        <div class="fw-semibold text-dark fs-7"><?= htmlspecialchars($allocation['company_address']) ?>, <?= htmlspecialchars($allocation['company_city']) ?>, <?= htmlspecialchars($allocation['company_state']) ?></div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="text-secondary fs-7 d-block">Company Supervisor / Contact</label>
                                        <div class="fw-semibold text-dark fs-7"><?= htmlspecialchars($allocation['contact_person']) ?></div>
                                        <div class="text-secondary fs-7"><i class="fa-solid fa-phone me-1 text-primary-green"></i> <?= htmlspecialchars($allocation['company_phone']) ?></div>
                                        <div class="text-secondary fs-7"><i class="fa-solid fa-envelope me-1 text-primary-green"></i> <?= htmlspecialchars($allocation['company_email']) ?></div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="text-secondary fs-7 d-block">Start Date</label>
                                        <div class="fw-semibold text-dark fs-7"><?= date('F d, Y', strtotime($allocation['start_date'])) ?></div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="text-secondary fs-7 d-block">End Date</label>
                                        <div class="fw-semibold text-dark fs-7"><?= date('F d, Y', strtotime($allocation['end_date'])) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fa-solid fa-clock-rotate-left fs-1 text-warning mb-3"></i>
                                <h5 class="fw-bold">Pending Allocation</h5>
                                <p class="text-secondary max-w-500 mx-auto fs-7 mb-0">
                                    Your application is currently under review by the department SIWES coordinator. Once an optimal company match is determined, details will automatically be displayed here.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
