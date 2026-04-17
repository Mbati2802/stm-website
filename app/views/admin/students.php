<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <div>
                <h1 class="h4 fw-bold mb-1">Student Accounts</h1>
                <p class="text-muted mb-0">Assign or generate admission numbers for student portal access.</p>
            </div>
            <form method="POST" action="<?= e(base_url('admin/students/bulk-assign')) ?>">
                <button class="btn btn-primary">
                    <i class="bi bi-magic me-1"></i>Generate Missing Admission Numbers
                </button>
            </form>
        </div>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <p class="text-muted small mb-3">
            Admission number format template (editable in Settings): <code><?= e($admissionNumberFormat) ?></code>
            using placeholders {YEAR}, {YY}, {MM}, {DD}, {SEQ4}, {SEQ5}, {SEQ6}, {ID}.
        </p>
        <div class="table-responsive soft-card p-3 admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Admission Number</th>
                        <th>Assign / Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= (int)$row['id'] ?></td>
                            <td><?= e((string)$row['name']) ?></td>
                            <td><?= e((string)$row['email']) ?></td>
                            <td><strong><?= e((string)($row['admission_number'] ?? 'Not assigned')) ?></strong></td>
                            <td>
                                <form method="POST" action="<?= e(base_url('admin/students/assign/' . (int)$row['id'])) ?>" class="d-flex gap-2">
                                    <input class="form-control form-control-sm" name="admission_number" value="<?= e((string)($row['admission_number'] ?? '')) ?>" placeholder="Leave blank to auto-generate">
                                    <button class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
