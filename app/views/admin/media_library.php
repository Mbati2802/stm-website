<section class="py-4">
  <div class="admin-content-wrap">
    <div class="admin-page-head mb-3">
      <h1 class="h4 fw-bold mb-0">Media Library</h1>
      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
      </div>
    </div>

    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

    <form method="POST" action="<?= e(base_url('admin/media/upload')) ?>" enctype="multipart/form-data" class="settings-section-card mb-4">
      <?= csrf_field() ?>
      <h6><i class="bi bi-cloud-upload me-2"></i>Upload New Media</h6>
      <div class="row g-3">
        <div class="col-md-3"><label class="form-label">Title Prefix (optional)</label><input name="title" class="form-control" placeholder="Graduation 2026"></div>
        <div class="col-md-3"><label class="form-label">Category</label><input name="category" class="form-control" placeholder="Events"></div>
        <div class="col-md-6"><label class="form-label">Image Files (bulk)</label><input required type="file" name="media_files[]" accept="image/*" class="form-control" multiple></div>
      </div>
      <div class="mt-3"><button class="btn btn-primary"><i class="bi bi-upload me-2"></i>Upload Media</button></div>
    </form>

    <div class="row g-3">
      <?php foreach ($rows as $row): ?>
        <div class="col-md-6 col-lg-4">
          <article class="settings-section-card h-100">
            <img src="<?= e(base_url(ltrim((string)$row['file_path'], '/'))) ?>" alt="<?= e((string)$row['title']) ?>" class="img-fluid mb-2 rounded" style="height:190px;object-fit:cover;width:100%">
            <h6 class="mb-1"><?= e((string)$row['title']) ?></h6>
            <p class="small text-muted mb-2"><?= e((string)$row['category']) ?></p>
            <input class="form-control form-control-sm mb-2" readonly value="<?= e(base_url(ltrim((string)$row['file_path'], '/'))) ?>">
            <div class="action-buttons">
              <a class="btn btn-sm btn-action-view" href="<?= e(base_url(ltrim((string)$row['file_path'], '/'))) ?>" target="_blank" title="View"><i class="bi bi-eye"></i></a>
              <a class="btn btn-sm btn-action-delete" href="<?= e(base_url('admin/media/delete/' . (int)$row['id'])) ?>" onclick="return confirm('Delete this media file?')" title="Delete"><i class="bi bi-trash"></i></a>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
      <?php if (empty($rows)): ?>
        <div class="col-12"><p class="text-muted mb-0 text-center">No media uploaded yet.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>
