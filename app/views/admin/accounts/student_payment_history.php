<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Payment History - <?= e($student['name']) ?></h1>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/accounts')) ?>"><i class="bi bi-receipt me-1"></i>Invoices</a>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg=flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <!-- Student Info -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Student Name:</strong> <?= e($student['name']) ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Admission Number:</strong> <?= e($student['admission_number']) ?></p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Programme:</strong> <?= e($student['programme_name']) ?> (<?= e($student['programme_abbr']) ?>)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th class="col-sm">Payment #</th>
                        <th class="col-sm">Invoice #</th>
                        <th class="col-md">Invoice Title</th>
                        <th class="col-sm">Amount</th>
                        <th class="col-sm">Payment Method</th>
                        <th class="col-sm">Transaction/Cheque #</th>
                        <th class="col-sm">Payment Date</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="8" class="text-center py-4">No payment history found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><strong><?= e($payment['payment_number']) ?></strong></td>
                                <td><?= e($payment['invoice_number']) ?></td>
                                <td><?= e($payment['invoice_title']) ?></td>
                                <td class="text-success fw-bold">KES <?= number_format($payment['amount'], 2) ?></td>
                                <td><?= e($payment['payment_method_name']) ?></td>
                                <td><?= e($payment['transaction_code'] ?? $payment['cheque_number'] ?? '-') ?></td>
                                <td><?= e($payment['payment_date']) ?></td>
                                <td class="col-actions">
                                    <div class="action-buttons">
                                        <a class="btn btn-sm btn-action-view" href="<?= e(base_url('admin/accounts/generate-receipt/' . $payment['id'])) ?>" target="_blank" title="View Receipt"><i class="bi bi-eye"></i></a>
                                        <a class="btn btn-sm btn-action-print" href="<?= e(base_url('admin/accounts/generate-receipt/' . $payment['id'])) ?>" target="_blank" title="Download Receipt"><i class="bi bi-download"></i></a>
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
