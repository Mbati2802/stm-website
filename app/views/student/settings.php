<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-gear me-2"></i>Account Settings</h4>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Theme Preference</label>
                    <select class="form-select">
                        <option>Light Mode</option>
                        <option>Dark Mode</option>
                        <option>System Default</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Language</label>
                    <select class="form-select">
                        <option>English</option>
                        <option>Swahili</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="autoSave" checked>
                        <label class="form-check-label" for="autoSave">Auto-save form data</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="showOnline" checked>
                        <label class="form-check-label" for="showOnline">Show online status</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="twoFactor">
                        <label class="form-check-label" for="twoFactor">Enable two-factor authentication</label>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Save Settings</button>
                </div>
            </div>
        </div>

        <div class="student-card mt-4">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-trash me-2"></i>Danger Zone</h4>
            </div>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> These actions are irreversible. Please proceed with caution.
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger">Download My Data</button>
                <button class="btn btn-danger">Delete Account</button>
            </div>
        </div>
    </div>
</section>
