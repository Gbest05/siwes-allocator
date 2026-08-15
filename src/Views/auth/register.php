<?php
$pageTitle = 'Student Registration';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Helper;

$flash = Helper::getFlash();
$siteSettings = Helper::getAllSettings();
?>

<div class="auth-wrapper py-3">
    <!-- Top-Left Back to Homepage Link -->
    <a href="<?= $baseUrl ?>/index.php?route=home" class="btn-auth-back d-inline-flex align-items-center gap-2 text-decoration-none" title="Back to Homepage">
        <div class="back-icon-circle">
            <i class="fa-solid fa-arrow-left text-primary-green"></i>
        </div>
        <span class="d-none d-sm-inline fs-7">Back to Home</span>
    </a>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-10 col-md-9 col-lg-7 col-xl-6">
                
                <!-- Brand / Logo Link -->
                <div class="text-center mb-2">
                    <a href="<?= $baseUrl ?>/index.php?route=home" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark-charcoal fw-bold fs-5">
                        <?php if (!empty($siteSettings['site_logo'])): ?>
                            <img src="<?= $baseUrl ?>/<?= htmlspecialchars($siteSettings['site_logo']) ?>" alt="Logo" style="height: 32px; max-width: 110px; object-fit: contain;">
                        <?php else: ?>
                            <div class="bg-primary-green text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                <i class="fa-solid fa-graduation-cap fs-5"></i>
                            </div>
                        <?php endif; ?>
                        <span><?= htmlspecialchars($siteSettings['site_name'] ?? 'SIWES Allocator') ?></span>
                    </a>
                </div>

                <div class="card-custom p-4 bg-white shadow-sm border">
                    <div class="text-center mb-3">
                        <h4 class="fw-bold text-dark-charcoal mb-0">Student Registration</h4>
                        <p class="text-secondary fs-8 mb-0">Create your account to start your SIWES placement application</p>
                    </div>

                    <?php if ($flash): ?>
                        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show fs-8 py-2 px-3 mb-2" role="alert">
                            <?= htmlspecialchars($flash['message']) ?>
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= $baseUrl ?>/index.php?route=register" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                        <div class="row g-2">
                            <div class="col-sm-6">
                                <label for="full_name" class="form-label fw-semibold fs-8 mb-1">Full Name *</label>
                                <input type="text" class="form-control form-control-sm" id="full_name" name="full_name" placeholder="e.g. Chidubem Chukwuma" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="matric_number" class="form-label fw-semibold fs-8 mb-1">Matriculation Number *</label>
                                <input type="text" class="form-control form-control-sm" id="matric_number" name="matric_number" placeholder="e.g. F/ND/22/3210005" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="email" class="form-label fw-semibold fs-8 mb-1">Email Address *</label>
                                <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder="student@institution.edu.ng" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="phone" class="form-label fw-semibold fs-8 mb-1">Phone Number *</label>
                                <input type="tel" class="form-control form-control-sm" id="phone" name="phone" placeholder="08123456789" required>
                            </div>

                            <div class="col-12">
                                <label for="department_id" class="form-label fw-semibold fs-8 mb-1">Department *</label>
                                <select class="form-select form-select-sm" id="department_id" name="department_id" required>
                                    <option value="">-- Select Academic Department --</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?> (<?= htmlspecialchars($dept['code']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label for="programme" class="form-label fw-semibold fs-8 mb-1">Programme *</label>
                                <select class="form-select form-select-sm" id="programme" name="programme" required>
                                    <option value="ND">National Diploma (ND)</option>
                                    <option value="HND">Higher National Diploma (HND)</option>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label for="level" class="form-label fw-semibold fs-8 mb-1">Level *</label>
                                <select class="form-select form-select-sm" id="level" name="level" required>
                                    <option value="ND1">ND 1</option>
                                    <option value="ND2" selected>ND 2</option>
                                    <option value="HND1">HND 1</option>
                                    <option value="HND2">HND 2</option>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label for="reg_password" class="form-label fw-semibold fs-8 mb-1">Password *</label>
                                <div class="input-group input-group-sm">
                                    <input type="password" class="form-control" id="reg_password" name="password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="reg_password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="confirm_password" class="form-label fw-semibold fs-8 mb-1">Confirm Password *</label>
                                <div class="input-group input-group-sm">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-check my-2">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label text-secondary fs-8" for="terms">
                                I agree to the <a href="#" class="text-primary-green">SIWES Placement Guidelines</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-green w-100 py-2 fw-bold fs-7">
                            <i class="fa-solid fa-user-plus me-1"></i> Register Student Account
                        </button>
                    </form>

                    <div class="mt-2 pt-2 border-top text-center fs-8">
                        <span class="text-secondary">Already registered?</span>
                        <a href="<?= $baseUrl ?>/index.php?route=login" class="text-primary-green fw-bold text-decoration-none ms-1">Sign In Here</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
