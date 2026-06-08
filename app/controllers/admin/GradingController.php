<?php

class GradingController extends Controller
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function index(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('settings')) {
            $this->redirect('admin');
        }

        $pdo = Database::getInstance($this->config['db']);
        
        // Get all exam types
        $stmt = $pdo->query('SELECT * FROM exam_types ORDER BY id ASC');
        $examTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all grading systems with exam type names
        $stmt = $pdo->query('
            SELECT gs.*, et.name as exam_type_name 
            FROM grading_systems gs 
            LEFT JOIN exam_types et ON gs.exam_type_id = et.id 
            ORDER BY gs.id ASC
        ');
        $gradingSystems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/grading/index', [
            'metaTitle' => 'Grading System',
            'examTypes' => $examTypes,
            'gradingSystems' => $gradingSystems,
        ]);
    }

    public function createExamType(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $name = trim($_POST['name'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $type = $_POST['type'] ?? 'single';
        $maxMarks = (float)($_POST['max_marks'] ?? 100);
        $displayMode = $_POST['display_mode'] ?? 'converted';
        $parentExamIds = $_POST['parent_exam_ids'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $code === '') {
            flash('error', 'Name and code are required.');
            $this->redirect('admin/grading');
        }

        // Validate max_marks
        if ($maxMarks <= 0 || $maxMarks > 100) {
            flash('error', 'Maximum marks must be between 0.01 and 100.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('INSERT INTO exam_types (name, code, type, max_marks, display_mode, parent_exam_ids, description) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $code, $type, $maxMarks, $displayMode, $parentExamIds ?: null, $description ?: null]);
            flash('success', 'Exam type created successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to create exam type: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function editExamType(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $type = $_POST['type'] ?? 'single';
        $maxMarks = (float)($_POST['max_marks'] ?? 100);
        $displayMode = $_POST['display_mode'] ?? 'converted';
        $parentExamIds = $_POST['parent_exam_ids'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if ($id === 0 || $name === '' || $code === '') {
            flash('error', 'ID, name and code are required.');
            $this->redirect('admin/grading');
        }

        // Validate max_marks
        if ($maxMarks <= 0 || $maxMarks > 100) {
            flash('error', 'Maximum marks must be between 0.01 and 100.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('UPDATE exam_types SET name = ?, code = ?, type = ?, max_marks = ?, display_mode = ?, parent_exam_ids = ?, description = ? WHERE id = ?');
            $stmt->execute([$name, $code, $type, $maxMarks, $displayMode, $parentExamIds ?: null, $description ?: null, $id]);
            flash('success', 'Exam type updated successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to update exam type: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function deleteExamType(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            flash('error', 'ID is required.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('DELETE FROM exam_types WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Exam type deleted successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to delete exam type: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function createGradingSystem(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $name = trim($_POST['name'] ?? '');
        $examTypeId = (int)($_POST['exam_type_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $isDefault = (int)($_POST['is_default'] ?? 0);

        if ($name === '' || $examTypeId === 0) {
            flash('error', 'Name and exam type are required.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);

            // If setting as default, remove default from others for this exam type
            if ($isDefault) {
                $stmt = $pdo->prepare('UPDATE grading_systems SET is_default = 0 WHERE exam_type_id = ?');
                $stmt->execute([$examTypeId]);
            }

            $stmt = $pdo->prepare('INSERT INTO grading_systems (name, exam_type_id, description, is_default) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $examTypeId, $description ?: null, $isDefault]);
            flash('success', 'Grading system created successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to create grading system: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function editGradingSystem(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $examTypeId = (int)($_POST['exam_type_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $isDefault = (int)($_POST['is_default'] ?? 0);

        if ($id === 0 || $name === '' || $examTypeId === 0) {
            flash('error', 'ID, name and exam type are required.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);

            // If setting as default, remove default from others for this exam type
            if ($isDefault) {
                $stmt = $pdo->prepare('UPDATE grading_systems SET is_default = 0 WHERE exam_type_id = ? AND id != ?');
                $stmt->execute([$examTypeId, $id]);
            }

            $stmt = $pdo->prepare('UPDATE grading_systems SET name = ?, exam_type_id = ?, description = ?, is_default = ? WHERE id = ?');
            $stmt->execute([$name, $examTypeId, $description ?: null, $isDefault, $id]);
            flash('success', 'Grading system updated successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to update grading system: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function deleteGradingSystem(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            flash('error', 'ID is required.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('DELETE FROM grading_systems WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Grading system deleted successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to delete grading system: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function addGradeRange(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $gradingSystemId = (int)($_POST['grading_system_id'] ?? 0);
        $gradeLetter = strtoupper(trim($_POST['grade_letter'] ?? ''));
        $minMarks = (float)($_POST['min_marks'] ?? 0);
        $maxMarks = (float)($_POST['max_marks'] ?? 0);
        $remarks = trim($_POST['remarks'] ?? '');
        $gpaValue = $_POST['gpa_value'] !== '' ? (float)$_POST['gpa_value'] : null;

        if ($gradingSystemId === 0 || $gradeLetter === '' || $minMarks < 0 || $maxMarks < 0) {
            flash('error', 'Grading system, grade letter, and valid marks are required.');
            $this->redirect('admin/grading');
        }

        if ($minMarks > $maxMarks) {
            flash('error', 'Minimum marks cannot be greater than maximum marks.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('INSERT INTO grade_ranges (grading_system_id, grade_letter, min_marks, max_marks, remarks, gpa_value) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$gradingSystemId, $gradeLetter, $minMarks, $maxMarks, $remarks ?: null, $gpaValue]);
            flash('success', 'Grade range added successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to add grade range: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function editGradeRange(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $id = (int)($_POST['id'] ?? 0);
        $gradeLetter = strtoupper(trim($_POST['grade_letter'] ?? ''));
        $minMarks = (float)($_POST['min_marks'] ?? 0);
        $maxMarks = (float)($_POST['max_marks'] ?? 0);
        $remarks = trim($_POST['remarks'] ?? '');
        $gpaValue = $_POST['gpa_value'] !== '' ? (float)$_POST['gpa_value'] : null;

        if ($id === 0 || $gradeLetter === '' || $minMarks < 0 || $maxMarks < 0) {
            flash('error', 'ID, grade letter, and valid marks are required.');
            $this->redirect('admin/grading');
        }

        if ($minMarks > $maxMarks) {
            flash('error', 'Minimum marks cannot be greater than maximum marks.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('UPDATE grade_ranges SET grade_letter = ?, min_marks = ?, max_marks = ?, remarks = ?, gpa_value = ? WHERE id = ?');
            $stmt->execute([$gradeLetter, $minMarks, $maxMarks, $remarks ?: null, $gpaValue, $id]);
            flash('success', 'Grade range updated successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to update grade range: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function deleteGradeRange(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            flash('error', 'ID is required.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('DELETE FROM grade_ranges WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Grade range deleted successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to delete grade range: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function bulkApplyGradeRanges(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/grading');
        }

        $sourceGradingSystemId = (int)($_POST['source_grading_system_id'] ?? 0);
        $targetGradingSystemIds = $_POST['target_grading_system_ids'] ?? [];
        $replaceExisting = isset($_POST['replace_existing']);

        if ($sourceGradingSystemId === 0) {
            flash('error', 'Source grading system is required.');
            $this->redirect('admin/grading');
        }

        if (empty($targetGradingSystemIds)) {
            flash('error', 'Please select at least one target exam to apply grade ranges to.');
            $this->redirect('admin/grading');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $pdo->beginTransaction();

            // Get grade ranges from source grading system
            $sourceStmt = $pdo->prepare('SELECT grade_letter, min_marks, max_marks, remarks, gpa_value FROM grade_ranges WHERE grading_system_id = ?');
            $sourceStmt->execute([$sourceGradingSystemId]);
            $sourceRanges = $sourceStmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($sourceRanges)) {
                flash('error', 'Source grading system has no grade ranges to copy.');
                $this->redirect('admin/grading');
            }

            $appliedCount = 0;
            $insertStmt = $pdo->prepare('INSERT INTO grade_ranges (grading_system_id, grade_letter, min_marks, max_marks, remarks, gpa_value) VALUES (?, ?, ?, ?, ?, ?)');

            foreach ($targetGradingSystemIds as $targetId) {
                $targetId = (int)$targetId;
                if ($targetId === $sourceGradingSystemId) {
                    continue; // Skip self
                }

                // Replace existing if requested
                if ($replaceExisting) {
                    $deleteStmt = $pdo->prepare('DELETE FROM grade_ranges WHERE grading_system_id = ?');
                    $deleteStmt->execute([$targetId]);
                }

                // Copy grade ranges to target
                foreach ($sourceRanges as $range) {
                    $insertStmt->execute([
                        $targetId,
                        $range['grade_letter'],
                        $range['min_marks'],
                        $range['max_marks'],
                        $range['remarks'],
                        $range['gpa_value']
                    ]);
                }
                $appliedCount++;
            }

            $pdo->commit();
            flash('success', "Grade ranges applied successfully to {$appliedCount} exam(s).");
        } catch (PDOException $e) {
            $pdo->rollBack();
            flash('error', 'Failed to apply grade ranges: ' . $e->getMessage());
        }

        $this->redirect('admin/grading');
    }

    public function getGradeRanges(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('settings')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $gradingSystemId = (int)($_GET['grading_system_id'] ?? 0);

        if ($gradingSystemId === 0) {
            echo json_encode(['error' => 'Grading system ID is required']);
            return;
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('SELECT * FROM grade_ranges WHERE grading_system_id = ? ORDER BY min_marks DESC');
            $stmt->execute([$gradingSystemId]);
            $gradeRanges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $gradeRanges]);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function calculateGrade(): void
    {
        Auth::requireAdmin();
        
        $marks = (float)($_GET['marks'] ?? 0);
        $gradingSystemId = (int)($_GET['grading_system_id'] ?? 0);

        if ($gradingSystemId === 0) {
            echo json_encode(['error' => 'Grading system ID is required']);
            return;
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('SELECT * FROM grade_ranges WHERE grading_system_id = ? AND ? >= min_marks AND ? <= max_marks LIMIT 1');
            $stmt->execute([$gradingSystemId, $marks, $marks]);
            $grade = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($grade) {
                echo json_encode(['success' => true, 'data' => $grade]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No grade range found for these marks']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getExamTypes(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('settings')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $gradingSystemId = (int)($_GET['grading_system_id'] ?? 0);
        if ($gradingSystemId === 0) {
            echo json_encode(['error' => 'Invalid grading system ID']);
            return;
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('SELECT * FROM exam_types WHERE id = (SELECT exam_type_id FROM grading_systems WHERE id = ?)');
            $stmt->execute([$gradingSystemId]);
            $examType = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($examType && $examType['type'] === 'consolidated') {
                // For consolidated exams, get parent exam types
                $parentIds = json_decode($examType['parent_exam_ids'] ?? '[]', true);
                if (!empty($parentIds)) {
                    $placeholders = str_repeat('?,', count($parentIds) - 1) . '?';
                    $stmt = $pdo->prepare("SELECT * FROM exam_types WHERE id IN ($placeholders)");
                    $stmt->execute($parentIds);
                    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                } else {
                    echo json_encode([]);
                }
            } else {
                // For single exams, return the exam type itself
                echo json_encode([$examType]);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getExams(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('settings')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->query('
                SELECT gs.*, et.id as exam_type_id, et.type, et.name as exam_type_name, et.max_marks, et.display_mode
                FROM grading_systems gs
                LEFT JOIN exam_types et ON gs.exam_type_id = et.id
                WHERE gs.is_active = 1
                ORDER BY gs.id ASC
            ');
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($exams);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getDefaultGradingSystem(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('settings')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->query('SELECT * FROM grading_systems WHERE is_default = 1 AND is_active = 1 LIMIT 1');
            $gradingSystem = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($gradingSystem) {
                echo json_encode(['success' => true, 'data' => $gradingSystem]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No default grading system found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
