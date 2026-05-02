<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Student Payments</h1>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/accounts')) ?>"><i class="bi bi-receipt me-1"></i>Invoices</a>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg=flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Programme</label>
                        <select name="programme_id" class="form-select">
                            <option value="">All Programmes</option>
                            <?php foreach ($programmes as $prog): ?>
                                <option value="<?= (int)$prog['id'] ?>" <?= $filters['programme_id'] == $prog['id'] ? 'selected' : '' ?>>
                                    <?= e($prog['name']) ?> (<?= e($prog['abbreviation']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Term</label>
                        <select name="term_id" class="form-select">
                            <option value="">All Terms</option>
                            <?php foreach ($terms as $term): ?>
                                <option value="<?= (int)$term['id'] ?>" <?= $filters['term_id'] == $term['id'] ? 'selected' : '' ?>>
                                    <?= e($term['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Session</label>
                        <select name="session_id" class="form-select">
                            <option value="">All Sessions</option>
                            <?php foreach ($sessions as $session): ?>
                                <option value="<?= (int)$session['id'] ?>" <?= $filters['session_id'] == $session['id'] ? 'selected' : '' ?>>
                                    <?= e($session['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Student Payments Table -->
        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th class="col-md">Student Name</th>
                        <th class="col-sm">Admission #</th>
                        <th class="col-md">Programme</th>
                        <th class="col-sm">Total Invoiced</th>
                        <th class="col-sm">Total Paid</th>
                        <th class="col-sm">Balance</th>
                        <th class="col-sm">Status</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr><td colspan="8" class="text-center py-4">No students found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><strong><?= e($student['name']) ?></strong></td>
                                <td><?= e($student['admission_number'] ?? '-') ?></td>
                                <td><?= e($student['programme_abbr'] ?? '-') ?></td>
                                <td>KES <?= number_format($student['total_invoiced'], 2) ?></td>
                                <td class="text-success">KES <?= number_format($student['total_paid'], 2) ?></td>
                                <td class="<?= $student['balance'] > 0 ? 'text-danger' : 'text-success' ?>">
                                    <strong>KES <?= number_format($student['balance'], 2) ?></strong>
                                </td>
                                <td>
                                    <?php if ($student['balance'] <= 0): ?>
                                        <span class="badge bg-success">Fully Paid</span>
                                    <?php elseif ($student['total_paid'] > 0): ?>
                                        <span class="badge bg-warning">Partial</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                                <td class="col-actions">
                                    <div class="action-buttons">
                                        <a class="btn btn-sm btn-action-view" href="<?= e(base_url('admin/students?search=' . urlencode($student['admission_number'] ?? ''))) ?>" title="View Student"><i class="bi bi-person"></i></a>
                                        <?php if ($student['total_paid'] > 0): ?>
                                            <a class="btn btn-sm btn-action-print" href="<?= e(base_url('admin/accounts/student-payments?student_id=' . $student['id'] . '&view_receipts=1')) ?>" title="View Receipts"><i class="bi bi-receipt"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
