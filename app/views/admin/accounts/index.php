<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Accounts & Billing</h1>
            <div class="d-flex flex-wrap gap-2">
                <?php if (Auth::canManageEntity('students')): ?>
                    <a class="btn btn-primary" href="<?= e(base_url('admin/accounts/create-invoice')) ?>"><i class="bi bi-plus-circle me-1"></i>Create Invoice</a>
                    <a class="btn btn-outline-primary" href="<?= e(base_url('admin/accounts/bulk-create-invoice')) ?>"><i class="bi bi-stack me-1"></i>Bulk Create</a>
                    <a class="btn btn-outline-primary" href="<?= e(base_url('admin/accounts/student-payments')) ?>"><i class="bi bi-people me-1"></i>Student Payments</a>
                <?php endif; ?>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg=flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

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
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="partial" <?= $filters['status'] === 'partial' ? 'selected' : '' ?>>Partial</option>
                            <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="overdue" <?= $filters['status'] === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                            <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th class="col-sm">Invoice #</th>
                        <th class="col-md">Student</th>
                        <th class="col-md">Programme</th>
                        <th class="col-md">Title</th>
                        <th class="col-sm">Amount</th>
                        <th class="col-sm">Paid</th>
                        <th class="col-sm">Balance</th>
                        <th class="col-sm">Status</th>
                        <th class="col-sm">Due Date</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr><td colspan="10" class="text-center py-4">No invoices found. <a href="<?= e(base_url('admin/accounts/create-invoice')) ?>">Create one</a></td></tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td><strong><?= e($invoice['invoice_number']) ?></strong></td>
                                <td>
                                    <?= e($invoice['student_name']) ?><br>
                                    <small class="text-muted"><?= e($invoice['admission_number']) ?></small>
                                </td>
                                <td><?= e($invoice['programme_abbr'] ?? '-') ?></td>
                                <td><?= e($invoice['title']) ?></td>
                                <td>KES <?= number_format($invoice['amount'], 2) ?></td>
                                <td>KES <?= number_format($invoice['paid_amount'], 2) ?></td>
                                <td class="<?= $invoice['balance'] > 0 ? 'text-danger' : 'text-success' ?>">
                                    <strong>KES <?= number_format($invoice['balance'], 2) ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match($invoice['status']) {
                                        'paid' => 'bg-success',
                                        'partial' => 'bg-warning',
                                        'pending' => 'bg-secondary',
                                        'overdue' => 'bg-danger',
                                        'cancelled' => 'bg-dark',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst($invoice['status']) ?></span>
                                </td>
                                <td><?= e($invoice['due_date'] ?? '-') ?></td>
                                <td class="col-actions">
                                    <div class="action-buttons">
                                        <a class="btn btn-sm btn-action-view" href="<?= e(base_url('admin/accounts/view-invoice/' . $invoice['id'])) ?>" title="View"><i class="bi bi-eye"></i></a>
                                        <?php if (Auth::canManageEntity('students') && $invoice['balance'] > 0): ?>
                                            <a class="btn btn-sm btn-action-edit" href="<?= e(base_url('admin/accounts/record-payment?invoice_id=' . $invoice['id'])) ?>" title="Record Payment"><i class="bi bi-cash"></i></a>
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
