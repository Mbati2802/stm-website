<?php
$admissionsEmail = trim((string)($settings['admissions_email'] ?? 'admission@stmarysmchmcollege.ac.ke'));
$admissionsPhone = trim((string)($settings['phone'] ?? '+254 791 309011'));
?>

<section class="section-stack">
  <div class="site-width boxed-section admissions-contact-theme">
    <div class="row g-4">
      <div class="col-lg-5">
        <div class="soft-card p-4 h-100">
          <h1 class="h4 fw-bold mb-3">Contact Admissions</h1>
          <p class="text-muted mb-3">Talk directly to our admissions team for course guidance, entry requirements, and application support.</p>
          <div class="small">
            <p class="mb-2"><strong>Email:</strong> <a href="mailto:<?= e($admissionsEmail) ?>"><?= e($admissionsEmail) ?></a></p>
            <p class="mb-0"><strong>Phone:</strong> <a href="tel:<?= e($admissionsPhone) ?>"><?= e($admissionsPhone) ?></a></p>
          </div>
        </div>
      </div>
      <div class="col-lg-7">
        <form method="POST" action="<?= e(base_url('contact-admissions')) ?>" class="soft-card p-4">
          <?= csrf_field() ?>
          <h2 class="h6 text-uppercase text-muted mb-3">Send Admissions Enquiry</h2>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="phone">
            </div>
            <div class="col-md-6">
              <label class="form-label">Subject</label>
              <input type="text" class="form-control" name="subject" value="Admissions Enquiry" required>
            </div>
            <div class="col-12">
              <label class="form-label">Message</label>
              <textarea class="form-control" rows="6" name="message" required></textarea>
            </div>
            <div class="col-12">
              <button class="btn btn-primary">Send Admissions Message</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
