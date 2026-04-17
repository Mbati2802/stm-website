<?php
$heroTitle = $settings['home_hero_title'] ?? 'Empowering individuals to acquire new medical knowledge, skills, and expertise.';
$heroDescription = $settings['home_hero_description'] ?? 'Discover endless possibilities for personal growth and healthcare impact through our practical and accredited programmes.';
$primaryCtaLabel = $settings['home_hero_primary_cta_label'] ?? 'How to Apply';
$primaryCtaLink = $settings['home_hero_primary_cta_link'] ?? 'programmes';
$secondaryCtaLabel = $settings['home_hero_secondary_cta_label'] ?? 'Downloads';
$secondaryCtaLink = $settings['home_hero_secondary_cta_link'] ?? 'about';
$programmeImageSettings = json_decode((string)($settings['home_programme_images_json'] ?? ''), true);
if (!is_array($programmeImageSettings)) {
    $programmeImageSettings = [];
}
$homeExtraSections = json_decode((string)($settings['home_extra_sections_json'] ?? ''), true);
if (!is_array($homeExtraSections)) {
    $homeExtraSections = [];
}
?>
<?php $sv = $sectionVisibility ?? ['hero'=>true,'cards'=>true,'banner'=>true,'why'=>true,'courses'=>true,'testimonials'=>true,'events'=>true,'news'=>true,'cta'=>true]; ?>
<?php if ($sv['hero']): ?>
<section class="hero-ou-wrap">
    <div class="site-width hero-boxed">
        <div class="hero-grid">
            <div class="hero-left" data-aos="fade-right">
                <div class="hero-content">
                <p class="text-uppercase fw-semibold small text-primary mb-2">The College For Inclusive Prosperity</p>
                <h1 class="display-5 fw-bold mb-3"><?= e($heroTitle) ?></h1>
                <p class="lead text-secondary-emphasis mb-4"><?= e($heroDescription) ?></p>
                <div class="d-flex gap-3 flex-wrap">
                    <a class="btn btn-primary btn-lg hero-apply-btn" href="<?= e(base_url($primaryCtaLink)) ?>"><?= e($primaryCtaLabel) ?></a>
                    <a class="btn btn-outline-primary btn-lg" href="<?= e(base_url($secondaryCtaLink)) ?>"><?= e($secondaryCtaLabel) ?></a>
                </div>
                </div>
            </div>
            <div class="hero-right" data-aos="fade-left">
                <div class="hero-image-shell">
                    <img src="<?= e($heroImages[0] ?? 'https://images.unsplash.com/photo-1516549655169-df83a0774514?w=1400') ?>" alt="Institutional campus">
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($sv['cards']): ?>
<section class="section-stack hero-cards-section">
    <div class="site-width boxed-section">
        <div class="row g-4">
            <?php foreach ($heroCards as $card): ?>
                <div class="col-md-6 col-lg-3" data-aos="fade-up">
                    <div class="soft-card p-3 h-100 bg-white value-card-vertical home-value-card">
                        <div class="icon-circle mb-3">
                            <i class="bi <?= e($card['icon'] ?? 'bi-stars') ?>"></i>
                        </div>
                        <h3 class="h5 split-title mb-3">
                            <span class="title-primary"><?= e($card['title_primary'] ?? 'Title') ?></span>
                            <span class="title-secondary"><?= e($card['title_secondary'] ?? '') ?></span>
                        </h3>
                        <p class="mb-3 small text-muted line-clamp-3"><?= e($card['text'] ?? '') ?></p>
                        <div class="mt-auto d-flex gap-2 flex-wrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url($card['cta_link'] ?? 'programmes')) ?>">Read More</a>
                            <a class="btn btn-sm btn-primary" href="<?= e(base_url($card['cta_link'] ?? 'programmes')) ?>">
                                <?= e($card['cta_label'] ?? 'Apply Now') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$bannerPublic = 'assets/images/banner.png';
