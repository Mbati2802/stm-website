<?php
$registrarEmail = trim((string)($settings['registrar_email'] ?? 'registrar@stmarysmchmcollege.ac.ke'));
$phone = trim((string)($settings['phone'] ?? '+254 791 309011'));
?>
<?php
$heroTitlePrimary = 'Contact';
$heroTitleSecondary = 'Registrar';
$heroTagline = 'Send your records and academic administration enquiries directly to the registrar office.';
$heroPrimaryLabel = 'Back to Registrar Page';
$heroPrimaryLink = 'registrar';
$heroSecondaryLabel = '';
$heroSecondaryLink = '';
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
  <div class="site-width boxed-section">
    <h1 class="split-title mb-4"><span class="title-primary">Registrar</span> | <span class="title-secondary">Contact Form</span></h1>
    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
    <div class="row g-4">
      <div class="col-md-6">
        <form method="POST" action="<?= e(base_url('contact-registrar')) ?>" class="soft-card p-4">
          <?= csrf_field() ?>
          <div class="hp-field" aria-hidden="true" style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden">
            <label for="website_url">Website</label>
            <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
          </div>
          <div class="mb-3"><label class="form-label">Name</label><input required name="name" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Email</label><input required type="email" name="email" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Subject</label><input name="subject" class="form-control" placeholder="Registrar Enquiry"></div>
          <div class="mb-3"><label class="form-label">Message</label><textarea required name="message" rows="5" class="form-control"></textarea></div>
          <button class="btn btn-primary">Send to Registrar</button>
        </form>
      </div>
      <div class="col-md-6 d-grid gap-3">
        <div class="soft-card p-4">
          <h2 class="h6 text-uppercase text-muted mb-3">Registrar Office Contacts</h2>
          <p class="mb-1"><strong>Email:</strong> <?= e($registrarEmail) ?></p>
          <p class="mb-1"><strong>Phone:</strong> <?= e($phone) ?></p>
          <p class="mb-0"><strong>Office:</strong> Main Campus Registrar Office</p>
        </div>
      </div>
    </div>
  </div>
</section>
