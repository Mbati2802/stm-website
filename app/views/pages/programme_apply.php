<?php
$kenyanCounties = [
    'Baringo','Bomet','Bungoma','Busia','Elgeyo-Marakwet','Embu','Garissa','Homa Bay','Isiolo','Kajiado','Kakamega',
    'Kericho','Kiambu','Kilifi','Kirinyaga','Kisii','Kisumu','Kitui','Kwale','Laikipia','Lamu','Machakos','Makueni',
    'Mandera','Marsabit','Meru','Migori','Mombasa','Murang\'a','Nairobi','Nakuru','Nandi','Narok','Nyamira','Nyandarua',
    'Nyeri','Samburu','Siaya','Taita-Taveta','Tana River','Tharaka-Nithi','Trans Nzoia','Turkana','Uasin Gishu',
    'Vihiga','Wajir','West Pokot'
];
$currentIntake = (string)($currentIntake ?? 'January');
?>

<section class="section-stack">
  <div class="site-width boxed-section">
    <h1 class="split-title mb-3"><span class="title-primary">Programme</span> | <span class="title-secondary">Application Form</span></h1>
    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= nl2br(e($msg)) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= nl2br(e($msg)) ?></div><?php endif; ?>

    <div id="applySendingOverlay" style="display:none; position:fixed; inset:0; background:rgba(255,255,255,.85); z-index:9999;">
      <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div class="text-center">
          <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
          <div class="mt-3 fw-semibold">Sending, Please Wait...</div>
          <div class="small text-muted mt-1">Submitting your application</div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <form id="programmeApplyForm" method="POST" action="<?= e(base_url('programmes/apply')) ?>" class="soft-card p-4 bg-white">
      <?= csrf_field() ?>
      <div class="hp-field" aria-hidden="true" style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden">
        <label for="website_url">Website</label>
        <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
      </div>
      <h2 class="h6 text-uppercase text-muted mb-3">Personal Information</h2>
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Name</label><input required name="name" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Phone</label><input required name="phone" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Email Address</label><input required type="email" name="email" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Guardian Name</label><input required name="guardian_name" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Guardian Number</label><input required name="guardian_phone" class="form-control"></div>
        <div class="col-md-6">
          <label class="form-label">County</label>
          <select required name="county" class="form-select">
            <option value="">Select County</option>
            <?php foreach ($kenyanCounties as $county): ?>
              <option value="<?= e($county) ?>"><?= e($county) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Unit Selection</label>
          <select required name="unit_selection" class="form-select">
            <option value="">Select Unit</option>
            <?php foreach ($programmes as $programme): ?>
              <option value="<?= e($programme['name']) ?>" <?= $selectedCourse === $programme['name'] ? 'selected' : '' ?>><?= e($programme['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Grade</label>
          <select required name="grade" class="form-select">
            <option value="">Select Grade</option>
            <option value="A">A</option><option value="A-">A-</option><option value="B+">B+</option><option value="B">B</option><option value="B-">B-</option>
            <option value="C+">C+</option><option value="C">C</option><option value="C-">C-</option><option value="D+">D+</option><option value="D">D</option>
            <option value="D-">D-</option><option value="E">E</option><option value="Other">Other</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Level</label>
          <select required name="level" class="form-select">
            <option value="">Select Level</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= e($cat) ?>" <?= $selectedLevel === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Preferred Intake</label>
          <select required name="preferred_intake" class="form-select">
            <option value="">Select Intake</option>
            <?php foreach (['January', 'March', 'May', 'July', 'September', 'November'] as $intake): ?>
              <option value="<?= e($intake) ?>" <?= $currentIntake === $intake ? 'selected' : '' ?>><?= e($intake) ?></option>
            <?php endforeach; ?>
          </select>
          <small class="text-muted">Current intake is set to <?= e($currentIntake) ?>.</small>
        </div>
        <div class="col-md-6">
          <label class="form-label">Where did you hear us from?</label>
          <input required name="referral_source" class="form-control" placeholder="e.g. Social media, Friend, Radio">
        </div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button id="programmeApplySubmitBtn" class="btn btn-primary" type="submit">Submit Application</button>
        <a class="btn btn-outline-primary" href="<?= e(base_url('programmes')) ?>">Back to Programmes</a>
      </div>
    </form>
      </div>

      <div class="col-lg-4">
        <div class="soft-card p-4 bg-white application-sidebar-coral">
          <h2 class="h6 text-uppercase text-muted mb-3">Useful Links</h2>
          <ul class="list-unstyled mb-0">
            <li class="mb-2"><a href="<?= e(base_url('programmes')) ?>">Browse Programmes</a></li>
            <li class="mb-2"><a href="<?= e(base_url('events')) ?>">Upcoming Events</a></li>
            <li class="mb-2"><a href="<?= e(base_url('library')) ?>">Library Resources</a></li>
            <li><a href="<?= e(base_url('contact')) ?>">Contact Admissions</a></li>
          </ul>
        </div>

        <div class="soft-card p-4 bg-white mt-3 application-sidebar-coral">
          <h2 class="h6 text-uppercase text-muted mb-3">Trending Units</h2>
          <ul class="small mb-0 ps-3">
            <?php foreach (array_slice($programmes, 0, 6) as $p): ?>
              <li class="mb-2"><a href="<?= e(base_url('programmes/' . $p['slug'])) ?>"><?= e($p['name']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="soft-card p-4 bg-white mt-3">
          <h2 class="h6 text-uppercase text-muted mb-3">Contacts</h2>
          <p class="mb-1 small text-muted">Phone: +254 791 309011 or +254101711499</p>
          <p class="mb-0 small text-muted">Email: admission@stmarysmchmcollege.ac.ke</p>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('programmeApplyForm');
  const overlay = document.getElementById('applySendingOverlay');
  const btn = document.getElementById('programmeApplySubmitBtn');
  if (!form || !overlay || !btn) return;

  form.addEventListener('submit', function () {
    btn.disabled = true;
    btn.textContent = 'Submitting...';
    overlay.style.display = 'block';
  });
});
</script>
