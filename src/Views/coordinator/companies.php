<?php
$pageTitle = 'Partner Organizations & Company Management';
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
                        <h4 class="fw-bold mb-1 text-dark-green"><i class="fa-solid fa-building me-2"></i> Partner Organizations & Capacity Management</h4>
                        <p class="text-secondary fs-7 mb-0">Register partner organizations, manage placement quotas, edit profiles, and suspend or delete companies.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-green text-dark-green fw-bold px-3 py-2 fs-7">
                            <?= count($companies) ?> Total Companies
                        </span>
                        <button class="btn btn-green fw-bold align-self-start align-self-md-auto" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                            <i class="fa-solid fa-plus me-1"></i> Register Company
                        </button>
                    </div>
                </div>

                <div class="row g-4">
                    <?php if (empty($companies)): ?>
                        <div class="col-12 text-center py-5">
                            <i class="fa-solid fa-building-circle-exclamation fs-1 text-secondary mb-3"></i>
                            <h5 class="fw-bold text-dark">No Partner Companies Registered</h5>
                            <p class="text-secondary fs-7 max-w-500 mx-auto">Click the button above to register your first industry partner organization.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($companies as $comp): ?>
                            <?php 
                                $isSuspended = ($comp['status'] ?? 'active') === 'suspended';
                                $allocatedCount = (int)($comp['allocated_count'] ?? 0);
                                $percent = round(($comp['available_slots'] / max($comp['total_capacity'], 1)) * 100);
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between border <?= $isSuspended ? 'bg-light border-warning border-opacity-50' : '' ?>">
                                    <div>
                                        <!-- Header Badges -->
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-light-green text-dark-green fw-bold fs-8"><?= htmlspecialchars($comp['industry']) ?></span>
                                            <div>
                                                <?php if ($isSuspended): ?>
                                                    <span class="badge bg-danger fs-8"><i class="fa-solid fa-ban me-1"></i> Suspended</span>
                                                <?php else: ?>
                                                    <span class="badge bg-emerald fs-8"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                                                <?php endif; ?>
                                                <span class="badge bg-light text-dark border fs-8 ms-1">RC: <?= htmlspecialchars($comp['reg_number']) ?></span>
                                            </div>
                                        </div>

                                        <!-- Company Name & Address -->
                                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($comp['name']) ?></h5>
                                        <p class="text-secondary fs-7 mb-3">
                                            <i class="fa-solid fa-location-dot me-1 text-primary-green"></i> 
                                            <?= htmlspecialchars($comp['address']) ?>, <?= htmlspecialchars($comp['city']) ?>, <?= htmlspecialchars($comp['state']) ?>
                                        </p>
                                        
                                        <!-- Quota & Capacity Bar -->
                                        <div class="p-3 bg-light rounded-3 mb-3 border">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-1 fs-7">
                                                <span class="fw-bold text-dark">Slot Availability</span>
                                                <span class="fw-bold <?= $comp['available_slots'] > 0 ? 'text-primary-green' : 'text-danger' ?>">
                                                    <?= $comp['available_slots'] ?> / <?= $comp['total_capacity'] ?> Available
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar <?= $comp['available_slots'] > 0 ? 'bg-primary-green' : 'bg-danger' ?>" role="progressbar" style="width: <?= min(100, max(0, $percent)) ?>%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted fs-8 mt-1">
                                                <span>Active Placements: <strong><?= $allocatedCount ?></strong></span>
                                                <span>Remaining: <strong><?= $comp['available_slots'] ?></strong></span>
                                            </div>
                                        </div>

                                        <!-- Contact Information -->
                                        <div class="fs-8 text-secondary mb-3">
                                            <div class="mb-1"><i class="fa-solid fa-user me-2 text-dark"></i> Contact: <strong><?= htmlspecialchars($comp['contact_person']) ?></strong></div>
                                            <div class="mb-1"><i class="fa-solid fa-phone me-2 text-dark"></i> Phone: <strong><?= htmlspecialchars($comp['phone']) ?></strong></div>
                                            <div><i class="fa-solid fa-envelope me-2 text-dark"></i> Email: <strong><?= htmlspecialchars($comp['email']) ?></strong></div>
                                        </div>
                                    </div>

                                    <!-- Administrative Actions Toolbar -->
                                    <div class="pt-3 border-top d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-inline-flex gap-1">
                                            <!-- Suspend / Reactivate Form -->
                                            <form action="<?= $baseUrl ?>/index.php?route=coordinator/companies/toggle-status" method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                                <input type="hidden" name="company_id" value="<?= $comp['id'] ?>">
                                                <button type="submit" class="btn btn-sm <?= $isSuspended ? 'btn-outline-success' : 'btn-outline-warning' ?> fw-semibold" title="<?= $isSuspended ? 'Reactivate company' : 'Suspend company from receiving new placements' ?>">
                                                    <i class="fa-solid <?= $isSuspended ? 'fa-circle-play' : 'fa-ban' ?> me-1"></i>
                                                    <?= $isSuspended ? 'Reactivate' : 'Suspend' ?>
                                                </button>
                                            </form>

                                            <!-- Edit Modal Trigger -->
                                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#editCompanyModal<?= $comp['id'] ?>" title="Edit company details">
                                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                            </button>
                                        </div>

                                        <!-- Delete Form -->
                                        <form action="<?= $baseUrl ?>/index.php?route=coordinator/companies/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete \'<?= htmlspecialchars(addslashes($comp['name'])) ?>\'? This cannot be undone.');">
                                            <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                            <input type="hidden" name="company_id" value="<?= $comp['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete partner company">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- EDIT COMPANY MODAL -->
                            <div class="modal fade" id="editCompanyModal<?= $comp['id'] ?>" tabindex="-1" aria-labelledby="labelEditComp<?= $comp['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-dark-green text-white">
                                            <h5 class="modal-title fw-bold text-white" id="labelEditComp<?= $comp['id'] ?>">
                                                <i class="fa-solid fa-pen-to-square me-2"></i> Edit Partner Organization
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?= $baseUrl ?>/index.php?route=coordinator/companies/update" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                                            <input type="hidden" name="company_id" value="<?= $comp['id'] ?>">

                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Company Name *</label>
                                                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($comp['name']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Registration Number (CAC) *</label>
                                                        <input type="text" name="reg_number" class="form-control" value="<?= htmlspecialchars($comp['reg_number']) ?>" required>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Street Address *</label>
                                                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($comp['address']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold fs-8 mb-1">State *</label>
                                                        <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($comp['state']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold fs-8 mb-1">City *</label>
                                                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($comp['city']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Industry Sector *</label>
                                                        <select name="industry" class="form-select" required>
                                                            <option value="Software Development & IT" <?= $comp['industry'] === 'Software Development & IT' ? 'selected' : '' ?>>Software Development & IT</option>
                                                            <option value="Information Technology" <?= $comp['industry'] === 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
                                                            <option value="Fintech & Cyber Security" <?= $comp['industry'] === 'Fintech & Cyber Security' ? 'selected' : '' ?>>Fintech & Cyber Security</option>
                                                            <option value="Telecommunications & Cloud" <?= $comp['industry'] === 'Telecommunications & Cloud' ? 'selected' : '' ?>>Telecommunications & Cloud</option>
                                                            <option value="Power & Electrical Grid" <?= $comp['industry'] === 'Power & Electrical Grid' ? 'selected' : '' ?>>Power & Electrical Grid</option>
                                                            <option value="Research & Hardware Systems" <?= $comp['industry'] === 'Research & Hardware Systems' ? 'selected' : '' ?>>Research & Hardware Systems</option>
                                                            <option value="Energy & Embedded Control" <?= $comp['industry'] === 'Energy & Embedded Control' ? 'selected' : '' ?>>Energy & Embedded Control</option>
                                                            <option value="Banking & Finance" <?= $comp['industry'] === 'Banking & Finance' ? 'selected' : '' ?>>Banking & Finance</option>
                                                            <option value="Manufacturing & Engineering" <?= $comp['industry'] === 'Manufacturing & Engineering' ? 'selected' : '' ?>>Manufacturing & Engineering</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Total Capacity *</label>
                                                        <input type="number" name="total_capacity" class="form-control" value="<?= (int)$comp['total_capacity'] ?>" min="1" max="500" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Available Slots *</label>
                                                        <input type="number" name="available_slots" class="form-control" value="<?= (int)$comp['available_slots'] ?>" min="0" max="500" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Contact Person *</label>
                                                        <input type="text" name="contact_person" class="form-control" value="<?= htmlspecialchars($comp['contact_person']) ?>" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Phone Number *</label>
                                                        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($comp['phone']) ?>" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Official Email *</label>
                                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($comp['email']) ?>" required>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label fw-semibold fs-8 mb-1">Partner Operational Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" <?= ($comp['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active (Open for student placement)</option>
                                                            <option value="suspended" <?= ($comp['status'] ?? 'active') === 'suspended' ? 'selected' : '' ?>>Suspended (Halted from new allocations)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-green btn-sm fw-bold">Save Company Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REGISTER NEW COMPANY MODAL -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark-green text-white">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-building-circle-plus me-2"></i> Register New Partner Company</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $baseUrl ?>/index.php?route=coordinator/companies" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-8 mb-1">Company Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Globacom Nigeria Ltd" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-8 mb-1">Registration Number (CAC) *</label>
                            <input type="text" name="reg_number" class="form-control" placeholder="RC-123456" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold fs-8 mb-1">Street Address *</label>
                            <input type="text" name="address" class="form-control" placeholder="Full office address..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-8 mb-1">State *</label>
                            <input type="text" name="state" class="form-control" placeholder="e.g. Lagos" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-8 mb-1">City *</label>
                            <input type="text" name="city" class="form-control" placeholder="e.g. Victoria Island" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-8 mb-1">Industry Sector *</label>
                            <select name="industry" class="form-select" required>
                                <option value="Software Development & IT">Software Development & IT</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Fintech & Cyber Security">Fintech & Cyber Security</option>
                                <option value="Telecommunications & Cloud">Telecommunications & Cloud</option>
                                <option value="Power & Electrical Grid">Power & Electrical Grid</option>
                                <option value="Research & Hardware Systems">Research & Hardware Systems</option>
                                <option value="Energy & Embedded Control">Energy & Embedded Control</option>
                                <option value="Banking & Finance">Banking & Finance</option>
                                <option value="Manufacturing & Engineering">Manufacturing & Engineering</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold fs-8 mb-1">Total SIWES Slot Capacity *</label>
                            <input type="number" name="total_capacity" class="form-control" value="10" min="1" max="500" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-8 mb-1">Contact Person *</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-8 mb-1">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" placeholder="08012345678" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-8 mb-1">Official Email *</label>
                            <input type="email" name="email" class="form-control" placeholder="info@company.com" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green btn-sm fw-bold">Register Partner Company</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