$bannerCandidates = [
    __DIR__ . '/../../../public/' . $bannerPublic,
    __DIR__ . '/../../../' . $bannerPublic,
];
$bannerFile = '';
foreach ($bannerCandidates as $candidate) {
    if (file_exists($candidate)) {
        $bannerFile = $candidate;
        break;
    }
}
?>
<?php if ($sv['banner'] && $bannerFile !== ''): ?>
<section class="section-stack">
    <div class="site-width boxed-section banner-section">
        <a href="<?= e(base_url('programmes/apply')) ?>" aria-label="Go to programme application page">
            <img src="<?= e(base_url($bannerPublic)) ?>" alt="Courses banner" class="img-fluid w-100">
        </a>
    </div>
</section>
<?php endif; ?>

<?php if ($sv['why']): ?>
<section class="section-stack">
    <div class="site-width boxed-section">
        <h2 class="h3 fw-bold mb-4 text-center" data-aos="fade-up">Why Choose Us</h2>
        <div class="row g-4 text-center">
            <div class="col-md-4" data-aos="zoom-in"><div class="p-4 soft-card bg-white h-100"><i class="bi bi-briefcase-fill fs-2 text-primary"></i><h3 class="h5 mt-3">Job Ready Skills</h3><p class="mb-0">Market-aligned programs designed with employers.</p></div></div>
            <div class="col-md-4" data-aos="zoom-in"><div class="p-4 soft-card bg-white h-100"><i class="bi bi-patch-check-fill fs-2 text-primary"></i><h3 class="h5 mt-3">Accredited Programmes</h3><p class="mb-0">Recognized qualifications that open doors across Kenya.</p></div></div>
            <div class="col-md-4" data-aos="zoom-in"><div class="p-4 soft-card bg-white h-100"><i class="bi bi-tools fs-2 text-primary"></i><h3 class="h5 mt-3">Practical Training</h3><p class="mb-0">Hands-on labs, supervised attachments, and real projects.</p></div></div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($sv['courses']): ?>
<section class="section-stack teaching-strip-section">
    <div class="site-width boxed-section">
        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
            <h2 class="h3 fw-bold mb-0 split-title">
                <span class="title-primary">Courses</span> |
                <span class="title-secondary">Programmes On Offer</span>
            </h2>
            <a href="<?= e(base_url('programmes')) ?>" class="btn btn-sm btn-outline-primary">Browse all</a>
        </div>
        <div class="row g-4">
            <?php
            $courseCards = array_slice($featuredProgrammes, 0, 6);
            $courseImageByCategory = [
                'Diploma' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=900',
                'Certificate' => 'https://images.unsplash.com/photo-1584982751601-97dcc096659c?w=900',
                'Short Course' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=900',
                'Artisan' => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=900',
            ];
            ?>
            <?php foreach ($courseCards as $programme): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <article class="card h-100 border-0 soft-card course-card">
                        <img
                            src="<?= e($programmeImageSettings[$programme['name']] ?? $programmeImageSettings[$programme['category']] ?? $courseImageByCategory[$programme['category']] ?? 'https://images.unsplash.com/photo-1584433144859-1fc3ab64a957?w=900') ?>"
                            class="card-img-top course-card-image"
                            alt="<?= e($programme['name']) ?>"
                        >
                        <div class="card-body">
                            <h3 class="h6"><?= e($programme['name']) ?></h3>
                            <p class="small text-secondary mb-1"><?= e($programme['category']) ?> • <?= (int)$programme['terms'] ?> Terms</p>
                            <p class="small text-muted mb-2 line-clamp-3"><?= e(plain_text($programme['description'] ?? '')) ?></p>
                            <a class="link-arrow mt-2" href="<?= e(base_url('programmes/' . $programme['slug'])) ?>">Read more <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($homeExtraSections !== []): ?>
<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="row g-4">
            <?php foreach ($homeExtraSections as $section): ?>
                <div class="col-lg-4">
                    <div class="soft-card p-3 h-100">
                        <?php if (!empty($section['image'])): ?><img src="<?= e((string)$section['image']) ?>" alt="" class="img-fluid mb-3"><?php endif; ?>
                        <h3 class="h5 mb-2"><?= e((string)($section['title'] ?? 'Section')) ?></h3>
                        <p class="small text-muted mb-3"><?= e((string)($section['text'] ?? '')) ?></p>
                        <?php if (!empty($section['button_link'])): ?>
                            <a class="btn btn-sm btn-primary" href="<?= e(base_url((string)$section['button_link'])) ?>"><?= e((string)($section['button_label'] ?? 'Learn More')) ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($sv['testimonials']): ?>
