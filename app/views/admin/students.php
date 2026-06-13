<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <div>
                <h1 class="h4 fw-bold mb-1">Student Accounts</h1>
                <p class="text-muted mb-0">Assign or generate admission numbers for student portal access.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= e(base_url('admin/students/export')) ?>" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Download Excel
                </a>
                <?php if (Auth::canManageEntity('students')): ?>
                <form method="POST" action="<?= e(base_url('admin/students/bulk-assign')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary">
                        <i class="bi bi-magic me-1"></i>Generate Missing Admission Numbers
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <p class="text-muted small mb-3">
            Admission number format template (editable in Settings): <code><?= e($admissionNumberFormat) ?></code>
            using placeholders {YEAR}, {YY}, {MM}, {DD}, {SEQ4}, {SEQ5}, {SEQ6}, {ID}.
        </p>
        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th class="col-xs">ID</th>
                        <th class="col-md">Name</th>
                        <th class="col-lg">Email</th>
                        <th class="col-md">Admission Number</th>
                        <th class="col-md">Status</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="col-xs"><?= (int)$row['id'] ?></td>
                            <td class="col-md" title="<?= e((string)$row['name']) ?>">
                                <?= !empty($row['is_suspended']) ? '<span class="text-danger fw-bold">' . e((string)$row['name']) . '</span>' : e((string)$row['name']) ?>
                            </td>
                            <td class="col-lg" title="<?= e((string)$row['email']) ?>"><?= e((string)$row['email']) ?></td>
                            <td class="col-md" title="<?= e((string)($row['admission_number'] ?? 'Not assigned')) ?>"><strong><?= e((string)($row['admission_number'] ?? 'Not assigned')) ?></strong></td>
                            <td class="col-md">
                                <?php if (!empty($row['is_suspended'])): ?>
                                    <span class="badge bg-danger">Suspended</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="col-actions">
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-action-view" data-bs-toggle="modal" data-bs-target="#viewStudentModal" data-student-id="<?= (int)$row['id'] ?>" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if (Auth::canManageEntity('students')): ?>
                                    <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#editStudentModal" data-student-id="<?= (int)$row['id'] ?>" title="Edit Student">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action-suspend" data-bs-toggle="modal" data-bs-target="#suspendStudentModal" data-student-id="<?= (int)$row['id'] ?>" data-student-name="<?= e((string)$row['name']) ?>" data-is-suspended="<?= !empty($row['is_suspended']) ? '1' : '0' ?>" title="<?= !empty($row['is_suspended']) ? 'Activate Account' : 'Suspend Account' ?>">
                                        <i class="bi <?= !empty($row['is_suspended']) ? 'bi-person-check' : 'bi-person-x' ?>"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action-reset" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-student-id="<?= (int)$row['id'] ?>" data-student-name="<?= e((string)$row['name']) ?>" data-student-email="<?= e((string)$row['email']) ?>" title="Reset Password">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action-edit <?= !empty($row['admission_number']) ? 'disabled' : '' ?>" <?= !empty($row['admission_number']) ? 'disabled' : '' ?> data-bs-toggle="modal" data-bs-target="#assignAdmissionModal" data-student-id="<?= (int)$row['id'] ?>" data-admission-number="<?= e((string)($row['admission_number'] ?? '')) ?>" title="<?= !empty($row['admission_number']) ? 'Admission Number Already Assigned' : 'Assign Admission Number' ?>">
                                        <i class="bi bi-person-badge"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action-delete" data-bs-toggle="modal" data-bs-target="#deleteStudentModal" data-student-id="<?= (int)$row['id'] ?>" data-student-name="<?= e((string)$row['name']) ?>" title="Delete Student">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- View Student Modal -->
        <div class="modal fade" id="viewStudentModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Student Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="viewStudentContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Student Modal -->
        <div class="modal fade" id="editStudentModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="<?= e(base_url('admin/students/edit')) ?>">
                        <?= csrf_field() ?>
                        <div class="modal-body" id="editStudentContent">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Suspend Student Modal -->
        <div class="modal fade" id="suspendStudentModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="suspendModalTitle">Suspend Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="<?= e(base_url('admin/students/suspend')) ?>">
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <input type="hidden" name="student_id" id="suspendStudentId">
                            <input type="hidden" name="is_suspended" id="suspendIsSuspended">
                            <p id="suspendModalMessage">Are you sure you want to suspend this student? They will not be able to log in.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="suspendSubmitBtn">Suspend</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Student Modal -->
        <div class="modal fade" id="deleteStudentModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="<?= e(base_url('admin/students/delete')) ?>">
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <input type="hidden" name="student_id" id="deleteStudentId">
                            <p>Are you sure you want to delete <strong id="deleteStudentName"></strong>? This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reset Password Modal -->
        <div class="modal fade" id="resetPasswordModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reset Student Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="<?= e(base_url('admin/students/reset-password')) ?>">
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <input type="hidden" name="student_id" id="resetStudentId">
                            <div class="mb-3">
                                <label class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="resetStudentName" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Student Email</label>
                                <input type="email" class="form-control" id="resetStudentEmail" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_password" required minlength="6">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assign Admission Number Modal -->
        <div class="modal fade" id="assignAdmissionModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Admission Number</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="<?= e(base_url('admin/students/assign')) ?>" id="assignAdmissionForm">
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <input type="hidden" name="student_id" id="assignStudentId">
                            <div class="mb-3">
                                <label class="form-label">Admission Number</label>
                                <input type="text" class="form-control" name="admission_number" id="assignAdmissionNumber" placeholder="Leave blank to auto-generate">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resetPasswordModal = document.getElementById('resetPasswordModal');
            const assignAdmissionModal = document.getElementById('assignAdmissionModal');
            const viewStudentModal = document.getElementById('viewStudentModal');
            const editStudentModal = document.getElementById('editStudentModal');
            const suspendStudentModal = document.getElementById('suspendStudentModal');
            const deleteStudentModal = document.getElementById('deleteStudentModal');

            if (resetPasswordModal) {
                resetPasswordModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('resetStudentId').value = button.getAttribute('data-student-id');
                    document.getElementById('resetStudentName').value = button.getAttribute('data-student-name');
                    document.getElementById('resetStudentEmail').value = button.getAttribute('data-student-email');
                });
            }

            if (assignAdmissionModal) {
                assignAdmissionModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('assignStudentId').value = button.getAttribute('data-student-id');
                    document.getElementById('assignAdmissionNumber').value = button.getAttribute('data-admission-number');
                });
            }

            if (viewStudentModal) {
                viewStudentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const studentId = button.getAttribute('data-student-id');
                    fetch('<?= e(base_url('admin/students/view')) ?>?id=' + studentId, {
                        credentials: 'same-origin'
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(html => {
                            document.getElementById('viewStudentContent').innerHTML = html;
                        })
                        .catch(() => {
                            document.getElementById('viewStudentContent').innerHTML = '<p class="text-danger">Failed to load student details.</p>';
                        });
                });
            }

            // Initialize edit modal loader
            if (editStudentModal) {
                editStudentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const studentId = button.getAttribute('data-student-id');
                    fetch('<?= e(base_url('admin/students/edit-form')) ?>?id=' + studentId, {
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        document.getElementById('editStudentContent').innerHTML = html;
                        try { initStudentEditContent(studentId); } catch (e) { console.error('initStudentEditContent error', e); }
                    })
                    .catch(() => {
                        document.getElementById('editStudentContent').innerHTML = '<p class="text-danger">Failed to load edit form.</p>';
                    });
                });
            }

            function initStudentEditContent(studentId) {
                // Initialize the enrollment management UI inside the loaded edit content
                try {
                    const editContent = document.getElementById('editStudentContent');
                    if (!editContent) return;
                    const manageBtn = editContent.querySelector('#manageEnrollmentBtn');
                    const modal = editContent.querySelector('#enrollmentManageModal');
                    const listArea = editContent.querySelector('#enrollmentListArea');
                    const newAY = editContent.querySelector('#newAcademicSession');
                    const newTerm = editContent.querySelector('#newTerm');
                    const newSession = editContent.querySelector('#newSession');
                    const createBtn = editContent.querySelector('#createEnrollmentBtn');

                    if (manageBtn && modal) {
                        manageBtn.addEventListener('click', function(){
                            const bsModal = new bootstrap.Modal(modal);
                            bsModal.show();
                            loadEnrollments(studentId, listArea);
                        });
                    }

                    if (newAY && newTerm) {
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
                            const ay = newAY.value;
                            const term = newTerm.value;
                            const session = newSession.value;
                            if (!ay || !term || !session) { alert('Select academic year, term, and session'); return; }
                            const body = new URLSearchParams({ student_id: studentId, academic_session_id: ay, term_id: term, session_id: session, programme_id: editContent.querySelector('[name="programme_id"]') ? editContent.querySelector('[name="programme_id"]').value : '' , intake_id: '' });
                            fetch('<?= e(base_url('admin/students/create-enrollment')) ?>', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
                                .then(r => r.json())
                                .then(resp => {
                                    if (resp.success) { loadEnrollments(studentId, listArea); alert('Enrollment created'); location.reload(); } else { alert(resp.message || 'Failed'); }
                                })
                                .catch(err => { console.error(err); alert('Request failed'); });
                        });
                    }

                    if (listArea) {
                        listArea.addEventListener('click', function(e){
                            const btn = e.target.closest('.btn-reactivate');
                            if (!btn) return;
                            const enrollmentId = btn.getAttribute('data-id');
                            if (!enrollmentId) return;
                            if (!confirm('Set this enrollment as active?')) return;
                            fetch('<?= e(base_url('admin/students/set-enrollment-status')) ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: new URLSearchParams({ enrollment_id: enrollmentId, student_id: studentId, status: 'active' })
                            })
                            .then(r => r.json())
                            .then(resp => {
                                if (resp.success) { loadEnrollments(studentId, listArea); location.reload(); } else { alert(resp.message || 'Failed'); }
                            })
                            .catch(err => { console.error(err); alert('Request failed'); });
                        });
                    }

                } catch (e) { console.error('initStudentEditContent', e); }
            }

            function loadEnrollments(studentId, listArea){
                if (!listArea) return;
                listArea.innerHTML = 'Loading...';
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
                                actions += `<button class="btn btn-sm btn-primary me-1 btn-reactivate" data-id="${row.id}" data-student="${studentId}">Make Active</button>`;
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


            if (suspendStudentModal) {
                suspendStudentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const studentId = button.getAttribute('data-student-id');
                    const studentName = button.getAttribute('data-student-name');
                    const isSuspended = button.getAttribute('data-is-suspended') === '1';

                    document.getElementById('suspendStudentId').value = studentId;
                    document.getElementById('suspendIsSuspended').value = isSuspended ? '0' : '1';
                    document.getElementById('deleteStudentName').textContent = studentName;

                    if (isSuspended) {
                        document.getElementById('suspendModalTitle').textContent = 'Activate Student';
                        document.getElementById('suspendModalMessage').textContent = 'Are you sure you want to activate this student? They will be able to log in.';
                        document.getElementById('suspendSubmitBtn').textContent = 'Activate';
                        document.getElementById('suspendSubmitBtn').className = 'btn btn-success';
                    } else {
                        document.getElementById('suspendModalTitle').textContent = 'Suspend Student';
                        document.getElementById('suspendModalMessage').textContent = 'Are you sure you want to suspend this student? They will not be able to log in.';
                        document.getElementById('suspendSubmitBtn').textContent = 'Suspend';
                        document.getElementById('suspendSubmitBtn').className = 'btn btn-warning';
                    }
                });
            }

            if (deleteStudentModal) {
                deleteStudentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('deleteStudentId').value = button.getAttribute('data-student-id');
                    document.getElementById('deleteStudentName').textContent = button.getAttribute('data-student-name');
                });
            }
        });
        </script>
    </div>
</section>
