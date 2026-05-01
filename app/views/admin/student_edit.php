<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= e((string)$student['name']) ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" value="<?= e((string)$student['email']) ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone</label>
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
    <div class="col-md-6">
        <label class="form-label">Guardian Name</label>
        <input type="text" name="guardian_name" class="form-control" value="<?= e((string)($student['guardian_name'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Guardian Phone</label>
        <input type="text" name="guardian_phone" class="form-control" value="<?= e((string)($student['guardian_phone'] ?? '')) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Programme</label>
        <select name="programme_id" class="form-select">
            <option value="">Select Programme</option>
            <?php foreach ($programmes as $programme): ?>
                <option value="<?= e((string)$programme['id']) ?>" <?= (int)$student['programme_id'] === (int)$programme['id'] ? 'selected' : '' ?>><?= e((string)$programme['name']) ?></option>
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
<input type="hidden" name="student_id" value="<?= (int)$student['id'] ?>">
