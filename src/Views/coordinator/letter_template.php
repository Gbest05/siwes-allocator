<?php
$pageTitle = 'SIWES Application & Placement Letter Template Editor';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
use App\Core\Auth;
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

            <div class="card-custom p-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="fw-bold mb-0 text-dark-green">
                                <i class="fa-solid fa-file-signature me-2"></i> SIWES Application & Placement Letter Editor
                            </h4>
                            <span class="badge bg-light-green text-dark-green fw-bold">Coordinator & Admin Control</span>
                        </div>
                        <p class="text-secondary fs-7 mb-0">
                            Edit and customize the official institution letterhead, introduction paragraphs, duration terms, and signatories for all student SIWES application and placement letters.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <a href="<?= $baseUrl ?>/index.php?route=allocation-letter" target="_blank" class="btn btn-outline-green btn-sm fw-bold">
                            <i class="fa-solid fa-eye me-1"></i> Preview Generated Letter
                        </a>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Form Column -->
                    <div class="col-lg-7">
                        <form action="<?= $baseUrl ?>/index.php?route=coordinator/letter-template" method="POST" id="letterTemplateForm">
                            <input type="hidden" name="csrf_token" value="<?= Helper::csrfToken() ?>">

                            <!-- SECTION 1: Institution Header & Letterhead -->
                            <div class="card p-3 p-md-4 mb-4 border bg-light-subtle rounded-3">
                                <h6 class="fw-bold text-dark-green mb-3 border-bottom pb-2">
                                    <i class="fa-solid fa-school me-2"></i> 1. Institution Header & Letterhead
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold fs-8 mb-1">Institution Name *</label>
                                        <input type="text" name="letter_institution_name" id="input_inst_name" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_institution_name'] ?? 'School of Technology & Applied Sciences') ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold fs-8 mb-1">Directorate / Unit Title *</label>
                                        <input type="text" name="letter_directorate_title" id="input_directorate" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_directorate_title'] ?? 'Directorate of Student Industrial Work Experience Scheme (SIWES)') ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold fs-8 mb-1">Contact Sub-header / Address *</label>
                                        <input type="text" name="letter_contact_info" id="input_contact_info" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_contact_info'] ?? 'P.M.B. 2011, Main Campus • Email: siwes@institution.edu.ng • Phone: +234 803 123 4567') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-8 mb-1">Reference Number Prefix *</label>
                                        <input type="text" name="letter_ref_prefix" id="input_ref_prefix" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_ref_prefix'] ?? 'STAS/SIWES/') ?>" required>
                                        <small class="text-muted fs-8">Appears as: [PREFIX]2026/001</small>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 2: Addressee & Main Subject -->
                            <div class="card p-3 p-md-4 mb-4 border bg-light-subtle rounded-3">
                                <h6 class="fw-bold text-dark-green mb-3 border-bottom pb-2">
                                    <i class="fa-solid fa-envelope-open-text me-2"></i> 2. Recipient Addressee & Main Title
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold fs-8 mb-1">Recipient Salutation Line *</label>
                                        <input type="text" name="letter_salutation" id="input_salutation" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_salutation'] ?? 'The Human Resources Manager / Industrial Supervisor,') ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold fs-8 mb-1">Letter Main Heading / Subject *</label>
                                        <input type="text" name="letter_title" id="input_title" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_title'] ?? 'LETTER OF ATTACHMENT FOR STUDENT INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES)') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 3: Letter Body Paragraphs & Placeholders -->
                            <div class="card p-3 p-md-4 mb-4 border bg-light-subtle rounded-3">
                                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                    <h6 class="fw-bold text-dark-green mb-0">
                                        <i class="fa-solid fa-align-left me-2"></i> 3. Letter Body Text Content
                                    </h6>
                                    <span class="badge bg-secondary fs-8">Supports Dynamic Tags</span>
                                </div>

                                <!-- Dynamic Tags Guide -->
                                <div class="p-3 bg-white rounded border mb-3">
                                    <small class="fw-bold text-dark d-block mb-1">Available Dynamic Placeholders (click tag to copy):</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-light text-dark-green border tag-pill" style="cursor: pointer;" title="Student's Full Name">{STUDENT_NAME}</span>
                                        <span class="badge bg-light text-dark-green border tag-pill" style="cursor: pointer;" title="Student's Matric Number">{MATRIC_NUMBER}</span>
                                        <span class="badge bg-light text-dark-green border tag-pill" style="cursor: pointer;" title="Student's Department">{DEPARTMENT_NAME}</span>
                                        <span class="badge bg-light text-dark-green border tag-pill" style="cursor: pointer;" title="Academic Programme (ND/HND/BSc)">{PROGRAMME}</span>
                                        <span class="badge bg-light text-dark-green border tag-pill" style="cursor: pointer;" title="Academic Level">{LEVEL}</span>
                                        <span class="badge bg-light text-dark-green border tag-pill" style="cursor: pointer;" title="Partner Company Name">{COMPANY_NAME}</span>
                                        <span class="badge bg-light text-dark-green border tag-pill" style="cursor: pointer;" title="Attachment Duration">{DURATION}</span>
                                        <span class="badge bg-light text-dark-green border tag-pill" style="cursor: pointer;" title="Attachment Start Date">{START_DATE}</span>
                                        <span class="badge bg-light text-dark-green border tag-pill" style="cursor: pointer;" title="Attachment End Date">{END_DATE}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-8 mb-1">Paragraph 1: Student Introduction *</label>
                                    <textarea name="letter_body_intro" id="input_intro" class="form-control form-control-sm" rows="3" required><?= htmlspecialchars($settings['letter_body_intro'] ?? 'We write to formally introduce {STUDENT_NAME} (Matriculation Number: {MATRIC_NUMBER}), a registered {PROGRAMME} ({LEVEL}) student in the Department of {DEPARTMENT_NAME}.') ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-8 mb-1">Paragraph 2: Training Term & Duration *</label>
                                    <textarea name="letter_body_training" id="input_training" class="form-control form-control-sm" rows="3" required><?= htmlspecialchars($settings['letter_body_training'] ?? 'In partial fulfillment of the academic requirements for the award of the Diploma, the above-named student has been officially allocated to your reputable organization for a mandatory {DURATION} industrial attachment training commencing on {START_DATE} and concluding on {END_DATE}.') ?></textarea>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-semibold fs-8 mb-1">Paragraph 3: Institutional Request & Form SP-1 *</label>
                                    <textarea name="letter_body_conclusion" id="input_conclusion" class="form-control form-control-sm" rows="3" required><?= htmlspecialchars($settings['letter_body_conclusion'] ?? 'We kindly request that you grant the student placement within your institution to enable them gain practical work experience relevant to their field of study. Please complete and return the attached Form SP-1 upon arrival.') ?></textarea>
                                </div>
                            </div>

                            <!-- SECTION 4: Signatory & Seal -->
                            <div class="card p-3 p-md-4 mb-4 border bg-light-subtle rounded-3">
                                <h6 class="fw-bold text-dark-green mb-3 border-bottom pb-2">
                                    <i class="fa-solid fa-stamp me-2"></i> 4. Signatory & Official Seal
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-8 mb-1">Closing Salutation *</label>
                                        <input type="text" name="letter_closing" id="input_closing" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_closing'] ?? 'Yours faithfully,') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-8 mb-1">Official Seal / Stamp Text *</label>
                                        <input type="text" name="letter_seal_text" id="input_seal" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_seal_text'] ?? 'OFFICIAL SEAL • SIWES UNIT') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-8 mb-1">Signatory Full Name *</label>
                                        <input type="text" name="letter_signatory_name" id="input_sig_name" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_signatory_name'] ?? 'Dr. Mrs. Amina Yusuf') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-8 mb-1">Signatory Official Designation *</label>
                                        <input type="text" name="letter_signatory_title" id="input_sig_title" class="form-control form-control-sm" value="<?= htmlspecialchars($settings['letter_signatory_title'] ?? 'Head of SIWES Directorate') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <button type="reset" class="btn btn-outline-secondary btn-sm px-3">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Reset Inputs
                                </button>
                                <button type="submit" class="btn btn-green fw-bold px-4 py-2">
                                    <i class="fa-solid fa-floppy-disk me-2"></i> Save Letter Content
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Live Preview Column -->
                    <div class="col-lg-5">
                        <div class="position-sticky" style="top: 20px;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-7 text-dark-green">
                                    <i class="fa-solid fa-eye me-1"></i> Live Document Preview
                                </span>
                                <small class="text-muted fs-8">Updates automatically as you type</small>
                            </div>

                            <!-- Sheet Simulation Card -->
                            <div class="card p-3 p-md-4 bg-white shadow-sm border rounded-3" style="font-family: 'Times New Roman', serif; font-size: 0.82rem; line-height: 1.5; color: #111;">
                                <!-- Letterhead -->
                                <div class="text-center border-bottom pb-2 mb-2">
                                    <div class="fw-bold text-uppercase fs-6 text-success" id="prev_inst_name">
                                        <?= htmlspecialchars($settings['letter_institution_name'] ?? 'School of Technology & Applied Sciences') ?>
                                    </div>
                                    <div class="fw-bold text-secondary text-uppercase fs-8" id="prev_directorate">
                                        <?= htmlspecialchars($settings['letter_directorate_title'] ?? 'Directorate of Student Industrial Work Experience Scheme (SIWES)') ?>
                                    </div>
                                    <div class="text-muted fs-9" style="font-size: 0.68rem;" id="prev_contact_info">
                                        <?= htmlspecialchars($settings['letter_contact_info'] ?? 'P.M.B. 2011, Main Campus • Email: siwes@institution.edu.ng') ?>
                                    </div>
                                </div>

                                <!-- Ref and Date -->
                                <div class="d-flex justify-content-between fs-8 mb-2">
                                    <div>
                                        <strong>Ref:</strong> <span id="prev_ref"><?= htmlspecialchars($settings['letter_ref_prefix'] ?? 'STAS/SIWES/') ?>2026/001</span><br>
                                        <strong>Date:</strong> <?= date('F d, Y') ?>
                                    </div>
                                    <span class="badge bg-light text-dark border p-1" style="height: fit-content; font-size: 0.65rem;">Official Letter</span>
                                </div>

                                <!-- Addressee -->
                                <div class="fs-8 mb-2">
                                    <strong id="prev_salutation"><?= htmlspecialchars($settings['letter_salutation'] ?? 'The Human Resources Manager / Industrial Supervisor,') ?></strong><br>
                                    <strong>Interswitch Group Tech Hub</strong>,<br>
                                    Plot 1644, Oko Awo Street, Victoria Island, Lagos State.
                                </div>

                                <!-- Title -->
                                <div class="fw-bold text-uppercase text-center my-2 text-decoration-underline" style="font-size: 0.82rem;" id="prev_title">
                                    <?= htmlspecialchars($settings['letter_title'] ?? 'LETTER OF ATTACHMENT FOR STUDENT INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES)') ?>
                                </div>

                                <!-- Paragraphs -->
                                <p class="mb-2" style="text-align: justify;" id="prev_intro">
                                    <?= htmlspecialchars(str_replace(
                                        ['{STUDENT_NAME}', '{MATRIC_NUMBER}', '{PROGRAMME}', '{LEVEL}', '{DEPARTMENT_NAME}'],
                                        ['CHIDUBEM CHUKWUMA', 'F/ND/22/3210001', 'ND', 'ND2', 'Computer Science'],
                                        $settings['letter_body_intro'] ?? 'We write to formally introduce...'
                                    )) ?>
                                </p>

                                <p class="mb-2" style="text-align: justify;" id="prev_training">
                                    <?= htmlspecialchars(str_replace(
                                        ['{DURATION}', '{START_DATE}', '{END_DATE}', '{COMPANY_NAME}'],
                                        ['Six (6) Months', 'Oct 20, 2026', 'Apr 20, 2027', 'Interswitch Group Tech Hub'],
                                        $settings['letter_body_training'] ?? 'In partial fulfillment...'
                                    )) ?>
                                </p>

                                <p class="mb-3" style="text-align: justify;" id="prev_conclusion">
                                    <?= htmlspecialchars($settings['letter_body_conclusion'] ?? 'We kindly request that you grant the student placement...') ?>
                                </p>

                                <!-- Closing -->
                                <div class="mt-2" id="prev_closing">
                                    <?= htmlspecialchars($settings['letter_closing'] ?? 'Yours faithfully,') ?>
                                </div>

                                <!-- Signatory & Seal -->
                                <div class="d-flex justify-content-between align-items-end mt-2 pt-2">
                                    <div>
                                        <div style="border-bottom: 1.5px solid #111; width: 110px; margin-bottom: 3px;"></div>
                                        <strong class="d-block fs-8" id="prev_sig_name"><?= htmlspecialchars($settings['letter_signatory_name'] ?? 'Dr. Mrs. Amina Yusuf') ?></strong>
                                        <small class="text-muted d-block" style="font-size: 0.65rem;" id="prev_sig_title"><?= htmlspecialchars($settings['letter_signatory_title'] ?? 'Head of SIWES Directorate') ?></small>
                                    </div>
                                    <div class="border border-success text-success rounded-circle p-1 text-center fw-bold" style="font-size: 0.58rem; width: 68px; height: 68px; display: flex; align-items: center; justify-content: center; transform: rotate(-10deg);" id="prev_seal">
                                        <?= htmlspecialchars($settings['letter_seal_text'] ?? 'OFFICIAL SEAL') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Preview Sync Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    function replaceMockTags(text) {
        return text
            .replace(/{STUDENT_NAME}/g, 'CHIDUBEM CHUKWUMA')
            .replace(/{MATRIC_NUMBER}/g, 'F/ND/22/3210001')
            .replace(/{PROGRAMME}/g, 'ND')
            .replace(/{LEVEL}/g, 'ND2')
            .replace(/{DEPARTMENT_NAME}/g, 'Computer Science')
            .replace(/{COMPANY_NAME}/g, 'Interswitch Group Tech Hub')
            .replace(/{DURATION}/g, 'Six (6) Months')
            .replace(/{START_DATE}/g, 'Oct 20, 2026')
            .replace(/{END_DATE}/g, 'Apr 20, 2027');
    }

    function sync() {
        document.getElementById('prev_inst_name').textContent = document.getElementById('input_inst_name').value;
        document.getElementById('prev_directorate').textContent = document.getElementById('input_directorate').value;
        document.getElementById('prev_contact_info').textContent = document.getElementById('input_contact_info').value;
        document.getElementById('prev_ref').textContent = document.getElementById('input_ref_prefix').value + '2026/001';
        document.getElementById('prev_salutation').textContent = document.getElementById('input_salutation').value;
        document.getElementById('prev_title').textContent = document.getElementById('input_title').value;
        document.getElementById('prev_intro').textContent = replaceMockTags(document.getElementById('input_intro').value);
        document.getElementById('prev_training').textContent = replaceMockTags(document.getElementById('input_training').value);
        document.getElementById('prev_conclusion').textContent = replaceMockTags(document.getElementById('input_conclusion').value);
        document.getElementById('prev_closing').textContent = document.getElementById('input_closing').value;
        document.getElementById('prev_sig_name').textContent = document.getElementById('input_sig_name').value;
        document.getElementById('prev_sig_title').textContent = document.getElementById('input_sig_title').value;
        document.getElementById('prev_seal').textContent = document.getElementById('input_seal').value;
    }

    const inputs = document.querySelectorAll('#letterTemplateForm input, #letterTemplateForm textarea');
    inputs.forEach(input => {
        input.addEventListener('input', sync);
    });

    // Tag click to insert
    document.querySelectorAll('.tag-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            const tag = this.textContent;
            navigator.clipboard.writeText(tag);
            const originalText = this.textContent;
            this.textContent = 'Copied!';
            this.classList.replace('text-dark-green', 'text-success');
            setTimeout(() => {
                this.textContent = originalText;
                this.classList.replace('text-success', 'text-dark-green');
            }, 1200);
        });
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
