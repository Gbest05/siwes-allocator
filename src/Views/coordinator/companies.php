<?php
$pageTitle = 'Company Management';
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
                        <h4 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-building me-2"></i> Partner Organizations & Company Capacity</h4>
                        <p class="text-secondary fs-7 mb-0">Manage partner organizations, available placement slots, and industry contact persons.</p>
                    </div>
                    <button class="btn btn-green fw-bold" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                        <i class="fa-solid fa-plus me-1"></i> Register New Company
                    </button>
                </div>

                <div class="row g-4">
                    <?php foreach ($companies as $comp): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between border">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-light-green text-dark-green fw-bold fs-8"><?= htmlspecialchars($comp['industry']) ?></span>
                                        <span class="badge bg-light text-dark border fs-8">RC: <?= htmlspecialchars($comp['reg_number']) ?></span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($comp['name']) ?></h5>
                                    <p class="text-secondary fs-7 mb-3"><i class="fa-solid fa-location-dot me-1 text-primary-green"></i> <?= htmlspecialchars($comp['address']) ?>, <?= htmlspecialchars($comp['city']) ?>, <?= htmlspecialchars($comp['state']) ?></p>
                                    
                                    <div class="p-3 bg-light rounded-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1 fs-7">
                                            <span class="fw-bold text-dark">Placement Capacity</span>
                                            <span class="fw-bold text-primary-green">Available Slots: <?= $comp['available_slots'] ?> / <?= $comp['total_capacity'] ?></span>
                                        </div>
                                        <?php 
                                            $percent = round(($comp['available_slots'] / max($comp['total_capacity'], 1)) * 100);
                                        ?>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary-green" role="progressbar" style="width: <?= $percent ?>%"></div>
                                        </div>
                                    </div>

                                    <div class="fs-7 text-secondary">
                                        <div><i class="fa-solid fa-user me-2 text-dark"></i> Contact: <strong><?= htmlspecialchars($comp['contact_person']) ?></strong></div>
                                        <div><i class="fa-solid fa-phone me-2 text-dark"></i> Phone: <strong><?= htmlspecialchars($comp['phone']) ?></strong></div>
                                        <div><i class="fa-solid fa-envelope me-2 text-dark"></i> Email: <strong><?= htmlspecialchars($comp['email']) ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REGISTER NEW COMPANY MODAL -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-building-circle-plus me-2"></i> Register New Partner Company</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/index.php?route=coordinator/companies" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Globacom Nigeria Ltd" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Registration Number (CAC) *</label>
                            <input type="text" name="reg_number" class="form-control" placeholder="RC-123456" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Street Address *</label>
                            <input type="text" name="address" class="form-control" placeholder="Full office address..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">State *</label>
                            <input type="text" name="state" class="form-control" placeholder="e.g. Lagos" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">City *</label>
                            <input type="text" name="city" class="form-control" placeholder="e.g. Victoria Island" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Industry Sector *</label>
                            <select name="industry" class="form-select" required>
                                <option value="Software Development & IT">Software Development & IT</option>
                                <option value="Fintech & Cyber Security">Fintech & Cyber Security</option>
                                <option value="Telecommunications & Cloud">Telecommunications & Cloud</option>
                                <option value="Power & Electrical Grid">Power & Electrical Grid</option>
                                <option value="Research & Hardware Systems">Research & Hardware Systems</option>
                                <option value="Energy & Embedded Control">Energy & Embedded Control</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total SIWES Slot Capacity *</label>
                            <input type="number" name="total_capacity" class="form-control" value="10" min="1" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contact Person *</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" placeholder="08012345678" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Official Email *</label>
                            <input type="email" name="email" class="form-control" placeholder="info@company.com" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green fw-bold">Register Partner Company</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
