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
$socialUpdates = $socialUpdates ?? [];
$socialUpdatesTitle = trim((string)($settings['social_updates_title'] ?? '')) !== '' ? (string)$settings['social_updates_title'] : 'Social Updates';
$testimonialTemplate = (string)($settings['testimonial_template'] ?? 'carousel');
$testimonialCardStyle = (string)($settings['testimonial_card_style'] ?? 'centered');
$testimonialAccent = (string)($settings['testimonial_accent_color'] ?? '#5fc7e7');
$testimonialBg = (string)($settings['testimonial_bg_color'] ?? '#f5f7fa');
$testimonialAutoplay = !isset($settings['testimonial_autoplay']) || $settings['testimonial_autoplay'] === '1';
$testimonialSpeed = (int)($settings['testimonial_speed'] ?? 5000);
if ($testimonialSpeed < 2000) { $testimonialSpeed = 5000; }
$validHexAccent = preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $testimonialAccent) ? $testimonialAccent : '#5fc7e7';
$validHexBg = preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $testimonialBg) ? $testimonialBg : '#f5f7fa';
$testimonialGridCount = max(2, min(4, (int)($settings['testimonial_grid_count'] ?? 3)));
$testimonialGridCol = match ($testimonialGridCount) { 2 => 'col-md-6', 4 => 'col-md-6 col-lg-3', default => 'col-md-6 col-lg-4' };
$testimonialSlideEffect = (string)($settings['testimonial_slide_effect'] ?? 'slide');
$testimonialItemsPerSlide = max(1, min(3, (int)($settings['testimonial_items_per_slide'] ?? 1)));
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
                <span class="title-primary">Top Treding</span> |
                <span class="title-secondary">Courses On Offer</span>
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

