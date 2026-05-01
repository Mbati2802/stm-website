<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-1">Deleted Records</h1>
            <p class="text-muted mb-0">View and restore deleted data from the system.</p>
        </div>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th class="col-xs">ID</th>
                        <th class="col-md">Table</th>
                        <th class="col-md">Record ID</th>
                        <th class="col-lg">Record Name</th>
                        <th class="col-md">Deleted By</th>
                        <th class="col-md">Deleted At</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <?php 
                        $recordData = json_decode($record['record_data'], true);
                        $recordName = $recordData['name'] ?? 'Unknown';
                        ?>
                        <tr>
                            <td class="col-xs"><?= (int)$record['id'] ?></td>
                            <td class="col-md"><?= e((string)$record['table_name']) ?></td>
                            <td class="col-md"><?= (int)$record['record_id'] ?></td>
                            <td class="col-lg" title="<?= e($recordName) ?>"><?= e($recordName) ?></td>
                            <td class="col-md"><?= e((string)($record['deleted_by_name'] ?? 'System')) ?></td>
                            <td class="col-md"><?= e((string)$record['deleted_at']) ?></td>
                            <td class="col-actions">
                                <div class="action-buttons">
                                    <?php if (Auth::canManageEntity('students')): ?>
                                    <form method="POST" action="<?= e(base_url('admin/deleted-records/restore')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="record_id" value="<?= (int)$record['id'] ?>">
                                        <button class="btn btn-sm btn-action-edit" title="Restore Record">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
