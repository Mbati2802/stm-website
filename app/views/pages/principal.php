<?php
$principalName = $settings['principal_name'] ?? 'Dr. Jane N. Wanjiku';
$principalTitle = $settings['principal_title'] ?? 'Principal, St. Mary\'s College';
$principalMessage = $settings['principal_message'] ?? ($page['content'] ?? 'Welcome to St. Mary\'s. We nurture competence, confidence, and character to build national impact.');
$principalImage = $settings['principal_image'] ?? 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=900';
$principalSignature = $settings['principal_signature'] ?? 'With gratitude and commitment to excellence.';
$visionPoints = array_filter(array_map('trim', explode('|', (string)($settings['principal_vision_points'] ?? 'Academic excellence|Character development|Community impact|Innovation in healthcare training'))));
$focusAreas = array_filter(array_map('trim', explode('|', (string)($settings['principal_focus_areas'] ?? 'Student support|Industry readiness|Research and practice|Digital learning'))));
$principalEmail = trim((string)($settings['principal_email'] ?? 'principal@stmarysmchmcollege.ac.ke'));
$principalFacebook = trim((string)($settings['principal_facebook'] ?? ''));
$principalX = trim((string)($settings['principal_x'] ?? ''));
$principalLinkedin = trim((string)($settings['principal_linkedin'] ?? ''));
?>

<?php
$heroTitlePrimary = 'The Principal';
$heroTitleSecondary = 'Leadership Desk';
$heroTagline = 'Insights, priorities, and the vision guiding student success and institutional growth.';
$heroPrimaryLabel = 'Contact Office';
$heroPrimaryLink = 'contact';
$heroSecondaryLabel = '';
$heroSecondaryLink = '';
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h1 class="split-title mb-4"><span class="title-primary">Principal</span> | <span class="title-secondary">Message</span></h1>
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                <div class="soft-card p-3">
                    <img class="img-fluid rounded-4 shadow-sm w-100 mb-3" src="<?= e($principalImage) ?>" alt="<?= e($principalName) ?>">
                    <h2 class="h5 fw-bold mb-1"><?= e($principalName) ?></h2>
                    <p class="text-muted mb-0"><?= e($principalTitle) ?></p>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="soft-card p-4 mb-3">
                    <?= $principalMessage ?>
                    <p class="mt-3 mb-0"><em><?= e($principalSignature) ?></em></p>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="soft-card p-4 h-100">
                            <h3 class="h6 text-uppercase text-muted mb-3">Vision Priorities</h3>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($visionPoints as $item): ?>
                                    <li class="mb-1"><?= e($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="soft-card p-4 h-100">
                            <h3 class="h6 text-uppercase text-muted mb-3">Current Focus Areas</h3>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($focusAreas as $item): ?>
                                    <li class="mb-1"><?= e($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="soft-card p-4 h-100">
                            <h3 class="h6 text-uppercase text-muted mb-3">Principal Contacts</h3>
                            <p class="mb-2"><strong>Email:</strong> <?= e($principalEmail) ?></p>
                            <p class="mb-2"><strong>Phone:</strong> +254 791 309011</p>
                            <p class="mb-0"><strong>Alt Phone:</strong> +254101711499</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="soft-card p-4 h-100">
                            <h3 class="h6 text-uppercase text-muted mb-3">Social Profiles</h3>
                            <ul class="list-unstyled mb-0">
                                <?php if ($principalFacebook !== ''): ?><li class="mb-2"><a href="<?= e($principalFacebook) ?>" target="_blank" rel="noopener">Facebook</a></li><?php endif; ?>
                                <?php if ($principalX !== ''): ?><li class="mb-2"><a href="<?= e($principalX) ?>" target="_blank" rel="noopener">X (Twitter)</a></li><?php endif; ?>
                                <?php if ($principalLinkedin !== ''): ?><li><a href="<?= e($principalLinkedin) ?>" target="_blank" rel="noopener">LinkedIn</a></li><?php endif; ?>
                                <?php if ($principalFacebook === '' && $principalX === '' && $principalLinkedin === ''): ?>
                                    <li class="text-muted">Social profile links are not set yet.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
