<?php
$heroTitlePrimary = 'College';
$heroTitleSecondary = 'Uniqueness';
$heroTagline = 'What sets St. Mary\'s apart in health sciences education.';
$heroPrimaryLabel = 'Apply Now';
$heroPrimaryLink = 'programmes/apply';
$heroSecondaryLabel = 'Contact Us';
$heroSecondaryLink = 'contact';
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h1 class="split-title mb-3"><span class="title-primary">What Makes</span> <span class="title-secondary">Us Different</span></h1>
        <div class="row g-3">
            <?php foreach ($whyItems as $item): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="soft-card p-4 h-100">
                        <i class="bi bi-patch-check-fill text-primary mb-2 d-inline-block"></i>
                        <p class="mb-0"><?= e($item) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
