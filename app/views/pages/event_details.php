<?php
$event = $event ?? [];
$title = (string)($event['title'] ?? 'Event');
$startsAt = (string)($event['starts_at'] ?? '');
$endsAt = (string)($event['ends_at'] ?? '');
$timeLabel = trim((string)($event['time_label'] ?? ''));
$location = trim((string)($event['location'] ?? ''));
$summary = trim((string)($event['summary'] ?? ''));
$body = (string)($event['body'] ?? '');
$image = trim((string)($event['image_path'] ?? ''));
$registrationUrl = trim((string)($event['registration_url'] ?? ''));

$startTs = $startsAt !== '' ? strtotime($startsAt) : null;
$endTs = $endsAt !== '' ? strtotime($endsAt) : null;
$dateLabel = $startTs ? date('l, j F Y', $startTs) : '';
$timeFromDate = $startTs ? date('g:i A', $startTs) : '';
$timeToDate = $endTs ? date('g:i A', $endTs) : '';

$displayTime = $timeLabel !== '' ? $timeLabel : trim($timeFromDate . ($timeToDate !== '' ? ' - ' . $timeToDate : ''));
?>

<section class="section-stack">
  <div class="site-width boxed-section programme-detail-layout" data-aos="fade-up">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <div>
        <h1 class="split-title mb-1"><span class="title-primary">Event</span> | <span class="title-secondary"><?= e($title) ?></span></h1>
        <div class="programme-detail-divider"></div>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-sm btn-primary" href="<?= e($registrationUrl !== '' ? $registrationUrl : base_url('events/' . ($event['slug'] ?? '') . '/register')) ?>">Register</a>
        <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('events')) ?>">All Events</a>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <div class="soft-card bg-white p-3 mb-3">
          <img
            class="img-fluid mb-3"
            src="<?= e($image !== '' ? $image : 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=900') ?>"
            alt="<?= e($title) ?>"
          >
          <div class="small text-muted">
            <?php if ($dateLabel !== ''): ?>
              <div class="mb-1"><strong>Date:</strong> <?= e($dateLabel) ?></div>
            <?php endif; ?>
            <?php if ($displayTime !== ''): ?>
              <div class="mb-1"><strong>Time:</strong> <?= e($displayTime) ?></div>
            <?php endif; ?>
            <?php if ($location !== ''): ?>
              <div class="mb-0"><strong>Location:</strong> <?= e($location) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="soft-card p-4 bg-white">
          <h2 class="h6 text-uppercase text-muted mb-3">Useful Links</h2>
          <ul class="list-unstyled mb-0">
            <li class="mb-2"><a href="<?= e(base_url('programmes')) ?>">Browse Programmes</a></li>
            <li class="mb-2"><a href="<?= e(base_url('programmes/apply')) ?>">Apply for a Course</a></li>
            <li class="mb-2"><a href="<?= e(base_url('library')) ?>">Library</a></li>
            <li><a href="<?= e(base_url('contact')) ?>">Contact Us</a></li>
          </ul>
        </div>
        <div class="soft-card p-4 bg-white mt-3">
          <h2 class="h6 text-uppercase text-muted mb-3">Contacts</h2>
          <p class="mb-1 small text-muted">Phone: <?= e($settings['phone'] ?? '+254 700 000 000') ?></p>
          <p class="mb-0 small text-muted">Email: <?= e($settings['email'] ?? 'admissions@stm.ac.ke') ?></p>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-lg-12">
        <?php if ($summary !== ''): ?>
          <div class="lead mb-3"><?= safe_html($summary) ?></div>
        <?php endif; ?>

        <div class="soft-card bg-white p-4">
          <?php if (trim($body) !== ''): ?>
            <?= safe_html($body) ?>
          <?php else: ?>
            <p class="mb-0 text-muted">Event details will be published soon.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

