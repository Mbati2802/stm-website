<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Bulk Create Invoice</h1>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/accounts/create-invoice')) ?>"><i class="bi bi-plus-circle me-1"></i>Create Single Invoice</a>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin/accounts')) ?>"><i class="bi bi-arrow-left me-1"></i>Back to Accounts</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg=flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Bulk Invoice Creation:</strong> This will create an invoice for ALL students matching the selected programme, term, and session. Make sure to verify the selection before submitting.
        </div>

        <div class="soft-card p-3">
            <form method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Programme *</label>
                        <select name="programme_id" class="form-select" required id="programmeSelect">
                            <option value="">Select Programme</option>
                            <?php foreach ($programmes as $prog): ?>
                                <option value="<?= (int)$prog['id'] ?>"><?= e($prog['name']) ?> (<?= e($prog['abbreviation']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Invoice Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g., Tuition Fee - May 2026" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Amount per Student (KES) *</label>
                        <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Due Date</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Term *</label>
                        <select name="term_id" class="form-select" required>
                            <option value="">Select Term</option>
                            <?php foreach ($terms as $term): ?>
                                <option value="<?= (int)$term['id'] ?>"><?= e($term['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Academic Session *</label>
                        <select name="session_id" class="form-select" required>
                            <option value="">Select Session</option>
                            <?php foreach ($sessions as $session): ?>
                                <option value="<?= (int)$session['id'] ?>"><?= e($session['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Invoice description or notes..."></textarea>
                    </div>
                    
                    <!-- Fee Items -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Fee Items (Optional)</label>
                        <div id="feeItemsContainer">
                            <div class="row g-2 mb-2 fee-item-row">
                                <div class="col-md-6">
                                    <input type="text" name="fee_items[0][description]" class="form-control" placeholder="Item description">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="fee_items[0][amount]" class="form-control" placeholder="Amount" step="0.01" min="0">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-fee-item" onclick="removeFeeItem(this)">Remove</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addFeeItem()">
                            <i class="bi bi-plus"></i> Add Fee Item
                        </button>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Bulk Invoices</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
let feeItemCount = 1;

function addFeeItem() {
    const container = document.getElementById('feeItemsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'row g-2 mb-2 fee-item-row';
    newRow.innerHTML = `
        <div class="col-md-6">
            <input type="text" name="fee_items[${feeItemCount}][description]" class="form-control" placeholder="Item description">
        </div>
        <div class="col-md-4">
            <input type="number" name="fee_items[${feeItemCount}][amount]" class="form-control" placeholder="Amount" step="0.01" min="0">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger w-100 remove-fee-item" onclick="removeFeeItem(this)">Remove</button>
        </div>
    `;
    container.appendChild(newRow);
    feeItemCount++;
}

function removeFeeItem(button) {
    const row = button.closest('.fee-item-row');
    if (document.querySelectorAll('.fee-item-row').length > 1) {
        row.remove();
    }
}
</script>
