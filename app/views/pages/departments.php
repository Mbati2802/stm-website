<section class="section-stack">
  <div class="site-width boxed-section">
    <h1 class="split-title mb-4"><span class="title-primary">Academic</span> | <span class="title-secondary">Departments</span></h1>
    <div class="row g-4">
      <?php foreach (($departments ?? []) as $department): ?>
        <?php $depId = (int)($department['id'] ?? 0); ?>
        <div class="col-md-6 col-lg-4">
          <article class="soft-card p-4 h-100">
            <h2 class="h5 mb-2"><?= e((string)($department['name'] ?? 'Department')) ?></h2>
            <p class="small text-muted mb-3"><?= e(plain_text((string)($department['description'] ?? ''))) ?></p>
            <p class="small mb-3"><strong>Programmes:</strong> <?= (int)($programmeCounts[$depId] ?? 0) ?></p>
            <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('programmes')) ?>">View Programmes</a>
          </article>
        </div>
      <?php endforeach; ?>
      <?php if (empty($departments ?? [])): ?>
        <div class="col-12"><p class="text-muted mb-0">No departments available at the moment.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>
