<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Contact Messages</h1>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-primary" href="<?= e(base_url('admin/messages/export')) ?>"><i class="bi bi-download me-1"></i>Download Excel</a>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
            </div>
        </div>
        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="col-lg">Email</th>
                        <th class="col-sm">Phone</th>
                        <th class="col-md">Subject</th>
                        <th class="col-xl">Message</th>
                        <th class="col-sm">Date</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="col-md" title="<?= e((string)$row['name']) ?>"><?= e($row['name']) ?></td>
                        <td class="col-lg" title="<?= e((string)$row['email']) ?>"><?= e($row['email']) ?></td>
                        <td class="col-sm" title="<?= e((string)$row['phone']) ?>"><?= e($row['phone']) ?></td>
                        <td class="col-md" title="<?= e((string)$row['subject']) ?>"><?= e($row['subject']) ?></td>
                        <td class="col-xl" title="<?= e((string)$row['message']) ?>"><?= e((string)$row['message']) ?></td>
                        <td class="col-sm" title="<?= e((string)$row['created_at']) ?>"><?= e($row['created_at']) ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a class="btn btn-sm btn-action-view" href="mailto:<?= e($row['email']) ?>" title="Send Email"><i class="bi bi-envelope"></i></a>
                                <a class="btn btn-sm btn-action-delete" href="<?= e(base_url('admin/messages/delete/' . $row['id'])) ?>" onclick="return confirm('Delete message?')" title="Delete"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
