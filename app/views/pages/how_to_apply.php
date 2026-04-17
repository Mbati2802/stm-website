<?php
$phone = trim((string)($settings['phone'] ?? '')) ?: '[Phone Number]';
$location = trim((string)($settings['location'] ?? '')) ?: '[Campus Location]';
$email = trim((string)($settings['email'] ?? '')) ?: '[Email Address]';
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
        <h2 class="split-title mb-2"><span class="title-primary">Application</span> | <span class="title-secondary">Process</span></h2>
        <p class="section-subtitle-standard mb-0">
            Follow the steps below to secure your place in one of our health science programmes.
        </p>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section" data-aos="fade-up">
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
