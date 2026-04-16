<section class="py-4">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <h1 class="h4 fw-bold mb-0">Media Library</h1>
      <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>">Dashboard</a>
    </div>

    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

    <form method="POST" action="<?= e(base_url('admin/media/upload')) ?>" enctype="multipart/form-data" class="soft-card p-4 mb-4">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Title (optional)</label><input name="title" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Category</label><input name="category" class="form-control" placeholder="Events"></div>
        <div class="col-md-5"><label class="form-label">Image File</label><input required type="file" name="media_file" accept="image/*" class="form-control"></div>
      </div>
      <div class="mt-3"><button class="btn btn-primary">Upload Media</button></div>
    </form>

    <div class="row g-3">
      <?php foreach ($rows as $row): ?>
        <div class="col-md-6 col-lg-4">
          <article class="soft-card p-3 h-100 bg-white">
            <img src="<?= e(base_url(ltrim((string)$row['file_path'], '/'))) ?>" alt="<?= e((string)$row['title']) ?>" class="img-fluid mb-2" style="height:190px;object-fit:cover;width:100%">
            <h2 class="h6 mb-1"><?= e((string)$row['title']) ?></h2>
            <p class="small text-muted mb-2"><?= e((string)$row['category']) ?></p>
            <input class="form-control form-control-sm mb-2" readonly value="<?= e(base_url(ltrim((string)$row['file_path'], '/'))) ?>">
            <div class="d-flex gap-2">
              <a class="btn btn-sm btn-outline-danger" href="<?= e(base_url('admin/media/delete/' . (int)$row['id'])) ?>" onclick="return confirm('Delete this media file?')">Delete</a>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
      <?php if (empty($rows)): ?>
        <div class="col-12"><p class="text-muted mb-0">No media uploaded yet.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>
