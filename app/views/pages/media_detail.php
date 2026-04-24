<?php
$item = $item ?? [];
$type = (string)($type ?? 'news');
$title = (string)($item['title'] ?? ucfirst($type));
$summary = (string)($item['summary'] ?? '');
$body = (string)($item['body'] ?? '');
$image = (string)($item['image_path'] ?? '');
?>
<section class="section-stack">
  <div class="site-width boxed-section">
    <a class="btn btn-sm btn-outline-primary mb-3" href="<?= e(base_url('media?type=' . urlencode($type))) ?>">
      <i class="bi bi-arrow-left me-1"></i>Back to <?= e(ucfirst($type)) ?>
    </a>
    <article class="soft-card p-4 p-md-5">
      <h1 class="split-title mb-3"><span class="title-primary"><?= e($title) ?></span></h1>
      <p class="text-muted small mb-3"><?= e((string)($item['created_at'] ?? '')) ?></p>
      <?php if ($image !== ''): ?>
        <img src="<?= e($image) ?>" alt="<?= e($title) ?>" class="img-fluid rounded-4 mb-4" loading="lazy">
      <?php endif; ?>
      <?php if ($summary !== ''): ?>
        <p class="lead"><?= e(plain_text($summary)) ?></p>
      <?php endif; ?>
      <div class="mt-3">
        <?= safe_html($body !== '' ? $body : nl2br(e($summary))) ?>
      </div>
    </article>
  </div>
</section>