<?php if ($sv['testimonials'] && $testimonials !== []): ?>
<section class="section-stack testimonials-section testimonials-template-<?= e($testimonialTemplate) ?> testimonials-card-<?= e($testimonialCardStyle) ?>" style="--testimonial-accent: <?= e($validHexAccent) ?>; --testimonial-bg: <?= e($validHexBg) ?>;">
    <div class="site-width boxed-section testimonials-box">
        <h2 class="h3 fw-bold mb-4" data-aos="fade-up">Student Testimonials</h2>

        <?php if ($testimonialTemplate === 'carousel'): ?>
            <?php $carouselChunks = array_chunk($testimonials, $testimonialItemsPerSlide); ?>
            <div id="testimonialCarousel" class="carousel slide<?= $testimonialSlideEffect === 'fade' ? ' carousel-fade' : '' ?>" <?= $testimonialAutoplay ? 'data-bs-ride="carousel"' : '' ?> data-bs-interval="<?= (int)$testimonialSpeed ?>" data-aos="fade-up">
                <div class="carousel-inner">
                    <?php foreach ($carouselChunks as $ci => $chunk): ?>
                        <div class="carousel-item <?= $ci === 0 ? 'active' : '' ?>">
                            <div class="row g-4 justify-content-center">
                                <?php $slideCol = $testimonialItemsPerSlide === 1 ? 'col-md-9 col-lg-7' : ($testimonialItemsPerSlide === 2 ? 'col-md-6' : 'col-md-4'); ?>
                                <?php foreach ($chunk as $testimonial): ?>
                                <div class="<?= $slideCol ?>">
                                    <div class="soft-card p-4 bg-white h-100 testimonial-card">
                                        <?php if (!empty($testimonial['image'])): ?>
                                            <div class="testimonial-avatar-wrap mb-3">
                                                <img src="<?= e($testimonial['image']) ?>" alt="<?= e($testimonial['name']) ?>" class="testimonial-avatar">
                                            </div>
                                        <?php endif; ?>
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
                    <?php endforeach; ?>
                </div>
                <?php if (count($testimonials) > 1): ?>
                <button class="carousel-control-prev testimonial-nav" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon testimonial-nav-icon"></span>
                </button>
                <button class="carousel-control-next testimonial-nav" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon testimonial-nav-icon"></span>
                </button>
                <?php endif; ?>
            </div>

        <?php elseif ($testimonialTemplate === 'cards'): ?>
            <div class="row g-4" data-aos="fade-up">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="<?= e($testimonialGridCol) ?>">
                        <div class="soft-card p-4 bg-white h-100 testimonial-card">
                            <?php if (!empty($testimonial['image'])): ?>
                                <div class="testimonial-avatar-wrap mb-3">
                                    <img src="<?= e($testimonial['image']) ?>" alt="<?= e($testimonial['name']) ?>" class="testimonial-avatar">
                                </div>
                            <?php endif; ?>
                            <p class="mb-3 testimonial-message">"<?= e($testimonial['message']) ?>"</p>
                            <strong class="d-block testimonial-name"><?= e($testimonial['name']) ?></strong>
                            <?php if ($testimonial['course'] !== ''): ?>
                                <p class="mb-0 small text-muted"><?= e($testimonial['course']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: /* minimal */ ?>
            <div class="row g-4 testimonials-minimal" data-aos="fade-up">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="col-md-6">
                        <div class="testimonial-quote-block p-4 h-100">
                            <i class="bi bi-quote testimonial-quote-icon"></i>
                            <p class="testimonial-message mb-3">"<?= e($testimonial['message']) ?>"</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <strong class="testimonial-name"><?= e($testimonial['name']) ?></strong>
                                <?php if ($testimonial['course'] !== ''): ?>
                                    <span class="small text-muted"><?= e($testimonial['course']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a class="btn btn-outline-primary" href="<?= e(base_url('testimonials')) ?>">View All Testimonials <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($sv['events']): ?>
<section class="section-stack">
    <div class="site-width boxed-section events-section" data-aos="fade-up">
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
</section>
<?php endif; ?>

<?php if (!empty($socialUpdates)):
    $suTemplate = (string)($settings['social_updates_template'] ?? 'cards');
    $suCols = (int)($settings['social_updates_cards_per_row'] ?? 3);
    $suShowImages = ($settings['social_updates_show_images'] ?? '1') === '1';
    $suContentLines = (int)($settings['social_updates_content_lines'] ?? 3);
    $suRows = (int)($settings['social_updates_rows'] ?? 2);
    $suBgColor = (string)($settings['social_updates_bg_color'] ?? '#ffffff');
    $suCardBg = (string)($settings['social_updates_card_bg'] ?? '#ffffff');
    $suAccent = (string)($settings['social_updates_accent_color'] ?? '#5fc7e7');
    $colClass = $suCols === 4 ? 'col-md-6 col-lg-3' : ($suCols === 2 ? 'col-md-6 col-lg-6' : 'col-md-6 col-lg-4');
    // Limit items shown based on rows setting
    if ($suRows > 0) {
        $suLimit = $suRows * $suCols;
        $socialUpdates = array_slice($socialUpdates, 0, $suLimit);
    }
    $hasMore = count($socialUpdates) >= ($suRows > 0 ? $suRows * $suCols : 999);
    // DEBUG: Check first item - temporarily enabled for diagnostics
    echo '<div style="background:#fff;padding:10px;margin:10px;border:2px solid red;z-index:9999;position:relative;font-family:monospace">';
    echo '<strong>DEBUG:</strong> Show Images: ' . ($suShowImages ? 'YES' : 'NO') . "<br>";
    if (!empty($socialUpdates)) {
        echo 'First item ID: ' . ($socialUpdates[0]['id'] ?? 'N/A') . "<br>";
        echo 'First item external_id: ' . ($socialUpdates[0]['external_id'] ?? 'N/A') . "<br>";
        echo 'First item image_path: ' . (empty($socialUpdates[0]['image_path']) ? 'EMPTY/NULL' : htmlspecialchars(substr($socialUpdates[0]['image_path'], 0, 100))) . "<br>";
        echo 'First item source: ' . ($socialUpdates[0]['source'] ?? 'N/A') . "<br>";
    }
    echo '</div>';
?>
<section class="section-stack social-updates-section" style="--su-bg:<?= e($suBgColor) ?>;--su-card-bg:<?= e($suCardBg) ?>;--su-accent:<?= e($suAccent) ?>">
    <div class="site-width boxed-section" data-aos="fade-up" style="background:var(--su-bg)">
        <h2 class="h4 fw-bold mb-4 split-title"><span class="title-primary"><?= e($socialUpdatesTitle) ?></span></h2>
        <?php if ($suTemplate === 'minimal'): ?>
        <div class="list-group">
            <?php foreach ($socialUpdates as $update): ?>
            <div class="list-group-item list-group-item-action" style="background:var(--su-card-bg)">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="social-feed-content<?= $suContentLines > 0 ? '' : ' social-feed-content-expanded' ?>" style="<?= $suContentLines > 0 ? '-webkit-line-clamp:' . $suContentLines . ';line-clamp:' . $suContentLines : '' ?>"><?= nl2br(e((string)($update['content'] ?? ''))) ?></div>
                    <small class="text-muted ms-2 flex-shrink-0"><?= e(date('M j', strtotime((string)($update['posted_at'] ?? $update['created_at'] ?? 'now')))) ?></small>
                </div>
                <?php if (!empty($update['link_url'])): ?>
                    <a href="<?= e((string)$update['link_url']) ?>" target="_blank" rel="noopener" class="small">View post <i class="bi bi-box-arrow-up-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif ($suTemplate === 'compact'): ?>
        <div class="row g-2">
            <?php foreach ($socialUpdates as $update): ?>
            <div class="col-12">
                <div class="d-flex align-items-center p-2 border rounded mb-1" style="background:var(--su-card-bg)">
                    <?php if ($suShowImages && !empty($update['image_path'])): ?>
                    <img src="<?= e((string)$update['image_path']) ?>" alt="" class="rounded me-2" style="width:60px;height:60px;object-fit:cover;flex-shrink:0" loading="lazy">
                    <?php endif; ?>
                    <div class="flex-grow-1 min-w-0">
                        <div class="social-feed-content<?= $suContentLines > 0 ? '' : ' social-feed-content-expanded' ?>" style="<?= $suContentLines > 0 ? '-webkit-line-clamp:' . $suContentLines . ';line-clamp:' . $suContentLines : '' ?>"><?= nl2br(e((string)($update['content'] ?? ''))) ?></div>
                        <small class="text-muted"><?= e(date('M j, Y', strtotime((string)($update['posted_at'] ?? $update['created_at'] ?? 'now')))) ?></small>
                    </div>
                    <?php if (!empty($update['link_url'])): ?>
                    <a href="<?= e((string)$update['link_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary ms-2 flex-shrink-0"><i class="bi bi-box-arrow-up-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="row g-3">
            <?php foreach ($socialUpdates as $update): ?>
                <div class="<?= $colClass ?>">
                    <article class="social-feed-item h-100 <?= !empty($update['is_pinned']) ? 'social-feed-pinned' : '' ?>" style="background:var(--su-card-bg);<?= !empty($update['is_pinned']) ? 'border-left-color:var(--su-accent)' : '' ?>">
                        <?php if (!empty($update['is_pinned'])): ?>
                            <span class="badge bg-warning text-dark social-feed-pin"><i class="bi bi-pin-angle-fill me-1"></i>Pinned</span>
                        <?php endif; ?>
                        <?php if (!empty($update['source'])): ?>
                            <span class="social-feed-source" style="color:var(--su-accent)"><i class="bi bi-<?= $update['source'] === 'instagram' ? 'instagram' : ($update['source'] === 'facebook' ? 'facebook' : 'tag') ?> me-1"></i><?= e(ucfirst((string)$update['source'])) ?></span>
                        <?php endif; ?>
                        <?php if ($suShowImages && !empty($update['image_path'])): ?>
                            <img src="<?= e((string)$update['image_path']) ?>" alt="" class="social-feed-image" loading="lazy">
                        <?php endif; ?>
                        <div class="social-feed-content<?= $suContentLines > 0 ? '' : ' social-feed-content-expanded' ?>" style="<?= $suContentLines > 0 ? '-webkit-line-clamp:' . $suContentLines . ';line-clamp:' . $suContentLines : '' ?>"><?= nl2br(e((string)($update['content'] ?? ''))) ?></div>
                        <div class="social-feed-meta">
                            <span class="text-muted small"><i class="bi bi-clock me-1"></i><?= e(date('M j, Y', strtotime((string)($update['posted_at'] ?? $update['created_at'] ?? 'now')))) ?></span>
                            <?php if (!empty($update['link_url'])): ?>
                                <a href="<?= e((string)$update['link_url']) ?>" target="_blank" rel="noopener" class="small ms-2">View post <i class="bi bi-box-arrow-up-right"></i></a>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if ($hasMore): ?>
        <div class="mt-3 text-center">
            <a href="<?= e(base_url('events')) ?>#social-updates" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-right me-1"></i>View All Updates</a>
        </div>
        <?php endif; ?>
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
                            <p class="small text-muted mb-3 line-clamp-3"><?= e(plain_text($item['summary'])) ?></p>
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
