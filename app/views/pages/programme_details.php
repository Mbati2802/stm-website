<?php
$programmeName = (string)($programme['name'] ?? 'Programme');
$programmeCategory = (string)($programme['category'] ?? 'Diploma');
$programmeTerms = (int)($programme['terms'] ?? 1);
$departmentName = (string)($programme['department_name'] ?? 'General');
$programmeDescription = trim((string)($programme['description'] ?? ''));
$currentIntake = (string)($currentIntake ?? ($settings['current_intake'] ?? 'January'));
$programmeContent = $programmeContent ?? [];

$durationText = match (strtolower($programmeCategory)) {
    'certificate' => 'Certificate: 6 months - 1 year',
    'artisan' => 'Artisan: 6 months - 1 year',
    'short course' => 'Short Course: 3 - 6 months',
    default => 'Diploma: 1 - 2 years',
};

$entryRequirement = match (strtolower($programmeCategory)) {
    'certificate' => 'Certificate: KCSE mean grade D (plain) and above',
    'artisan' => 'Artisan: KCSE mean grade D- (minus) and above',
    'short course' => 'Short Course: Open entry depending on course area',
    default => 'Diploma: KCSE mean grade C- (minus) and above',
};
$overviewText = trim((string)($programmeContent['overview'] ?? ''));
$objectives = is_array($programmeContent['objectives'] ?? null) ? $programmeContent['objectives'] : [];
$contentAreas = is_array($programmeContent['content_areas'] ?? null) ? $programmeContent['content_areas'] : [];
$careerOpportunities = is_array($programmeContent['career_opportunities'] ?? null) ? $programmeContent['career_opportunities'] : [];
$whyStudyItems = is_array($programmeContent['why_study'] ?? null) ? $programmeContent['why_study'] : [];
$defaultWhyStudyItems = [
    'High demand for mental health support',
    'Opportunity to make a real difference in people\'s lives',
    'Flexible career paths across multiple sectors',
    'Strong foundation for further studies in psychology',
];
$whyList = $whyStudyItems !== [] ? $whyStudyItems : $defaultWhyStudyItems;
if (($programmeContent['duration_override'] ?? '') !== '') {
    $durationText = trim((string)$programmeContent['duration_override']);
}
if (($programmeContent['entry_requirement_override'] ?? '') !== '') {
    $entryRequirement = trim((string)$programmeContent['entry_requirement_override']);
}
$overviewDisplay = $overviewText !== ''
    ? $overviewText
    : ($programmeDescription !== ''
        ? $programmeDescription
        : ($programmeName . ' is a people-centered course designed to equip students with the skills and knowledge needed to support individuals facing emotional, psychological, and social challenges. This programme focuses on understanding human behavior, effective communication, and practical counselling techniques that can be applied in real-life situations.'));
