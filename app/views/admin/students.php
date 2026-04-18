<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <div>
                <h1 class="h4 fw-bold mb-1">Student Accounts</h1>
                <p class="text-muted mb-0">Assign or generate admission numbers for student portal access.</p>
            </div>
            <form method="POST" action="<?= e(base_url('admin/students/bulk-assign')) ?>">
                <button class="btn btn-primary">
                    <i class="bi bi-magic me-1"></i>Generate Missing Admission Numbers
                </button>
            </form>
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
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Admission Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= (int)$row['id'] ?></td>
                            <td><?= e((string)$row['name']) ?></td>
                            <td><?= e((string)$row['email']) ?></td>
                            <td><strong><?= e((string)($row['admission_number'] ?? 'Not assigned')) ?></strong></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-action-reset" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-student-id="<?= (int)$row['id'] ?>" data-student-name="<?= e((string)$row['name']) ?>" data-student-email="<?= e((string)$row['email']) ?>" title="Reset Password">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#assignAdmissionModal" data-student-id="<?= (int)$row['id'] ?>" data-admission-number="<?= e((string)($row['admission_number'] ?? '')) ?>" title="Assign Admission Number">
                                        <i class="bi bi-person-badge"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
                    <form method="POST" action="<?= e(base_url('admin/students/assign')) ?>">
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
        });
        </script>
    </div>
</section>
