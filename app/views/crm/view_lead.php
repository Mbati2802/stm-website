<h2>Lead Details</h2>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-person"></i> Lead Information</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Name</label>
                        <div class="fw-bold"><?= e($lead['name']) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Phone</label>
                        <div><?= e($lead['phone']) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Email</label>
                        <div><?= e($lead['email'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Lead Source</label>
                        <div><?= e(ucwords(str_replace('_', ' ', $lead['lead_source']))) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Program Interest</label>
                        <div><?= e($lead['program_interest'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Intake</label>
                        <div><?= e($lead['intake_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Location</label>
                        <div><?= e($lead['location'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Assigned Officer</label>
                        <div><?= e($lead['officer_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            <span class="status-badge" style="background: <?= $lead['status_color'] ?>; color: white;">
                                <?= e($lead['status_name']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Created</label>
                        <div><?= date('F j, Y g:i A', strtotime($lead['created_at'])) ?></div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Notes</label>
                        <div><?= e($lead['notes'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-gear"></i> Actions</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Update Status</label>
                    <select id="statusSelect" class="form-select">
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['id'] ?>" <?= $status['id'] == $lead['status_id'] ? 'selected' : '' ?>>
                                <?= e($status['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea id="statusNotes" class="form-control" rows="2"></textarea>
                </div>
                <button class="btn btn-primary w-100 mb-2" onclick="updateStatus()">Update Status</button>
                <a class="btn btn-success w-100 mb-2" href="/crm/leads/<?= $lead['id'] ?>/record-payment">
                    <i class="bi bi-cash"></i> Record Payment
                </a>
                <a class="btn btn-info w-100 mb-2" href="/crm/leads/<?= $lead['id'] ?>/admission-letter" target="_blank">
                    <i class="bi bi-file-earmark-text"></i> Admission Letter
                </a>
                <a href="/crm/leads" class="btn btn-secondary w-100">Back to Leads</a>
            </div>
        </div>
    </div>
</div>

<!-- Status History -->
<div class="card mb-4">
    <div class="card-header">
        <span><i class="bi bi-clock-history"></i> Status History</span>
    </div>
    <div class="card-body">
        <?php if (empty($history)): ?>
            <p class="text-muted">No status changes recorded.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Changed By</th>
                            <th>Date</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td><?= e($h['old_status_name'] ?? '-') ?></td>
                                <td><strong><?= e($h['new_status_name']) ?></strong></td>
                                <td><?= e($h['changed_by_name'] ?? '-') ?></td>
                                <td><?= date('M j, Y g:i A', strtotime($h['created_at'])) ?></td>
                                <td><?= e($h['notes'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Communication History -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-chat"></i> Communication History</span>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sendCommunicationModal">
            <i class="bi bi-send"></i> Send Message
        </button>
    </div>
    <div class="card-body">
        <?php if (empty($communications)): ?>
            <p class="text-muted">No communications recorded.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Sent By</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($communications as $c): ?>
                            <tr>
                                <td>
                                    <?php if ($c['type'] === 'whatsapp'): ?>
                                        <i class="bi bi-whatsapp text-success"></i> WhatsApp
                                    <?php elseif ($c['type'] === 'sms'): ?>
                                        <i class="bi bi-chat-dots text-info"></i> SMS
                                    <?php elseif ($c['type'] === 'email'): ?>
                                        <i class="bi bi-envelope text-primary"></i> Email
                                    <?php else: ?>
                                        <i class="bi bi-telephone"></i> Call
                                    <?php endif; ?>
                                </td>
                                <td><?= e(substr($c['message'], 0, 100)) ?>...</td>
                                <td><?= e($c['sent_by'] ?? '-') ?></td>
                                <td><?= date('M j, Y g:i A', strtotime($c['sent_at'])) ?></td>
                                <td>
                                    <?php if ($c['status'] === 'sent'): ?>
                                        <span class="badge bg-success">Sent</span>
                                    <?php elseif ($c['status'] === 'delivered'): ?>
                                        <span class="badge bg-info">Delivered</span>
                                    <?php elseif ($c['status'] === 'failed'): ?>
                                        <span class="badge bg-danger">Failed</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pending</span>
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

<!-- Payments -->
<div class="card mb-4">
    <div class="card-header">
        <span><i class="bi bi-cash"></i> Payments</span>
    </div>
    <div class="card-body">
        <?php if (empty($payments)): ?>
            <p class="text-muted">No payments recorded.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Transaction Code</th>
                            <th>Payment Date</th>
                            <th>Receipt #</th>
                            <th>Status</th>
                            <th>Verified By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td class="text-success">KES <?= number_format($p['amount'], 2) ?></td>
                                <td><?= e(ucwords($p['payment_method'])) ?></td>
                                <td><?= e($p['transaction_code'] ?? '-') ?></td>
                                <td><?= date('M j, Y', strtotime($p['payment_date'])) ?></td>
                                <td><?= e($p['receipt_number'] ?? '-') ?></td>
                                <td>
                                    <?php if ($p['status'] === 'verified'): ?>
                                        <span class="badge bg-success">Verified</span>
                                    <?php elseif ($p['status'] === 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                        <button class="btn btn-sm btn-success ms-2" onclick="verifyPayment(<?= $p['id'] ?>, 'verified')">Verify</button>
                                        <button class="btn btn-sm btn-danger ms-1" onclick="verifyPayment(<?= $p['id'] ?>, 'rejected')">Reject</button>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($p['verified_by_name'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Admission Offers -->
<div class="card">
    <div class="card-header">
        <span><i class="bi bi-file-earmark-text"></i> Admission Offers</span>
    </div>
    <div class="card-body">
        <?php if (empty($offers)): ?>
            <p class="text-muted">No admission offers issued.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Issued Date</th>
                            <th>Expiry Date</th>
                            <th>Letter Generated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $o): ?>
                            <tr>
                                <td>
                                    <?php if ($o['offer_type'] === 'provisional'): ?>
                                        <span class="badge bg-warning">Provisional</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Confirmed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M j, Y', strtotime($o['issued_date'])) ?></td>
                                <td><?= date('M j, Y', strtotime($o['expiry_date'])) ?></td>
                                <td><?= $o['letter_generated'] ? '<i class="bi bi-check-circle text-success"></i> Yes' : '<i class="bi bi-x-circle text-danger"></i> No' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Send Communication Modal -->
<div class="modal fade" id="sendCommunicationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="communicationForm">
                    <div class="mb-3">
                        <label class="form-label">Communication Type *</label>
                        <select name="type" class="form-select" required>
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="email">Email</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message *</label>
                        <textarea name="message" class="form-control" rows="5" required placeholder="Enter your message here..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendMessage()">Send Message</button>
            </div>
        </div>
    </div>
</div>

<script>
function sendMessage() {
    const form = document.getElementById('communicationForm');
    const formData = new FormData(form);
    formData.append('lead_id', <?= $lead['id'] ?>);
    
    fetch('/crm/communication/send', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Error sending message');
    });
}

function verifyPayment(paymentId, status) {
    if (!confirm('Are you sure you want to ' + status + ' this payment?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('status', status);
    
    fetch('/crm/payments/' + paymentId + '/verify', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Error verifying payment');
    });
}

function updateStatus() {
    const statusId = document.getElementById('statusSelect').value;
    const notes = document.getElementById('statusNotes').value;
    
    const formData = new FormData();
    formData.append('lead_id', <?= $lead['id'] ?>);
    formData.append('status_id', statusId);
    formData.append('notes', notes);
    
    fetch('/crm/leads/update-status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Error updating status');
    });
}

function e(string) {
    return string ? string.replace(/[&<>"']/g, function(m) {
        return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[m];
    }) : '';
}
</script>
