<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Record Payment</h1>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin/accounts/view-invoice/' . $invoice['id'])) ?>"><i class="bi bi-arrow-left me-1"></i>Back to Invoice</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg=flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <!-- Invoice Summary -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Invoice Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Invoice Number:</strong> <?= e($invoice['invoice_number']) ?></p>
                        <p><strong>Title:</strong> <?= e($invoice['title']) ?></p>
                        <p><strong>Student:</strong> <?= e($invoice['student_name']) ?></p>
                        <p><strong>Admission Number:</strong> <?= e($invoice['admission_number']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Invoice Amount:</strong> KES <?= number_format($invoice['amount'], 2) ?></p>
                        <?php
                        // Get current paid amount
                        $pdo = Database::getInstance($this->config['db']);
                        $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) AS paid FROM payments WHERE invoice_id = ?');
                        $stmt->execute([$invoice['id']]);
                        $currentPaid = $stmt->fetch(PDO::FETCH_ASSOC)['paid'];
                        $remainingBalance = $invoice['amount'] - $currentPaid;
                        ?>
                        <p><strong>Already Paid:</strong> KES <?= number_format($currentPaid, 2) ?></p>
                        <p><strong>Remaining Balance:</strong> KES <?= number_format($remainingBalance, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="soft-card p-3">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="invoice_id" value="<?= (int)$invoice['id'] ?>">
                <input type="hidden" name="student_id" value="<?= (int)$invoice['student_id'] ?>">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Amount (KES) *</label>
                        <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                        <small class="text-muted">Maximum: KES <?= number_format($remainingBalance, 2) ?></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Date *</label>
                        <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Method *</label>
                        <select name="payment_method_id" class="form-select" required id="paymentMethodSelect">
                            <option value="">Select Payment Method</option>
                            <?php foreach ($paymentMethods as $method): ?>
                                <option value="<?= (int)$method['id'] ?>" data-type="<?= e(strtolower($method['name'])) ?>">
                                    <?= e($method['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- M-PESA Fields -->
                    <div class="col-md-6" id="mpesaFields" style="display: none;">
                        <label class="form-label fw-semibold">M-PESA Transaction Code *</label>
                        <input type="text" name="transaction_code" class="form-control" placeholder="e.g., ABC123XYZ" id="mpesaTransactionCode">
                        <small class="text-muted">Enter the M-PESA transaction code from the SMS</small>
                    </div>
                    
                    <!-- Bankers Cheque Fields -->
                    <div class="col-md-6" id="chequeFields" style="display: none;">
                        <label class="form-label fw-semibold">Cheque Number *</label>
                        <input type="text" name="cheque_number" class="form-control" placeholder="Cheque number">
                    </div>
                    <div class="col-md-6" id="bankFields" style="display: none;">
                        <label class="form-label fw-semibold">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="Bank name">
                    </div>
                    
                    <!-- Transaction Code for other methods -->
                    <div class="col-md-6" id="transactionFields" style="display: none;">
                        <label class="form-label fw-semibold">Transaction/Reference Number</label>
                        <input type="text" name="transaction_code" class="form-control" placeholder="Transaction reference">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Transaction/Reference Number (Optional)</label>
                        <input type="text" name="transaction_code_general" class="form-control" placeholder="Enter transaction code or reference number for any payment method">
                        <small class="text-muted">This field can be used for any payment method</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Payment notes or additional information..."></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Record Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.getElementById('paymentMethodSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const methodType = selectedOption.getAttribute('data-type');
    
    // Hide all method-specific fields
    document.getElementById('mpesaFields').style.display = 'none';
    document.getElementById('chequeFields').style.display = 'none';
    document.getElementById('bankFields').style.display = 'none';
    document.getElementById('transactionFields').style.display = 'none';
    
    // Remove required attribute from all
    document.getElementById('mpesaTransactionCode').removeAttribute('required');
    
    // Show relevant fields based on payment method
    if (methodType === 'm-pesa') {
        document.getElementById('mpesaFields').style.display = 'block';
        document.getElementById('mpesaTransactionCode').setAttribute('required', 'required');
    } else if (methodType === 'bankers cheque') {
        document.getElementById('chequeFields').style.display = 'block';
        document.getElementById('bankFields').style.display = 'block';
    } else {
        document.getElementById('transactionFields').style.display = 'block';
    }
});
</script>
