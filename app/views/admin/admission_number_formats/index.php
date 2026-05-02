<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Admission Number Formats</h1>
            <div class="d-flex flex-wrap gap-2">
                <?php if (Auth::canManageEntity('portal_courses')): ?>
                    <a class="btn btn-primary" href="<?= e(base_url('admin/admission-number-formats/create')) ?>"><i class="bi bi-plus-circle me-1"></i>Add New Format</a>
                <?php endif; ?>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg=flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th class="col-md">Name</th>
                        <th class="col-lg">Format Pattern</th>
                        <th class="col-sm">Default</th>
                        <th class="col-sm">Created</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($formats)): ?>
                        <tr><td colspan="5" class="text-center py-4">No admission number formats found. <a href="<?= e(base_url('admin/admission-number-formats/create')) ?>">Create one</a></td></tr>
                    <?php else: ?>
                        <?php foreach ($formats as $format): ?>
                            <tr>
                                <td><?= e($format['name']) ?></td>
                                <td><code><?= e($format['format_pattern']) ?></code></td>
                                <td>
                                    <?php if ($format['is_default']): ?>
                                        <span class="badge bg-success">Default</span>
                                    <?php else: ?>
                                        <?php if (Auth::canManageEntity('portal_courses')): ?>
                                            <a class="btn btn-sm btn-link p-0" href="<?= e(base_url('admin/admission-number-formats/set-default/' . $format['id'])) ?>" onclick="return confirm('Set this as default format?')">Set Default</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($format['created_at']) ?></td>
                                <td class="col-actions">
                                    <div class="action-buttons">
                                        <?php if (Auth::canManageEntity('portal_courses')): ?>
                                            <a class="btn btn-sm btn-action-edit" href="<?= e(base_url('admin/admission-number-formats/edit/' . $format['id'])) ?>" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            <a class="btn btn-sm btn-action-delete" href="<?= e(base_url('admin/admission-number-formats/delete/' . $format['id'])) ?>" onclick="return confirm('Delete this format?')" title="Delete"><i class="bi bi-trash"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="soft-card p-3 mt-3">
            <h6 class="fw-bold mb-2">Available Placeholders:</h6>
            <div class="row g-2">
                <div class="col-md-3"><code>{PROG_ABBR}</code> - Programme Abbreviation (e.g., DPTT)</div>
                <div class="col-md-3"><code>{YYYY}</code> - Full Year (e.g., 2026)</div>
                <div class="col-md-3"><code>{YY}</code> - Short Year (e.g., 26)</div>
                <div class="col-md-3"><code>{MM}</code> - Month Number (e.g., 05)</div>
                <div class="col-md-3"><code>{MON}</code> - Month Name (e.g., MAY)</div>
                <div class="col-md-3"><code>{M}</code> - Month Initial (e.g., J)</div>
                <div class="col-md-3"><code>{SEQ4}</code> - Sequential 4-digit (e.g., 0001)</div>
                <div class="col-md-3"><code>{SEQ3}</code> - Sequential 3-digit (e.g., 001)</div>
                <div class="col-md-3"><code>{SEQ2}</code> - Sequential 2-digit (e.g., 01)</div>
            </div>
            <div class="mt-2">
                <strong>Example formats:</strong>
                <ul class="mb-0">
                    <li><code>STM/{YYYY}/{SEQ4}</code> → STM/2026/0001</li>
                    <li><code>{PROG_ABBR}/{YYYY}/{MM}/{SEQ3}</code> → DPTT/2026/05/001</li>
                    <li><code>{PROG_ABBR}/{SEQ4}/{MON}/{YYYY}</code> → DPTT/0001/MAY/2026</li>
                    <li><code>{PROG_ABBR}/{M}/{YYYY}/{SEQ3}</code> → DPTT/J/2026/001</li>
                </ul>
            </div>
        </div>
    </div>
</section>
