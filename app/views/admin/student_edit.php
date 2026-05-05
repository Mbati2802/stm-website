<h6 class="text-uppercase text-muted mb-3 mt-1">Personal Information</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= e((string)$student['name']) ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" value="<?= e((string)$student['email']) ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
            <option value="">Select Gender</option>
            <option value="Male" <?= ($student['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= ($student['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" class="form-control" value="<?= e((string)($student['date_of_birth'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">National ID Number</label>
        <input type="text" name="national_id" class="form-control" value="<?= e((string)($student['national_id'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone Number</label>
        <input type="text" name="phone" class="form-control" value="<?= e((string)($student['phone'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">County</label>
        <select name="county" class="form-select">
            <option value="">Select County</option>
            <?php foreach ($kenyanCounties as $county): ?>
                <option value="<?= e($county) ?>" <?= (string)($student['county'] ?? '') === $county ? 'selected' : '' ?>><?= e($county) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Sub-County/Town</label>
        <input type="text" name="sub_county" class="form-control" value="<?= e((string)($student['sub_county'] ?? '')) ?>">
    </div>
</div>

<h6 class="text-uppercase text-muted mb-3">Guardian Information</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Guardian Name</label>
        <input type="text" name="guardian_name" class="form-control" value="<?= e((string)($student['guardian_name'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Guardian Relationship</label>
        <select name="guardian_relationship" class="form-select">
            <option value="">Select Relationship</option>
            <option value="Parent" <?= ($student['guardian_relationship'] ?? '') === 'Parent' ? 'selected' : '' ?>>Parent</option>
            <option value="Guardian" <?= ($student['guardian_relationship'] ?? '') === 'Guardian' ? 'selected' : '' ?>>Guardian</option>
            <option value="Sponsor" <?= ($student['guardian_relationship'] ?? '') === 'Sponsor' ? 'selected' : '' ?>>Sponsor</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Guardian Phone</label>
        <input type="text" name="guardian_phone" class="form-control" value="<?= e((string)($student['guardian_phone'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Guardian Email</label>
        <input type="email" name="guardian_email" class="form-control" value="<?= e((string)($student['guardian_email'] ?? '')) ?>">
    </div>
</div>

<h6 class="text-uppercase text-muted mb-3">Academic Information</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Previous School</label>
        <input type="text" name="previous_school" class="form-control" value="<?= e((string)($student['previous_school'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">KCSE Year</label>
        <input type="number" name="kcse_year" class="form-control" placeholder="YYYY" min="2000" max="<?= date('Y') ?>" value="<?= e((string)($student['kcse_year'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">KCSE Grade</label>
        <select name="kcse_grade" class="form-select">
            <option value="">Select Grade</option>
            <?php foreach (['A','A-','B+','B','B-','C+','C','C-','D+','D','D-','E'] as $grade): ?>
                <option value="<?= e($grade) ?>" <?= ($student['kcse_grade'] ?? '') === $grade ? 'selected' : '' ?>><?= e($grade) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">KCSE Index Number</label>
        <input type="text" name="kcse_index" class="form-control" value="<?= e((string)($student['kcse_index'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Programme</label>
        <select name="programme_id" class="form-select">
            <option value="">Select Programme</option>
            <?php foreach ($programmes as $programme): ?>
                <option value="<?= e((string)$programme['id']) ?>" <?= (int)($student['programme_id'] ?? 0) === (int)$programme['id'] ? 'selected' : '' ?>><?= e((string)$programme['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Preferred Intake</label>
        <select name="preferred_intake" class="form-select">
            <option value="">Select Intake</option>
            <?php foreach (['January', 'March', 'May', 'July', 'September', 'November'] as $intake): ?>
                <option value="<?= e($intake) ?>" <?= (string)($student['preferred_intake'] ?? '') === $intake ? 'selected' : '' ?>><?= e($intake) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<h6 class="text-uppercase text-muted mb-3">Additional Information</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Disability Status</label>
        <select name="disability_status" class="form-select">
            <option value="None" <?= ($student['disability_status'] ?? 'None') === 'None' ? 'selected' : '' ?>>None</option>
            <option value="Physical" <?= ($student['disability_status'] ?? '') === 'Physical' ? 'selected' : '' ?>>Physical</option>
            <option value="Visual" <?= ($student['disability_status'] ?? '') === 'Visual' ? 'selected' : '' ?>>Visual</option>
            <option value="Hearing" <?= ($student['disability_status'] ?? '') === 'Hearing' ? 'selected' : '' ?>>Hearing</option>
            <option value="Other" <?= ($student['disability_status'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Referral Source</label>
        <input type="text" name="referral_source" class="form-control" value="<?= e((string)($student['referral_source'] ?? '')) ?>">
    </div>
    <div class="col-12">
        <label class="form-label">Additional Notes</label>
        <textarea name="additional_notes" rows="3" class="form-control"><?= e((string)($student['additional_notes'] ?? '')) ?></textarea>
    </div>
</div>

<h6 class="text-uppercase text-muted mb-3">Account Status</h6>
<div class="row g-3 mb-2">
    <div class="col-md-6">
        <label class="form-label">Admission Number</label>
        <input type="text" name="admission_number" class="form-control" value="<?= e((string)($student['admission_number'] ?? '')) ?>" placeholder="e.g. STM/2026/0001">
        <div class="form-text">Leave blank to keep existing. Must be unique.</div>
    </div>
    <div class="col-md-6 d-flex align-items-center pt-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_suspended" id="isSuspended" value="1" <?= !empty($student['is_suspended']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="isSuspended">Suspend this student account</label>
        </div>
    </div>
</div>

<input type="hidden" name="student_id" value="<?= (int)$student['id'] ?>">
