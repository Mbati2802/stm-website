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
$overviewDisplay = $programmeDescription !== ''
    ? $programmeDescription
    : ($overviewText !== ''
        ? $overviewText
        : ($programmeName . ' is a people-centered course designed to equip students with the skills and knowledge needed to support individuals facing emotional, psychological, and social challenges. This programme focuses on understanding human behavior, effective communication, and practical counselling techniques that can be applied in real-life situations.'));
$overviewNormalized = plain_text_multiline($overviewDisplay);
$overviewParagraphs = array_values(array_filter(array_map('trim', preg_split('/\n+/', $overviewNormalized) ?: [])));
if ($overviewParagraphs === []) {
    $overviewParagraphs = [$overviewNormalized];
}
$programmeMainImage = trim((string)($settings['programme_detail_image'] ?? 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=900'));
?>

<section class="section-stack">
  <div class="site-width boxed-section programme-detail-layout">
    <aside class="programme-floating-sidebar">
      <div class="soft-card p-3 mb-3">
        <img class="img-fluid mb-3" src="<?= e($programmeMainImage) ?>" alt="<?= e($programmeName) ?>">
        <h3 class="h4 fw-bold text-primary mb-2">Need Guidance?</h3>
        <p class="small text-muted">Kindly ask for a return call from our proficient consultants to have your inquiries addressed.</p>
        <a class="btn btn-sm btn-primary" href="<?= e(base_url('programmes/apply?course=' . urlencode($programmeName) . '&level=' . urlencode($programmeCategory))) ?>">Apply Now</a>
      </div>

      <div class="soft-card p-3">
        <h3 class="h5 fw-bold mb-3">Other Programmes Offered</h3>
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
    <div class="mb-3">
      <?php foreach ($overviewParagraphs as $paragraph): ?>
        <p class="mb-3"><?= e($paragraph) ?></p>
      <?php endforeach; ?>
    </div>

    <h2 class="h4 fw-bold mt-4">Course Objectives</h2>
    <p>By the end of this course, students will be able to:</p>
    <ul>
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

    <h2 class="h4 fw-bold mt-4">Course Content</h2>
    <p>Key areas of study include:</p>
    <ul>
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

    <div class="row g-3 mt-2 programme-detail-cards">
      <div class="col-md-6 col-lg-3">
        <div class="soft-card p-3 h-100">
          <h2 class="h5 fw-bold mb-3">Career Opportunities</h2>
          <p class="mb-2">Graduates can work in:</p>
          <ul class="mb-0">
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
          <?php foreach ($whyList as $index => $item): ?>
            <p class="<?= $index === count($whyList) - 1 ? 'mb-0' : 'mb-1' ?>">✔ <?= e(plain_text($item)) ?></p>
          <?php endforeach; ?>
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
    <div class="clearfix"></div>
  </div>
</section>
