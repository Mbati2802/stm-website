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

    <div class="col-12 mt-3">
        <h5 class="mb-2">Enrollment</h5>
        <?php if (!empty($enrollment)): ?>
            <p class="mb-1"><strong>Academic Year:</strong> <?= e((string)($academicSessionName ?? ($enrollment['academic_session_id'] ?? '-'))) ?></p>
            <p class="mb-1"><strong>Term:</strong> <?= e((string)($termName ?? ($enrollment['term_id'] ?? '-'))) ?></p>
        <?php else: ?>
            <p class="mb-1 text-warning">No active enrollment found for this student.</p>
        <?php endif; ?>

        <label class="form-label fw-semibold">Academic Year</label>
        <div class="d-flex gap-2 align-items-start mb-2">
            <select id="enrollmentAcademicYearSelect" class="form-select" style="max-width:320px;" data-student-id="<?= e((string)$student['id']) ?>">
                <option value="">Select academic year</option>
                <?php foreach ($academicYears ?? [] as $ay): ?>
                    <option value="<?= (int)$ay['id'] ?>" <?= (!empty($enrollment) && ((int)$enrollment['academic_session_id'] === (int)$ay['id'])) ? 'selected' : '' ?>><?= e($ay['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <label class="form-label fw-semibold">Term</label>
        <div class="d-flex gap-2 align-items-start mb-2">
            <select id="enrollmentTermSelect" class="form-select" style="max-width:320px;">
                <option value="">Select term</option>
                <?php foreach ($termsForSession ?? [] as $t): ?>
                    <option value="<?= (int)$t['id'] ?>" <?= (!empty($enrollment) && ((int)$enrollment['term_id'] === (int)$t['id'])) ? 'selected' : '' ?>><?= e($t['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <label class="form-label fw-semibold">Student Session</label>
        <div class="d-flex gap-2 align-items-start">
            <select id="enrollmentSessionSelect" class="form-select" style="max-width:240px;">
                <option value="">Select session</option>
                <?php foreach ($sessions ?? [] as $s): ?>
                    <option value="<?= (int)$s['id'] ?>" <?= (!empty($enrollment) && ((int)$enrollment['session_id'] === (int)$s['id'])) ? 'selected' : '' ?>><?= e($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button id="saveEnrollmentBtn" class="btn btn-sm btn-primary">Save</button>
        </div>
        <div id="enrollmentMessage" class="mt-2"></div>
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

<script>
(function(){
    const btn = document.getElementById('saveEnrollmentBtn');
    if (!btn) return;

    const aySelect = document.getElementById('enrollmentAcademicYearSelect');
    const termSelect = document.getElementById('enrollmentTermSelect');
    const sessionSelect = document.getElementById('enrollmentSessionSelect');
    const studentId = aySelect ? aySelect.getAttribute('data-student-id') : null;

    // When academic year changes, load terms via AJAX
    if (aySelect) {
        aySelect.addEventListener('change', function() {
            const ay = this.value;
            termSelect.innerHTML = '<option value="">Loading...</option>';
            if (!ay) {
                termSelect.innerHTML = '<option value="">Select term</option>';
                return;
            }
            fetch('<?= e(base_url('admin/semester/terms')) ?>?session_id=' + encodeURIComponent(ay))
                .then(r => r.json())
                .then(data => {
                    termSelect.innerHTML = '<option value="">Select term</option>';
                    if (Array.isArray(data)) {
                        data.forEach(t => {
                            const opt = document.createElement('option');
                            opt.value = t.id;
                            opt.textContent = t.name;
                            termSelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    termSelect.innerHTML = '<option value="">Select term</option>';
                });
        });
    }

    btn.addEventListener('click', function(){
        const sessionId = sessionSelect.value;
        const academicSessionId = aySelect ? aySelect.value : '';
        const termId = termSelect ? termSelect.value : '';
        const msg = document.getElementById('enrollmentMessage');
        msg.innerHTML = '';
        if (!sessionId) {
            msg.innerHTML = '<div class="alert alert-warning">Please select a session.</div>';
            return;
        }
        const body = new URLSearchParams({ student_id: studentId, session_id: sessionId });
        if (academicSessionId) body.append('academic_session_id', academicSessionId);
        if (termId) body.append('term_id', termId);

        fetch('<?= e(base_url('admin/students/update-enrollment')) ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="alert alert-success">' + (data.message || 'Updated') + '</div>';
                if (data.academic_session_name) html += '<div>Academic Year: ' + data.academic_session_name + '</div>';
                if (data.term_name) html += '<div>Term: ' + data.term_name + '</div>';
                if (data.new_session_name) html += '<div>Session: ' + data.new_session_name + '</div>';
                msg.innerHTML = html;
            } else {
                msg.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Failed to update enrollment') + '</div>';
            }
        })
        .catch(err => {
            console.error(err);
            msg.innerHTML = '<div class="alert alert-danger">Request failed</div>';
        });
    });
})();
</script>
