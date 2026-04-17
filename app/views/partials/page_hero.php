<?php
$heroTitlePrimary = (string)($heroTitlePrimary ?? '');
$heroTitleSecondary = (string)($heroTitleSecondary ?? '');
$heroTagline = (string)($heroTagline ?? '');
$heroPrimaryLabel = (string)($heroPrimaryLabel ?? '');
$heroPrimaryLink = (string)($heroPrimaryLink ?? '');
$heroSecondaryLabel = (string)($heroSecondaryLabel ?? '');
$heroSecondaryLink = (string)($heroSecondaryLink ?? '');
?>
<section class="hero-ou-wrap">
  <div class="site-width hero-boxed">
    <div class="boxed-section" style="background:linear-gradient(120deg, var(--primary), var(--secondary)); color:#fff;">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <h1 class="split-title mb-2">
            <span class="title-primary text-white"><?= e($heroTitlePrimary) ?></span> |
            <span class="title-secondary text-white"><?= e($heroTitleSecondary) ?></span>
          </h1>
          <p class="mb-0 text-white-50"><?= e($heroTagline) ?></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <?php if ($heroPrimaryLabel !== '' && $heroPrimaryLink !== ''): ?>
            <a class="btn btn-primary hero-apply-btn" href="<?= e(base_url($heroPrimaryLink)) ?>"><?= e($heroPrimaryLabel) ?></a>
          <?php endif; ?>
          <?php if ($heroSecondaryLabel !== '' && $heroSecondaryLink !== ''): ?>
            <a class="btn btn-outline-light" href="<?= e(base_url($heroSecondaryLink)) ?>"><?= e($heroSecondaryLabel) ?></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

