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

	<div class="col-12 mt-3">
		<div class="d-flex justify-content-between align-items-center mb-2">
			<h6 class="text-uppercase text-muted mb-0">Enrollment</h6>
			<button type="button" class="btn btn-sm btn-outline-secondary" id="manageEnrollmentBtn">Manage Enrollments</button>
		</div>
		<?php if (!empty($enrollment)): ?>
			<p class="mb-1"><strong>Current Academic Year:</strong> <?= e((string)($enrollment['academic_session_id'] ?? '-')) ?></p>
			<p class="mb-1"><strong>Current Term:</strong> <?= e((string)($enrollment['term_id'] ?? '-')) ?></p>
		<?php else: ?>
			<p class="mb-1 text-warning">No active enrollment found for this student.</p>
		<?php endif; ?>

		<div class="row g-2">
			<div class="col-md-4">
				<label class="form-label">Academic Year</label>
				<select name="academic_session_id" id="editAcademicYearSelect" class="form-select">
					<option value="">Select Academic Year</option>
					<?php foreach (($academicYears ?? []) as $ay): ?>
						<option value="<?= e((string)$ay['id']) ?>" <?= (!empty($enrollment) && (int)$enrollment['academic_session_id'] === (int)$ay['id']) ? 'selected' : '' ?>><?= e((string)$ay['name']) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-md-4">
				<label class="form-label">Term</label>
				<select name="term_id" id="editTermSelect" class="form-select">
					<option value="">Select Term</option>
					<?php foreach (($termsForSession ?? []) as $t): ?>
						<option value="<?= e((string)$t['id']) ?>" <?= (!empty($enrollment) && (int)$enrollment['term_id'] === (int)$t['id']) ? 'selected' : '' ?>><?= e((string)$t['name']) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-md-4">
				<label class="form-label">Student Session</label>
				<select name="session_id" id="editSessionSelect" class="form-select">
					<option value="">Select session</option>
					<?php foreach (($sessions ?? []) as $s): ?>
						<option value="<?= e((string)$s['id']) ?>" <?= (!empty($enrollment) && (int)$enrollment['session_id'] === (int)$s['id']) ? 'selected' : '' ?>><?= e((string)$s['name']) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>

	<!-- Enrollment management modal -->
	<div class="modal fade" id="enrollmentManageModal" tabindex="-1" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-scrollable">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Manage Enrollments</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        <div id="enrollmentListArea">Loading...</div>
	        <hr>
	        <h6>Create new enrollment</h6>
	        <div class="row g-2">
	          <div class="col-md-4"><select id="newAcademicSession" class="form-select"><option value="">Select Academic Year</option><?php foreach(($academicYears ?? []) as $ay): ?><option value="<?= e((string)$ay['id']) ?>"><?= e((string)$ay['name']) ?></option><?php endforeach; ?></select></div>
	          <div class="col-md-4"><select id="newTerm" class="form-select"><option value="">Select Term</option></select></div>
	          <div class="col-md-4"><select id="newSession" class="form-select"><option value="">Select Session</option><?php foreach(($sessions ?? []) as $s): ?><option value="<?= e((string)$s['id']) ?>"><?= e((string)$s['name']) ?></option><?php endforeach; ?></select></div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	        <button type="button" id="createEnrollmentBtn" class="btn btn-primary">Create Enrollment</button>
	      </div>
	    </div>
	  </div>
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

