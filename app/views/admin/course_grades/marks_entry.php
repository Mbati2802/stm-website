<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Marks Entry</h1>
            <p class="text-muted mb-0">Enter marks for students by programme and unit.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="marksFilterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Programme</label>
                    <select class="form-select" id="programmeFilter" required>
                        <option value="">Select programme</option>
                        <?php foreach ($programmes ?? [] as $programme): ?>
                        <option value="<?= e((string)$programme['id']) ?>"><?= e($programme['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unit</label>
                    <select class="form-select" id="unitFilter" required>
                        <option value="">Select unit</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Academic Session</label>
                    <select class="form-select" id="sessionFilter" required>
                        <option value="">Select session</option>
                        <?php foreach ($academicSessions ?? [] as $session): ?>
                        <option value="<?= e((string)$session['id']) ?>" <?= $session['is_current'] ? 'selected' : '' ?>><?= e($session['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Term</label>
                    <select class="form-select" id="termFilter" required>
                        <option value="">Select term</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grading System</label>
                    <select class="form-select" id="gradingSystemFilter" required>
                        <option value="">Select grading system</option>
                        <?php foreach ($gradingSystems ?? [] as $system): ?>
                        <option value="<?= e((string)$system['id']) ?>"><?= e($system['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Load Students</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Marks Entry Table -->
    <div class="card" id="marksEntryCard" style="display: none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Marks Entry</h5>
            <button class="btn btn-sm btn-success" id="saveAllMarks">
                <i class="bi bi-save"></i> Save All Marks
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="marksEntryTable">
                    <thead>
                        <tr id="marksTableHeader">
                            <th>Student</th>
                            <th>Admission No.</th>
                            <!-- Exam type columns will be added dynamically -->
                        </tr>
                    </thead>
                    <tbody id="marksTableBody">
                        <!-- Student rows will be added dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let examTypes = [];
let courseId = 0;

// Load units when programme is selected
document.getElementById('programmeFilter').addEventListener('change', function() {
    const programmeId = this.value;
    if (programmeId) {
        fetch(`<?= e(base_url('admin/list/portal_courses')) ?>?programme_id=${programmeId}`)
            .then(response => response.json())
            .then(data => {
                const unitSelect = document.getElementById('unitFilter');
                unitSelect.innerHTML = '<option value="">Select unit</option>';
                data.forEach(course => {
                    unitSelect.innerHTML += `<option value="${course.id}">${course.code} - ${course.title}</option>`;
                });
            });
    } else {
        document.getElementById('unitFilter').innerHTML = '<option value="">Select unit</option>';
    }
});

// Load terms when session is selected
document.getElementById('sessionFilter').addEventListener('change', function() {
    const sessionId = this.value;
    if (sessionId) {
        fetch(`<?= e(base_url('admin/semester/terms')) ?>?session_id=${sessionId}`)
            .then(response => response.json())
            .then(data => {
                const termSelect = document.getElementById('termFilter');
                termSelect.innerHTML = '<option value="">Select term</option>';
                data.forEach(term => {
                    termSelect.innerHTML += `<option value="${term.id}" ${term.is_current ? 'selected' : ''}>${term.name}</option>`;
                });
            });
    } else {
        document.getElementById('termFilter').innerHTML = '<option value="">Select term</option>';
    }
});

// Load students and exam types when form is submitted
document.getElementById('marksFilterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const programmeId = document.getElementById('programmeFilter').value;
    const courseId = document.getElementById('unitFilter').value;
    const sessionId = document.getElementById('sessionFilter').value;
    const termId = document.getElementById('termFilter').value;
    const gradingSystemId = document.getElementById('gradingSystemFilter').value;
    
    if (!programmeId || !courseId || !sessionId || !termId || !gradingSystemId) {
        alert('Please select all filters');
        return;
    }
    
    // Load exam types from grading system
    fetch(`<?= e(base_url('admin/grading/exam-types')) ?>?grading_system_id=${gradingSystemId}`)
        .then(response => response.json())
        .then(data => {
            examTypes = data;
            loadStudents(programmeId, courseId, sessionId, termId);
        });
});

function loadStudents(programmeId, courseId, sessionId, termId) {
    fetch(`<?= e(base_url('admin/students/by-enrollment')) ?>?programme_id=${programmeId}&session_id=${sessionId}&term_id=${termId}`)
        .then(response => response.json())
        .then(data => {
            renderMarksTable(data);
        });
}

function renderMarksTable(students) {
    const header = document.getElementById('marksTableHeader');
    const body = document.getElementById('marksTableBody');
    
    // Build header with exam types
    let headerHTML = '<th>Student</th><th>Admission No.</th>';
    examTypes.forEach(type => {
        headerHTML += `<th>${type.name}</th>`;
    });
    headerHTML += '<th>Total</th><th>Grade</th>';
    header.innerHTML = headerHTML;
    
    // Build rows
    body.innerHTML = '';
    students.forEach(student => {
        let rowHTML = `
            <tr data-student-id="${student.id}">
                <td>${student.name}</td>
                <td>${student.admission_number || 'N/A'}</td>
        `;
        
        examTypes.forEach(type => {
            rowHTML += `
                <td>
                    <input type="number" 
                           class="form-control marks-input" 
                           data-exam-type-id="${type.id}" 
                           data-student-id="${student.id}"
                           min="0" 
                           max="100" 
                           step="0.01" 
                           placeholder="0">
                </td>
            `;
        });
        
        rowHTML += `
                <td class="total-marks">0</td>
                <td class="grade-display">-</td>
            </tr>
        `;
        body.innerHTML += rowHTML;
    });
    
    document.getElementById('marksEntryCard').style.display = 'block';
    
    // Add event listeners for marks input
    document.querySelectorAll('.marks-input').forEach(input => {
        input.addEventListener('input', calculateTotalAndGrade);
    });
}

function calculateTotalAndGrade(e) {
    const row = e.target.closest('tr');
    const inputs = row.querySelectorAll('.marks-input');
    let total = 0;
    
    inputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    row.querySelector('.total-marks').textContent = total.toFixed(2);
    
    // Calculate grade based on total
    const gradingSystemId = document.getElementById('gradingSystemFilter').value;
    fetch(`<?= e(base_url('admin/grading/calculate-grade')) ?>?grading_system_id=${gradingSystemId}&marks=${total}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                row.querySelector('.grade-display').textContent = data.data.grade_letter;
            }
        });
}

// Save all marks
document.getElementById('saveAllMarks').addEventListener('click', function() {
    const courseId = document.getElementById('unitFilter').value;
    const gradingSystemId = document.getElementById('gradingSystemFilter').value;
    const sessionId = document.getElementById('sessionFilter').value;
    const termId = document.getElementById('termFilter').value;
    
    const marksData = [];
    document.querySelectorAll('#marksTableBody tr').forEach(row => {
        const studentId = row.dataset.studentId;
        row.querySelectorAll('.marks-input').forEach(input => {
            const examTypeId = input.dataset.examTypeId;
            const marks = input.value;
            if (marks) {
                marksData.push({
                    student_id: studentId,
                    course_id: courseId,
                    exam_type_id: examTypeId,
                    grading_system_id: gradingSystemId,
                    marks: marks,
                    academic_session_id: sessionId,
                    term_id: termId
                });
            }
        });
    });
    
    if (marksData.length === 0) {
        alert('No marks to save');
        return;
    }
    
    fetch('<?= e(base_url('admin/course-grades/bulk-save')) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ marks: marksData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Marks saved successfully');
        } else {
            alert('Error saving marks: ' + data.message);
        }
    });
});
</script>
