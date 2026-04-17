<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0 text-capitalize">Manage <?= e(str_replace('_',' ',$entity)) ?></h1>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-primary" href="<?= e(base_url('admin/create/' . $entity)) ?>"><i class="bi bi-plus-circle me-1"></i>Add New</a>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <div class="table-responsive soft-card p-3 admin-table-card">
            <table class="table table-sm align-middle admin-table">
                <thead>
                <tr>
                    <?php if(!empty($rows)): foreach(array_keys($rows[0]) as $h): ?><th><?= e($h) ?></th><?php endforeach; endif; ?>
                    <th>Visibility</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($rows as $row): ?>
                    <tr>
                        <?php foreach($row as $v): ?><td><?= e((string)$v) ?></td><?php endforeach; ?>
                        <td><?php $hiddenIds = $hiddenIds ?? []; $isVisible = !in_array((int)$row['id'], $hiddenIds, true); ?><a class="btn btn-sm <?= $isVisible ? 'btn-outline-success' : 'btn-outline-warning' ?>" href="<?= e(base_url('admin/toggle/' . $entity . '/' . $row['id'])) ?>" title="<?= $isVisible ? 'Visible' : 'Hidden' ?>"><?= $isVisible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>' ?></a></td>
                        <td class="d-flex gap-1"><a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('admin/edit/' . $entity . '/' . $row['id'])) ?>"><i class="bi bi-pencil-square me-1"></i>Edit</a><a class="btn btn-sm btn-outline-danger" href="<?= e(base_url('admin/delete/' . $entity . '/' . $row['id'])) ?>" onclick="return confirm('Delete item?')"><i class="bi bi-trash me-1"></i>Delete</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
