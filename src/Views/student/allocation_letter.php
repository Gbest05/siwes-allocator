<?php
$pageTitle = 'SIWES Official Application & Placement Letter';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Auth;
use App\Core\Helper;

$settings = Helper::getAllSettings();

// Build placeholder replacement dictionary
$replacements = [
    '{STUDENT_NAME}'     => strtoupper(htmlspecialchars($student['full_name'] ?? 'STUDENT NAME')),
    '{MATRIC_NUMBER}'    => htmlspecialchars($student['matric_number'] ?? 'MATRIC/000'),
    '{PROGRAMME}'        => htmlspecialchars($student['programme'] ?? 'ND'),
    '{LEVEL}'            => htmlspecialchars($student['level'] ?? 'ND2'),
    '{DEPARTMENT_NAME}'  => htmlspecialchars($student['department_name'] ?? 'Academic Department'),
    '{COMPANY_NAME}'     => htmlspecialchars($allocation['company_name'] ?? 'Partner Organization'),
    '{COMPANY_ADDRESS}'  => htmlspecialchars($allocation['company_address'] ?? 'Official Address'),
    '{COMPANY_CITY}'     => htmlspecialchars($allocation['company_city'] ?? 'City'),
    '{COMPANY_STATE}'    => htmlspecialchars($allocation['company_state'] ?? 'State'),
    '{START_DATE}'       => !empty($allocation['start_date']) ? date('M d, Y', strtotime($allocation['start_date'])) : 'To Be Announced',
    '{END_DATE}'         => !empty($allocation['end_date']) ? date('M d, Y', strtotime($allocation['end_date'])) : 'To Be Announced',
    '{DURATION}'         => 'Six (6) Months',
    '{DATE}'             => date('F d, Y'),
    '{REF_NO}'           => ($settings['letter_ref_prefix'] ?? 'STAS/SIWES/') . date('Y') . '/0' . ($student['id'] ?? 1),
];

function formatLetterText(string $template, array $replacements): string {
    return str_replace(array_keys($replacements), array_values($replacements), $template);
}

$isProspective = !empty($allocation['is_prospective']);
$canEdit = Auth::check() && in_array(Auth::role(), ['coordinator', 'admin'], true);
?>

