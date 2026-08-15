<?php
$pageTitle = 'Login';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Helper;

$flash = Helper::getFlash();
$siteSettings = Helper::getAllSettings();
?>

<div class="auth-wrapper">
    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-9 col-md-6 col-lg-5 col-xl-4">
                
                <!-- Brand / Return Link -->
                <div class="text-center mb-3">
                    <a href="<?= $baseUrl ?>/index.php?route=home" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark-charcoal fw-bold fs-5">
                        <?php if (!empty($siteSettings['site_logo'])): ?>
                            <img src="<?= $baseUrl ?>/<?= htmlspecialchars($siteSettings['site_logo']) ?>" alt="Logo" style="height: 36px; max-width: 120px; object-fit: contain;">
                        <?php else: ?>
                            <div class="bg-primary-green text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fa-solid fa-graduation-cap fs-5"></i>
                            </div>
                        <?php endif; ?>
                        <span><?= htmlspecialchars($siteSettings['site_name'] ?? 'SIWES Allocator') ?></span>
                    </a>
                </div>

                <div class="card-custom p-4 p-md-4 bg-white shadow-sm border">
                    <div class="text-center mb-3">
                        <h4 class="fw-bold text-dark-charcoal mb-1">Welcome Back</h4>
                        <p class="text-secondary fs-7 mb-0">Sign in to your SIWES portal account</p>
                    </div>

                    <?php if ($flash): ?>
                        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show fs-7 py-2 px-3 mb-3" role="alert">
                            <?= htmlspecialchars($flash['message']) ?>
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= $baseUrl ?>/index.php?route=login" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                        <div class="mb-3">
                            <label for="login_id" class="form-label fw-semibold fs-7 mb-1">Email or Matric Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-secondary"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="login_id" name="login_id" placeholder="e.g. F/ND/22/3210001 or email" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label fw-semibold fs-7 mb-0">Password</label>
                                <a href="#" class="text-primary-green fs-8 text-decoration-none fw-semibold">Forgot Password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-secondary"></i></span>
                                <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="password" name="password" placeholder="Enter password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label text-secondary fs-8" for="remember">
                                Keep me signed in
                            </label>
                        </div>

                        <button type="submit" class="btn btn-green w-100 py-2 fw-bold">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> Log In
                        </button>
                    </form>

                    <div class="mt-3 pt-2 border-top text-center fs-7">
                        <span class="text-secondary fs-8">Don't have an account?</span>
                        <a href="<?= $baseUrl ?>/index.php?route=register" class="text-primary-green fw-bold text-decoration-none ms-1 fs-8">Create Account</a>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="<?= $baseUrl ?>/index.php?route=home" class="text-secondary fs-8 text-decoration-none">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Homepage
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
