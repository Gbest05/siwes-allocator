<?php
$pageTitle = 'Landing Page & Site Settings';
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
                    <i class="fa-solid fa-circle-check me-2"></i> <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card-custom p-3 p-sm-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark-green fs-5"><i class="fa-solid fa-sliders me-2"></i> Site Customization & Landing Page CMS</h4>
                        <p class="text-secondary fs-7 mb-0">Customize website name, logo, hero banner, headlines, about section, and contact info.</p>
                    </div>
                    <a href="<?= $baseUrl ?>/index.php?route=home" target="_blank" class="btn btn-outline-green btn-sm fw-bold align-self-start align-self-md-auto">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Preview Live Landing Page
                    </a>
                </div>

                <form action="<?= $baseUrl ?>/index.php?route=admin/settings" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                    <!-- Navigation Tabs (Mobile-Scrollable / Responsive) -->
                    <div class="table-responsive mb-4 pb-1">
                        <ul class="nav nav-pills flex-nowrap flex-sm-wrap gap-2 text-nowrap" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold fs-7" id="branding-tab" data-bs-toggle="pill" data-bs-target="#tab-branding" type="button" role="tab">
                                    <i class="fa-solid fa-palette me-1"></i> Branding & Identity
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold fs-7" id="hero-tab" data-bs-toggle="pill" data-bs-target="#tab-hero" type="button" role="tab">
                                    <i class="fa-solid fa-image me-1"></i> Hero Section
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold fs-7" id="about-tab" data-bs-toggle="pill" data-bs-target="#tab-about" type="button" role="tab">
                                    <i class="fa-solid fa-address-card me-1"></i> About Section
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold fs-7" id="contact-tab" data-bs-toggle="pill" data-bs-target="#tab-contact" type="button" role="tab">
                                    <i class="fa-solid fa-phone me-1"></i> CTA & Footer
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="settingsTabContent">
                        
                        <!-- TAB 1: Branding & Identity -->
                        <div class="tab-pane fade show active" id="tab-branding" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold fs-7">Website / Portal Name *</label>
                                    <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? 'SIWES Allocator') ?>" required>
                                    <small class="text-muted fs-8">Displays in header navbar, browser title, and footer</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold fs-7">Institution Name *</label>
                                    <input type="text" name="institution_name" class="form-control" value="<?= htmlspecialchars($settings['institution_name'] ?? 'School of Technology & Applied Sciences') ?>" required>
                                    <small class="text-muted fs-8">Displays in official documents and letters</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7">Custom Logo (Image Upload)</label>
                                    <div class="row align-items-center g-3">
                                        <div class="col-12 col-sm-8">
                                            <input type="file" name="site_logo" class="form-control" accept="image/*">
                                            <small class="text-muted fs-8">PNG, JPG, SVG, or WEBP (Max 5MB). Leave empty to keep default.</small>
                                        </div>
                                        <div class="col-12 col-sm-4 text-center text-sm-start">
                                            <?php if (!empty($settings['site_logo'])): ?>
                                                <img src="<?= $baseUrl ?>/<?= htmlspecialchars($settings['site_logo']) ?>" alt="Logo Preview" class="img-thumbnail d-inline-block" style="max-height: 50px;">
                                                <div class="fs-8 text-success mt-1"><i class="fa-solid fa-check-circle"></i> Custom Logo Active</div>
                                            <?php else: ?>
                                                <div class="p-2 border rounded text-secondary fs-8 bg-light d-inline-block">
                                                    <i class="fa-solid fa-graduation-cap text-primary-green fs-5"></i> Default Icon Active
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: Hero Section -->
                        <div class="tab-pane fade" id="tab-hero" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7">Hero Badge Text</label>
                                    <input type="text" name="hero_badge" class="form-control" value="<?= htmlspecialchars($settings['hero_badge'] ?? 'Digital SIWES Management Platform') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7">Hero Main Headline *</label>
                                    <input type="text" name="hero_title" class="form-control" value="<?= htmlspecialchars($settings['hero_title'] ?? 'Simplifying SIWES Placement and Allocation') ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7">Hero Subtitle / Description</label>
                                    <textarea name="hero_description" class="form-control" rows="3"><?= htmlspecialchars($settings['hero_description'] ?? 'A smart digital platform for managing student SIWES registration, company placement, allocation, and monitoring efficiently.') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7">Hero Background Image</label>
                                    <div class="row align-items-center g-3">
                                        <div class="col-12 col-sm-8">
                                            <input type="file" name="hero_image" class="form-control" accept="image/*">
                                            <small class="text-muted fs-8">Upload a new hero background photo (JPG, PNG, WEBP, up to 8MB)</small>
                                        </div>
                                        <div class="col-12 col-sm-4 text-center text-sm-start">
                                            <img src="<?= $baseUrl ?>/<?= htmlspecialchars($settings['hero_image'] ?? 'images/hero.jpg') ?>" alt="Hero Preview" class="img-thumbnail rounded d-inline-block" style="max-height: 80px; object-fit: cover;">
                                            <div class="fs-8 text-secondary mt-1">Current Hero Image</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: About Section -->
                        <div class="tab-pane fade" id="tab-about" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold fs-7">About Section Badge</label>
                                    <input type="text" name="about_badge" class="form-control" value="<?= htmlspecialchars($settings['about_badge'] ?? 'About SIWES Portal') ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold fs-7">About Section Title</label>
                                    <input type="text" name="about_title" class="form-control" value="<?= htmlspecialchars($settings['about_title'] ?? 'Modernizing Industrial Training Placement') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7">About Description Paragraph 1</label>
                                    <textarea name="about_description_1" class="form-control" rows="3"><?= htmlspecialchars($settings['about_description_1'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7">About Description Paragraph 2</label>
                                    <textarea name="about_description_2" class="form-control" rows="3"><?= htmlspecialchars($settings['about_description_2'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7">About Section Image</label>
                                    <div class="row align-items-center g-3">
                                        <div class="col-12 col-sm-8">
                                            <input type="file" name="about_image" class="form-control" accept="image/*">
                                            <small class="text-muted fs-8">Upload about section image (JPG, PNG, WEBP, up to 8MB)</small>
                                        </div>
                                        <div class="col-12 col-sm-4 text-center text-sm-start">
                                            <img src="<?= $baseUrl ?>/<?= htmlspecialchars($settings['about_image'] ?? 'images/about.jpg') ?>" alt="About Preview" class="img-thumbnail rounded d-inline-block" style="max-height: 80px; object-fit: cover;">
                                            <div class="fs-8 text-secondary mt-1">Current About Image</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: CTA & Footer -->
                        <div class="tab-pane fade" id="tab-contact" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold fs-7">Call to Action (CTA) Title</label>
                                    <input type="text" name="cta_title" class="form-control" value="<?= htmlspecialchars($settings['cta_title'] ?? 'Ready to simplify SIWES management?') ?>">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold fs-7">CTA Subtitle / Description</label>
                                    <input type="text" name="cta_description" class="form-control" value="<?= htmlspecialchars($settings['cta_description'] ?? 'Experience a faster, paperless, and intelligent student industrial work experience scheme.') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7">Footer About / Description Text</label>
                                    <textarea name="footer_description" class="form-control" rows="2"><?= htmlspecialchars($settings['footer_description'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold fs-7">Contact Directorate / Office Address</label>
                                    <input type="text" name="contact_address" class="form-control" value="<?= htmlspecialchars($settings['contact_address'] ?? 'SIWES Directorate, Admin Block') ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold fs-7">Support Email</label>
                                    <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($settings['contact_email'] ?? 'siwes@institution.edu.ng') ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold fs-7">Support Phone</label>
                                    <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($settings['contact_phone'] ?? '+234 803 123 4567') ?>">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                        <button type="submit" class="btn btn-green fw-bold px-4 py-2 w-100 w-sm-auto">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Save All Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
