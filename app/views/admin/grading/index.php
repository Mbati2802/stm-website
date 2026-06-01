<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-1">Grading System</h1>
            <p class="text-muted mb-0">Manage exam types, grading systems, and grade ranges.</p>
        </div>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <!-- Exam Types Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Exam Types</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#examTypeModal">
                    <i class="bi bi-plus"></i> Add Exam Type
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Max Marks</th>
                                <th>Parent Exams</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examTypes as $type): ?>
                            <tr>
                                <td><span class="badge bg-light text-dark"><?= (int)$type['id'] ?></span></td>
                                <td><?= e((string)$type['name']) ?></td>
                                <td><code><?= e((string)$type['code']) ?></code></td>
                                <td>
                                    <span class="badge <?= $type['type'] === 'consolidated' ? 'bg-info' : 'bg-secondary' ?>">
                                        <?= e((string)$type['type']) ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-light text-dark"><?= (float)($type['max_marks'] ?? 100) ?></span></td>
                                <td>
                                    <?php if ($type['type'] === 'consolidated' && !empty($type['parent_exam_ids'])): ?>
                                        <small class="text-muted"><?= e($type['parent_exam_ids']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $type['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $type['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#examTypeModal" 
                                            data-id="<?= (int)$type['id'] ?>" 
                                            data-name="<?= e((string)$type['name']) ?>" 
                                            data-code="<?= e((string)$type['code']) ?>" 
                                            data-type="<?= e((string)$type['type']) ?>" 
                                            data-max-marks="<?= (float)($type['max_marks'] ?? 100) ?>"
                                            data-display-mode="<?= e((string)($type['display_mode'] ?? 'converted')) ?>"
                                            data-parent-exam-ids="<?= e((string)($type['parent_exam_ids'] ?? '')) ?>" 
                                            data-description="<?= e((string)($type['description'] ?? '')) ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="<?= e(base_url('admin/grading/exam-type/delete')) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$type['id'] ?>">
                                        <button class="btn btn-sm btn-action-delete" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Exams Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Exams</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#examModal">
                    <i class="bi bi-plus"></i> Add Exam
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Exam Type</th>
                                <th>Description</th>
                                <th>Default</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gradingSystems as $exam): ?>
                            <tr>
                                <td><?= e((string)$exam['name']) ?></td>
                                <td><?= e((string)($exam['exam_type_name'] ?? '-')) ?></td>
                                <td><?= e((string)($exam['description'] ?? '-')) ?></td>
                                <td>
                                    <?php if ($exam['is_default']): ?>
                                    <span class="badge bg-primary">Default</span>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $exam['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $exam['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#examModal" 
                                            data-id="<?= (int)$exam['id'] ?>" 
                                            data-name="<?= e((string)$exam['name']) ?>" 
                                            data-exam-type-id="<?= (int)$exam['exam_type_id'] ?>" 
                                            data-description="<?= e((string)($exam['description'] ?? '')) ?>" 
                                            data-is-default="<?= (int)$exam['is_default'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action-view" onclick="loadGradeRanges(<?= (int)$exam['id'] ?>)">
                                        <i class="bi bi-list"></i> View Grades
                                    </button>
                                    <form method="POST" action="<?= e(base_url('admin/grading/grading-system/delete')) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$exam['id'] ?>">
                                        <button class="btn btn-sm btn-action-delete" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Grade Ranges Section -->
        <div class="card mb-4" id="gradeRangesCard">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0">Grade Ranges</h5>
                    <select class="form-select form-select-sm" id="gradingSystemSelector" style="max-width: 300px;">
                        <option value="">Select Exam</option>
                        <?php foreach ($gradingSystems as $exam): ?>
                        <option value="<?= (int)$exam['id'] ?>" <?= $exam['is_default'] ? 'selected' : '' ?>>
                            <?= e((string)$exam['name']) ?> (<?= e((string)($exam['exam_type_name'] ?? 'All')) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkApplyModal" id="bulkApplyBtn" style="display: none;">
                        <i class="bi bi-files"></i> Apply to Others
                    </button>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#gradeRangeModal">
                        <i class="bi bi-plus"></i> Add Grade Range
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Grade</th>
                                <th>Min Marks</th>
                                <th>Max Marks</th>
                                <th>Remarks</th>
                                <th>GPA Value</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="gradeRangesTable">
                            <tr><td colspan="6" class="text-center">Select an exam to view grade ranges</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Type Modal -->
    <div class="modal fade" id="examTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="examTypeModalTitle">Add Exam Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= e(base_url('admin/grading/exam-type/create')) ?>">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="examTypeId">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="examTypeName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="code" id="examTypeCode" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type" id="examTypeType">
                                <option value="single">Single</option>
                                <option value="consolidated">Consolidated</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maximum Marks</label>
                            <input type="number" class="form-control" name="max_marks" id="examTypeMaxMarks" value="100" min="1" max="100" step="0.01" required>
                            <small class="text-muted">Maximum marks for this exam type (e.g., CW=10, EX=40, TOTAL=100)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Display Mode</label>
                            <select class="form-select" name="display_mode" id="examTypeDisplayMode">
                                <option value="converted">Show Converted Marks (e.g., 5/10)</option>
                                <option value="percentage">Show Percentage Only (e.g., 50%)</option>
                                <option value="both">Show Both</option>
                            </select>
                            <small class="text-muted">How marks appear on transcripts and result slips</small>
                        </div>
                        <div class="mb-3" id="parentExamIdsDiv" style="display: none;">
                            <label class="form-label">Parent Exam IDs (JSON array)</label>
                            <input type="text" class="form-control" name="parent_exam_ids" id="parentExamIds" placeholder='[1, 2, 3]'>
                            <small class="text-muted">
                                For consolidated exams, enter IDs from the table above (e.g., [1, 2, 3]). 
                                <br>Available exam IDs: 
                                <?php 
                                $singleExams = array_filter($examTypes, fn($t) => $t['type'] === 'single');
                                echo implode(', ', array_map(fn($t) => $t['id'] . '=' . $t['code'], array_slice($singleExams, 0, 5)));
                                if (count($singleExams) > 5) echo '...';
                                ?>
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="examTypeDescription" rows="3"></textarea>
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

    <!-- Exam Modal -->
    <div class="modal fade" id="examModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="examModalTitle">Add Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= e(base_url('admin/grading/grading-system/create')) ?>">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="examId">
                        <div class="mb-3">
                            <label class="form-label">Exam Name</label>
                            <input type="text" class="form-control" name="name" id="examName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Exam Type</label>
                            <select class="form-select" name="exam_type_id" id="examExamTypeId" required>
                                <option value="">Select Exam Type</option>
                                <?php foreach ($examTypes as $type): ?>
                                <option value="<?= (int)$type['id'] ?>"><?= e((string)$type['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="examDescription" rows="3"></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" id="examIsDefault" value="1">
                            <label class="form-check-label" for="examIsDefault">Set as default for this exam type</label>
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

    <!-- Grade Range Modal -->
    <div class="modal fade" id="gradeRangeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradeRangeModalTitle">Add Grade Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= e(base_url('admin/grading/grade-range/add')) ?>">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="gradeRangeId">
                        <input type="hidden" name="grading_system_id" id="gradeRangeGradingSystemId">
                        <div class="mb-3">
                            <label class="form-label">Grade Letter</label>
                            <input type="text" class="form-control" name="grade_letter" id="gradeRangeLetter" required maxlength="2" placeholder="A">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Min Marks</label>
                                <input type="number" class="form-control" name="min_marks" id="gradeRangeMinMarks" required step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Marks</label>
                                <input type="number" class="form-control" name="max_marks" id="gradeRangeMaxMarks" required step="0.01" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <input type="text" class="form-control" name="remarks" id="gradeRangeRemarks" placeholder="Excellent">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">GPA Value (Optional)</label>
                            <input type="number" class="form-control" name="gpa_value" id="gradeRangeGpaValue" step="0.01" min="0" max="5">
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

    <!-- Bulk Apply Grade Ranges Modal -->
    <div class="modal fade" id="bulkApplyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply Grade Ranges to Other Exams</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= e(base_url('admin/grading/grade-range/bulk-apply')) ?>">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <input type="hidden" name="source_grading_system_id" id="bulkApplySourceId">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> This will copy all grade ranges from <strong id="bulkApplySourceName">current exam</strong> to the selected exams below.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Exams to Apply To:</label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach ($gradingSystems as $exam): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="target_grading_system_ids[]" value="<?= (int)$exam['id'] ?>" id="target_exam_<?= (int)$exam['id'] ?>">
                                    <label class="form-check-label" for="target_exam_<?= (int)$exam['id'] ?>">
                                        <?= e((string)$exam['name']) ?> (<?= e((string)($exam['exam_type_name'] ?? 'All')) ?>)
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="replace_existing" value="1" id="replaceExisting">
                            <label class="form-check-label" for="replaceExisting">
                                Replace existing grade ranges (will delete current ranges before applying new ones)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Apply Grade Ranges</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    let currentGradingSystemId = 0;

    document.addEventListener('DOMContentLoaded', function() {
        const examTypeModal = document.getElementById('examTypeModal');
        const examModal = document.getElementById('examModal');
        const gradeRangeModal = document.getElementById('gradeRangeModal');
        const gradingSystemSelector = document.getElementById('gradingSystemSelector');

        // Load grade ranges for default exam on page load
        if (gradingSystemSelector) {
            const defaultSystemId = gradingSystemSelector.value;
            if (defaultSystemId) {
                loadGradeRanges(defaultSystemId);
            }
            
            // Load grade ranges when selector changes
            gradingSystemSelector.addEventListener('change', function() {
                const systemId = this.value;
                if (systemId) {
                    loadGradeRanges(systemId);
                    // Show bulk apply button when a grading system is selected
                    document.getElementById('bulkApplyBtn').style.display = 'inline-block';
                } else {
                    document.getElementById('gradeRangesTable').innerHTML = '<tr><td colspan="6" class="text-center">Select an exam to view grade ranges</td></tr>';
                    document.getElementById('bulkApplyBtn').style.display = 'none';
                }
            });
        }

        // Bulk Apply Modal
        const bulkApplyModal = document.getElementById('bulkApplyModal');
        if (bulkApplyModal) {
            bulkApplyModal.addEventListener('show.bs.modal', function() {
                const sourceId = currentGradingSystemId;
                const sourceName = gradingSystemSelector.options[gradingSystemSelector.selectedIndex]?.text || 'current exam';
                document.getElementById('bulkApplySourceId').value = sourceId;
                document.getElementById('bulkApplySourceName').textContent = sourceName;
                
                // Uncheck the current exam in the target list (can't apply to self)
                const currentCheckbox = document.getElementById('target_exam_' + sourceId);
                if (currentCheckbox) {
                    currentCheckbox.checked = false;
                    currentCheckbox.disabled = true;
                }
            });
        }

        // Exam Type Modal
        if (examTypeModal) {
            examTypeModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const examTypeType = document.getElementById('examTypeType');
                const parentExamIdsDiv = document.getElementById('parentExamIdsDiv');

                const form = examTypeModal.querySelector('form');
                if (id) {
                    document.getElementById('examTypeModalTitle').textContent = 'Edit Exam Type';
                    document.getElementById('examTypeId').value = id;
                    document.getElementById('examTypeName').value = button.getAttribute('data-name');
                    document.getElementById('examTypeCode').value = button.getAttribute('data-code');
                    examTypeType.value = button.getAttribute('data-type');
                    document.getElementById('examTypeMaxMarks').value = button.getAttribute('data-max-marks') || '100';
                    document.getElementById('examTypeDisplayMode').value = button.getAttribute('data-display-mode') || 'converted';
                    document.getElementById('parentExamIds').value = button.getAttribute('data-parent-exam-ids') || '';
                    document.getElementById('examTypeDescription').value = button.getAttribute('data-description') || '';
                    // Change form action to edit endpoint
                    form.action = '<?= e(base_url('admin/grading/exam-type/edit')) ?>';
                } else {
                    document.getElementById('examTypeModalTitle').textContent = 'Add Exam Type';
                    document.getElementById('examTypeId').value = '';
                    document.getElementById('examTypeName').value = '';
                    document.getElementById('examTypeCode').value = '';
                    examTypeType.value = 'single';
                    document.getElementById('examTypeMaxMarks').value = '100';
                    document.getElementById('examTypeDisplayMode').value = 'converted';
                    document.getElementById('parentExamIds').value = '';
                    document.getElementById('examTypeDescription').value = '';
                    // Change form action to create endpoint
                    form.action = '<?= e(base_url('admin/grading/exam-type/create')) ?>';
                }

                // Show/hide parent exam IDs based on type
                examTypeType.addEventListener('change', function() {
                    parentExamIdsDiv.style.display = this.value === 'consolidated' ? 'block' : 'none';
                });
                parentExamIdsDiv.style.display = examTypeType.value === 'consolidated' ? 'block' : 'none';
            });
        }

        // Exam Modal
        if (examModal) {
            examModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');

                if (id) {
                    document.getElementById('examModalTitle').textContent = 'Edit Exam';
                    document.getElementById('examId').value = id;
                    document.getElementById('examName').value = button.getAttribute('data-name');
                    document.getElementById('examExamTypeId').value = button.getAttribute('data-exam-type-id');
                    document.getElementById('examDescription').value = button.getAttribute('data-description') || '';
                    document.getElementById('examIsDefault').checked = button.getAttribute('data-is-default') === '1';
                } else {
                    document.getElementById('examModalTitle').textContent = 'Add Exam';
                    document.getElementById('examId').value = '';
                    document.getElementById('examName').value = '';
                    document.getElementById('examExamTypeId').value = '';
                    document.getElementById('examDescription').value = '';
                    document.getElementById('examIsDefault').checked = false;
                }
            });
        }

        // Grade Range Modal
        if (gradeRangeModal) {
            gradeRangeModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');

                document.getElementById('gradeRangeGradingSystemId').value = currentGradingSystemId;

                if (id) {
                    document.getElementById('gradeRangeModalTitle').textContent = 'Edit Grade Range';
                    document.getElementById('gradeRangeId').value = id;
                    document.getElementById('gradeRangeLetter').value = button.getAttribute('data-grade-letter');
                    document.getElementById('gradeRangeMinMarks').value = button.getAttribute('data-min-marks');
                    document.getElementById('gradeRangeMaxMarks').value = button.getAttribute('data-max-marks');
                    document.getElementById('gradeRangeRemarks').value = button.getAttribute('data-remarks') || '';
                    document.getElementById('gradeRangeGpaValue').value = button.getAttribute('data-gpa-value') || '';
                } else {
                    document.getElementById('gradeRangeModalTitle').textContent = 'Add Grade Range';
                    document.getElementById('gradeRangeId').value = '';
                    document.getElementById('gradeRangeLetter').value = '';
                    document.getElementById('gradeRangeMinMarks').value = '';
                    document.getElementById('gradeRangeMaxMarks').value = '';
                    document.getElementById('gradeRangeRemarks').value = '';
                    document.getElementById('gradeRangeGpaValue').value = '';
                }
            });
        }
    });

    function loadGradeRanges(gradingSystemId) {
        currentGradingSystemId = gradingSystemId;
        fetch('<?= e(base_url('admin/grading/grade-ranges')) ?>?grading_system_id=' + gradingSystemId)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const table = document.getElementById('gradeRangesTable');
                    table.innerHTML = '';
                    if (result.data.length === 0) {
                        table.innerHTML = '<tr><td colspan="6" class="text-center">No grade ranges defined for this exam</td></tr>';
                        return;
                    }
                    result.data.forEach(range => {
                        table.innerHTML += `
                            <tr>
                                <td><strong>${range.grade_letter}</strong></td>
                                <td>${range.min_marks}</td>
                                <td>${range.max_marks}</td>
                                <td>${range.remarks || '-'}</td>
                                <td>${range.gpa_value || '-'}</td>
                                <td>
                                    <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#gradeRangeModal" 
                                            data-id="${range.id}" 
                                            data-grade-letter="${range.grade_letter}" 
                                            data-min-marks="${range.min_marks}" 
                                            data-max-marks="${range.max_marks}" 
                                            data-remarks="${range.remarks || ''}" 
                                            data-gpa-value="${range.gpa_value || ''}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="<?= e(base_url('admin/grading/grade-range/delete')) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="${range.id}">
                                        <button class="btn btn-sm btn-action-delete" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
    }

    function hideGradeRanges() {
        document.getElementById('gradeRangesCard').style.display = 'none';
    }
    </script>
</section>
