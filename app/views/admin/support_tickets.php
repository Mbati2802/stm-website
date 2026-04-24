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
                        <th class="col-md">Name</th>
                        <th class="col-lg">Email</th>
                        <th class="col-lg">Subject</th>
                        <th class="col-xl">Message</th>
                        <th class="col-sm">Date</th>
                        <th class="col-actions">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="col-md" title="<?= e((string)$row['name']) ?>"><?= e((string)$row['name']) ?></td>
                        <td class="col-lg" title="<?= e((string)$row['email']) ?>"><?= e((string)$row['email']) ?></td>
                        <td class="col-lg" title="<?= e((string)$row['subject']) ?>"><?= e((string)$row['subject']) ?></td>
                        <td class="col-xl" title="<?= e((string)$row['message']) ?>"><?= e((string)$row['message']) ?></td>
                        <td class="col-sm" title="<?= e((string)$row['created_at']) ?>"><?= e((string)$row['created_at']) ?></td>
                        <td class="col-actions">
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