<div class="container py-4">
    <!-- Action Bar (Hidden when printing) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 no-print">
        <a href="<?= $baseUrl ?>/index.php?route=<?= Auth::role() === 'student' ? 'dashboard' : 'coordinator/letter-template' ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
        <div class="d-flex gap-2">
            <?php if ($canEdit): ?>
                <a href="<?= $baseUrl ?>/index.php?route=coordinator/letter-template" class="btn btn-outline-success fw-bold">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Letter Content
                </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-green fw-bold">
                <i class="fa-solid fa-print me-1"></i> Print / Save as PDF
            </button>
        </div>
    </div>

    <!-- Official Letter Document -->
    <div class="card-custom p-4 p-sm-5 bg-white printable-letter shadow-sm border mx-auto" style="max-width: 820px; font-family: 'Times New Roman', Times, serif; color: #111;">
        
        <!-- Institution Letterhead -->
        <div class="text-center border-bottom pb-4 mb-4">
            <h3 class="fw-bold text-uppercase mb-1" style="color: #166534; letter-spacing: 0.05em; font-size: clamp(1.15rem, 3vw, 1.8rem);">
                <?= htmlspecialchars($settings['letter_institution_name'] ?? $settings['institution_name'] ?? 'School of Technology & Applied Sciences') ?>
            </h3>
            <h6 class="fw-bold text-uppercase text-secondary mb-1 fs-7">
                <?= htmlspecialchars($settings['letter_directorate_title'] ?? 'Directorate of Student Industrial Work Experience Scheme (SIWES)') ?>
            </h6>
            <p class="mb-0 fs-8 text-muted">
                <?= htmlspecialchars($settings['letter_contact_info'] ?? 'P.M.B. 2011, Main Campus • Email: siwes@institution.edu.ng • Phone: +234 803 123 4567') ?>
            </p>
        </div>

        <!-- Reference & Date Line -->
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
            <div class="fs-7">
                <strong>Ref No:</strong> <?= htmlspecialchars($replacements['{REF_NO}']) ?><br>
                <strong>Date:</strong> <?= date('F d, Y') ?>
            </div>
            <div class="text-sm-end">
                <span class="badge bg-light text-dark border p-2 fs-8">
                    <?= $isProspective ? 'Official SIWES Placement Request Letter' : 'Official SIWES Posting Letter' ?>
                </span>
            </div>
        </div>

        <!-- Addressee / Salutation -->
        <div class="mb-4 fs-7">
            <strong><?= nl2br(htmlspecialchars($settings['letter_salutation'] ?? 'The Human Resources Manager / Industrial Supervisor,')) ?></strong><br>
            <strong><?= htmlspecialchars($allocation['company_name'] ?? 'Partner Organization') ?></strong>,<br>
            <?php if (!empty($allocation['company_address'])): ?>
                <?= htmlspecialchars($allocation['company_address']) ?>,<br>
            <?php endif; ?>
            <?php if (!empty($allocation['company_city']) || !empty($allocation['company_state'])): ?>
                <?= htmlspecialchars($allocation['company_city'] ?? '') ?><?= !empty($allocation['company_city']) && !empty($allocation['company_state']) ? ', ' : '' ?><?= htmlspecialchars($allocation['company_state'] ?? '') ?> State.
            <?php endif; ?>
        </div>

        <!-- Letter Main Subject / Heading -->
        <h5 class="fw-bold text-uppercase text-center mb-4 text-decoration-underline" style="font-size: clamp(0.95rem, 2vw, 1.15rem); line-height: 1.4;">
            <?= htmlspecialchars($settings['letter_title'] ?? 'LETTER OF ATTACHMENT FOR STUDENT INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES)') ?>
        </h5>

        <!-- Body Paragraph 1: Introduction -->
        <p class="fs-7 mb-3" style="text-align: justify; line-height: 1.8;">
            <?= nl2br(formatLetterText($settings['letter_body_intro'] ?? 'We write to formally introduce {STUDENT_NAME} (Matriculation Number: {MATRIC_NUMBER}), a registered {PROGRAMME} ({LEVEL}) student in the Department of {DEPARTMENT_NAME}.', $replacements)) ?>
        </p>

        <!-- Body Paragraph 2: Training & Duration -->
        <p class="fs-7 mb-3" style="text-align: justify; line-height: 1.8;">
            <?= nl2br(formatLetterText($settings['letter_body_training'] ?? 'In partial fulfillment of the academic requirements for the award of the Diploma, the above-named student has been officially allocated to your reputable organization for a mandatory {DURATION} industrial attachment training commencing on {START_DATE} and concluding on {END_DATE}.', $replacements)) ?>
        </p>

        <!-- Body Paragraph 3: Institutional Placement Request -->
        <p class="fs-7 mb-4" style="text-align: justify; line-height: 1.8;">
            <?= nl2br(formatLetterText($settings['letter_body_conclusion'] ?? 'We kindly request that you grant the student placement within your institution to enable them gain practical work experience relevant to their field of study. Please complete and return the attached Form SP-1 upon arrival.', $replacements)) ?>
        </p>

        <!-- Closing Salutation -->
        <p class="mt-4 fs-7 mb-0">
            <?= htmlspecialchars($settings['letter_closing'] ?? 'Yours faithfully,') ?>
        </p>

        <!-- Signatory & Official Stamp Seal -->
        <div class="row align-items-end mt-4 pt-3 gy-3">
            <div class="col-12 col-sm-6">
                <div style="border-bottom: 2px solid #111; max-width: 220px; width: 100%; margin-bottom: 6px;"></div>
                <strong class="fs-7 d-block"><?= htmlspecialchars($settings['letter_signatory_name'] ?? 'Dr. Mrs. Amina Yusuf') ?></strong>
                <small class="text-muted fs-8"><?= htmlspecialchars($settings['letter_signatory_title'] ?? 'Head of SIWES Directorate') ?></small>
            </div>
            <div class="col-12 col-sm-6 text-sm-end">
                <div class="d-inline-block border border-2 border-success rounded-circle p-2 p-sm-3 text-success fw-bold opacity-75" style="transform: rotate(-12deg); font-size: 0.8rem;">
                    <?= htmlspecialchars($settings['letter_seal_text'] ?? 'OFFICIAL SEAL • SIWES UNIT') ?>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .no-print, .app-sidebar, .app-topbar, .site-footer {
        display: none !important;
    }
    .container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .printable-letter {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        max-width: 100% !important;
    }
}
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
