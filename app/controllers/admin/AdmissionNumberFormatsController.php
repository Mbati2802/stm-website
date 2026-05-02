<?php

class AdmissionNumberFormatsController extends Controller
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function index(): void
    {
        if (!Auth::canViewEntity('portal_courses')) {
            $this->redirect('admin');
            return;
        }

        $pdo = Database::getInstance($this->config['db']);
        $stmt = $pdo->query('SELECT * FROM admission_number_formats ORDER BY is_default DESC, id ASC');
        $formats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/admission_number_formats/index', [
            'metaTitle' => 'Admission Number Formats',
            'formats' => $formats
        ]);
    }

    public function create(): void
    {
        if (!Auth::canManageEntity('portal_courses')) {
            $this->redirect('admin');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $formatPattern = trim($_POST['format_pattern'] ?? '');
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            if (empty($name) || empty($formatPattern)) {
                flash('error', 'Name and format pattern are required.');
                $this->redirect('admin/admission-number-formats/create');
                return;
            }

            $pdo = Database::getInstance($this->config['db']);

            // If setting as default, unset all other defaults
            if ($isDefault) {
                $pdo->prepare('UPDATE admission_number_formats SET is_default = 0')->execute();
            }

            $stmt = $pdo->prepare('INSERT INTO admission_number_formats (name, format_pattern, is_default) VALUES (?, ?, ?)');
            $stmt->execute([$name, $formatPattern, $isDefault]);

            flash('success', 'Admission number format created successfully.');
            $this->redirect('admin/admission-number-formats');
            return;
        }

        $this->view('admin/admission_number_formats/form', [
            'metaTitle' => 'Create Admission Number Format',
            'format' => null
        ]);
    }

    public function edit(int $id): void
    {
        if (!Auth::canManageEntity('portal_courses')) {
            $this->redirect('admin');
            return;
        }

        $pdo = Database::getInstance($this->config['db']);
        $stmt = $pdo->prepare('SELECT * FROM admission_number_formats WHERE id = ?');
        $stmt->execute([$id]);
        $format = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$format) {
            flash('error', 'Format not found.');
            $this->redirect('admin/admission-number-formats');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $formatPattern = trim($_POST['format_pattern'] ?? '');
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            if (empty($name) || empty($formatPattern)) {
                flash('error', 'Name and format pattern are required.');
                $this->redirect('admin/admission-number-formats/edit/' . $id);
                return;
            }

            // If setting as default, unset all other defaults
            if ($isDefault) {
                $pdo->prepare('UPDATE admission_number_formats SET is_default = 0')->execute();
            }

            $stmt = $pdo->prepare('UPDATE admission_number_formats SET name = ?, format_pattern = ?, is_default = ? WHERE id = ?');
            $stmt->execute([$name, $formatPattern, $isDefault, $id]);

            flash('success', 'Admission number format updated successfully.');
            $this->redirect('admin/admission-number-formats');
            return;
        }

        $this->view('admin/admission_number_formats/form', [
            'metaTitle' => 'Edit Admission Number Format',
            'format' => $format
        ]);
    }

    public function delete(int $id): void
    {
        if (!Auth::canManageEntity('portal_courses')) {
            $this->redirect('admin');
            return;
        }

        $pdo = Database::getInstance($this->config['db']);
        $stmt = $pdo->prepare('SELECT * FROM admission_number_formats WHERE id = ?');
        $stmt->execute([$id]);
        $format = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$format) {
            flash('error', 'Format not found.');
            $this->redirect('admin/admission-number-formats');
            return;
        }

        // Don't delete if it's the only format
        $countStmt = $pdo->query('SELECT COUNT(*) FROM admission_number_formats');
        $count = $countStmt->fetchColumn();

        if ($count <= 1) {
            flash('error', 'Cannot delete the only admission number format.');
            $this->redirect('admin/admission-number-formats');
            return;
        }

        // If deleting default, set another as default
        if ($format['is_default']) {
            $pdo->prepare('UPDATE admission_number_formats SET is_default = 1 WHERE id != ? LIMIT 1')->execute([$id]);
        }

        $stmt = $pdo->prepare('DELETE FROM admission_number_formats WHERE id = ?');
        $stmt->execute([$id]);

        flash('success', 'Admission number format deleted successfully.');
        $this->redirect('admin/admission-number-formats');
    }

    public function setDefault(int $id): void
    {
        if (!Auth::canManageEntity('portal_courses')) {
            $this->redirect('admin');
            return;
        }

        $pdo = Database::getInstance($this->config['db']);
        $pdo->prepare('UPDATE admission_number_formats SET is_default = 0')->execute();
        $stmt = $pdo->prepare('UPDATE admission_number_formats SET is_default = 1 WHERE id = ?');
        $stmt->execute([$id]);

        flash('success', 'Default format updated successfully.');
        $this->redirect('admin/admission-number-formats');
    }

    /**
     * Generate admission number based on format and programme
     * 
     * @param string $formatPattern Format pattern with placeholders
     * @param string $programmeAbbr Programme abbreviation (e.g., DPTT)
     * @param int $sequenceNumber Sequential number
     * @return string Generated admission number
     */
    public static function generateAdmissionNumber(string $formatPattern, string $programmeAbbr, int $sequenceNumber): string
    {
        $year = date('Y');
        $shortYear = date('y');
        $month = date('m');
        $monthName = strtoupper(date('M'));
        $monthInitial = strtoupper(substr(date('F'), 0, 1));

        // Format sequence number with padding
        $seq4 = str_pad((string)$sequenceNumber, 4, '0', STR_PAD_LEFT);
        $seq3 = str_pad((string)$sequenceNumber, 3, '0', STR_PAD_LEFT);
        $seq2 = str_pad((string)$sequenceNumber, 2, '0', STR_PAD_LEFT);

        // Replace placeholders
        $admissionNumber = $formatPattern;
        $admissionNumber = str_replace('{PROG_ABBR}', $programmeAbbr, $admissionNumber);
        $admissionNumber = str_replace('{YYYY}', $year, $admissionNumber);
        $admissionNumber = str_replace('{YY}', $shortYear, $admissionNumber);
        $admissionNumber = str_replace('{MM}', $month, $admissionNumber);
        $admissionNumber = str_replace('{MON}', $monthName, $admissionNumber);
        $admissionNumber = str_replace('{M}', $monthInitial, $admissionNumber);
        $admissionNumber = str_replace('{SEQ4}', $seq4, $admissionNumber);
        $admissionNumber = str_replace('{SEQ3}', $seq3, $admissionNumber);
        $admissionNumber = str_replace('{SEQ2}', $seq2, $admissionNumber);

        return $admissionNumber;
    }
}
