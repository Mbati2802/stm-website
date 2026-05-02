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
                <div class="col-md-3">
                    <label class="form-label">Unit/Course <span class="text-danger">*</span></label>
                    <select class="form-select" id="unitFilter" required>
                        <option value="">Select unit</option>
                        <?php foreach ($courses ?? [] as $course): ?>
                        <option value="<?= e((string)$course['id']) ?>"><?= e($course['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Marks Entry Table -->
    <div class="card" id="marksEntryCard" style="display: none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Marks Entry</h5>
                <small class="text-muted" id="unitNameDisplay"></small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary" id="refreshStudents">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
                <button class="btn btn-sm btn-success" id="saveAllMarks">
                    <i class="bi bi-save"></i> Save All Marks
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="noStudentsMessage" class="alert alert-warning" style="display: none;">
                No students found for the selected filters. Please ensure students are enrolled in the selected programme, academic year, term, and session.
            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('Marks Entry: DOM loaded');
    
    // Exam types will be loaded dynamically from grading system
    window.exams = [];
    window.gradeRanges = [];
    window.defaultGradingSystemId = 0;

    // Load terms when session is selected
    document.getElementById('sessionFilter').addEventListener('change', function() {
        const sessionId = this.value;
        console.log('Marks Entry: Academic year changed, sessionId =', sessionId);
        if (sessionId) {
            const url = `<?= e(base_url('admin/semester/terms')) ?>?session_id=${sessionId}`;
            console.log('Marks Entry: Fetching terms from', url);
            fetch(url)
                .then(response => {
                    console.log('Marks Entry: Terms response status', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Marks Entry: Terms data received', data);
                    const termSelect = document.getElementById('termFilter');
                    termSelect.innerHTML = '<option value="">Select term</option>';
                    data.forEach(term => {
                        termSelect.innerHTML += `<option value="${term.id}" ${term.is_current ? 'selected' : ''}>${term.name}</option>`;
                    });
                    checkAndLoadStudents();
                })
                .catch(error => {
                    console.error('Marks Entry: Error fetching terms', error);
                });
        } else {
            document.getElementById('termFilter').innerHTML = '<option value="">Select term</option>';
        }
    });

    // Auto-load students when any filter changes
    document.getElementById('programmeFilter').addEventListener('change', function() {
        console.log('Marks Entry: Programme changed');
        checkAndLoadStudents();
    });
    document.getElementById('termFilter').addEventListener('change', function() {
        console.log('Marks Entry: Term changed');
        checkAndLoadStudents();
    });
    document.getElementById('studentSessionFilter').addEventListener('change', function() {
        console.log('Marks Entry: Student session changed');
        checkAndLoadStudents();
    });
    document.getElementById('unitFilter').addEventListener('change', function() {
        console.log('Marks Entry: Unit changed');
        checkAndLoadStudents();
    });

    function checkAndLoadStudents() {
        const programmeId = document.getElementById('programmeFilter').value;
        const sessionId = document.getElementById('sessionFilter').value;
        const termId = document.getElementById('termFilter').value;
        const studentSessionId = document.getElementById('studentSessionFilter').value;
        const unitId = document.getElementById('unitFilter').value;
        
        console.log('Marks Entry: checkAndLoadStudents called with', {
            programmeId,
            sessionId,
            termId,
            studentSessionId,
            unitId
        });
        
        if (programmeId && sessionId && termId && studentSessionId && unitId) {
            // Load exams (grading systems) if not already loaded
            if (!window.exams || window.exams.length === 0) {
                console.log('Marks Entry: Exams not loaded, loading them first');
                fetch(`<?= e(base_url('admin/grading/exams')) ?>`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Marks Entry: Exams received', data);
                        window.exams = data;
                        // Load default grading system for grade calculation
                        loadDefaultGradingSystem();
                        loadStudents(programmeId, sessionId, termId, studentSessionId, unitId);
                    })
                    .catch(error => {
                        console.error('Marks Entry: Error fetching exams', error);
                    });
            } else {
                console.log('Marks Entry: All filters set, loading students');
                loadStudents(programmeId, sessionId, termId, studentSessionId, unitId);
            }
        } else {
            console.log('Marks Entry: Not all filters set yet');
        }
    }

    function loadDefaultGradingSystem() {
        console.log('Marks Entry: Loading default grading system');
        fetch(`<?= e(base_url('admin/grading/default')) ?>`)
            .then(response => response.json())
            .then(data => {
                console.log('Marks Entry: Default grading system received', data);
                if (data.success && data.data) {
                    window.defaultGradingSystemId = data.data.id;
                    loadGradeRangesForGradingSystem(data.data.id);
                }
            })
            .catch(error => {
                console.error('Marks Entry: Error fetching default grading system', error);
            });
    }

    function loadGradeRangesForGradingSystem(gradingSystemId) {
        if (!gradingSystemId) return;
        
        console.log('Marks Entry: Loading grade ranges for grading system', gradingSystemId);
        fetch(`<?= e(base_url('admin/grading/grade-ranges')) ?>?grading_system_id=${gradingSystemId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Marks Entry: Grade ranges received', data);
                if (data.success) {
                    window.gradeRanges = data.data;
                }
            })
            .catch(error => {
                console.error('Marks Entry: Error fetching grade ranges', error);
            });
    }

    function loadStudents(programmeId, sessionId, termId, studentSessionId, unitId) {
        // Fetch unit name for display
        const unitSelect = document.getElementById('unitFilter');
        const unitName = unitSelect.options[unitSelect.selectedIndex].text;
        document.getElementById('unitNameDisplay').textContent = `Unit: ${unitName}`;
        
        const url = `<?= e(base_url('admin/students/by-enrollment')) ?>?programme_id=${programmeId}&session_id=${sessionId}&term_id=${termId}&student_session_id=${studentSessionId}&unit_id=${unitId}`;
        console.log('Marks Entry: Fetching students from', url);
        fetch(url)
            .then(response => {
                console.log('Marks Entry: Students response status', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Marks Entry: Students data received', data);
                renderMarksTable(data);
            })
            .catch(error => {
                console.error('Marks Entry: Error fetching students', error);
            });
    }

    function renderMarksTable(students) {
        const header = document.getElementById('marksTableHeader');
        const body = document.getElementById('marksTableBody');
        const noStudentsMessage = document.getElementById('noStudentsMessage');
        const table = document.getElementById('marksEntryTable');
        const examTypes = window.examTypes || [];
        
        // Handle error response
        if (students.error) {
            console.error('Marks Entry: Server error', students.error);
            noStudentsMessage.textContent = 'Error loading students: ' + students.error;
            noStudentsMessage.className = 'alert alert-danger';
            noStudentsMessage.style.display = 'block';
            table.style.display = 'none';
            document.getElementById('marksEntryCard').style.display = 'block';
            return;
        }
        
        // Handle empty array
        if (!Array.isArray(students) || students.length === 0) {
            console.log('Marks Entry: No students found');
            noStudentsMessage.textContent = 'No students found for the selected filters. Please ensure students are enrolled in the selected programme, academic year, term, and session.';
            noStudentsMessage.className = 'alert alert-warning';
            noStudentsMessage.style.display = 'block';
            table.style.display = 'none';
            document.getElementById('marksEntryCard').style.display = 'block';
            return;
        }
        
        console.log('Marks Entry: Rendering table with', students.length, 'students');
        
        // Show table, hide message
        noStudentsMessage.style.display = 'none';
        table.style.display = 'table';
        
        // Build header with exams (exclude consolidated types as they are computed)
        let headerHTML = '<th>Admission No.</th><th>Student Name</th>';
        window.exams.forEach(exam => {
            // Only show columns for non-consolidated exam types
            if (exam.type !== 'consolidated') {
                headerHTML += `<th>${exam.name}</th>`;
            }
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
            
            window.exams.forEach(exam => {
                // Only show input fields for non-consolidated exam types
                if (exam.type !== 'consolidated') {
                    rowHTML += `
                        <td>
                            <input type="number" 
                                   class="form-control marks-input" 
                                   data-exam-id="${exam.id}" 
                                   data-student-id="${student.id}"
                                   min="0" 
                                   max="100" 
                                   step="0.01" 
                                   placeholder="0">
                        </td>
                    `;
                }
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
        
        // Ensure total does not exceed 100
        if (total > 100) {
            total = 100;
        }
        
        row.querySelector('.total-marks').textContent = total.toFixed(2);
        
        // Calculate grade using grade ranges from grading system
        let grade = '-';
        if (window.gradeRanges && window.gradeRanges.length > 0) {
            for (const range of window.gradeRanges) {
                if (total >= range.min_marks && total <= range.max_marks) {
                    grade = range.grade_letter;
                    break;
                }
            }
        } else {
            // Fallback to simple grade calculation if no grade ranges loaded
            if (total >= 70) grade = 'A';
            else if (total >= 60) grade = 'B';
            else if (total >= 50) grade = 'C';
            else if (total >= 40) grade = 'D';
            else if (total > 0) grade = 'F';
        }
        
        row.querySelector('.grade-display').textContent = grade;
    }

    // Refresh students
    document.getElementById('refreshStudents').addEventListener('click', function() {
        console.log('Marks Entry: Refresh button clicked');
        checkAndLoadStudents();
    });

    // Save all marks
    document.getElementById('saveAllMarks').addEventListener('click', function() {
        const sessionId = document.getElementById('sessionFilter').value;
        const termId = document.getElementById('termFilter').value;
        const unitId = document.getElementById('unitFilter').value;
        
        if (!window.defaultGradingSystemId) {
            alert('No default grading system found. Please create a grading system in the Grading System page.');
            return;
        }
        
        const marksData = [];
        document.querySelectorAll('#marksTableBody tr').forEach(row => {
            const studentId = row.dataset.studentId;
            row.querySelectorAll('.marks-input').forEach(input => {
                const examId = input.dataset.examId;
                const marks = input.value;
                if (marks) {
                    marksData.push({
                        student_id: studentId,
                        course_id: unitId,
                        exam_type_id: examId,
                        marks: marks,
                        academic_session_id: sessionId,
                        term_id: termId,
                        grading_system_id: window.defaultGradingSystemId
                    });
                }
            });
        });
        
        if (marksData.length === 0) {
            alert('No marks to save');
            return;
        }
        
        console.log('Saving marks:', marksData);
        
        fetch('<?= e(base_url('admin/course-grades/bulk-save')) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                marks: marksData,
                _token: '<?= e(csrf_token()) ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Save response:', data);
            if (data.success) {
                alert('Marks saved successfully');
            } else {
                alert('Error saving marks: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error saving marks:', error);
            alert('Error saving marks: ' + error.message);
        });
    });
});
</script>