<script>
document.addEventListener('DOMContentLoaded', function(){
    const ay = document.getElementById('editAcademicYearSelect');
    const term = document.getElementById('editTermSelect');
    const manageBtn = document.getElementById('manageEnrollmentBtn');
    const modal = document.getElementById('enrollmentManageModal');
    const listArea = document.getElementById('enrollmentListArea');

    if (ay && term) {
        ay.addEventListener('change', function(){
            const sessionId = this.value;
            term.innerHTML = '<option value="">Loading...</option>';
            if (!sessionId) { term.innerHTML = '<option value="">Select Term</option>'; return; }
            fetch('<?= e(base_url('admin/semester/terms')) ?>?session_id=' + encodeURIComponent(sessionId))
                .then(r => r.json())
                .then(data => {
                    term.innerHTML = '<option value="">Select Term</option>';
                    if (Array.isArray(data)) {
                        data.forEach(t => {
                            const opt = document.createElement('option');
                            opt.value = t.id;
                            opt.textContent = t.name;
                            term.appendChild(opt);
                        });
                    }
                })
                .catch(err => { console.error(err); term.innerHTML = '<option value="">Select Term</option>'; });
        });
    }

    if (manageBtn && modal) {
        manageBtn.addEventListener('click', function(){
            // open bootstrap modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            loadEnrollments();
        });
    }

    function loadEnrollments(){
        listArea.innerHTML = 'Loading...';
        const studentId = <?= (int)$student['id'] ?>;
        fetch('<?= e(base_url('admin/students/enrollments')) ?>?student_id=' + studentId)
            .then(r => r.json())
            .then(data => {
                if (!Array.isArray(data)) { listArea.innerHTML = '<div class="alert alert-danger">Failed to load</div>'; return; }
                if (data.length === 0) { listArea.innerHTML = '<div class="alert alert-info">No enrollments found</div>'; return; }
                let html = '<ul class="list-group">';
                data.forEach(row => {
                                    const badgeClass = (row.status === 'active') ? 'bg-success' : 'bg-secondary';
                                    let actions = '';
                                    if (row.status !== 'active') {
                                        actions += `<button class="btn btn-sm btn-primary me-1 btn-reactivate" data-id="${row.id}" data-student="${<?= (int)$student['id'] ?>}">Make Active</button>`;
                                    } else {
                                        actions += `<span class="text-muted small">Active</span>`;
                                    }
                                    html += `<li class="list-group-item d-flex justify-content-between align-items-start"><div><strong>${escapeHtml(row.academic_session_name||row.academic_session_id||'')}</strong> — ${escapeHtml(row.term_name||row.term_id||'')} <br><small>Session: ${escapeHtml(row.session_name||row.session_id||'')}</small></div><div>${actions}<span class="badge ${badgeClass} ms-2">${escapeHtml(row.status)}</span></div></li>`;
                                });
                html += '</ul>';
                listArea.innerHTML = html;
            })
            .catch(err => { console.error(err); listArea.innerHTML = '<div class="alert alert-danger">Failed to load</div>'; });
    }

    // delegate clicks for reactivate buttons
    listArea.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-reactivate');
        if (!btn) return;
        const enrollmentId = btn.getAttribute('data-id');
        const studentId = btn.getAttribute('data-student');
        if (!enrollmentId || !studentId) return;
        if (!confirm('Set this enrollment as active?')) return;
        fetch('<?= e(base_url('admin/students/set-enrollment-status')) ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ enrollment_id: enrollmentId, student_id: studentId, status: 'active' })
        })
        .then(r => r.json())
        .then(resp => {
            if (resp.success) { loadEnrollments(); location.reload(); } else { alert(resp.message || 'Failed'); }
        })
        .catch(err => { console.error(err); alert('Request failed'); });
    });

    // Create new enrollment flows
    const newAY = document.getElementById('newAcademicSession');
    const newTerm = document.getElementById('newTerm');
    const newSession = document.getElementById('newSession');
    const createBtn = document.getElementById('createEnrollmentBtn');
    if (newAY) {
        newAY.addEventListener('change', function(){
            const ay = this.value;
            newTerm.innerHTML = '<option value="">Loading...</option>';
            if (!ay) { newTerm.innerHTML = '<option value="">Select Term</option>'; return; }
            fetch('<?= e(base_url('admin/semester/terms')) ?>?session_id=' + encodeURIComponent(ay))
                .then(r => r.json())
                .then(data => {
                    newTerm.innerHTML = '<option value="">Select Term</option>';
                    if (Array.isArray(data)) data.forEach(t => { const o = document.createElement('option'); o.value = t.id; o.textContent = t.name; newTerm.appendChild(o); });
                })
                .catch(err => { console.error(err); newTerm.innerHTML = '<option value="">Select Term</option>'; });
        });
    }

    if (createBtn) {
        createBtn.addEventListener('click', function(){
            const studentId = <?= (int)$student['id'] ?>;
            const ay = newAY.value;
            const term = newTerm.value;
            const session = newSession.value;
            if (!ay || !term || !session) { alert('Select academic year, term, and session'); return; }
            const body = new URLSearchParams({ student_id: studentId, academic_session_id: ay, term_id: term, session_id: session, programme_id: <?= (int)($student['programme_id'] ?? 0) ?>, intake_id: '' });
            fetch('<?= e(base_url('admin/students/create-enrollment')) ?>', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
                                            .then(async r => {
                                                const ct = r.headers.get('content-type') || '';
                                                if (ct.indexOf('application/json') !== -1) return r.json();
                                                const text = await r.text(); throw new Error('Server returned non-JSON response:\n' + text);
                                            })
                                            .then(resp => {
                                                if (resp.success) { loadEnrollments(studentId, listArea); alert('Enrollment created'); location.reload(); } else { alert(resp.message || 'Failed'); }
                                            })
                                            .catch(err => { console.error(err); alert('Request failed: ' + (err.message || '')); });
        });
    }

    function escapeHtml(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

});
</script>
