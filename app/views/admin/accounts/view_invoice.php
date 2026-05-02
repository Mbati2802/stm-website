<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Invoice <?= e($invoice['invoice_number']) ?></h1>
            <div class="d-flex flex-wrap gap-2">
                <?php if (Auth::canManageEntity('students') && $balance > 0): ?>
                    <a class="btn btn-primary" href="<?= e(base_url('admin/accounts/record-payment?invoice_id=' . $invoice['id'])) ?>"><i class="bi bi-cash me-1"></i>Record Payment</a>
                <?php endif; ?>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin/accounts')) ?>"><i class="bi bi-arrow-left me-1"></i>Back to Accounts</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg=flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <!-- Invoice Details -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Invoice Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Invoice Number:</strong> <?= e($invoice['invoice_number']) ?></p>
                        <p><strong>Title:</strong> <?= e($invoice['title']) ?></p>
                        <p><strong>Student:</strong> <?= e($invoice['student_name']) ?></p>
                        <p><strong>Admission Number:</strong> <?= e($invoice['admission_number']) ?></p>
                        <p><strong>Email:</strong> <?= e($invoice['student_email']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Programme:</strong> <?= e($invoice['programme_name'] ?? '-') ?> (<?= e($invoice['programme_abbr'] ?? '-') ?>)</p>
                        <p><strong>Term:</strong> <?= e($invoice['term_name'] ?? '-') ?></p>
                        <p><strong>Session:</strong> <?= e($invoice['session_name'] ?? '-') ?></p>
                        <p><strong>Course:</strong> <?= e($invoice['course_code'] ?? '-') ?> - <?= e($invoice['course_title'] ?? '-') ?></p>
                        <p><strong>Due Date:</strong> <?= e($invoice['due_date'] ?? '-') ?></p>
                    </div>
                </div>
                <?php if ($invoice['description']): ?>
                    <div class="mt-3">
                        <strong>Description:</strong>
                        <p class="text-muted"><?= e($invoice['description']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Fee Items -->
        <?php if (!empty($feeItems)): ?>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Fee Items</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feeItems as $item): ?>
                            <tr>
                                <td><?= e($item['description']) ?></td>
                                <td class="text-end">KES <?= number_format($item['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="fw-bold">Total</td>
                            <td class="text-end fw-bold">KES <?= number_format(array_sum(array_column($feeItems, 'amount')), 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Payment Summary -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Payment Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <p class="mb-1 text-muted">Total Amount</p>
                            <h3 class="mb-0">KES <?= number_format($invoice['amount'], 2) ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-success bg-opacity-10 rounded">
                            <p class="mb-1 text-muted">Total Paid</p>
                            <h3 class="mb-0 text-success">KES <?= number_format($totalPaid, 2) ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 <?= $balance > 0 ? 'bg-danger bg-opacity-10' : 'bg-success bg-opacity-10' ?> rounded">
                            <p class="mb-1 text-muted">Balance</p>
                            <h3 class="mb-0 <?= $balance > 0 ? 'text-danger' : 'text-success' ?>">KES <?= number_format($balance, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments History -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Payment History</h5>
                <span class="badge bg-secondary"><?= count($payments) ?> payment(s)</span>
            </div>
            <div class="card-body">
                <?php if (empty($payments)): ?>
                    <p class="text-center text-muted py-4">No payments recorded yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Payment #</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Transaction/Cheque #</th>
                                    <th>Payment Date</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><strong><?= e($payment['payment_number']) ?></strong></td>
                                        <td class="text-success fw-bold">KES <?= number_format($payment['amount'], 2) ?></td>
                                        <td><?= e($payment['payment_method_name']) ?></td>
                                        <td>
                                            <?= e($payment['transaction_code'] ?? $payment['cheque_number'] ?? '-') ?>
                                            <?php if ($payment['bank_name']): ?>
                                                <br><small class="text-muted"><?= e($payment['bank_name']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= e($payment['payment_date']) ?></td>
                                        <td>
                                            <?php if ($payment['receipt_generated']): ?>
                                                <span class="badge bg-success">Generated</span>
                                            <?php else: ?>
                                                <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('admin/accounts/generate-receipt/' . $payment['id'])) ?>">
                                                    <i class="bi bi-receipt"></i> Generate
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
