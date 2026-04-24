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
            <th class="col-sm">Date</th>
            <th class="col-lg">Event</th>
            <th class="col-md">Name</th>
            <th class="col-lg">Email</th>
            <th class="col-sm">Phone</th>
            <th class="col-sm">Student</th>
            <th class="col-xl">Notes</th>
            <th class="col-actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td class="col-sm" title="<?= e((string)($row['created_at'] ?? '')) ?>"><?= e((string)($row['created_at'] ?? '')) ?></td>
              <td class="col-lg" title="<?= e((string)($row['event_title'] ?? 'Event')) ?>">
                <?php if (!empty($row['event_slug'])): ?>
                  <a href="<?= e(base_url('events/' . $row['event_slug'])) ?>" target="_blank" class="text-primary"><?= e((string)($row['event_title'] ?? 'Event')) ?></a>
                <?php else: ?>
                  <?= e((string)($row['event_title'] ?? 'Event')) ?>
                <?php endif; ?>
              </td>
              <td class="col-md" title="<?= e((string)($row['name'] ?? '')) ?>"><?= e((string)($row['name'] ?? '')) ?></td>
              <td class="col-lg" title="<?= e((string)($row['email'] ?? '')) ?>"><?= e((string)($row['email'] ?? '')) ?></td>
              <td class="col-sm" title="<?= e((string)($row['phone'] ?? '')) ?>"><?= e((string)($row['phone'] ?? '')) ?></td>
              <td class="col-sm"><?= !empty($row['is_student']) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
              <td class="col-xl" title="<?= e((string)($row['notes'] ?? '')) ?>"><?= e((string)($row['notes'] ?? '')) ?></td>
              <td class="col-actions">
                <div class="action-buttons">
                  <button
                    type="button"
                    class="btn btn-sm btn-action-view js-view-reg"
                    title="View Registration"
                    data-name="<?= e((string)($row['name'] ?? '')) ?>"
                    data-email="<?= e((string)($row['email'] ?? '')) ?>"
                    data-phone="<?= e((string)($row['phone'] ?? '')) ?>"
                    data-event="<?= e((string)($row['event_title'] ?? 'Event')) ?>"
                    data-notes="<?= e((string)($row['notes'] ?? '')) ?>"
                    data-date="<?= e((string)($row['created_at'] ?? '')) ?>"
                  ><i class="bi bi-eye"></i></button>
                  <button
                    type="button"
                    class="btn btn-sm btn-action-view js-email-reg"
                    title="Email Registrant"
                    data-id="<?= (int)($row['id'] ?? 0) ?>"
                    data-name="<?= e((string)($row['name'] ?? '')) ?>"
                    data-email="<?= e((string)($row['email'] ?? '')) ?>"
                    data-event="<?= e((string)($row['event_title'] ?? 'Event')) ?>"
                  ><i class="bi bi-envelope"></i></button>
                  <?php $waPhone = preg_replace('/\D+/', '', (string)($row['phone'] ?? '')); ?>
                  <?php if ($waPhone !== ''): ?>
                    <?php
                      $waText = 'Hello ' . (string)($row['name'] ?? '') . ",\n"
                        . 'Thank you for registering for ' . (string)($row['event_title'] ?? 'our event') . ".\n"
                        . 'We are reaching out with an update regarding your registration.';
                    ?>
                    <a class="btn btn-sm btn-success" target="_blank" rel="noopener" href="<?= e('https://wa.me/' . $waPhone . '?text=' . rawurlencode($waText)) ?>" title="Send WhatsApp"><i class="bi bi-whatsapp"></i></a>
                  <?php endif; ?>
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

<div class="modal fade" id="eventRegViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registration Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body small">
        <p><strong>Name:</strong> <span id="regViewName"></span></p>
        <p><strong>Email:</strong> <span id="regViewEmail"></span></p>
        <p><strong>Phone:</strong> <span id="regViewPhone"></span></p>
        <p><strong>Event:</strong> <span id="regViewEvent"></span></p>
        <p><strong>Date:</strong> <span id="regViewDate"></span></p>
        <p class="mb-0"><strong>Notes:</strong> <span id="regViewNotes"></span></p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="eventRegEmailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="eventRegEmailForm" action="<?= e(base_url('admin/event-registrations/email/0')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="registration_id" id="eventRegEmailRegistrationId" value="0">
        <div class="modal-header">
          <h5 class="modal-title">Email Registrant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted mb-2">To: <span id="regEmailTo"></span></p>
          <div class="mb-2">
            <label class="form-label">Subject</label>
            <input class="form-control" name="subject" id="regEmailSubject" required>
          </div>
          <div>
            <label class="form-label">Message</label>
            <textarea class="form-control" name="body" rows="6" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Send Email</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const viewModalEl = document.getElementById('eventRegViewModal');
  const emailModalEl = document.getElementById('eventRegEmailModal');
  const viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;
  const emailModal = emailModalEl ? new bootstrap.Modal(emailModalEl) : null;
  const setText = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.textContent = value || '';
  };

  document.querySelectorAll('.js-view-reg').forEach((btn) => {
    btn.addEventListener('click', function () {
      setText('regViewName', btn.getAttribute('data-name') || '');
      setText('regViewEmail', btn.getAttribute('data-email') || '');
      setText('regViewPhone', btn.getAttribute('data-phone') || '');
      setText('regViewEvent', btn.getAttribute('data-event') || '');
      setText('regViewDate', btn.getAttribute('data-date') || '');
      setText('regViewNotes', btn.getAttribute('data-notes') || '');
      if (viewModal) viewModal.show();
    });
  });

  document.querySelectorAll('.js-email-reg').forEach((btn) => {
    btn.addEventListener('click', function () {
      const id = btn.getAttribute('data-id') || '0';
      const email = btn.getAttribute('data-email') || '';
      const eventTitle = btn.getAttribute('data-event') || 'Event';
      const form = document.getElementById('eventRegEmailForm');
      if (form) {
        form.action = '<?= e(base_url('admin/event-registrations/email')) ?>/' + id;
      }
      const hiddenId = document.getElementById('eventRegEmailRegistrationId');
      if (hiddenId) hiddenId.value = id;
      setText('regEmailTo', email);
      const subjectInput = document.getElementById('regEmailSubject');
      if (subjectInput) {
        subjectInput.value = 'Update on ' + eventTitle;
      }
      if (emailModal) emailModal.show();
    });
  });
});
</script>

