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
                    <label class="form-label">Academic Year</label>
                    <select class="form-select" id="sessionFilter" required>
                        <option value="">Select year</option>
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
                    <label class="form-label">Session</label>
                    <select class="form-select" id="studentSessionFilter" required>
                        <option value="">Select session</option>
                        <?php foreach ($studentSessions ?? [] as $session): ?>
                        <option value="<?= e((string)$session['id']) ?>"><?= e($session['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
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
// Hardcoded exam types (CAT, Assignment, etc.)
const examTypes = [
    { id: 'cat', name: 'CAT' },
    { id: 'assignment', name: 'Assignment' },
    { id: 'exam', name: 'Exam' }
];

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
                checkAndLoadStudents();
            });
    } else {
        document.getElementById('termFilter').innerHTML = '<option value="">Select term</option>';
    }
});

// Auto-load students when any filter changes
document.getElementById('programmeFilter').addEventListener('change', checkAndLoadStudents);
document.getElementById('termFilter').addEventListener('change', checkAndLoadStudents);
document.getElementById('studentSessionFilter').addEventListener('change', checkAndLoadStudents);

function checkAndLoadStudents() {
    const programmeId = document.getElementById('programmeFilter').value;
    const sessionId = document.getElementById('sessionFilter').value;
    const termId = document.getElementById('termFilter').value;
    const studentSessionId = document.getElementById('studentSessionFilter').value;
    
    if (programmeId && sessionId && termId && studentSessionId) {
        loadStudents(programmeId, sessionId, termId, studentSessionId);
    }
}

function loadStudents(programmeId, sessionId, termId, studentSessionId) {
    fetch(`<?= e(base_url('admin/students/by-enrollment')) ?>?programme_id=${programmeId}&session_id=${sessionId}&term_id=${termId}&session_id=${studentSessionId}`)
        .then(response => response.json())
        .then(data => {
            renderMarksTable(data);
        });
}

function renderMarksTable(students) {
    const header = document.getElementById('marksTableHeader');
    const body = document.getElementById('marksTableBody');
    
    // Build header with exam types
    let headerHTML = '<th>Admission No.</th><th>Student Name</th>';
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
                <td>${student.admission_number || 'N/A'}</td>
                <td>${student.name}</td>
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
    
    // Simple grade calculation (can be customized)
    let grade = '-';
    if (total >= 70) grade = 'A';
    else if (total >= 60) grade = 'B';
    else if (total >= 50) grade = 'C';
    else if (total >= 40) grade = 'D';
    else if (total > 0) grade = 'F';
    
    row.querySelector('.grade-display').textContent = grade;
}

// Save all marks
document.getElementById('saveAllMarks').addEventListener('click', function() {
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
                    exam_type_id: examTypeId,
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
