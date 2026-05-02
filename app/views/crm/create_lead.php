<h2>Create New Lead</h2>

<div class="card">
    <div class="card-header">
        <span><i class="bi bi-person-plus"></i> Lead Information</span>
    </div>
    <div class="card-body">
        <form method="POST" action="/crm/leads/create">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone *</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
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
            </div>
            <div class="mb-3">
                <label class="form-label">Program Interest</label>
                <select name="program_interest" class="form-select">
                    <option value="">Select Program</option>
                    <?php foreach ($programs ?? [] as $program): ?>
                        <option value="<?= e($program['name']) ?>"><?= e($program['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Intake</label>
                <select name="intake_id" class="form-select">
                    <option value="">Select Intake</option>
                    <?php foreach ($intakes ?? [] as $intake): ?>
                        <option value="<?= $intake['id'] ?>"><?= e($intake['name']) ?> (<?= e($intake['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" placeholder="e.g., Nairobi, Mombasa">
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="4" placeholder="Any additional information about this lead"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Lead</button>
                <a href="/crm/leads" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
