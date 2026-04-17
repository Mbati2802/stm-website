<section class="py-5">
    <div class="container">
        <h1 class="h4 fw-bold mb-3">Student Accounts</h1>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <p class="text-muted small mb-3">
            Admission number format template (editable in Settings): <code><?= e($admissionNumberFormat) ?></code>
            using placeholders {YEAR}, {YY}, {MM}, {DD}, {SEQ4}, {SEQ5}, {SEQ6}, {ID}.
        </p>
        <div class="table-responsive soft-card p-3">
            <table class="table align-middle">
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
                                    <button class="btn btn-sm btn-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
