<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Student Support Tickets</h1>
            <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
        </div>
        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e((string)$row['name']) ?></td>
                        <td><?= e((string)$row['email']) ?></td>
                        <td><?= e((string)$row['subject']) ?></td>
                        <td><?= e(substr((string)$row['message'], 0, 80)) ?>...</td>
                        <td><?= e((string)$row['created_at']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-action-view" href="mailto:<?= e((string)$row['email']) ?>"><i class="bi bi-envelope"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="6" class="text-center text-muted">No support tickets yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
