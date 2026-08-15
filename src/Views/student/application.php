<?php
$pageTitle = 'SIWES Application';
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

            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="card-custom p-3 p-sm-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <div class="bg-light-green text-dark-green rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-file-signature fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 fs-5">SIWES Placement Preferences Application</h4>
                                <p class="text-secondary fs-7 mb-0">Specify your placement preferences for the smart allocation algorithm.</p>
                            </div>
                        </div>

                        <form action="<?= $baseUrl ?>/index.php?route=student/application" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label for="preferred_industry" class="form-label fw-semibold fs-7">Preferred Industry Sector *</label>
                                    <select class="form-select" id="preferred_industry" name="preferred_industry" required>
                                        <option value="">-- Select Industry Sector --</option>
                                        <option value="Software Development & IT" <?= ($application['preferred_industry'] ?? '') === 'Software Development & IT' ? 'selected' : '' ?>>Software Development & IT</option>
                                        <option value="Fintech & Cyber Security" <?= ($application['preferred_industry'] ?? '') === 'Fintech & Cyber Security' ? 'selected' : '' ?>>Fintech & Cyber Security</option>
                                        <option value="Telecommunications & Cloud" <?= ($application['preferred_industry'] ?? '') === 'Telecommunications & Cloud' ? 'selected' : '' ?>>Telecommunications & Cloud</option>
                                        <option value="Power & Electrical Grid" <?= ($application['preferred_industry'] ?? '') === 'Power & Electrical Grid' ? 'selected' : '' ?>>Power & Electrical Grid</option>
                                        <option value="Research & Hardware Systems" <?= ($application['preferred_industry'] ?? '') === 'Research & Hardware Systems' ? 'selected' : '' ?>>Research & Hardware Systems</option>
                                        <option value="Energy & Embedded Control" <?= ($application['preferred_industry'] ?? '') === 'Energy & Embedded Control' ? 'selected' : '' ?>>Energy & Embedded Control</option>
                                    </select>
                                    <small class="text-muted fs-8">Used to calculate +30 points Industry Relevance Score</small>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="preferred_location" class="form-label fw-semibold fs-7">Preferred State / Location *</label>
                                    <select class="form-select" id="preferred_location" name="preferred_location" required>
                                        <option value="">-- Select Target Location --</option>
                                        <option value="Lagos" <?= ($application['preferred_location'] ?? '') === 'Lagos' ? 'selected' : '' ?>>Lagos State</option>
                                        <option value="Abuja" <?= ($application['preferred_location'] ?? '') === 'Abuja' ? 'selected' : '' ?>>FCT Abuja</option>
                                        <option value="Oyo" <?= ($application['preferred_location'] ?? '') === 'Oyo' ? 'selected' : '' ?>>Oyo State (Ibadan)</option>
                                        <option value="Rivers" <?= ($application['preferred_location'] ?? '') === 'Rivers' ? 'selected' : '' ?>>Rivers State (Port Harcourt)</option>
                                        <option value="Enugu" <?= ($application['preferred_location'] ?? '') === 'Enugu' ? 'selected' : '' ?>>Enugu State</option>
                                        <option value="Kaduna" <?= ($application['preferred_location'] ?? '') === 'Kaduna' ? 'selected' : '' ?>>Kaduna State</option>
                                    </select>
                                    <small class="text-muted fs-8">Used to calculate +20 points Geographic Proximity Score</small>
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label fw-semibold fs-7">Special Skills & Technical Focus Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Mention relevant software skills, programming languages, hardware experience or health accommodations..."><?= htmlspecialchars($application['notes'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <a href="<?= $baseUrl ?>/index.php?route=dashboard" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
                                </a>
                                <button type="submit" class="btn btn-green fw-bold px-4 py-2">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
