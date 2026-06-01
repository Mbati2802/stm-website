<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card mb-4">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-wallet2 me-2"></i>Fee Statement</h4>
            </div>
            
            <!-- Balance Summary -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded text-center">
                        <p class="mb-1 text-muted">Total Invoiced</p>
                        <h3 class="mb-0">KES <?= number_format($balance['total_invoiced'], 2) ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-success bg-opacity-10 rounded text-center">
                        <p class="mb-1 text-muted">Total Paid</p>
                        <h3 class="mb-0 text-success">KES <?= number_format($balance['total_paid'], 2) ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 <?= $balance['balance'] > 0 ? 'bg-danger bg-opacity-10' : 'bg-success bg-opacity-10' ?> rounded text-center">
                        <p class="mb-1 text-muted">Outstanding Balance</p>
                        <h3 class="mb-0 <?= $balance['balance'] > 0 ? 'text-danger' : 'text-success' ?>">KES <?= number_format($balance['balance'], 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices -->
        <div class="student-card mb-4">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-receipt me-2"></i>Invoices</h4>
            </div>
            
            <?php if (empty($invoices)): ?>
                <p class="text-muted mb-0">No invoices found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover student-mobile-table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Title</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td data-label="Invoice #"><strong><?= e($invoice['invoice_number']) ?></strong></td>
                                    <td data-label="Title"><?= e($invoice['title']) ?></td>
                                    <td data-label="Amount">KES <?= number_format($invoice['amount'], 2) ?></td>
                                    <td data-label="Paid" class="text-success">KES <?= number_format($invoice['paid_amount'], 2) ?></td>
                                    <td data-label="Balance" class="<?= $invoice['balance'] > 0 ? 'text-danger' : 'text-success' ?>">
                                        <strong>KES <?= number_format($invoice['balance'], 2) ?></strong>
                                    </td>
                                    <td data-label="Status">
                                        <?php
                                        $statusClass = match($invoice['status']) {
                                            'paid' => 'bg-success',
                                            'partial' => 'bg-warning',
                                            'pending' => 'bg-secondary',
                                            'overdue' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= ucfirst($invoice['status']) ?></span>
                                    </td>
                                    <td data-label="Due"><?= e($invoice['due_date'] ?? '-') ?></td>
                                    <td data-label="Action">
                                        <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('student/invoice/' . $invoice['id'] . '?download=1')) ?>" target="_blank" title="Download Invoice"><i class="bi bi-download"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Payment History -->
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-clock-history me-2"></i>Payment History</h4>
            </div>
            
            <?php if (empty($payments)): ?>
                <p class="text-muted mb-0">No payment history found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover student-mobile-table">
                        <thead>
                            <tr>
                                <th>Payment #</th>
                                <th>Invoice #</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td data-label="Payment #"><strong><?= e($payment['payment_number']) ?></strong></td>
                                    <td data-label="Invoice #"><?= e($payment['invoice_number']) ?></td>
                                    <td data-label="Amount" class="text-success fw-bold">KES <?= number_format($payment['amount'], 2) ?></td>
                                    <td data-label="Method"><?= e($payment['payment_method_name']) ?></td>
                                    <td data-label="Ref"><?= e($payment['transaction_code'] ?? $payment['cheque_number'] ?? '-') ?></td>
                                    <td data-label="Date"><?= e($payment['payment_date']) ?></td>
                                    <td data-label="Receipt">
                                        <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('student/receipt/' . $payment['id'])) ?>" target="_blank" title="View Receipt"><i class="bi bi-eye"></i></a>
                                        <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('student/receipt/' . $payment['id'] . '?download=1')) ?>" target="_blank" title="Download Receipt"><i class="bi bi-download"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
