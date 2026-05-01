<?php $registrarEmail = trim((string)($settings['registrar_email'] ?? 'registrar@stmarysmchmcollege.ac.ke')); ?>
<?php $registrarImage = trim((string)($settings['registrar_image'] ?? 'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=900')); ?>
<?php $registrarMessage = trim((string)($settings['registrar_message'] ?? 'Welcome to the Registrar&rsquo;s Office at St. Mary&rsquo;s College of Health Sciences. Our role is to ensure a smooth and efficient academic process for all students&mdash;from application and admission to examination and certification. We are committed to providing timely, accurate, and supportive services to help you succeed in your academic journey.')); ?>
<?php $registrarAboutText = trim((string)($settings['registrar_about_text'] ?? 'The Registrar&rsquo;s Office is responsible for managing all student academic records, admissions processes, and institutional documentation. We ensure that every student&rsquo;s academic journey is well-coordinated, accurate, and aligned with institutional and regulatory standards.')); ?>
<?php $keyFunctions = array_filter(array_map('trim', explode('|', (string)($settings['registrar_key_functions'] ?? 'Student admissions and enrollment|Academic records management|Examination coordination|Certification and transcripts|Unit registration support|Compliance with regulatory bodies')))); ?>
<?php $servicesOffered = array_filter(array_map('trim', explode('|', (string)($settings['registrar_services'] ?? 'Application and admission processing|Issuance of admission letters|Exam registration and results processing|Transcripts and academic documents|Student data updates and verification|Graduation clearance and certification')))); ?>
<?php $officeHours = trim((string)($settings['registrar_office_hours'] ?? 'Monday to Friday: 8:00 AM &ndash; 5:00 PM|Saturday: 9:00 AM &ndash; 1:00 PM|Sunday &amp; Public Holidays: Closed')); ?>
<?php $importantNotices = array_filter(array_map('trim', explode('|', (string)($settings['registrar_important_notice'] ?? 'Registration deadlines will be posted on this page.|Examination dates and schedules will be communicated early.|Ensure timely submission of required academic documents.')))); ?>
<?php
$heroTitlePrimary = 'Office of the';
$heroTitleSecondary = 'Registrar';
$heroTagline = 'Supporting your academic journey from admission to graduation.';
$heroPrimaryLabel = 'Contact Registrar';
$heroPrimaryLink = 'contact-registrar';
$heroSecondaryLabel = '';
$heroSecondaryLink = '';
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h2 class="h4 mb-3">About the Registrar&rsquo;s Office</h2>
        <div class="soft-card p-4">
            <p class="mb-0 text-muted">
                <?= e($registrarAboutText) ?>
            </p>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h2 class="h4 mb-3">Registrar&rsquo;s Message</h2>
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div class="soft-card p-4">
                    <p class="mb-0">
                        <?= $registrarMessage ?>
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <img src="<?= e($registrarImage) ?>" alt="Registrar" class="img-fluid rounded-4 shadow-sm w-100">
            </div>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="row g-4">
            <div class="col-lg-6">
                <h3 class="h5 mb-3">Our Key Functions</h3>
                <ul class="list-unstyled soft-card p-4 mb-0">
                    <?php foreach ($keyFunctions as $index => $function): ?>
                        <li class="<?= $index < count($keyFunctions) - 1 ? 'mb-2' : 'mb-0' ?>"><?= e($function) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-6">
                <h3 class="h5 mb-3">Services We Offer</h3>
                <ul class="list-unstyled soft-card p-4 mb-0">
                    <?php foreach ($servicesOffered as $index => $service): ?>
                        <li class="<?= $index < count($servicesOffered) - 1 ? 'mb-2' : 'mb-0' ?>"><?= e($service) ?></li>
                    <?php endforeach; ?>
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
                    <?php $hours = array_filter(array_map('trim', explode('|', $officeHours))); ?>
                    <?php foreach ($hours as $hour): ?>
                        <p class="mb-1"><?= e($hour) ?></p>
                    <?php endforeach; ?>
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
                <?php foreach ($importantNotices as $notice): ?>
                    <li><?= e($notice) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="cta-banner p-5 text-center text-white">
            <h2 class="fw-bold mb-2">Need assistance with your academic records or application?</h2>
            <p class="mb-3 text-white-50">Visit the Registrar&rsquo;s Office or contact us today&mdash;we are here to help you every step of the way.</p>
            <a class="btn btn-light" href="<?= e(base_url('contact-registrar')) ?>">Contact Registrar Office</a>
        </div>
    </div>
</section>
