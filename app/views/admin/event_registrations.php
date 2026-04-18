<section class="py-4">
  <div class="admin-content-wrap">
    <div class="admin-page-head mb-3">
      <h1 class="h4 fw-bold mb-0">Event Registrations</h1>
      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
      </div>
    </div>

    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

    <div class="table-responsive admin-table-card">
      <table class="table align-middle admin-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Event</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Student</th>
            <th>Notes</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td><?= e((string)($row['created_at'] ?? '')) ?></td>
              <td>
                <?php if (!empty($row['event_slug'])): ?>
                  <a href="<?= e(base_url('events/' . $row['event_slug'])) ?>" target="_blank" class="text-primary"><?= e((string)($row['event_title'] ?? 'Event')) ?></a>
                <?php else: ?>
                  <?= e((string)($row['event_title'] ?? 'Event')) ?>
                <?php endif; ?>
              </td>
              <td><?= e((string)($row['name'] ?? '')) ?></td>
              <td><?= e((string)($row['email'] ?? '')) ?></td>
              <td><?= e((string)($row['phone'] ?? '')) ?></td>
              <td><?= !empty($row['is_student']) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
              <td><?= e(substr((string)($row['notes'] ?? ''), 0, 30)) ?>...</td>
              <td>
                <div class="action-buttons">
                  <a class="btn btn-sm btn-action-view" href="mailto:<?= e($row['email']) ?>" title="Send Email"><i class="bi bi-envelope"></i></a>
                  <a class="btn btn-sm btn-action-delete" href="<?= e(base_url('admin/event-registrations/delete/' . $row['id'])) ?>" onclick="return confirm('Delete registration?')" title="Delete"><i class="bi bi-trash"></i></a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($rows)): ?>
            <tr><td colspan="8" class="text-muted text-center">No registrations yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

