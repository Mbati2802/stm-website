<?php
$phone = trim((string)($settings['phone'] ?? '')) ?: '[Phone Number]';
$location = trim((string)($settings['location'] ?? '')) ?: '[Campus Location]';
$email = trim((string)($settings['email'] ?? '')) ?: '[Email Address]';
?>

<section class="py-5 bg-light border-bottom">
    <div class="site-width">
        <h1 class="display-6 fw-bold mb-2">How to Apply</h1>
        <p class="text-muted mb-0">
            Starting your journey at St. Mary's College of Health Sciences is simple and straightforward.
        </p>
    </div>
</section>

<section class="py-5">
    <div class="site-width">
        <div class="soft-card p-4 p-lg-5">
            <p class="mb-4">
                Follow the steps below to secure your place in one of our health science programmes.
            </p>

            <h2 class="h5 fw-bold">Step 1: Choose Your Course</h2>
            <p>
                Explore our range of diploma, certificate, and short courses and select a programme that aligns with your
                career goals in the healthcare field.
            </p>

            <h2 class="h5 fw-bold">Step 2: Check Entry Requirements</h2>
            <p>
                Ensure you meet the minimum entry requirements for your chosen course. Our admissions team is available to
                guide you if you are unsure about your qualifications.
            </p>

            <h2 class="h5 fw-bold">Step 3: Submit Your Application</h2>
            <p>Apply by filling out the application form:</p>
            <ul>
                <li>Online via our website</li>
                <li>Or physically at our admissions office</li>
            </ul>
            <p>
                Make sure you provide accurate information and attach all required documents.
            </p>

            <h2 class="h5 fw-bold">Step 4: Attach Required Documents</h2>
            <p>Prepare and submit the following:</p>
            <ul>
                <li>Copy of KCSE certificate/result slip</li>
                <li>Copy of National ID or Birth Certificate</li>
                <li>Passport-size photographs</li>
            </ul>

            <h2 class="h5 fw-bold">Step 5: Application Review</h2>
            <p>
                Your application will be reviewed by our admissions team. You may be contacted for clarification or guidance during this process.
            </p>

            <h2 class="h5 fw-bold">Step 6: Receive Admission Letter</h2>
            <p>
                Successful applicants will receive an official admission letter with details about reporting date, fees, and course information.
            </p>

            <h2 class="h5 fw-bold">Step 7: Confirm &amp; Report</h2>
            <p>
                Confirm your slot by following the instructions in your admission letter and report to the college on the specified date to begin your training.
            </p>

            <hr class="my-4">

            <h2 class="h5 fw-bold">Need Help?</h2>
            <p>Our admissions team is ready to assist you every step of the way.</p>
            <p class="mb-1">Call/WhatsApp: <?= e($phone) ?></p>
            <p class="mb-1">Visit Us: <?= e($location) ?></p>
            <p class="mb-0">Email: <?= e($email) ?></p>
        </div>
    </div>
</section>
