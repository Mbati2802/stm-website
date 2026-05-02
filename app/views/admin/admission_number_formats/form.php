<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0"><?= $format ? 'Edit Admission Number Format' : 'Create Admission Number Format' ?></h1>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin/admission-number-formats')) ?>"><i class="bi bi-arrow-left me-1"></i>Back to Formats</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg=flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <div class="soft-card p-3">
            <form method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Format Name</label>
                        <input name="name" type="text" class="form-control" value="<?= e($format['name'] ?? '') ?>" placeholder="e.g., Standard Format 2026" required>
                        <small class="text-muted">A descriptive name for this format</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Format Pattern</label>
                        <input name="format_pattern" type="text" class="form-control" value="<?= e($format['format_pattern'] ?? '') ?>" placeholder="e.g., STM/{YYYY}/{SEQ4}" required>
                        <small class="text-muted">Use placeholders like {PROG_ABBR}, {YYYY}, {SEQ4}</small>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault" <?= ($format && $format['is_default']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isDefault">Set as default format</label>
                            <small class="text-muted d-block">This format will be used for new student admissions</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $format ? 'Update Format' : 'Create Format' ?></button>
                    </div>
                </div>
            </form>
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
