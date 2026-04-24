<section class="py-4">
    <div class="admin-content-wrap">
        <?php
        $columnClassMap = [
            'id' => 'col-xs',
            'name' => 'col-md',
            'title' => 'col-lg',
            'email' => 'col-lg',
            'phone' => 'col-sm',
            'slug' => 'col-md',
            'category' => 'col-sm',
            'summary' => 'col-xl',
            'body' => 'col-xl',
            'message' => 'col-xl',
            'description' => 'col-xl',
            'created_at' => 'col-sm',
            'updated_at' => 'col-sm',
        ];
        ?>
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0 text-capitalize">Manage <?= e(str_replace('_',' ',$entity)) ?></h1>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-primary" href="<?= e(base_url('admin/create/' . $entity)) ?>"><i class="bi bi-plus-circle me-1"></i>Add New</a>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                <tr>
                    <?php if(!empty($rows)): foreach(array_keys($rows[0]) as $h): ?>
                        <?php $headerClass = $columnClassMap[(string)$h] ?? 'col-md'; ?>
                        <th class="<?= e($headerClass) ?>"><?= e($h) ?></th>
                    <?php endforeach; endif; ?>
                    <th class="col-sm">Visibility</th>
                    <th class="col-actions">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($rows as $row): ?>
                    <tr>
                    <?php foreach($row as $k => $v): ?>
                        <?php
                        $key = (string)$k;
                        $cellClass = $columnClassMap[$key] ?? 'col-md';
                        $fullValue = (string)$v;
                        ?>
                        <td class="<?= e($cellClass) ?>" title="<?= e($key === 'password' ? '' : $fullValue) ?>">
                            <?php if ((string)$k === 'password'): ?>
                                ••••••••
                            <?php else: ?>
                                <?= e($fullValue) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                        <td class="col-sm"><?php $hiddenIds = $hiddenIds ?? []; $isVisible = !in_array((int)$row['id'], $hiddenIds, true); ?><a class="btn btn-sm btn-action-toggle" href="<?= e(base_url('admin/toggle/' . $entity . '/' . $row['id'])) ?>" title="<?= $isVisible ? 'Visible' : 'Hidden' ?>"><?= $isVisible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>' ?></a></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a class="btn btn-sm btn-action-edit" href="<?= e(base_url('admin/edit/' . $entity . '/' . $row['id'])) ?>" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <a class="btn btn-sm btn-action-delete" href="<?= e(base_url('admin/delete/' . $entity . '/' . $row['id'])) ?>" onclick="return confirm('Delete item?')" title="Delete"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
