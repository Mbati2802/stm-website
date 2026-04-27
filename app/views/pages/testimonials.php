<?php
$testimonialTemplate = (string)($settings['testimonial_template'] ?? 'cards');
$testimonialCardStyle = (string)($settings['testimonial_card_style'] ?? 'centered');
$testimonialAccent = (string)($settings['testimonial_accent_color'] ?? '#5fc7e7');
$testimonialBg = (string)($settings['testimonial_bg_color'] ?? '#f5f7fa');
$validHexAccent = preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $testimonialAccent) ? $testimonialAccent : '#5fc7e7';
$validHexBg = preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $testimonialBg) ? $testimonialBg : '#f5f7fa';
$gridCount = max(2, min(5, (int)($settings['testimonial_grid_count'] ?? 3)));
$colClass = match ($gridCount) {
    2 => 'col-md-6',
    4 => 'col-md-6 col-lg-3',
    5 => 'col-md-6 testimonial-grid-col-5',
    default => 'col-md-6 col-lg-4'
};
?>

<?php
$heroTitlePrimary = 'What Our';
$heroTitleSecondary = 'Students Say';
$heroTagline = 'Real experiences from students, parents, and guardians who are part of the St. Mary\'s community.';
$heroPrimaryLabel = 'View Programmes';
$heroPrimaryLink = 'programmes';
$heroSecondaryLabel = 'Contact Us';
$heroSecondaryLink = 'contact';
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack testimonials-section testimonials-template-cards testimonials-card-<?= e($testimonialCardStyle) ?>" style="--testimonial-accent: <?= e($validHexAccent) ?>; --testimonial-bg: <?= e($validHexBg) ?>;">
    <div class="site-width boxed-section" style="background:var(--testimonial-bg)">
        <div class="row g-4">
            <div class="col-lg-8">
                <h1 class="split-title mb-3">
                    <span class="title-primary">Student</span>
                    <span class="title-secondary">Testimonials</span>
                </h1>
                <p class="text-muted mb-4">Hear directly from our students, alumni, parents and guardians about their experience at St. Mary's College of Health Sciences.</p>

                <div class="row g-4">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="<?= e($colClass) ?>">
                            <div class="soft-card p-4 h-100 testimonial-card" style="background:#fff;border-left:4px solid var(--testimonial-accent)">
                                <?php if (!empty($testimonial['image'])): ?>
                                    <div class="testimonial-avatar-wrap mb-3">
                                        <img src="<?= e($testimonial['image']) ?>" alt="<?= e($testimonial['name']) ?>" class="testimonial-avatar" loading="lazy">
                                    </div>
                                <?php endif; ?>
                                <i class="bi bi-quote testimonial-quote-icon mb-2 d-block" style="color:var(--testimonial-accent)"></i>
                                <p class="mb-3 testimonial-message">"<?= e($testimonial['message']) ?>"</p>
                                <strong class="d-block testimonial-name"><?= e($testimonial['name']) ?></strong>
                                <?php if ($testimonial['course'] !== ''): ?>
                                    <p class="mb-0 small text-muted"><?= e($testimonial['course']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="soft-card p-4 mb-3" style="background:#fff">
                        <h3 class="h6 fw-bold mb-3"><i class="bi bi-link-45deg me-1" style="color:var(--testimonial-accent)"></i>Quick Links</h3>
                        <ul class="list-unstyled sidebar-links mb-0">
                            <li><a href="<?= e(base_url('programmes')) ?>"><i class="bi bi-mortarboard me-2"></i>Our Programmes</a></li>
                            <li><a href="<?= e(base_url('programmes/how-to-apply')) ?>"><i class="bi bi-file-earmark-text me-2"></i>How to Apply</a></li>
                            <li><a href="<?= e(base_url('programmes/apply')) ?>"><i class="bi bi-pencil-square me-2"></i>Apply Online</a></li>
                            <li><a href="<?= e(base_url('events')) ?>"><i class="bi bi-calendar-event me-2"></i>Events</a></li>
                            <li><a href="<?= e(base_url('about')) ?>"><i class="bi bi-info-circle me-2"></i>About Us</a></li>
                            <li><a href="<?= e(base_url('contact')) ?>"><i class="bi bi-envelope me-2"></i>Contact Us</a></li>
                        </ul>
                    </div>

                    <div class="soft-card p-4 mb-3" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff;">
                        <h3 class="h6 fw-bold mb-2 text-white"><i class="bi bi-star me-1"></i>Why Choose St. Mary's?</h3>
                        <ul class="list-unstyled mb-3 small" style="opacity:.9">
                            <li class="mb-1"><i class="bi bi-check-circle me-1"></i> Practical-based learning</li>
                            <li class="mb-1"><i class="bi bi-check-circle me-1"></i> Market-driven courses</li>
                            <li class="mb-1"><i class="bi bi-check-circle me-1"></i> Supportive environment</li>
                            <li class="mb-1"><i class="bi bi-check-circle me-1"></i> Career-focused training</li>
                        </ul>
                        <a class="btn btn-light btn-sm" href="<?= e(base_url('programmes/apply')) ?>">Apply Now</a>
                    </div>

                    <div class="soft-card p-4" style="background:#fff">
                        <h3 class="h6 fw-bold mb-3"><i class="bi bi-telephone me-1" style="color:var(--testimonial-accent)"></i>Get in Touch</h3>
                        <p class="small text-muted mb-2">Have questions? We're here to help.</p>
                        <div class="d-grid gap-2">
                            <a class="btn btn-outline-primary btn-sm" href="<?= e(base_url('contact')) ?>"><i class="bi bi-envelope me-1"></i>Send a Message</a>
                            <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('faqs')) ?>"><i class="bi bi-question-circle me-1"></i>View FAQs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
