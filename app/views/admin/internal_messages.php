<section class="py-4">
    <div class="admin-content-wrap">
        <?php if ($msg = flash('success')): ?>
            <div class="alert alert-success"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-danger"><?= e($msg) ?></div>
        <?php endif; ?>

        <div class="soft-card p-4 mb-3">
            <h2 class="h6 text-uppercase text-primary mb-3">Send Team Message</h2>
            <form method="POST" action="<?= e(base_url('admin/internal-messages/send')) ?>" class="row g-3">
                <?= csrf_field() ?>
                <div class="col-md-4">
                    <label class="form-label">Recipient</label>
                    <select name="recipient_id" class="form-select" required>
                        <option value="">Select admin user</option>
                        <?php foreach ($users as $user): ?>
                            <?php if ((int)($user['id'] ?? 0) === (int)($_SESSION['admin_id'] ?? 0)) continue; ?>
                            <option value="<?= (int)($user['id'] ?? 0) ?>">
                                <?= e((string)($user['name'] ?? '')) ?> (<?= e((string)($user['role'] ?? '')) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" maxlength="190" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Message</label>
                    <textarea name="body" class="form-control" rows="4" required></textarea>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-send me-1"></i>Send</button>
                </div>
            </form>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Inbox</h2>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead>
                                <tr>
                                    <th class="col-md">From</th>
                                    <th class="col-md">Subject</th>
                                    <th class="col-lg">Message</th>
                                    <th class="col-sm">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($inbox)): ?>
                                    <tr><td colspan="4" class="text-muted">No inbox messages yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($inbox as $row): ?>
                                        <tr>
                                            <td title="<?= e((string)($row['sender_name'] ?? '')) ?>"><?= e((string)($row['sender_name'] ?? '')) ?></td>
                                            <td title="<?= e((string)($row['subject'] ?? '')) ?>"><?= e((string)($row['subject'] ?? '')) ?></td>
                                            <td title="<?= e((string)($row['body'] ?? '')) ?>"><?= e((string)($row['body'] ?? '')) ?></td>
                                            <td><?= e((string)($row['created_at'] ?? '')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Sent</h2>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead>
                                <tr>
                                    <th class="col-md">To</th>
                                    <th class="col-md">Subject</th>
                                    <th class="col-lg">Message</th>
                                    <th class="col-sm">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sent)): ?>
                                    <tr><td colspan="4" class="text-muted">No sent messages yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($sent as $row): ?>
                                        <tr>
                                            <td title="<?= e((string)($row['recipient_name'] ?? '')) ?>"><?= e((string)($row['recipient_name'] ?? '')) ?></td>
                                            <td title="<?= e((string)($row['subject'] ?? '')) ?>"><?= e((string)($row['subject'] ?? '')) ?></td>
                                            <td title="<?= e((string)($row['body'] ?? '')) ?>"><?= e((string)($row['body'] ?? '')) ?></td>
                                            <td><?= e((string)($row['created_at'] ?? '')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
