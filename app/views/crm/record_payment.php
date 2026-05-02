<h2>Record Payment</h2>

<div class="row mb-3">
    <div class="col-12">
        <a href="/crm/leads/<?= $lead['id'] ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Lead
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="bi bi-cash"></i> Registration Fee Payment</span>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Lead:</strong> <?= e($lead['name']) ?><br>
            <strong>Phone:</strong> <?= e($lead['phone']) ?><br>
            <strong>Course:</strong> <?= e($lead['course_interest'] ?? '-') ?><br>
            <strong>Registration Fee:</strong> KES 5,000
        </div>

        <form id="paymentForm">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Amount (KES) *</label>
                    <input type="number" name="amount" class="form-control" value="5000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Method *</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="mpesa">M-PESA</option>
                        <option value="bank">Bank Deposit</option>
                        <option value="cash">Cash</option>
                        <option value="online">Online Payment</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Transaction Code *</label>
                    <input type="text" name="transaction_code" class="form-control" placeholder="M-PESA code or reference" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Date *</label>
                    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Receipt Number</label>
                <input type="text" name="receipt_number" class="form-control" placeholder="Bank receipt number (if applicable)">
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Additional payment details"></textarea>
            </div>
            <button type="button" class="btn btn-primary" onclick="submitPayment()">Record Payment</button>
        </form>
    </div>
</div>

<script>
function submitPayment() {
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);
    
    fetch('/crm/leads/<?= $lead['id'] ?>/record-payment', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '/crm/leads/<?= $lead['id'] ?>';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Error recording payment');
    });
}

function e(string) {
    return string ? string.replace(/[&<>"']/g, function(m) {
        return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[m];
    }) : '';
}
</script>
