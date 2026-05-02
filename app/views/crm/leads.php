<h2>Leads</h2>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people"></i> All Leads</span>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createLeadModal">
            <i class="bi bi-plus"></i> New Lead
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Course Interest</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Assigned Officer</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td><strong><?= e($lead['name']) ?></strong></td>
                            <td><?= e($lead['phone']) ?></td>
                            <td><?= e($lead['email'] ?? '-') ?></td>
                            <td><?= e($lead['course_interest'] ?? '-') ?></td>
                            <td><?= e(ucwords(str_replace('_', ' ', $lead['lead_source']))) ?></td>
                            <td>
                                <span class="status-badge" style="background: <?= $lead['status_color'] ?>; color: white;">
                                    <?= e($lead['status_name']) ?>
                                </span>
                            </td>
                            <td><?= e($lead['officer_name'] ?? '-') ?></td>
                            <td><?= date('M j, Y', strtotime($lead['created_at'])) ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="/crm/leads/<?= $lead['id'] ?>">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Lead Modal -->
<div class="modal fade" id="createLeadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createLeadForm">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course Interest</label>
                        <input type="text" name="course_interest" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Intake</label>
                        <select name="intake_id" class="form-select">
                            <option value="">Select Intake</option>
                            <?php foreach ($intakes ?? [] as $intake): ?>
                                <option value="<?= $intake['id'] ?>"><?= e($intake['name']) ?> (<?= date('M Y', strtotime($intake['start_date'])) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lead Source *</label>
                        <select name="lead_source" class="form-select" required>
                            <option value="">Select Source</option>
                            <option value="website">Website</option>
                            <option value="social_media">Social Media</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="call">Call</option>
                            <option value="walk_in">Walk-in</option>
                            <option value="referral">Referral</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createLead()">Create Lead</button>
            </div>
        </div>
    </div>
</div>

<script>
function createLead() {
    const form = document.getElementById('createLeadForm');
    const formData = new FormData(form);
    
    fetch('/crm/leads/create', {
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
        alert('Error creating lead');
    });
}

function e(string) {
    return string ? string.replace(/[&<>"']/g, function(m) {
        return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[m];
    }) : '';
}
</script>