$overviewHtml = safe_html($overviewDisplay);
$programmeMainImage = trim((string)($settings['programme_detail_image'] ?? 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=900'));
$programmeSidebarTitle = trim((string)($settings['programme_sidebar_title'] ?? 'Need Guidance?'));
$programmeSidebarText = trim((string)($settings['programme_sidebar_text'] ?? 'Kindly ask for a return call from our proficient consultants to have your inquiries addressed.'));
$programmeSidebarPrimaryLabel = trim((string)($settings['programme_sidebar_primary_label'] ?? 'Apply Now'));
$programmeSidebarPrimaryLink = trim((string)($settings['programme_sidebar_primary_link'] ?? 'programmes/apply'));
$programmeSidebarSecondaryLabel = trim((string)($settings['programme_sidebar_secondary_label'] ?? 'Contact Admissions'));
$programmeSidebarSecondaryLink = trim((string)($settings['programme_sidebar_secondary_link'] ?? 'contact-admissions'));
if ($programmeSidebarSecondaryLabel === '' || strtolower($programmeSidebarSecondaryLabel) === 'contact registrar') {
  $programmeSidebarSecondaryLabel = 'Contact Admissions';
}
if ($programmeSidebarSecondaryLink === '' || $programmeSidebarSecondaryLink === 'contact-registrar') {
  $programmeSidebarSecondaryLink = 'contact-admissions';
}
$programmeSidebarOtherTitle = trim((string)($settings['programme_sidebar_other_title'] ?? 'Other Programmes Offered'));
$mosaicImages = json_decode((string)($settings['programme_mosaic_images_json'] ?? '[]'), true);
if (!is_array($mosaicImages)) {
    $mosaicImages = [];
}
?>

<section class="section-stack">
  <div class="site-width boxed-section programme-detail-layout">
    <div class="programme-detail-wrapper">
      <aside class="programme-floating-sidebar">
        <div class="soft-card p-3 mb-3">
          <img class="img-fluid mb-3" src="<?= e($programmeMainImage) ?>" alt="<?= e($programmeName) ?>">
          <h3 class="h4 fw-bold text-primary mb-2"><?= e($programmeSidebarTitle) ?></h3>
          <p class="small text-muted"><?= e($programmeSidebarText) ?></p>
          <div class="d-grid gap-2">
            <a class="btn btn-sm btn-primary" href="<?= e(base_url($programmeSidebarPrimaryLink . '?course=' . urlencode($programmeName) . '&level=' . urlencode($programmeCategory))) ?>"><?= e($programmeSidebarPrimaryLabel) ?></a>
            <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url($programmeSidebarSecondaryLink)) ?>"><?= e($programmeSidebarSecondaryLabel) ?></a>
          </div>
        </div>

        <div class="soft-card p-3">
          <h3 class="h5 fw-bold mb-3"><?= e($programmeSidebarOtherTitle) ?></h3>
          <ul class="small mb-0 ps-3">
            <?php foreach ($otherProgrammes as $item): ?>
              <li class="mb-2">
                <a href="<?= e(base_url('programmes/' . $item['slug'])) ?>"><?= e($item['name']) ?></a>
              </li>
            <?php endforeach; ?>
          </ul>
          <a class="btn btn-link p-0 mt-2" href="<?= e(base_url('programmes')) ?>">View More</a>
        </div>
      </aside>

      <div class="programme-main-content">
        <h1 class="h3 fw-bold mb-2"><?= e($programmeName) ?></h1>
        <div class="programme-detail-divider mb-3"></div>

        <div class="programme-meta-card mb-3">
          <div><strong>Department:</strong> <?= e($departmentName) ?></div>
          <div><strong>Level:</strong> <?= e($programmeCategory) ?></div>
          <div><strong>Duration:</strong> <?= e($programmeTerms) ?> terms</div>
          <div>
            <a class="btn btn-sm btn-primary" href="<?= e(base_url('programmes/apply?course=' . urlencode($programmeName) . '&level=' . urlencode($programmeCategory))) ?>">Apply Now</a>
          </div>
        </div>

        <h2 class="h4 fw-bold mt-4">Course Overview</h2>
        <div class="mb-3 course-overview-content"><?= $overviewHtml ?></div>

        <div class="row g-3 mt-4 mb-4">
          <div class="col-lg-6">
            <h2 class="h4 fw-bold mb-3">Course Objectives</h2>
            <p>By the end of this course, students will be able to:</p>
            <ul class="programme-detail-bullets">
              <?php foreach (($objectives !== [] ? $objectives : [
                'Understand human growth, behavior, and mental processes',
                'Apply basic counselling skills in different settings',
                'Provide emotional and psychological support to individuals',
                'Handle ethical issues in counselling practice',
                'Communicate effectively with clients from diverse backgrounds',
              ]) as $item): ?>
                <li><?= e(plain_text($item)) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>

          <div class="col-lg-6">
            <h2 class="h4 fw-bold mb-3">Course Content</h2>
            <p>Key areas of study include:</p>
            <ul class="programme-detail-bullets">
              <?php foreach (($contentAreas !== [] ? $contentAreas : [
                'Introduction to Counselling Psychology',
                'Human Development and Behavior',
                'Communication and Interpersonal Skills',
                'Counselling Theories and Approaches',
                'Mental Health Awareness',
                'Ethics and Professional Practice',
                'Crisis Intervention and Support',
              ]) as $item): ?>
                <li><?= e(plain_text($item)) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

    <div class="row g-3 mt-2 programme-detail-cards">
      <div class="col-md-6 col-lg-3">
        <div class="soft-card p-3 h-100">
          <h2 class="h5 fw-bold mb-3">Career Opportunities</h2>
          <p class="mb-2">Graduates can work in:</p>
          <ul class="programme-detail-bullets mb-0">
            <?php foreach (($careerOpportunities !== [] ? $careerOpportunities : [
              'Hospitals and health centers',
              'Schools and educational institutions',
              'NGOs and community organizations',
              'Rehabilitation centers',
              'Private counselling practice (with experience)',
            ]) as $item): ?>
              <li><?= e(plain_text($item)) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="soft-card p-3 h-100 entry-requirements-card">
          <h2 class="h5 fw-bold mb-3">Entry Requirements</h2>
          <p class="mb-3"><?= e($entryRequirement) ?></p>
          <div class="entry-card-divider"></div>
          <h3 class="h6 fw-bold mt-3 mb-2">Duration of the Course</h3>
          <p class="mb-0"><?= e($durationText) ?></p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="soft-card p-3 h-100">
          <h2 class="h5 fw-bold mb-3">Why Study This Course?</h2>
          <ul class="programme-detail-bullets mb-0">
            <?php foreach ($whyList as $item): ?>
              <li><?= e(plain_text($item)) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="soft-card p-3 h-100 d-flex flex-column">
          <h2 class="h5 fw-bold mb-3">Ready to Join?</h2>
          <p class="mb-2"><strong>Current Intake:</strong> <?= e($currentIntake) ?></p>
          <p class="small text-muted mb-3">Applications for <?= e($currentIntake) ?> intake are ongoing.</p>
          <a class="btn btn-primary mt-auto" href="<?= e(base_url('programmes/apply?course=' . urlencode($programmeName) . '&level=' . urlencode($programmeCategory))) ?>">Apply Now</a>
        </div>
      </div>
    </div>
    <?php if ($mosaicImages !== []): ?>
      <div class="programme-mosaic-grid mt-4">
        <?php foreach ($mosaicImages as $mosaicImg): ?>
          <figure class="programme-mosaic-item mb-0">
            <img src="<?= e((string)$mosaicImg) ?>" alt="Programme gallery image">
          </figure>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
      </div>
    </div>
  </div>
</section>
