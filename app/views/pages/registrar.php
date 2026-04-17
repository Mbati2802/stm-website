<?php $registrarEmail = trim((string)($settings['registrar_email'] ?? 'registrar@stmarysmchmcollege.ac.ke')); ?>
<?php
$heroTitlePrimary = 'Office of the';
$heroTitleSecondary = 'Registrar';
$heroTagline = 'Supporting your academic journey from admission to graduation.';
$heroPrimaryLabel = 'Contact Registrar';
$heroPrimaryLink = 'contact';
$heroSecondaryLabel = '';
$heroSecondaryLink = '';
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h2 class="h4 mb-3">About the Registrar&rsquo;s Office</h2>
        <p class="mb-0 text-muted">
            The Registrar&rsquo;s Office is responsible for managing all student academic records, admissions processes, and institutional documentation. We ensure that every student&rsquo;s academic journey is well-coordinated, accurate, and aligned with institutional and regulatory standards.
        </p>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h2 class="h4 mb-3">Registrar&rsquo;s Message</h2>
        <div class="soft-card p-4">
            <p class="mb-0">
                Welcome to the Registrar&rsquo;s Office at St. Mary&rsquo;s College of Health Sciences. Our role is to ensure a smooth and efficient academic process for all students&mdash;from application and admission to examination and certification. We are committed to providing timely, accurate, and supportive services to help you succeed in your academic journey.
            </p>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="row g-4">
            <div class="col-lg-6">
                <h3 class="h5 mb-3">Our Key Functions</h3>
                <ul class="list-unstyled soft-card p-4 mb-0">
                    <li class="mb-2">Student admissions and enrollment</li>
                    <li class="mb-2">Academic records management</li>
                    <li class="mb-2">Examination coordination</li>
                    <li class="mb-2">Certification and transcripts</li>
                    <li class="mb-2">Course registration support</li>
                    <li class="mb-0">Compliance with regulatory bodies</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <h3 class="h5 mb-3">Services We Offer</h3>
                <ul class="list-unstyled soft-card p-4 mb-0">
                    <li class="mb-2">Application and admission processing</li>
                    <li class="mb-2">Issuance of admission letters</li>
                    <li class="mb-2">Exam registration and results processing</li>
                    <li class="mb-2">Transcripts and academic documents</li>
                    <li class="mb-2">Student data updates and verification</li>
                    <li class="mb-0">Graduation clearance and certification</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="soft-card p-4 h-100">
                    <h3 class="h6 text-uppercase text-muted mb-3">Office Hours</h3>
                    <p class="mb-1">Monday &ndash; Friday: 8:00 AM &ndash; 5:00 PM</p>
                    <p class="mb-1">Saturday: 9:00 AM &ndash; 1:00 PM</p>
                    <p class="mb-0">Sunday &amp; Public Holidays: Closed</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="soft-card p-4 h-100">
                    <h3 class="h6 text-uppercase text-muted mb-3">Contact the Registrar</h3>
                    <p class="mb-1">Office Location: Within Campus</p>
                    <p class="mb-1">Phone: +254 791 309011 or +254101711499</p>
                    <p class="mb-0">Email: <?= e($registrarEmail) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="soft-card p-4">
            <h3 class="h6 text-uppercase text-muted mb-3">Important Notice</h3>
            <ul class="mb-0">
                <li>Registration deadlines will be posted on this page.</li>
                <li>Examination dates and schedules will be communicated early.</li>
                <li>Ensure timely submission of required academic documents.</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="cta-banner p-5 text-center text-white">
            <h2 class="fw-bold mb-2">Need assistance with your academic records or application?</h2>
            <p class="mb-3 text-white-50">Visit the Registrar&rsquo;s Office or contact us today&mdash;we are here to help you every step of the way.</p>
            <a class="btn btn-light" href="<?= e(base_url('contact')) ?>">Contact Registrar Office</a>
        </div>
    </div>
</section>
