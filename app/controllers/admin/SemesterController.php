<?php

class SemesterController extends Controller
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
        
        // Get all academic years
        $stmt = $pdo->query('SELECT * FROM academic_years ORDER BY start_date DESC');
        $academicYears = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all terms with year names
        $stmt = $pdo->query('
            SELECT t.*, ay.name as year_name, ay.code as year_code 
            FROM terms t 
            LEFT JOIN academic_years ay ON t.academic_session_id = ay.id 
            ORDER BY ay.start_date DESC, t.start_date ASC
        ');
        $terms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all intakes
        $stmt = $pdo->query('SELECT * FROM intakes ORDER BY start_date ASC');
        $intakes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all sessions
        $stmt = $pdo->query('SELECT * FROM sessions ORDER BY sequence_number ASC');
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/semester/index', [
            'metaTitle' => 'Semester Management',
            'academicYears' => $academicYears,
            'terms' => $terms,
            'intakes' => $intakes,
            'sessions' => $sessions,
        ]);
    }

    public function createAcademicYear(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/semester');
        }

        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $isCurrent = isset($_POST['is_current']) ? 1 : 0;
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $code === '' || $startDate === '' || $endDate === '') {
            flash('error', 'All required fields must be filled.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            
            // If setting as current, unset all other current years
            if ($isCurrent) {
                $pdo->prepare('UPDATE academic_years SET is_current = 0')->execute();
            }

            $stmt = $pdo->prepare('INSERT INTO academic_years (name, code, start_date, end_date, is_current, description) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $code, $startDate, $endDate, $isCurrent, $description]);
            
            flash('success', 'Academic year created successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to create academic year: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function editAcademicYear(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/semester');
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $isCurrent = isset($_POST['is_current']) ? 1 : 0;
        $description = trim($_POST['description'] ?? '');

        if ($id === 0 || $name === '' || $code === '' || $startDate === '' || $endDate === '') {
            flash('error', 'All required fields must be filled.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            
            // If setting as current, unset all other current years
            if ($isCurrent) {
                $pdo->prepare('UPDATE academic_years SET is_current = 0 WHERE id != ?')->execute([$id]);
            }

            $stmt = $pdo->prepare('UPDATE academic_years SET name = ?, code = ?, start_date = ?, end_date = ?, is_current = ?, description = ? WHERE id = ?');
            $stmt->execute([$name, $code, $startDate, $endDate, $isCurrent, $description, $id]);
            
            flash('success', 'Academic year updated successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to update academic year: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function deleteAcademicYear(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            flash('error', 'Invalid year ID.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('DELETE FROM academic_years WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Academic year deleted successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to delete academic year: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function createTerm(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/semester');
        }

        $sessionId = (int)($_POST['academic_session_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $isCurrent = isset($_POST['is_current']) ? 1 : 0;

        if ($sessionId === 0 || $name === '' || $code === '' || $startDate === '' || $endDate === '') {
            flash('error', 'All required fields must be filled.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            
            // If setting as current, unset all other current terms
            if ($isCurrent) {
                $pdo->prepare('UPDATE terms SET is_current = 0')->execute();
            }

            $stmt = $pdo->prepare('INSERT INTO terms (academic_session_id, name, code, start_date, end_date, is_current) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$sessionId, $name, $code, $startDate, $endDate, $isCurrent]);
            
            flash('success', 'Term created successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to create term: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function editTerm(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/semester');
        }

        $id = (int)($_POST['id'] ?? 0);
        $sessionId = (int)($_POST['academic_session_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $isCurrent = isset($_POST['is_current']) ? 1 : 0;

        if ($id === 0 || $sessionId === 0 || $name === '' || $code === '' || $startDate === '' || $endDate === '') {
            flash('error', 'All required fields must be filled.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            
            // If setting as current, unset all other current terms
            if ($isCurrent) {
                $pdo->prepare('UPDATE terms SET is_current = 0 WHERE id != ?')->execute([$id]);
            }

            $stmt = $pdo->prepare('UPDATE terms SET academic_session_id = ?, name = ?, code = ?, start_date = ?, end_date = ?, is_current = ? WHERE id = ?');
            $stmt->execute([$sessionId, $name, $code, $startDate, $endDate, $isCurrent, $id]);
            
            flash('success', 'Term updated successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to update term: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function deleteTerm(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            flash('error', 'Invalid term ID.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('DELETE FROM terms WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Term deleted successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to delete term: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function createIntake(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/semester');
        }

        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $code === '' || $startDate === '') {
            flash('error', 'All required fields must be filled.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('INSERT INTO intakes (name, code, start_date, end_date, description) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $code, $startDate, $endDate, $description]);
            
            flash('success', 'Intake created successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to create intake: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function editIntake(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/semester');
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($id === 0 || $name === '' || $code === '' || $startDate === '') {
            flash('error', 'All required fields must be filled.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('UPDATE intakes SET name = ?, code = ?, start_date = ?, end_date = ?, description = ? WHERE id = ?');
            $stmt->execute([$name, $code, $startDate, $endDate, $description, $id]);
            
            flash('success', 'Intake updated successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to update intake: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function deleteIntake(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            flash('error', 'Invalid intake ID.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('DELETE FROM intakes WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Intake deleted successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to delete intake: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function getTerms(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('settings')) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $sessionId = (int)($_GET['session_id'] ?? 0);
        if ($sessionId === 0) {
            echo json_encode(['error' => 'Invalid session ID']);
            return;
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('SELECT * FROM terms WHERE academic_year_id = ? ORDER BY start_date ASC');
            $stmt->execute([$sessionId]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