<section class="section-stack testimonials-section">
    <div class="site-width boxed-section testimonials-box">
        <h2 class="h3 fw-bold mb-4" data-aos="fade-up">Student Testimonials</h2>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up">
            <div class="carousel-inner">
                <?php $testimonialSlides = array_chunk($testimonials, 3); ?>
                <?php foreach ($testimonialSlides as $index => $slide): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="row g-4">
                            <?php foreach ($slide as $testimonial): ?>
                                <div class="col-md-4">
                                    <div class="soft-card p-4 bg-white h-100 testimonial-card">
                                        <p class="mb-2">"<?= e($testimonial['message']) ?>"</p>
                                        <strong><?= e($testimonial['name']) ?></strong>
                                        <p class="mb-0 small text-muted"><?= e($testimonial['course']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($sv['events']): ?>
<section class="section-stack">
    <div class="site-width boxed-section events-section" data-aos="fade-up">
        <div class="row g-4">
            <div class="col-lg-7">
                <h2 class="h4 fw-bold mb-4 split-title"><span class="title-primary">Upcoming</span> <span class="title-secondary">Events</span></h2>
                <?php foreach ($events as $event): ?>
                    <?php
                    $startsAt = (string)($event['starts_at'] ?? '');
                    $startTs = $startsAt !== '' ? strtotime($startsAt) : null;
                    $day = $startTs ? date('d', $startTs) : '';
                    $month = $startTs ? strtoupper(date('M', $startTs)) : '';
                    $timeLabel = trim((string)($event['time_label'] ?? ''));
                    $location = trim((string)($event['location'] ?? ''));
                    ?>
                    <a class="event-row d-flex align-items-center gap-3 text-decoration-none" href="<?= e(base_url('events/' . ($event['slug'] ?? ''))) ?>">
                        <div class="event-date text-center">
                            <span class="d-block event-day"><?= e($day) ?></span>
                            <span class="d-block event-month"><?= e($month) ?></span>
                        </div>
                        <div class="event-info">
                            <h3 class="h5 mb-1"><?= e($event['title'] ?? '') ?></h3>
                            <p class="mb-0 text-muted"><?= e($timeLabel) ?><?= $timeLabel !== '' && $location !== '' ? ' • ' : '' ?><?= e($location) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
                <div class="mt-3">
                    <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('events')) ?>">View all events</a>
                </div>
            </div>
            <div class="col-lg-5">
                <h3 class="h6 text-muted mb-3">Social Updates</h3>
                <div class="event-social-box">
                    <p class="mb-2 fw-semibold">Tweets / Social Feed</p>
                    <p class="small text-muted mb-0">Embed your latest updates here from official social channels.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($sv['news']): ?>
<section class="section-stack">
    <div class="site-width boxed-section">
        <h2 class="h3 fw-bold mb-4" data-aos="fade-up">Latest News</h2>
        <div class="row g-4">
            <?php foreach ($news as $item): ?>
                <div class="col-md-4" data-aos="fade-up">
                    <article class="card border-0 soft-card h-100">
                        <img src="<?= e($item['image_path'] ?: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800') ?>" class="card-img-top" alt="News">
                        <div class="card-body">
                            <h3 class="h6"><?= e($item['title']) ?></h3>
                            <p class="small text-muted mb-3 line-clamp-3"><?= e($item['summary']) ?></p>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('media?type=news')) ?>">Read More</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($sv['cta']): ?>
<section class="section-stack">
    <div class="site-width boxed-section" data-aos="fade-up">
        <div class="cta-banner p-5 text-center text-white">
            <h2 class="fw-bold">Don't wait for opportunity. Create it. Join us today.</h2>
            <a class="btn btn-light mt-3" href="<?= e(base_url('programmes')) ?>">Start Your Application</a>
        </div>
    </div>
</section>
<?php endif; ?>
