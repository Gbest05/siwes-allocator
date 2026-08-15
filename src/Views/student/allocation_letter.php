<?php
$pageTitle = 'SIWES Allocation Letter';
require __DIR__ . '/../layouts/header.php';
$baseUrl = (require __DIR__ . '/../../../config/app.php')['base_url'];
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="<?= $baseUrl ?>/index.php?route=dashboard" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
        </a>
        <button onclick="window.print()" class="btn btn-green fw-bold">
            <i class="fa-solid fa-print me-1"></i> Print / Save as PDF
        </button>
    </div>

    <!-- Official Letter Document -->
    <div class="card-custom p-5 bg-white printable-letter shadow-sm border mx-auto" style="max-width: 800px; font-family: 'Times New Roman', serif;">
        
        <!-- Institution Letterhead -->
        <div class="text-center border-bottom pb-4 mb-4">
            <h3 class="fw-bold text-uppercase mb-1" style="color: #166534; letter-spacing: 0.05em;">School of Technology & Applied Sciences</h3>
            <h6 class="fw-bold text-uppercase text-secondary mb-1">Directorate of Student Industrial Work Experience Scheme (SIWES)</h6>
            <p class="mb-0 fs-7 text-muted">P.M.B. 2011, Main Campus &bull; Email: siwes@institution.edu.ng &bull; Phone: +234 803 123 4567</p>
        </div>

        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <strong>Ref No:</strong> STAS/SIWES/2026/0<?= $student['id'] ?><br>
                <strong>Date:</strong> <?= date('F d, Y') ?>
            </div>
            <div class="text-end">
                <span class="badge bg-light text-dark border p-2">Official SIWES Posting Letter</span>
            </div>
        </div>

        <div class="mb-4">
            <strong>The Human Resources Manager / Industrial Supervisor,</strong><br>
            <strong><?= htmlspecialchars($allocation['company_name']) ?></strong>,<br>
            <?= htmlspecialchars($allocation['company_address']) ?>,<br>
            <?= htmlspecialchars($allocation['company_city']) ?>, <?= htmlspecialchars($allocation['company_state']) ?> State.
        </div>

        <h5 class="fw-bold text-uppercase text-center mb-4 text-decoration-underline" style="font-size: 1.1rem;">
            LETTER OF ATTACHMENT FOR STUDENT INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES)
        </h5>

        <p style="text-align: justify; line-height: 1.8;">
            We write to formally introduce <strong><?= strtoupper(htmlspecialchars($student['full_name'])) ?></strong> (Matriculation Number: <strong><?= htmlspecialchars($student['matric_number']) ?></strong>), a registered <strong><?= htmlspecialchars($student['programme']) ?> (<?= htmlspecialchars($student['level']) ?>)</strong> student in the Department of <strong><?= htmlspecialchars($student['department_name']) ?></strong>.
        </p>

        <p style="text-align: justify; line-height: 1.8;">
            In partial fulfillment of the academic requirements for the award of the Diploma, the above-named student has been officially allocated to your reputable organization for a mandatory <strong>Six (6) Months</strong> industrial attachment training commencing on <strong><?= date('M d, Y', strtotime($allocation['start_date'])) ?></strong> and concluding on <strong><?= date('M d, Y', strtotime($allocation['end_date'])) ?></strong>.
        </p>

        <p style="text-align: justify; line-height: 1.8;">
            We kindly request that you grant the student placement within your institution to enable them gain practical work experience relevant to their field of study. Please complete and return the attached Form SP-1 upon arrival.
        </p>

        <p class="mt-4">Yours faithfully,</p>

        <div class="row align-items-end mt-5 pt-3">
            <div class="col-6">
                <div style="border-bottom: 2px solid #111; width: 220px; margin-bottom: 5px;"></div>
                <strong>Dr. Mrs. Amina Yusuf</strong><br>
                <small class="text-muted">Head of SIWES Directorate</small>
            </div>
            <div class="col-6 text-end">
                <div class="d-inline-block border border-2 border-success rounded-circle p-3 text-success fw-bold opacity-75" style="transform: rotate(-12deg); font-size: 0.85rem;">
                    OFFICIAL SEAL &bull; SIWES UNIT
                </div>
            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
