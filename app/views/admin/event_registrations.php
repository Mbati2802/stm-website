<section class="py-4">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <h1 class="h4 fw-bold mb-0">Event Registrations</h1>
      <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>">Dashboard</a>
    </div>

    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

    <div class="table-responsive soft-card p-3 bg-white">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>Date</th>
            <th>Event</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Student</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td><?= e((string)($row['created_at'] ?? '')) ?></td>
              <td>
                <?php if (!empty($row['event_slug'])): ?>
                  <a href="<?= e(base_url('events/' . $row['event_slug'])) ?>" target="_blank"><?= e((string)($row['event_title'] ?? 'Event')) ?></a>
                <?php else: ?>
                  <?= e((string)($row['event_title'] ?? 'Event')) ?>
                <?php endif; ?>
              </td>
              <td><?= e((string)($row['name'] ?? '')) ?></td>
              <td><?= e((string)($row['email'] ?? '')) ?></td>
              <td><?= e((string)($row['phone'] ?? '')) ?></td>
              <td><?= !empty($row['is_student']) ? 'Yes' : 'No' ?></td>
              <td><?= e((string)($row['notes'] ?? '')) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($rows)): ?>
            <tr><td colspan="7" class="text-muted">No registrations yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

