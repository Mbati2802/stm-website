<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Name</label>
        <p class="mb-0"><?= e((string)$student['name']) ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Email</label>
        <p class="mb-0"><?= e((string)$student['email']) ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Admission Number</label>
        <p class="mb-0"><strong><?= e((string)($student['admission_number'] ?? 'Not assigned')) ?></strong></p>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Status</label>
        <p class="mb-0">
            <?php if (!empty($student['is_suspended'])): ?>
                <span class="badge bg-danger">Suspended</span>
            <?php else: ?>
                <span class="badge bg-success">Active</span>
            <?php endif; ?>
        </p>
    </div>
    <?php if (!empty($student['national_id'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">National ID</label>
        <p class="mb-0"><?= e((string)$student['national_id']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['gender'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Gender</label>
        <p class="mb-0"><?= e((string)$student['gender']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['date_of_birth'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Date of Birth</label>
        <p class="mb-0"><?= e((string)$student['date_of_birth']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['phone'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Phone</label>
        <p class="mb-0"><?= e((string)$student['phone']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['county'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">County</label>
        <p class="mb-0"><?= e((string)$student['county']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['sub_county'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Sub-County/Town</label>
        <p class="mb-0"><?= e((string)$student['sub_county']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['guardian_name'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Guardian Name</label>
        <p class="mb-0"><?= e((string)$student['guardian_name']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['guardian_phone'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Guardian Phone</label>
        <p class="mb-0"><?= e((string)$student['guardian_phone']) ?></p>
    </div>
    <?php endif; ?>
    <?php if ($programme): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Programme</label>
        <p class="mb-0"><?= e((string)$programme['name']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['preferred_intake'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Preferred Intake</label>
        <p class="mb-0"><?= e((string)$student['preferred_intake']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['previous_school'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Previous School</label>
        <p class="mb-0"><?= e((string)$student['previous_school']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['kcse_year'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">KCSE Year</label>
        <p class="mb-0"><?= e((string)$student['kcse_year']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['kcse_grade'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">KCSE Grade</label>
        <p class="mb-0"><?= e((string)$student['kcse_grade']) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($student['created_at'])): ?>
    <div class="col-md-6">
        <label class="form-label fw-bold">Created At</label>
        <p class="mb-0"><?= e((string)$student['created_at']) ?></p>
    </div>
    <?php endif; ?>
</div>
