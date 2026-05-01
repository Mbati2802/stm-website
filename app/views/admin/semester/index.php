<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Semester Management</h1>
            <p class="text-muted mb-0">Manage academic sessions, terms, and intakes.</p>
        </div>
    </div>

    <!-- Academic Sessions Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Academic Sessions</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#sessionModal">
                <i class="bi bi-plus"></i> Add Session
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($academicSessions as $session): ?>
                        <tr>
                            <td><?= e($session['name']) ?></td>
                            <td><span class="badge bg-secondary"><?= e($session['code']) ?></span></td>
                            <td><?= e(date('M d, Y', strtotime($session['start_date']))) ?></td>
                            <td><?= e(date('M d, Y', strtotime($session['end_date']))) ?></td>
                            <td>
                                <?php if ($session['is_current']): ?>
                                    <span class="badge bg-success">Current</span>
                                <?php endif; ?>
                                <?php if ($session['is_active']): ?>
                                    <span class="badge bg-primary">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#sessionModal"
                                    data-id="<?= e((string)$session['id']) ?>"
                                    data-name="<?= e($session['name']) ?>"
                                    data-code="<?= e($session['code']) ?>"
                                    data-start-date="<?= e($session['start_date']) ?>"
                                    data-end-date="<?= e($session['end_date']) ?>"
                                    data-is-current="<?= e((string)$session['is_current']) ?>"
                                    data-description="<?= e($session['description'] ?? '') ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-action-delete" onclick="confirmDelete('session', <?= e((string)$session['id']) ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Terms Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Terms</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#termModal">
                <i class="bi bi-plus"></i> Add Term
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Session</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($terms as $term): ?>
                        <tr>
                            <td><?= e($term['session_name'] ?? 'N/A') ?> (<?= e($term['session_code'] ?? '') ?>)</td>
                            <td><?= e($term['name']) ?></td>
                            <td><span class="badge bg-secondary"><?= e($term['code']) ?></span></td>
                            <td><?= e(date('M d, Y', strtotime($term['start_date']))) ?></td>
                            <td><?= e(date('M d, Y', strtotime($term['end_date']))) ?></td>
                            <td>
                                <?php if ($term['is_current']): ?>
                                    <span class="badge bg-success">Current</span>
                                <?php endif; ?>
                                <?php if ($term['is_active']): ?>
                                    <span class="badge bg-primary">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#termModal"
                                    data-id="<?= e((string)$term['id']) ?>"
                                    data-session-id="<?= e((string)$term['academic_session_id']) ?>"
                                    data-name="<?= e($term['name']) ?>"
                                    data-code="<?= e($term['code']) ?>"
                                    data-start-date="<?= e($term['start_date']) ?>"
                                    data-end-date="<?= e($term['end_date']) ?>"
                                    data-is-current="<?= e((string)$term['is_current']) ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-action-delete" onclick="confirmDelete('term', <?= e((string)$term['id']) ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Intakes Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Admission Intakes</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#intakeModal">
                <i class="bi bi-plus"></i> Add Intake
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($intakes as $intake): ?>
                        <tr>
                            <td><?= e($intake['name']) ?></td>
                            <td><span class="badge bg-secondary"><?= e($intake['code']) ?></span></td>
                            <td><?= e(date('M d, Y', strtotime($intake['start_date']))) ?></td>
                            <td><?= e($intake['end_date'] ? date('M d, Y', strtotime($intake['end_date'])) : 'Ongoing') ?></td>
                            <td>
                                <?php if ($intake['is_active']): ?>
                                    <span class="badge bg-primary">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#intakeModal"
                                    data-id="<?= e((string)$intake['id']) ?>"
                                    data-name="<?= e($intake['name']) ?>"
                                    data-code="<?= e($intake['code']) ?>"
                                    data-start-date="<?= e($intake['start_date']) ?>"
                                    data-end-date="<?= e($intake['end_date'] ?? '') ?>"
                                    data-description="<?= e($intake['description'] ?? '') ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-action-delete" onclick="confirmDelete('intake', <?= e((string)$intake['id']) ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Academic Session Modal -->
<div class="modal fade" id="sessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessionModalTitle">Add Academic Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= e(base_url('admin/semester/session/create')) ?>">
                <div class="modal-body">
                    <input type="hidden" name="id" id="sessionId">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="sessionName" required placeholder="e.g., 2024-2025">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" id="sessionCode" required placeholder="e.g., 2024-2025">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="sessionStartDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" id="sessionEndDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="sessionDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_current" id="sessionIsCurrent" value="1">
                            <label class="form-check-label" for="sessionIsCurrent">Set as current session</label>
                        </div>
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

