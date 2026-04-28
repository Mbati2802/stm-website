<?php
$heroTitlePrimary = 'Choose Your';
$heroTitleSecondary = 'Portal';
$heroTagline = 'Access the right portal for your role at St. Mary’s Mother and Child Hospital Medical Training College.';
$heroPrimaryLabel = 'Student Portal';
$heroPrimaryLink = 'portal/login';
$heroSecondaryLabel = 'Staff Portal';
$heroSecondaryLink = admin_login_url();
$heroPrimaryTargetBlank = true;
$heroSecondaryTargetBlank = true;
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="soft-card p-4 h-100 bg-white">
                    <h2 class="h5 mb-2">Student Portal</h2>
                    <p class="text-muted mb-3">For enrolled students to view courses, timetables, grades, assignments, and study resources.</p>
                    <a class="btn btn-primary" href="<?= e(base_url('portal/login')) ?>" target="_blank" rel="noopener noreferrer">Open Student Portal</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="soft-card p-4 h-100 bg-white">
                    <h2 class="h5 mb-2">Staff Portal</h2>
                    <p class="text-muted mb-3">For administrators and teachers to manage programmes, academic content, and portal data.</p>
                    <a class="btn btn-outline-primary" href="<?= e(admin_login_url()) ?>" target="_blank" rel="noopener noreferrer">Open Staff Portal</a>
                </div>
            </div>
        </div>
    </div>
</section>
