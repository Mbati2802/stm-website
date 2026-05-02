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
                                <th>Name</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examTypes as $type): ?>
                            <tr>
                                <td><?= e((string)$type['name']) ?></td>
                                <td><code><?= e((string)$type['code']) ?></code></td>
                                <td>
                                    <span class="badge <?= $type['type'] === 'consolidated' ? 'bg-info' : 'bg-secondary' ?>">
                                        <?= e((string)$type['type']) ?>
                                    </span>
                                </td>
                                <td><?= e((string)($type['description'] ?? '-')) ?></td>
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

        <!-- Grading Systems Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Grading Systems</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#gradingSystemModal">
                    <i class="bi bi-plus"></i> Add Grading System
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Exam Type</th>
                                <th>Description</th>
                                <th>Default</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gradingSystems as $system): ?>
                            <tr>
                                <td><?= e((string)$system['name']) ?></td>
                                <td><?= e((string)($system['exam_type_name'] ?? '-')) ?></td>
                                <td><?= e((string)($system['description'] ?? '-')) ?></td>
                                <td>
                                    <?php if ($system['is_default']): ?>
                                    <span class="badge bg-primary">Default</span>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $system['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $system['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-action-edit" data-bs-toggle="modal" data-bs-target="#gradingSystemModal" 
                                            data-id="<?= (int)$system['id'] ?>" 
                                            data-name="<?= e((string)$system['name']) ?>" 
                                            data-exam-type-id="<?= (int)$system['exam_type_id'] ?>" 
                                            data-description="<?= e((string)($system['description'] ?? '')) ?>" 
                                            data-is-default="<?= (int)$system['is_default'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action-view" onclick="loadGradeRanges(<?= (int)$system['id'] ?>)">
                                        <i class="bi bi-list"></i> View Grades
                                    </button>
                                    <form method="POST" action="<?= e(base_url('admin/grading/grading-system/delete')) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$system['id'] ?>">
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
                <h5 class="mb-0">Grade Ranges</h5>
                <div>
                    <button class="btn btn-sm btn-secondary" onclick="hideGradeRanges()">Close</button>
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
                            <tr><td colspan="6" class="text-center">Select a grading system to view grade ranges</td></tr>
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
                        <div class="mb-3" id="parentExamIdsDiv" style="display: none;">
                            <label class="form-label">Parent Exam IDs (JSON array)</label>
                            <input type="text" class="form-control" name="parent_exam_ids" id="parentExamIds" placeholder='[1, 2, 3]'>
                            <small class="text-muted">For consolidated exams, enter IDs of exams to sum (e.g., [1, 2, 3])</small>
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

    <!-- Grading System Modal -->
    <div class="modal fade" id="gradingSystemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradingSystemModalTitle">Add Grading System</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= e(base_url('admin/grading/grading-system/create')) ?>">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="gradingSystemId">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="gradingSystemName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Exam Type</label>
                            <select class="form-select" name="exam_type_id" id="gradingSystemExamTypeId" required>
                                <option value="">Select Exam Type</option>
                                <?php foreach ($examTypes as $type): ?>
                                <option value="<?= (int)$type['id'] ?>"><?= e((string)$type['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="gradingSystemDescription" rows="3"></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" id="gradingSystemIsDefault" value="1">
                            <label class="form-check-label" for="gradingSystemIsDefault">Set as default for this exam type</label>
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

    <script>
    let currentGradingSystemId = 0;

    document.addEventListener('DOMContentLoaded', function() {
        const examTypeModal = document.getElementById('examTypeModal');
        const gradingSystemModal = document.getElementById('gradingSystemModal');
        const gradeRangeModal = document.getElementById('gradeRangeModal');

        // Exam Type Modal
        if (examTypeModal) {
            examTypeModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const examTypeType = document.getElementById('examTypeType');
                const parentExamIdsDiv = document.getElementById('parentExamIdsDiv');

                if (id) {
                    document.getElementById('examTypeModalTitle').textContent = 'Edit Exam Type';
                    document.getElementById('examTypeId').value = id;
                    document.getElementById('examTypeName').value = button.getAttribute('data-name');
                    document.getElementById('examTypeCode').value = button.getAttribute('data-code');
                    examTypeType.value = button.getAttribute('data-type');
                    document.getElementById('parentExamIds').value = button.getAttribute('data-parent-exam-ids') || '';
                    document.getElementById('examTypeDescription').value = button.getAttribute('data-description') || '';
                } else {
                    document.getElementById('examTypeModalTitle').textContent = 'Add Exam Type';
                    document.getElementById('examTypeId').value = '';
                    document.getElementById('examTypeName').value = '';
                    document.getElementById('examTypeCode').value = '';
                    examTypeType.value = 'single';
                    document.getElementById('parentExamIds').value = '';
                    document.getElementById('examTypeDescription').value = '';
                }

                // Show/hide parent exam IDs based on type
                examTypeType.addEventListener('change', function() {
                    parentExamIdsDiv.style.display = this.value === 'consolidated' ? 'block' : 'none';
                });
                parentExamIdsDiv.style.display = examTypeType.value === 'consolidated' ? 'block' : 'none';
            });
        }

        // Grading System Modal
        if (gradingSystemModal) {
            gradingSystemModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');

                if (id) {
                    document.getElementById('gradingSystemModalTitle').textContent = 'Edit Grading System';
                    document.getElementById('gradingSystemId').value = id;
                    document.getElementById('gradingSystemName').value = button.getAttribute('data-name');
                    document.getElementById('gradingSystemExamTypeId').value = button.getAttribute('data-exam-type-id');
                    document.getElementById('gradingSystemDescription').value = button.getAttribute('data-description') || '';
                    document.getElementById('gradingSystemIsDefault').checked = button.getAttribute('data-is-default') === '1';
                } else {
                    document.getElementById('gradingSystemModalTitle').textContent = 'Add Grading System';
                    document.getElementById('gradingSystemId').value = '';
                    document.getElementById('gradingSystemName').value = '';
                    document.getElementById('gradingSystemExamTypeId').value = '';
                    document.getElementById('gradingSystemDescription').value = '';
                    document.getElementById('gradingSystemIsDefault').checked = false;
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
                    document.getElementById('gradeRangesCard').style.display = 'block';
                }
            });
    }

    function hideGradeRanges() {
        document.getElementById('gradeRangesCard').style.display = 'none';
    }
    </script>
</section>