<!-- Term Modal -->
<div class="modal fade" id="termModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termModalTitle">Add Term</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= e(base_url('admin/semester/term/create')) ?>">
                <div class="modal-body">
                    <input type="hidden" name="id" id="termId">
                    <div class="mb-3">
                        <label class="form-label">Academic Session</label>
                        <select class="form-select" name="academic_session_id" id="termSessionId" required>
                            <option value="">Select session</option>
                            <?php foreach ($academicSessions as $session): ?>
                            <option value="<?= e((string)$session['id']) ?>"><?= e($session['name']) ?> (<?= e($session['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="termName" required placeholder="e.g., Term 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" id="termCode" required placeholder="e.g., T1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="termStartDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" id="termEndDate" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_current" id="termIsCurrent" value="1">
                            <label class="form-check-label" for="termIsCurrent">Set as current term</label>
                        </div>
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

<!-- Intake Modal -->
<div class="modal fade" id="intakeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="intakeModalTitle">Add Intake</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= e(base_url('admin/semester/intake/create')) ?>">
                <div class="modal-body">
                    <input type="hidden" name="id" id="intakeId">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="intakeName" required placeholder="e.g., January Intake">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" id="intakeCode" required placeholder="e.g., JAN">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="intakeStartDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" id="intakeEndDate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="intakeDescription" rows="3"></textarea>
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
function confirmDelete(type, id) {
    if (confirm('Are you sure you want to delete this ' + type + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= e(base_url('admin/semester/' . type . '/delete')) ?>';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Session Modal
const sessionModal = document.getElementById('sessionModal');
if (sessionModal) {
    sessionModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (button && button.hasAttribute('data-id')) {
            document.getElementById('sessionModalTitle').textContent = 'Edit Academic Session';
            document.getElementById('sessionId').value = button.getAttribute('data-id');
            document.getElementById('sessionName').value = button.getAttribute('data-name');
            document.getElementById('sessionCode').value = button.getAttribute('data-code');
            document.getElementById('sessionStartDate').value = button.getAttribute('data-start-date');
            document.getElementById('sessionEndDate').value = button.getAttribute('data-end-date');
            document.getElementById('sessionIsCurrent').checked = button.getAttribute('data-is-current') === '1';
            document.getElementById('sessionDescription').value = button.getAttribute('data-description') || '';
        } else {
            document.getElementById('sessionModalTitle').textContent = 'Add Academic Session';
            document.getElementById('sessionId').value = '';
            document.getElementById('sessionName').value = '';
            document.getElementById('sessionCode').value = '';
            document.getElementById('sessionStartDate').value = '';
            document.getElementById('sessionEndDate').value = '';
            document.getElementById('sessionIsCurrent').checked = false;
            document.getElementById('sessionDescription').value = '';
        }
    });
}

// Term Modal
const termModal = document.getElementById('termModal');
if (termModal) {
    termModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (button && button.hasAttribute('data-id')) {
            document.getElementById('termModalTitle').textContent = 'Edit Term';
            document.getElementById('termId').value = button.getAttribute('data-id');
            document.getElementById('termSessionId').value = button.getAttribute('data-session-id');
            document.getElementById('termName').value = button.getAttribute('data-name');
            document.getElementById('termCode').value = button.getAttribute('data-code');
            document.getElementById('termStartDate').value = button.getAttribute('data-start-date');
            document.getElementById('termEndDate').value = button.getAttribute('data-end-date');
            document.getElementById('termIsCurrent').checked = button.getAttribute('data-is-current') === '1';
        } else {
            document.getElementById('termModalTitle').textContent = 'Add Term';
            document.getElementById('termId').value = '';
            document.getElementById('termSessionId').value = '';
            document.getElementById('termName').value = '';
            document.getElementById('termCode').value = '';
            document.getElementById('termStartDate').value = '';
            document.getElementById('termEndDate').value = '';
            document.getElementById('termIsCurrent').checked = false;
        }
    });
}

// Intake Modal
const intakeModal = document.getElementById('intakeModal');
if (intakeModal) {
    intakeModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (button && button.hasAttribute('data-id')) {
            document.getElementById('intakeModalTitle').textContent = 'Edit Intake';
            document.getElementById('intakeId').value = button.getAttribute('data-id');
            document.getElementById('intakeName').value = button.getAttribute('data-name');
            document.getElementById('intakeCode').value = button.getAttribute('data-code');
            document.getElementById('intakeStartDate').value = button.getAttribute('data-start-date');
            document.getElementById('intakeEndDate').value = button.getAttribute('data-end-date') || '';
            document.getElementById('intakeDescription').value = button.getAttribute('data-description') || '';
        } else {
            document.getElementById('intakeModalTitle').textContent = 'Add Intake';
            document.getElementById('intakeId').value = '';
            document.getElementById('intakeName').value = '';
            document.getElementById('intakeCode').value = '';
            document.getElementById('intakeStartDate').value = '';
            document.getElementById('intakeEndDate').value = '';
            document.getElementById('intakeDescription').value = '';
        }
    });
}
</script>
