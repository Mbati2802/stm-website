<?php
$aboutTitle = $settings['about_title'] ?? 'About St. Mary\'s College of Health Sciences';
$aboutIntro = $settings['about_intro'] ?? ($page['content'] ?? 'St. Mary\'s College of Health Sciences is a forward-thinking institution dedicated to shaping the next generation of healthcare professionals. We are committed to providing practical, career-focused training that equips students with the knowledge, skills, and confidence needed to thrive in today\'s dynamic healthcare environment.' . "\n\n" . 'At St. Mary\'s, we believe education should go beyond the classroom. Our programs are designed to combine strong academic foundations with hands-on experience, ensuring our graduates are job-ready and capable of making a real impact in hospitals, clinics, and communities across Kenya and beyond.');
$aboutMission = $settings['about_mission'] ?? 'To empower students through quality, practical, and accessible healthcare education that transforms lives and communities.';
$aboutVision = $settings['about_vision'] ?? 'To be a leading institution in health sciences training, recognized for excellence, innovation, and the production of highly competent professionals.';
$aboutValues = $settings['about_values'] ?? 'Integrity, Professionalism, Innovation, Compassion, Accountability';
$aboutDifferentiators = $settings['about_differentiators'] ?? 'Practical-Based Learning – We focus on real-world skills, not just theory|Market-Driven Courses – Programs aligned with current healthcare demands|Supportive Learning Environment – We guide every student to succeed|Affordable & Accessible Education – Opportunities for all|Career-Focused Training – We train for employment, not just certificates';
$aboutProgrammes = $settings['about_programmes'] ?? 'Nursing-related support courses|Biomedical Engineering|Perioperative Theatre Technology|Mortuary Science|Orthopedic & Trauma Medicine|Community Health|Counselling Psychology|Caregiving (CNA)|English proficiency programs (IELTS, TOEFL, PTE, OET)';
$aboutCommitment = $settings['about_commitment'] ?? 'At St. Mary\'s College of Health Sciences, we are committed to nurturing not just skilled professionals, but responsible individuals who are ready to serve humanity with compassion, integrity, and excellence.' . "\n\n" . 'We take pride in building a community where students are supported, inspired, and prepared to step confidently into their future careers.';
$aboutShortTagline = $settings['about_short_tagline'] ?? 'At St. Mary\'s College of Health Sciences, we do not just train students - we prepare future healthcare professionals who are ready to serve, save lives, and make a difference.';
$aboutCta1Label = $settings['about_cta_primary_label'] ?? 'View Programmes';
$aboutCta1Link = $settings['about_cta_primary_link'] ?? 'programmes';
$aboutCta2Label = $settings['about_cta_secondary_label'] ?? 'Contact Admissions';
$aboutCta2Link = $settings['about_cta_secondary_link'] ?? 'contact';
$aboutStats = $settings['about_stats'] ?? '10+|Career-focused Programmes,100%|Practical Training Orientation,1|Purpose-driven Health Sciences College';

$valueItems = array_filter(array_map('trim', explode(',', $aboutValues)));
$whyItems = array_filter(array_map('trim', explode('|', $aboutDifferentiators)));
$programmeItems = array_filter(array_map('trim', explode('|', $aboutProgrammes)));
$statsItems = array_filter(array_map('trim', explode(',', $aboutStats)));
$renderRich = function (string $value): string {
    if (str_contains($value, '<')) {
        return $value;
    }
    return nl2br(e($value));
};
?>

<?php
$heroTitlePrimary = 'About Us';
$heroTitleSecondary = "St. Mary's";
$heroTagline = plain_text($aboutShortTagline);
$heroPrimaryLabel = $aboutCta1Label;
$heroPrimaryLink = $aboutCta1Link;
$heroSecondaryLabel = $aboutCta2Label;
$heroSecondaryLink = $aboutCta2Link;
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <h1 class="split-title mb-3">
                    <span class="title-primary">About</span>
                    <span class="title-secondary">St. Mary's</span>
                </h1>
                <h2 class="h4 mb-3"><?= e($aboutTitle) ?></h2>
                <div class="mb-3 text-muted"><?= $renderRich($aboutIntro) ?></div>
                <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-primary" href="<?= e(base_url($aboutCta1Link)) ?>"><?= e($aboutCta1Label) ?></a>
                    <a class="btn btn-outline-primary" href="<?= e(base_url($aboutCta2Link)) ?>"><?= e($aboutCta2Label) ?></a>
                </div>
            </div>
            <div class="col-lg-4">
                <img class="img-fluid w-100 soft-card" src="https://images.unsplash.com/photo-1550831107-1553da8c8464?w=1000" alt="About section image">
            </div>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="soft-card p-4 h-100">
                    <h3 class="h5 split-title mb-3"><span class="title-primary">Our</span> <span class="title-secondary">Mission</span></h3>
                    <div class="mb-0"><?= $renderRich($aboutMission) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="soft-card p-4 h-100">
                    <h3 class="h5 split-title mb-3"><span class="title-primary">Our</span> <span class="title-secondary">Vision</span></h3>
                    <div class="mb-0"><?= $renderRich($aboutVision) ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h3 class="h5 split-title mb-3"><span class="title-primary">Core</span> <span class="title-secondary">Values</span></h3>
        <div class="row g-3">
            <?php foreach ($valueItems as $value): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="soft-card p-3 h-100 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-primary"></i>
                        <span><?= e($value) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h3 class="h5 split-title mb-3"><span class="title-primary">What Makes</span> <span class="title-secondary">Us Different</span></h3>
        <div class="row g-3">
            <?php foreach ($whyItems as $item): ?>
                <div class="col-md-6">
                    <div class="soft-card p-3 h-100 d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-right-circle text-secondary"></i>
                        <span><?= e($item) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h3 class="h5 split-title mb-3"><span class="title-primary">Our</span> <span class="title-secondary">Programmes</span></h3>
        <p class="text-muted">We offer a wide range of diploma, certificate, and short courses in health sciences, including:</p>
        <div class="row g-3">
            <?php foreach ($programmeItems as $programme): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="soft-card p-3 h-100 d-flex align-items-center gap-2">
                        <i class="bi bi-dot text-primary"></i>
                        <span><?= e($programme) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h3 class="h5 split-title mb-3"><span class="title-primary">Institution</span> <span class="title-secondary">Snapshot</span></h3>
        <div class="row g-4">
            <?php foreach ($statsItems as $stat): ?>
                <?php [$num, $label] = array_pad(explode('|', $stat, 2), 2, ''); ?>
                <div class="col-md-4">
                    <div class="soft-card p-4 text-center h-100">
                        <h4 class="display-6 fw-bold mb-2 text-primary"><?= e($num) ?></h4>
                        <p class="mb-0"><?= e($label) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <h3 class="h5 split-title mb-3"><span class="title-primary">Our</span> <span class="title-secondary">Commitment</span></h3>
        <div class="mb-3 text-muted"><?= $renderRich($aboutCommitment) ?></div>
        <div class="soft-card p-4">
            <div class="mb-0 fw-semibold"><?= $renderRich($aboutShortTagline) ?></div>
        </div>
    </div>
</section>
