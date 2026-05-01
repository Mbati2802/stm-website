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
        
        // Get all academic sessions
        $stmt = $pdo->query('SELECT * FROM academic_sessions ORDER BY start_date DESC');
        $academicSessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all terms with session names
        $stmt = $pdo->query('
            SELECT t.*, s.name as session_name, s.code as session_code 
            FROM terms t 
            LEFT JOIN academic_sessions s ON t.academic_session_id = s.id 
            ORDER BY s.start_date DESC, t.start_date ASC
        ');
        $terms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all intakes
        $stmt = $pdo->query('SELECT * FROM intakes ORDER BY start_date ASC');
        $intakes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/semester/index', [
            'metaTitle' => 'Semester Management',
            'academicSessions' => $academicSessions,
            'terms' => $terms,
            'intakes' => $intakes,
        ]);
    }

    public function createAcademicSession(): void
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
            
            // If setting as current, unset all other current sessions
            if ($isCurrent) {
                $pdo->prepare('UPDATE academic_sessions SET is_current = 0')->execute();
            }

            $stmt = $pdo->prepare('INSERT INTO academic_sessions (name, code, start_date, end_date, is_current, description) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $code, $startDate, $endDate, $isCurrent, $description]);
            
            flash('success', 'Academic session created successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to create academic session: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function editAcademicSession(): void
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
            
            // If setting as current, unset all other current sessions
            if ($isCurrent) {
                $pdo->prepare('UPDATE academic_sessions SET is_current = 0 WHERE id != ?')->execute([$id]);
            }

            $stmt = $pdo->prepare('UPDATE academic_sessions SET name = ?, code = ?, start_date = ?, end_date = ?, is_current = ?, description = ? WHERE id = ?');
            $stmt->execute([$name, $code, $startDate, $endDate, $isCurrent, $description, $id]);
            
            flash('success', 'Academic session updated successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to update academic session: ' . $e->getMessage());
        }

        $this->redirect('admin/semester');
    }

    public function deleteAcademicSession(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('settings')) {
            $this->redirect('admin/semester');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            flash('error', 'Invalid session ID.');
            $this->redirect('admin/semester');
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            $stmt = $pdo->prepare('DELETE FROM academic_sessions WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Academic session deleted successfully.');
        } catch (PDOException $e) {
            flash('error', 'Failed to delete academic session: ' . $e->getMessage());
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
}
