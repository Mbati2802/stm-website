<?php
$heroTitlePrimary = 'Contact';
$heroTitleSecondary = 'Admissions';
$heroTagline = 'Talk to our team for applications, event registration, and programme guidance.';
$heroPrimaryLabel = 'Apply Now';
$heroPrimaryLink = 'programmes/apply';
$heroSecondaryLabel = '';
$heroSecondaryLink = '';
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
  <div class="site-width boxed-section">
    <h1 class="split-title mb-4"><span class="title-primary">Contact</span> | <span class="title-secondary">Us</span></h1>
    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
    <div class="row g-4">
      <div class="col-md-6">
        <form method="POST" class="soft-card p-4">
          <div class="mb-3"><label class="form-label">Name</label><input required name="name" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Email</label><input required type="email" name="email" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Subject</label><input name="subject" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Message</label><textarea required name="message" rows="4" class="form-control"></textarea></div>
          <button class="btn btn-primary">Send Message</button>
        </form>
      </div>
      <div class="col-md-6"><iframe class="w-100 rounded-4" height="450" src="https://maps.google.com/maps?q=Nairobi%20Kenya&t=&z=12&ie=UTF8&iwloc=&output=embed"></iframe></div>
    </div>
  </div>
</section>
