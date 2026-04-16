<?php
$event = $event ?? [];
$title = (string)($event['title'] ?? 'Event');
$slug = (string)($event['slug'] ?? '');
?>

<section class="section-stack">
  <div class="site-width boxed-section" data-aos="fade-up">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <div>
        <h1 class="split-title mb-1"><span class="title-primary">Event Registration</span> | <span class="title-secondary"><?= e($title) ?></span></h1>
        <p class="section-subtitle-standard">Fill in your details and we’ll confirm your registration.</p>
      </div>
      <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('events/' . $slug)) ?>">Back to Event</a>
    </div>

    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

    <div class="row g-4">
      <div class="col-lg-8">
        <form method="POST" action="<?= e(base_url('events/' . $slug . '/register')) ?>" class="soft-card p-4 bg-white">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input required name="name" class="form-control" value="<?= e(old('name')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input required name="phone" class="form-control" value="<?= e(old('phone')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input required type="email" name="email" class="form-control" value="<?= e(old('email')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Are you a student?</label>
              <select name="is_student" class="form-select">
                <option value="1">Yes</option>
                <option value="0">No</option>
              </select>
            </div>
            <div class="col-12"><label class="form-label">Notes (optional)</label><textarea name="notes" rows="4" class="form-control"><?= e(old('notes')) ?></textarea></div>
          </div>
          <div class="mt-4 d-flex gap-2 flex-wrap">
            <button class="btn btn-primary">Submit Registration</button>
            <a class="btn btn-outline-primary" href="<?= e(base_url('events')) ?>">All Events</a>
          </div>
        </form>
      </div>

      <div class="col-lg-4">
        <div class="soft-card p-4 bg-white">
          <h2 class="h6 text-uppercase text-muted mb-3">Helpful Links</h2>
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
  </div>
</section>

