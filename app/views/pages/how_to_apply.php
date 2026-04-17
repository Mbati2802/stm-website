<?php
$phone = '+254 791 309011 or +254101711499';
$location = trim((string)($settings['location'] ?? '')) ?: '[Campus Location]';
$email = 'admission@stmarysmchmcollege.ac.ke';
?>

<?php
$heroTitlePrimary = 'How to';
$heroTitleSecondary = 'Apply';
$heroTagline = "Starting your journey at St. Mary's College of Health Sciences is simple and straightforward.";
$heroPrimaryLabel = 'Apply Online';
$heroPrimaryLink = 'programmes/apply';
$heroSecondaryLabel = 'Contact Admissions';
$heroSecondaryLink = 'contact';
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
    <div class="site-width boxed-section" data-aos="fade-up">
        <div class="row g-4">
            <div class="col-lg-8">
                <h2 class="split-title mb-2"><span class="title-primary">Application</span> | <span class="title-secondary">Process</span></h2>
                <p class="section-subtitle-standard mb-3">
                    Follow the steps below to secure your place in one of our health science programmes.
                </p>
                <div class="soft-card p-4 p-lg-5">
                    <div class="mb-4">
                        <h3 class="h5 fw-bold">Step 1: Choose Your Course</h3>
                        <p class="mb-0">Explore our range of diploma, certificate, and short courses and select a programme that aligns with your career goals in the healthcare field.</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="h5 fw-bold">Step 2: Check Entry Requirements</h3>
                        <p class="mb-0">Ensure you meet the minimum entry requirements for your chosen course. Our admissions team is available to guide you if you are unsure about your qualifications.</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="h5 fw-bold">Step 3: Submit Your Application</h3>
                        <p>Apply by filling out the application form:</p>
                        <ul>
                            <li>Online via our website</li>
                            <li>Or physically at our admissions office</li>
                        </ul>
                        <p class="mb-0">Make sure you provide accurate information and attach all required documents.</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="h5 fw-bold">Step 4: Attach Required Documents</h3>
                        <p>Prepare and submit the following:</p>
                        <ul class="mb-0">
                            <li>Copy of KCSE certificate/result slip</li>
                            <li>Copy of National ID or Birth Certificate</li>
                            <li>Passport-size photographs</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h3 class="h5 fw-bold">Step 5: Application Review</h3>
                        <p class="mb-0">Your application will be reviewed by our admissions team. You may be contacted for clarification or guidance during this process.</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="h5 fw-bold">Step 6: Receive Admission Letter</h3>
                        <p class="mb-0">Successful applicants will receive an official admission letter with details about reporting date, fees, and course information.</p>
                    </div>

                    <div>
                        <h3 class="h5 fw-bold">Step 7: Confirm &amp; Report</h3>
                        <p class="mb-0">Confirm your slot by following the instructions in your admission letter and report to the college on the specified date to begin your training.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="soft-card p-3 mb-3">
                    <h3 class="h6 text-uppercase text-muted mb-3">Important Links</h3>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><a href="<?= e(base_url('programmes')) ?>">Programmes</a></li>
                        <li class="mb-2"><a href="<?= e(base_url('programmes/apply')) ?>">Online Application</a></li>
                        <li class="mb-2"><a href="<?= e(base_url('contact')) ?>">Admissions Contacts</a></li>
                        <li><a href="<?= e(base_url('library')) ?>">Library / Documents</a></li>
                    </ul>
                </div>
                <div class="soft-card p-3">
                    <ul class="nav nav-tabs mb-3" id="applyDocsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="fees-tab" data-bs-toggle="tab" data-bs-target="#fees-tab-pane" type="button" role="tab">Fee Structure</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="brochure-tab" data-bs-toggle="tab" data-bs-target="#brochure-tab-pane" type="button" role="tab">Brochure</button>
                        </li>
                    </ul>
                    <div class="tab-content small">
                        <div class="tab-pane fade show active" id="fees-tab-pane" role="tabpanel">
                            <p class="mb-2">Download the latest fee structure document from admissions.</p>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('contact')) ?>">Request Fee Structure</a>
                        </div>
                        <div class="tab-pane fade" id="brochure-tab-pane" role="tabpanel">
                            <p class="mb-2">Get the current college brochure with available courses and intake details.</p>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('contact')) ?>">Request Brochure</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section" data-aos="fade-up">
        <div class="cta-banner p-5 text-center text-white">
            <h2 class="fw-bold mb-2">Need Help?</h2>
            <p class="mb-3 text-white-50">Our admissions team is ready to assist you every step of the way.</p>
            <p class="mb-1">Call/WhatsApp: <?= e($phone) ?></p>
            <p class="mb-1">Visit Us: <?= e($location) ?></p>
            <p class="mb-4">Email: <?= e($email) ?></p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a class="btn btn-light" href="<?= e(base_url('programmes/apply')) ?>">Apply Online</a>
                <a class="btn btn-outline-light" href="<?= e(base_url('contact')) ?>">Contact Us</a>
            </div>
        </div>
    </div>
</section>
