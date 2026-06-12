<?php
class StudentPortalController extends Controller
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->pdo = Database::getInstance($config['db']);
    }

    public function registerForm(): void
    {
        $this->view('pages/portal_register', ['metaTitle' => 'Student Portal - Register']);
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        $normalizedEmail = strtolower(trim($email));
        $validDomain = (bool)preg_match('/^[a-z0-9._%+\-]+@stmarysmchmcollege\.ac\.ke$/', $normalizedEmail);
        if (
            $name === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !$validDomain ||
            strlen($password) < 6 ||
            $password !== $confirmPassword
        ) {
            flash('error', 'Use your official college email ending with @stmarysmchmcollege.ac.ke. Password must be at least 6 characters and match confirmation.');
            $this->redirect('portal/register');
        }

        $model = new StudentPortalModel($this->config);
        if ($model->findStudentByEmail($email) !== null) {
            flash('error', 'An account with that email already exists.');
            $this->redirect('portal/register');
        }

        $model->createStudent($name, $email, password_hash($password, PASSWORD_DEFAULT));
        flash('success', 'Account created successfully. Admissions will assign your admission number. You will use admission number plus your password to log in.');
        $this->redirect('portal/login');
    }

    public function loginForm(): void
    {
        $this->view('pages/portal_login', ['metaTitle' => 'Student Portal - Login']);
    }

    public function login(): void
    {
        $limitKey = 'portal_login_' . md5((string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        if (!rate_limit_check($limitKey, 5, 15 * 60)) {
            flash('error', 'Too many login attempts. Please wait 15 minutes and try again.');
            $this->redirect('portal/login');
        }

        $admissionNumber = strtoupper(trim($_POST['admission_number'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $model = new StudentPortalModel($this->config);
        $student = $model->findStudentByAdmissionNumber($admissionNumber);

        if ($student === null || !password_verify($password, (string)$student['password'])) {
            rate_limit_increment($limitKey);
            flash('error', 'Invalid admission number or password.');
            $this->redirect('portal/login');
        }

        if (!empty($student['is_suspended'])) {
            rate_limit_increment($limitKey);
            flash('error', 'Your account has been suspended. Please contact the administration.');
            $this->redirect('portal/login');
        }

        rate_limit_clear($limitKey);
        session_regenerate_id(true);
        $_SESSION['student_id'] = (int)$student['id'];
        $_SESSION['student_name'] = (string)$student['name'];
        $_SESSION['student_email'] = (string)$student['email'];
        $_SESSION['student_admission_number'] = (string)($student['admission_number'] ?? '');
        $this->redirect('portal/dashboard');
    }

    public function logout(): void
    {
        unset($_SESSION['student_id'], $_SESSION['student_name'], $_SESSION['student_email'], $_SESSION['student_admission_number']);
        flash('success', 'You have been logged out.');
        $this->redirect('portal/login');
    }

    public function dashboard(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);

        // Fetch programme information
        $programmeName = '';
        if (!empty($student['programme_id'])) {
            $stmt = $this->pdo->prepare('SELECT name FROM programmes WHERE id = ? LIMIT 1');
            $stmt->execute([(int)$student['programme_id']]);
            $programme = $stmt->fetch(PDO::FETCH_ASSOC);
            $programmeName = $programme['name'] ?? '';
        }

        // Fetch student's courses based on their programme (many-to-many support)
        $courses = [];
        if (!empty($student['programme_id'])) {
            try {
                // Use junction table to get all courses linked to student's programme
                $stmt = $this->pdo->prepare('
                    SELECT pc.*, p.name AS programme_name, u.name AS teacher_name
                    FROM portal_courses pc
                    JOIN portal_course_programmes pcp ON pcp.portal_course_id = pc.id
                    LEFT JOIN programmes p ON p.id = pcp.programme_id
                    LEFT JOIN users u ON u.id = pc.teacher_id
                    WHERE pcp.programme_id = ?
                    GROUP BY pc.id
                    ORDER BY pc.created_at DESC
                ');
                $stmt->execute([(int)$student['programme_id']]);
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException) {
                $courses = [];
            }
        }

        // Fetch assignments for student's courses
        $assignments = [];
        if (!empty($courses)) {
            $courseIds = array_column($courses, 'id');
            if (!empty($courseIds)) {
                try {
                    $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
                    $stmt = $this->pdo->prepare("
                        SELECT ca.*, pc.title AS course_title, pc.code AS course_code
                        FROM course_assignments ca
                        LEFT JOIN portal_courses pc ON pc.id = ca.course_id
                        WHERE ca.course_id IN ($placeholders)
                        ORDER BY ca.created_at DESC
                    ");
                    $stmt->execute($courseIds);
                    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException) {
                    $assignments = [];
                }
            }
        }

        $this->view('student/dashboard', [
            'metaTitle' => 'Student Portal - Dashboard',
            'student' => $student,
            'programmeName' => $programmeName,
            'timetables' => $model->latestTimetables(8),
            'announcements' => $model->latestAnnouncements(8),
            'courses' => $courses,
            'assignments' => $assignments,
        ], 'student');
    }

    public function forgotPasswordForm(): void
    {
        $this->view('pages/portal_forgot_password', ['metaTitle' => 'Student Portal - Forgot Password']);
    }

    public function sendResetCode(): void
    {
        $limitKey = 'portal_reset_' . md5((string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        if (!rate_limit_check($limitKey, 5, 15 * 60)) {
            flash('error', 'Too many reset requests. Please wait 15 minutes and try again.');
            $this->redirect('portal/forgot-password');
        }

        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            rate_limit_increment($limitKey);
            flash('error', 'Please provide a valid email address.');
            $this->redirect('portal/forgot-password');
        }

        $model = new StudentPortalModel($this->config);
        $student = $model->findStudentByEmail($email);
        if ($student === null) {
            rate_limit_increment($limitKey);
            flash('error', 'No student account found with that email.');
            $this->redirect('portal/forgot-password');
        }

        $code = (string)random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', time() + 15 * 60);
        $model->saveResetCode((int)$student['id'], $code, $expiresAt);

        $mailBody = implode("\n", [
            'Your Student Portal password reset code is: ' . $code,
            'This code expires in 15 minutes.',
            '',
            'If you did not request this reset, please ignore this message.',
        ]);
        send_notification_email($email, 'Student Portal Password Reset Code', $mailBody);

        rate_limit_clear($limitKey);
        flash('success', 'A reset code has been sent to your email.');
        $this->redirect('portal/reset-password');
    }

    public function resetPasswordForm(): void
    {
        $this->view('pages/portal_reset_password', ['metaTitle' => 'Student Portal - Reset Password']);
    }

    public function resetPassword(): void
    {
        $email = trim($_POST['email'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');

        // Enhanced validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please provide a valid email address.');
            $this->redirect('portal/reset-password');
        }

        if ($code === '' || strlen($code) < 4) {
            flash('error', 'Please provide a valid reset code.');
            $this->redirect('portal/reset-password');
        }

        if (strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters long.');
            $this->redirect('portal/reset-password');
        }

        if ($password !== $confirm) {
            flash('error', 'Passwords do not match. Please try again.');
            $this->redirect('portal/reset-password');
        }

        $model = new StudentPortalModel($this->config);
        $reset = $model->findValidResetCode($email, $code);

        if ($reset === null) {
            flash('error', 'Invalid or expired reset code. Please request a new code.');
            $this->redirect('portal/reset-password');
        }

        $studentId = (int)$reset['student_id'];
        try {
            $model->updateStudentPassword($studentId, password_hash($password, PASSWORD_DEFAULT));
            $model->markResetCodeUsed((int)$reset['id']);
            flash('success', 'Password reset successful. You can now log in with your new password.');
            $this->redirect('portal/login');
        } catch (Exception $e) {
            flash('error', 'An error occurred while resetting your password. Please try again.');
            $this->redirect('portal/reset-password');
        }
    }

    private function requireStudent(): array
    {
        $studentId = (int)($_SESSION['student_id'] ?? 0);
        if ($studentId <= 0) {
            flash('error', 'Please log in to continue.');
            $this->redirect('portal/login');
        }

        $model = new StudentPortalModel($this->config);
        $student = $model->findStudentById($studentId);
        if ($student === null) {
            unset($_SESSION['student_id'], $_SESSION['student_name'], $_SESSION['student_email']);
            flash('error', 'Session expired. Please log in again.');
            $this->redirect('portal/login');
        }
        return $student;
    }

    // Academic Section
    public function courses(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);

        // Get courses specific to the student's programme
        $courses = [];
        if (!empty($student['programme_id'])) {
            $courses = $model->getStudentCourses((int)$student['programme_id']);
        }

        $this->view('student/courses', [
            'metaTitle' => 'Student Portal - My Units',
            'courses' => $courses,
        ], 'student');
    }

    public function grades(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $gradesTable = $model->getStudentGradesTable((int)($student['id'] ?? 0));
        $this->view('student/grades', [
            'metaTitle' => 'Student Portal - Grades',
            'gradeRows' => $gradesTable['rows'],
            'examColumns' => $gradesTable['examColumns'],
        ], 'student');
    }

    public function attendance(): void
    {
        $student = $this->requireStudent();
        $this->view('student/attendance', ['metaTitle' => 'Student Portal - Attendance'], 'student');
    }

    public function timetable(): void
    {
        $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $this->view('student/timetable', [
            'metaTitle' => 'Student Portal - Timetable',
            'timetables' => $model->allProgrammeTimetables(),
        ], 'student');
    }

    // Resources Section
    public function library(): void
    {
        $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $this->view('student/library', [
            'metaTitle' => 'Student Portal - Digital Library',
            'libraryResources' => $model->allLibraryResources(),
        ], 'student');
    }

    public function assignments(): void
    {
        $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $this->view('student/assignments', [
            'metaTitle' => 'Student Portal - Assignments',
            'assignments' => $model->allAssignments(),
        ], 'student');
    }

    public function resources(): void
    {
        $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $this->view('student/resources', [
            'metaTitle' => 'Student Portal - Study Materials',
            'materials' => $model->allStudyMaterials(),
        ], 'student');
    }

    public function exams(): void
    {
        $student = $this->requireStudent();
        $this->view('student/exams', ['metaTitle' => 'Student Portal - Exams'], 'student');
    }

    // Campus Life Section
    public function events(): void
    {
        $student = $this->requireStudent();
        try {
            $stmt = $this->pdo->query('SELECT * FROM events ORDER BY starts_at DESC');
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException) {
            $events = [];
        }
        $this->view('student/events', [
            'metaTitle' => 'Student Portal - Campus Events',
            'events' => $events,
        ], 'student');
    }

    public function clubs(): void
    {
        $student = $this->requireStudent();
        $this->view('student/clubs', ['metaTitle' => 'Student Portal - Clubs & Societies'], 'student');
    }

    public function announcements(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $this->view('student/announcements', [
            'metaTitle' => 'Student Portal - Announcements',
            'announcements' => $model->latestAnnouncements(20),
        ], 'student');
    }

    // Services Section
    public function fees(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $invoices = $model->getStudentInvoices((int)$student['id']);
        $payments = $model->getStudentPayments((int)$student['id']);
        $balance = $model->getStudentBalance((int)$student['id']);

        $this->view('student/fees', [
            'metaTitle' => 'Student Portal - Fee Statement',
            'invoices' => $invoices,
            'payments' => $payments,
            'balance' => $balance
        ], 'student');
    }

    public function clearance(): void
    {
        $student = $this->requireStudent();
        $this->view('student/clearance', ['metaTitle' => 'Student Portal - Clearance Status'], 'student');
    }

    public function certificates(): void
    {
        $student = $this->requireStudent();
        $this->view('student/certificates', ['metaTitle' => 'Student Portal - Certificates'], 'student');
    }

    public function support(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $this->view('student/support', [
            'metaTitle' => 'Student Portal - IT Support',
            'tickets' => $model->getSupportTicketsByStudent((int)$student['id']),
        ], 'student');
    }

    public function receipt(int $paymentId): void
    {
        $student = $this->requireStudent();

        try {
            $pdo = Database::getInstance($this->config['db']);

            // Get payment details
            $stmt = $pdo->prepare('SELECT p.*, i.invoice_number, i.title AS invoice_title, i.amount AS invoice_amount,
                                          s.name AS student_name, s.admission_number, s.email AS student_email,
                                          pm.name AS payment_method_name
                                   FROM payments p
                                   LEFT JOIN invoices i ON p.invoice_id = i.id
                                   LEFT JOIN student_accounts s ON p.student_id = s.id
                                   LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
                                   WHERE p.id = ? AND p.student_id = ?');
            $stmt->execute([$paymentId, $student['id']]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$payment) {
                flash('error', 'Payment not found.');
                $this->redirect('student/fees');
                return;
            }

            // Get invoice balance info
            $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) AS total_paid FROM payments WHERE invoice_id = ?');
            $stmt->execute([$payment['invoice_id']]);
            $totalPaid = $stmt->fetch(PDO::FETCH_ASSOC)['total_paid'];

            // Mark receipt as generated
            $stmt = $pdo->prepare('UPDATE payments SET receipt_generated = 1 WHERE id = ?');
            $stmt->execute([$paymentId]);

            // Get settings for contact details
            $stmt = $pdo->prepare('SELECT setting_key, setting_value FROM settings WHERE setting_key IN (?, ?, ?)');
            $stmt->execute(['phone', 'email', 'location']);
            $settingsRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $settings = [];
            foreach ($settingsRows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }

            if ((string)($_GET['download'] ?? '') === '1') {
                $pdf = $this->buildStudentReceiptPdf($payment, (float)$totalPaid, $settings);
                $this->streamPdfDownload($pdf, 'Receipt_' . preg_replace('/[^A-Za-z0-9_-]+/', '_', (string)$payment['payment_number']) . '.pdf');
                return;
            }

            // Render receipt without layout for standalone display
            $receiptPath = __DIR__ . '/../../views/student/receipt.php';
            if (!file_exists($receiptPath)) {
                flash('error', 'Receipt view not found at: ' . $receiptPath);
                $this->redirect('student/fees');
                return;
            }
            require_once $receiptPath;
            exit;
        } catch (PDOException $e) {
            flash('error', 'Database error: ' . $e->getMessage());
            $this->redirect('student/fees');
        } catch (Throwable $e) {
            flash('error', 'Error: ' . $e->getMessage());
            $this->redirect('student/fees');
        }
    }

    public function invoice(int $invoiceId): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);

        try {
            $pdo = Database::getInstance($this->config['db']);

            // Get invoice details
            $invoice = $model->getInvoiceById($invoiceId);

            if (!$invoice) {
                flash('error', 'Invoice not found.');
                $this->redirect('student/fees');
                return;
            }

            // Check if this invoice belongs to the logged-in student
            if ($invoice['student_id'] != $student['id']) {
                flash('error', 'Access denied.');
                $this->redirect('student/fees');
                return;
            }

            // Get fee items
            $stmt = $pdo->prepare('SELECT * FROM fee_items WHERE invoice_id = ?');
            $stmt->execute([$invoiceId]);
            $feeItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get payments
            $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) AS total_paid FROM payments WHERE invoice_id = ?');
            $stmt->execute([$invoiceId]);
            $totalPaid = $stmt->fetch(PDO::FETCH_ASSOC)['total_paid'];

            $balance = $invoice['amount'] - $totalPaid;

            // Get settings for contact details
            $stmt = $pdo->prepare('SELECT setting_key, setting_value FROM settings WHERE setting_key IN (?, ?, ?)');
            $stmt->execute(['phone', 'email', 'location']);
            $settingsRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $settings = [];
            foreach ($settingsRows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }

            if ((string)($_GET['download'] ?? '') === '1') {
                $pdf = $this->buildStudentInvoicePdf($invoice, $feeItems, (float)$totalPaid, (float)$balance, $settings);
                $this->streamPdfDownload($pdf, 'Invoice_' . preg_replace('/[^A-Za-z0-9_-]+/', '_', (string)$invoice['invoice_number']) . '.pdf');
                return;
            }

            // Render invoice without layout for standalone display
            $invoicePath = __DIR__ . '/../../views/student/invoice.php';
            if (!file_exists($invoicePath)) {
                flash('error', 'Invoice view not found at: ' . $invoicePath);
                $this->redirect('student/fees');
                return;
            }
            require_once $invoicePath;
            exit;
        } catch (PDOException $e) {
            flash('error', 'Database error: ' . $e->getMessage());
            $this->redirect('student/fees');
        } catch (Throwable $e) {
            flash('error', 'Error: ' . $e->getMessage());
            $this->redirect('student/fees');
        }
    }

    private function streamPdfDownload(string $pdf, string $filename): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        header('Cache-Control: private, max-age=0, must-revalidate');
        echo $pdf;
        exit;
    }

    private function buildStudentInvoicePdf(array $invoice, array $feeItems, float $totalPaid, float $balance, array $settings): string
    {
        $lines = [
            ['Official Student Invoice', 16],
            ['Invoice Number: ' . (string)($invoice['invoice_number'] ?? '-'), 10],
            ['Student: ' . (string)($invoice['student_name'] ?? '-'), 10],
            ['Admission No: ' . (string)($invoice['admission_number'] ?? '-'), 10],
            ['Title: ' . (string)($invoice['title'] ?? '-'), 10],
            ['Issue Date: ' . (string)($invoice['issue_date'] ?? '-'), 10],
            ['Due Date: ' . (string)($invoice['due_date'] ?? '-'), 10],
            ['', 8],
            ['Fee Items', 12],
        ];

        foreach ($feeItems as $item) {
            $lines[] = [(string)($item['description'] ?? $item['title'] ?? 'Fee Item') . ': KES ' . number_format((float)($item['amount'] ?? 0), 2), 9];
        }

        $lines[] = ['', 8];
        $lines[] = ['Total Amount: KES ' . number_format((float)($invoice['amount'] ?? 0), 2), 10];
        $lines[] = ['Total Paid: KES ' . number_format($totalPaid, 2), 10];
        $lines[] = ['Balance: KES ' . number_format($balance, 2), 10];
        $lines[] = ['Status: ' . ucfirst((string)($invoice['status'] ?? '-')), 10];

        return $this->buildSimpleFinancialPdf($lines, $settings);
    }

    private function buildStudentReceiptPdf(array $payment, float $totalPaid, array $settings): string
    {
        $lines = [
            ['Official Payment Receipt', 16],
            ['Receipt Number: ' . (string)($payment['payment_number'] ?? '-'), 10],
            ['Payment Date: ' . (string)($payment['payment_date'] ?? '-'), 10],
            ['Student: ' . (string)($payment['student_name'] ?? '-'), 10],
            ['Admission No: ' . (string)($payment['admission_number'] ?? '-'), 10],
            ['Invoice Number: ' . (string)($payment['invoice_number'] ?? '-'), 10],
            ['Invoice Title: ' . (string)($payment['invoice_title'] ?? '-'), 10],
            ['Payment Method: ' . (string)($payment['payment_method_name'] ?? '-'), 10],
            ['Reference: ' . (string)($payment['transaction_code'] ?? $payment['cheque_number'] ?? 'N/A'), 10],
            ['', 8],
            ['Amount Paid: KES ' . number_format((float)($payment['amount'] ?? 0), 2), 12],
            ['Invoice Total Paid: KES ' . number_format($totalPaid, 2), 10],
        ];

        return $this->buildSimpleFinancialPdf($lines, $settings);
    }

    private function buildSimpleFinancialPdf(array $lines, array $settings): string
    {
        $pageWidth = 595;
        $pageHeight = 842;
        $content = '';
        $y = 790;
        $x = 58;
        $collegeName = 'St. Mary\'s Mother and Child Hospital Medical Training College';

        $write = function (string $value, int $size = 10, string $font = 'F1') use (&$content, &$y, $x): void {
            if ($value === '') {
                $y -= 8;
                return;
            }
            $content .= sprintf("BT /%s %d Tf %.2f %.2f Td (%s) Tj ET\n", $font, $size, $x, $y, $this->pdfEscape($value));
            $y -= ($size + 8);
        };

        $write($collegeName, 13, 'F2');
        if (!empty($settings['location'])) {
            $write((string)$settings['location'], 9);
        }
        $contact = trim(implode(' | ', array_filter([(string)($settings['phone'] ?? ''), (string)($settings['email'] ?? '')])));
        if ($contact !== '') {
            $write($contact, 9);
        }
        $content .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x, $y, $pageWidth - $x, $y);
        $y -= 24;

        foreach ($lines as $line) {
            $write((string)($line[0] ?? ''), (int)($line[1] ?? 10), ((int)($line[1] ?? 10) >= 12 ? 'F2' : 'F1'));
            if ($y < 70) {
                break;
            }
        }

        $y -= 20;
        $write('Generated: ' . date('F j, Y'), 8);

        return $this->assemblePdf([$content], $pageWidth, $pageHeight);
    }

    public function transcript(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $transcript = $model->getStudentTranscriptData((int)$student['id']);

        if (empty($transcript['student'])) {
            flash('error', 'Transcript details could not be loaded right now.');
            $this->redirect('portal/grades');
            return;
        }

        if ((string)($_GET['download'] ?? '') === '1') {
            $this->streamTranscriptPdf($transcript);
            return;
        }

        $transcriptPath = __DIR__ . '/../../views/student/transcript.php';
        if (!file_exists($transcriptPath)) {
            flash('error', 'Transcript view not found.');
            $this->redirect('portal/grades');
            return;
        }

        require_once $transcriptPath;
        exit;
    }

    private function streamTranscriptPdf(array $transcript): void
    {
        $admissionNumber = preg_replace('/[^A-Za-z0-9_-]+/', '_', (string)($transcript['student']['admission_number'] ?? 'student'));
        $admissionNumber = trim((string)$admissionNumber, '_');
        if ($admissionNumber === '') {
            $admissionNumber = 'student';
        }

        try {
            $pdf = $this->buildTranscriptPdf($transcript);
        } catch (Throwable $e) {
            error_log('Transcript PDF generation failed: ' . $e->getMessage());
            $pdf = $this->buildTranscriptFallbackPdf($transcript);
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Transcript_' . $admissionNumber . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        header('Cache-Control: private, max-age=0, must-revalidate');
        echo $pdf;
        exit;
    }

    private function buildTranscriptPdf(array $transcript): string
    {
        // A4 portrait in PDF points.
        $pageWidth = 595;
        $pageHeight = 842;
        $margin = 34;
        $bottomMargin = 56;
        $pages = [];
        $content = '';
        $pageNumber = 0;
        $y = $pageHeight - $margin;

        $primary = [24, 84, 144];
        $secondary = [10, 170, 232];
        $dark = [31, 41, 55];
        $muted = [75, 85, 99];
        $border = [216, 225, 238];
        $light = [248, 250, 252];
        $white = [255, 255, 255];

        $rgb = static fn(array $color): string => sprintf('%.3f %.3f %.3f', $color[0] / 255, $color[1] / 255, $color[2] / 255);
        $setFill = function (array $color) use (&$content, $rgb): void {
            $content .= $rgb($color) . " rg\n";
        };
        $setStroke = function (array $color, float $width = 0.5) use (&$content, $rgb): void {
            $content .= $rgb($color) . sprintf(" RG %.2f w\n", $width);
        };
        $fillRect = function (float $x, float $rectY, float $width, float $height, array $color) use (&$content, $setFill): void {
            $setFill($color);
            $content .= sprintf("%.2f %.2f %.2f %.2f re f\n", $x, $rectY, $width, $height);
        };
        $strokeRect = function (float $x, float $rectY, float $width, float $height, array $color, float $lineWidth = 0.5) use (&$content, $setStroke): void {
            $setStroke($color, $lineWidth);
            $content .= sprintf("%.2f %.2f %.2f %.2f re S\n", $x, $rectY, $width, $height);
        };
        $line = function (float $x1, float $y1, float $x2, float $y2, array $color, float $lineWidth = 0.5) use (&$content, $setStroke): void {
            $setStroke($color, $lineWidth);
            $content .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x1, $y1, $x2, $y2);
        };
        $text = function (float $x, float $textY, int $size, string $value, array $color, string $font = 'F1') use (&$content, $setFill): void {
            $setFill($color);
            $content .= sprintf("BT /%s %d Tf %.2f %.2f Td (%s) Tj ET\n", $font, $size, $x, $textY, $this->pdfEscape($value));
        };
        $estimateTextWidth = static function (string $value, int $size): float {
            $width = 0.0;
            $chars = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: str_split($value);
            foreach ($chars as $char) {
                if ($char === ' ') {
                    $width += 0.28;
                } elseif (preg_match('/[ilI1\|]/u', $char)) {
                    $width += 0.24;
                } elseif (preg_match('/[MW@#%&]/u', $char)) {
                    $width += 0.78;
                } elseif (preg_match('/[A-Z0-9]/u', $char)) {
                    $width += 0.56;
                } else {
                    $width += 0.48;
                }
            }
            return $width * $size;
        };
        $centerText = function (float $centerX, float $textY, int $size, string $value, array $color, string $font = 'F1') use ($text, $estimateTextWidth): void {
            $textWidth = $estimateTextWidth($value, $size);
            $text($centerX - ($textWidth / 2), $textY, $size, $value, $color, $font);
        };
        $wrappedText = function (float $x, float $textY, int $size, string $value, float $width, int $maxLines, array $color, string $font = 'F1') use ($text): int {
            $maxChars = max(4, (int)floor($width / max(3.2, $size * 0.48)));
            $lines = $this->pdfWrapText($value, $maxChars, $maxLines);
            foreach ($lines as $lineIndex => $lineText) {
                $text($x, $textY - ($lineIndex * ($size + 2)), $size, $lineText, $color, $font);
            }
            return count($lines);
        };

        $finishPage = function () use (&$pages, &$content): void {
            if ($content !== '') {
                $pages[] = $content;
                $content = '';
            }
        };

        $student = $transcript['student'] ?? [];
        $settings = $transcript['settings'] ?? [];
        $examColumns = array_values($transcript['examColumns'] ?? []);
        $rows = array_values($transcript['rows'] ?? []);
        $gradeRanges = $transcript['gradeRanges'] ?? [];
        $issuedAt = date('F j, Y');
        $verificationCode = 'TR-' . strtoupper(substr(sha1((string)($student['id'] ?? '') . '|' . (string)($student['admission_number'] ?? '') . '|' . date('YmdHis')), 0, 12));
        $serialNumber = 'SR-' . date('Ymd') . '-' . str_pad((string)((int)($student['id'] ?? 0)), 5, '0', STR_PAD_LEFT);
        $transcriptNumber = 'TR-' . date('Ymd-His') . '-' . strtoupper(substr($verificationCode, -6));
        $appName = (string)($this->config['app_name'] ?? 'St. Mary\'s Mother and Child Hospital Medical Training College');
        $collegeName = $appName !== '' ? $appName : 'St. Mary\'s Mother and Child Hospital Medical Training College';
        $collegeLineOne = 'ST. MARY\'S MOTHER AND CHILD HOSPITAL';
        $collegeLineTwo = 'MEDICAL TRAINING COLLEGE';
        $collegeAddress = trim((string)($settings['address'] ?? $settings['location'] ?? ''));
        $collegeWebsite = trim((string)($settings['website'] ?? $settings['site_url'] ?? $this->config['base_url'] ?? ''));
        $collegeContacts = trim(implode(' | ', array_filter([
            (string)($settings['phone'] ?? ''),
            $collegeWebsite,
            (string)($settings['email'] ?? ''),
        ])));
        $summary = $this->buildTranscriptAcademicSummary($rows);
        try {
            $logoImage = $this->prepareTranscriptPdfLogo($settings);
        } catch (Throwable $e) {
            error_log('Transcript logo loading failed: ' . $e->getMessage());
            $logoImage = null;
        }
        $pdfImages = $logoImage !== null ? ['Logo' => $logoImage] : [];

        $drawLogo = function (float $x, float $logoY, float $size) use (&$content, $logoImage, $primary, $white, $fillRect, $strokeRect, $text): void {
            if ($logoImage !== null) {
                $content .= sprintf("q %.2f 0 0 %.2f %.2f %.2f cm /Logo Do Q\n", $size, $size, $x, $logoY);
                return;
            }
            $fillRect($x, $logoY, $size, $size, $white);
            $strokeRect($x, $logoY, $size, $size, $primary, 0.8);
            $text($x + 7, $logoY + ($size / 2) - 4, 10, 'STM', $primary, 'F2');
        };

        $drawPageHeader = function (bool $firstPage) use (&$y, &$pageNumber, $pageWidth, $pageHeight, $margin, $primary, $secondary, $dark, $muted, $line, $text, $centerText, $drawLogo, $collegeLineOne, $collegeLineTwo, $collegeAddress, $collegeContacts, $serialNumber, $transcriptNumber, $issuedAt, $verificationCode): void {
            $pageNumber++;
            $y = $pageHeight - $margin;
            $logoSize = 40;
            $logoY = $pageHeight - 70;
            $drawLogo($margin + 6, $logoY, $logoSize);
            $drawLogo($pageWidth - $margin - $logoSize - 6, $logoY, $logoSize);
            $centerX = $pageWidth / 2;
            $centerText($centerX, $pageHeight - 33, 12, $collegeLineOne, $primary, 'F5');
            $centerText($centerX, $pageHeight - 47, 12, $collegeLineTwo, $primary, 'F5');
            if ($collegeAddress !== '') {
                $centerText($centerX, $pageHeight - 60, 9, $this->truncatePdfText($collegeAddress, 58), $dark, 'F1');
            }
            if ($collegeContacts !== '') {
                $centerText($centerX, $pageHeight - 71, 9, $this->truncatePdfText($collegeContacts, 64), $dark, 'F1');
            }
            $line($margin, $pageHeight - 86, $pageWidth - $margin, $pageHeight - 86, $primary, 1.0);
            $line($margin, $pageHeight - 89, $pageWidth - $margin, $pageHeight - 89, $secondary, 0.6);
            $y = $pageHeight - 107;

            if ($firstPage) {
                $centerText($pageWidth / 2, $y, 17, 'OFFICIAL ACADEMIC TRANSCRIPT', $primary, 'F5');
                $centerText($pageWidth / 2, $y - 18, 9, 'Serial No: ' . $serialNumber . '  I  Transcript No: ' . $transcriptNumber, $dark, 'F1');
                $line($margin, $y - 30, $pageWidth - $margin, $y - 30, $secondary, 0.9);
                $y -= 42;
            } else {
                $text($margin, $y, 8, 'Continuation for ' . (string)($student['name'] ?? 'Student'), $muted, 'F2');
                $line($margin, $y - 6, $pageWidth - $margin, $y - 6, $secondary, 0.6);
                $y -= 17;
            }
        };

        $drawFooter = function () use ($pageWidth, $margin, $primary, $muted, $line, $text, $issuedAt): void {
            $line($margin, 42, $pageWidth - $margin, 42, $primary, 0.8);
            $text($margin, 28, 8, 'Generated from official student portal records.', $muted, 'F1');
            $text($pageWidth - 145, 28, 8, 'Generated: ' . $issuedAt, $muted, 'F1');
        };

        $drawMeta = function () use (&$y, $pageWidth, $margin, $primary, $dark, $muted, $border, $white, $fillRect, $strokeRect, $line, $text, $wrappedText, $student, $transcript): void {
            $boxWidth = ($pageWidth - ($margin * 2) - 12) / 2;
            $boxHeight = 88;
            $leftX = $margin;
            $rightX = $margin + $boxWidth + 12;
            $boxY = $y - $boxHeight;
            $fillRect($leftX, $boxY, $boxWidth, $boxHeight, $white);
            $fillRect($rightX, $boxY, $boxWidth, $boxHeight, $white);
            $strokeRect($leftX, $boxY, $boxWidth, $boxHeight, $border, 0.5);
            $strokeRect($rightX, $boxY, $boxWidth, $boxHeight, $border, 0.5);
            $text($leftX + 8, $y - 14, 10, 'Student Details', $primary, 'F2');
            $text($rightX + 8, $y - 14, 10, 'Academic Details', $primary, 'F2');
            $line($leftX + 8, $y - 21, $leftX + $boxWidth - 8, $y - 21, $border, 0.4);
            $line($rightX + 8, $y - 21, $rightX + $boxWidth - 8, $y - 21, $border, 0.4);

            $metaY = $y - 33;
            $labelWidth = 72;
            $text($leftX + 8, $metaY, 9, 'Name', $muted, 'F2');
            $wrappedText($leftX + $labelWidth, $metaY, 9, (string)($student['name'] ?? '-'), $boxWidth - $labelWidth - 8, 1, $dark);
            $metaY -= 16;
            $text($leftX + 8, $metaY, 9, 'Admission No.', $muted, 'F2');
            $wrappedText($leftX + $labelWidth, $metaY, 9, (string)($student['admission_number'] ?? '-'), $boxWidth - $labelWidth - 8, 1, $dark);
            $metaY -= 16;
            $text($leftX + 8, $metaY, 9, 'National ID', $muted, 'F2');
            $text($leftX + $labelWidth, $metaY, 9, (string)($student['national_id'] ?? '-'), $dark);
            $metaY -= 16;
            $text($leftX + 8, $metaY, 9, 'Email', $muted, 'F2');
            $wrappedText($leftX + $labelWidth, $metaY, 9, (string)($student['email'] ?? '-'), $boxWidth - $labelWidth - 8, 1, $dark);

            $metaY = $y - 33;
            $text($rightX + 8, $metaY, 9, 'Programme', $muted, 'F2');
            $wrappedText($rightX + $labelWidth, $metaY, 9, (string)($student['programme_name'] ?? '-'), $boxWidth - $labelWidth - 8, 1, $dark);
            $metaY -= 16;
            $text($rightX + 8, $metaY, 9, 'Session', $muted, 'F2');
            $text($rightX + $labelWidth, $metaY, 9, (string)($transcript['session_name'] ?? '-'), $dark);
            $metaY -= 16;
            $text($rightX + 8, $metaY, 9, 'Term/Semester', $muted, 'F2');
            $text($rightX + $labelWidth, $metaY, 9, (string)($transcript['term_name'] ?? '-'), $dark);
            $metaY -= 16;
            $text($rightX + 8, $metaY, 9, 'Date Issued', $muted, 'F2');
            $text($rightX + $labelWidth, $metaY, 9, date('F j, Y'), $dark);
            $y = $boxY - 14;
        };

        $contentWidth = $pageWidth - ($margin * 2);
        $examCount = count($examColumns);
        $codeWidth = 48;
        $unitWidth = $examCount > 4 ? 112 : 132;
        $gradeWidth = 34;
        $commentWidth = $examCount > 4 ? 72 : 88;
        $examWidth = $examCount > 0 ? floor(($contentWidth - $codeWidth - $unitWidth - $gradeWidth - $commentWidth) / $examCount) : 0;
        if ($examWidth < 24 && $examCount > 0) {
            $examWidth = 24;
            $unitWidth = max(86, $contentWidth - $codeWidth - $gradeWidth - $commentWidth - ($examWidth * $examCount));
        }

        $columns = [
            ['label' => 'Unit Code', 'key' => 'course_code', 'width' => $codeWidth, 'align' => 'left'],
            ['label' => 'Unit Name', 'key' => 'course_title', 'width' => $unitWidth, 'align' => 'left'],
        ];
        foreach ($examColumns as $examColumn) {
            $columns[] = [
                'label' => (string)($examColumn['label'] ?? 'Exam'),
                'exam_id' => (int)($examColumn['id'] ?? 0),
                'width' => $examWidth,
                'align' => 'center',
            ];
        }
        $columns[] = ['label' => 'Grade', 'key' => 'grade', 'width' => $gradeWidth, 'align' => 'center'];
        $columns[] = ['label' => 'Remarks', 'key' => 'comment', 'width' => $commentWidth, 'align' => 'left'];

        $drawTableHeader = function () use (&$y, $margin, $columns, $light, $dark, $border, $fillRect, $strokeRect, $wrappedText): void {
            $rowHeight = 20;
            $x = $margin;
            foreach ($columns as $column) {
                $width = (float)$column['width'];
                $fillRect($x, $y - $rowHeight, $width, $rowHeight, $light);
                $strokeRect($x, $y - $rowHeight, $width, $rowHeight, $border, 0.3);
                $wrappedText($x + 3, $y - 7, 8, (string)$column['label'], $width - 6, 2, $dark, 'F2');
                $x += $width;
            }
            $y -= $rowHeight;
        };

        $drawPageHeader(true);
        $drawMeta();
        $drawTableHeader();

        if ($rows === []) {
            $text($margin, $y - 18, 9, 'No grades are available to include in this transcript yet.', $muted);
        } else {
            foreach ($rows as $rowIndex => $row) {
                $rowHeight = 20;
                if ($y - $rowHeight < $bottomMargin) {
                    $drawFooter();
                    $finishPage();
                    $drawPageHeader(false);
                    $drawTableHeader();
                }

                $x = $margin;
                $fill = $rowIndex % 2 === 0 ? $white : $light;
                foreach ($columns as $column) {
                    $width = (float)$column['width'];
                    if (isset($column['exam_id'])) {
                        $mark = $row['exam_marks'][(int)$column['exam_id']] ?? null;
                        $value = ($mark === null || $mark === '') ? '-' : (string)$mark;
                    } else {
                        $value = (string)($row[$column['key']] ?? '-');
                        $value = $value === '' ? '-' : $value;
                    }
                    $fillRect($x, $y - $rowHeight, $width, $rowHeight, $fill);
                    $strokeRect($x, $y - $rowHeight, $width, $rowHeight, $border, 0.3);
                    $font = ($column['key'] ?? '') === 'grade' ? 'F2' : 'F1';
                    $size = 9;
                    $align = (string)($column['align'] ?? 'left');
                    if ($align === 'center') {
                        $textX = $x + ($width / 2) - (strlen($value) * $size * 0.26);
                    } elseif ($align === 'right') {
                        $textX = $x + $width - 5 - (strlen($value) * $size * 0.28);
                    } else {
                        $textX = $x + 3;
                    }
                    $wrappedText(max($x + 3, $textX), $y - 7, $size, $value, $width - 6, 1, $dark, $font);
                    $x += $width;
                }
                $y -= $rowHeight;
            }
        }

        if ($y - 40 < $bottomMargin) {
            $drawFooter();
            $finishPage();
            $drawPageHeader(false);
        }
        $y -= 18;
        $text($margin, $y, 10, 'ACADEMIC SUMMARY', $primary, 'F5');
        $y -= 14;
        $summaryLineOne = 'Units Registered: ' . $summary['registered']
            . '      Units Completed: ' . $summary['completed']
            . '      Units Passed: ' . $summary['passed'];
        $summaryLineTwo = 'Average Score: ' . $summary['average'] . '%'
            . '       Mean Grade: ' . $summary['mean_grade']
            . '           GPA: ' . $summary['gpa'];
        $text($margin, $y, 9, $summaryLineOne, $dark, 'F1');
        $y -= 14;
        $text($margin, $y, 9, $summaryLineTwo, $dark, 'F1');
        $y -= 22;

        $gradingKey = [];
        foreach ($examColumns as $examColumn) {
            if ((string)($examColumn['type'] ?? '') !== 'consolidated') {
                continue;
            }
            $gradingSystemId = (int)($examColumn['id'] ?? 0);
            foreach (($gradeRanges[$gradingSystemId] ?? []) as $range) {
                $grade = trim((string)($range['grade_letter'] ?? ''));
                if ($grade === '') {
                    continue;
                }
                $min = (int)round((float)($range['min_marks'] ?? 0));
                $max = (int)round((float)($range['max_marks'] ?? 0));
                $gradingKey[] = [
                    'grade' => $grade,
                    'range' => $min . '-' . $max,
                ];
            }
            if ($gradingKey !== []) {
                break;
            }
        }

        if ($gradingKey !== []) {
            $gradeCount = count($gradingKey);
            $gradeCellWidth = $gradeCount > 0 ? floor($contentWidth / $gradeCount) : $contentWidth;
            $gradingTableHeight = 42;
            if ($y - ($gradingTableHeight + 30) < $bottomMargin) {
                $drawFooter();
                $finishPage();
                $drawPageHeader(false);
            }
            $y -= 20;
            $text($margin, $y, 10, 'Grading System', $primary, 'F5');
            $y -= 12;
            $x = $margin;
            foreach ($gradingKey as $gradeRange) {
                $fillRect($x, $y - 18, $gradeCellWidth, 18, $light);
                $strokeRect($x, $y - 18, $gradeCellWidth, 18, $border, 0.3);
                $centerText($x + ($gradeCellWidth / 2), $y - 11, 9, (string)$gradeRange['grade'], $dark, 'F2');
                $x += $gradeCellWidth;
            }
            $y -= 18;
            $x = $margin;
            foreach ($gradingKey as $gradeRange) {
                $fillRect($x, $y - 16, $gradeCellWidth, 16, $white);
                $strokeRect($x, $y - 16, $gradeCellWidth, 16, $border, 0.3);
                $centerText($x + ($gradeCellWidth / 2), $y - 10, 9, (string)$gradeRange['range'], $dark, 'F1');
                $x += $gradeCellWidth;
            }
            $y -= 24;
        }


        if ($y - 112 < $bottomMargin) {
            $drawFooter();
            $finishPage();
            $drawPageHeader(false);
        }

        $y -= 22;
        $text($margin, $y, 10, 'Certification', $primary, 'F5');
        $y -= 16;
        $wrappedText($margin, $y, 9, 'This is to certify that the academic record appearing on this transcript is a true and accurate representation of the student\'s performance as maintained in the official records of the College.', $contentWidth, 3, $dark, 'F3');
        $y -= 32;
        $text($margin, $y, 9, 'Issued without alteration and for official purposes only.', $dark, 'F3');
        $y -= 36;
        $line($margin, $y, $margin + 150, $y, $dark, 0.5);
        $line($pageWidth - $margin - 150, $y, $pageWidth - $margin, $y, $dark, 0.5);
        $text($margin, $y - 14, 9, 'Registrar / Academic Officer', $muted, 'F1');
        $text($margin, $y - 26, 9, 'Date: ________________', $muted, 'F1');
        $text($pageWidth - $margin - 150, $y - 14, 9, 'Official College Stamp', $muted, 'F1');
        $text($pageWidth - $margin - 150, $y - 26, 9, 'Date: ________________', $muted, 'F1');

        $drawFooter();
        $finishPage();

        return $this->assemblePdf($pages, $pageWidth, $pageHeight, $pdfImages);
    }

    private function buildTranscriptFallbackPdf(array $transcript): string
    {
        $pageWidth = 595;
        $pageHeight = 842;
        $content = '';
        $student = $transcript['student'] ?? [];
        $y = 790;

        $write = function (float $x, int $size, string $value) use (&$content, &$y): void {
            $safe = $this->pdfEscape($value);
            $content .= sprintf("BT /F1 %d Tf %.2f %.2f Td (%s) Tj ET\n", $size, $x, $y, $safe);
            $y -= ($size + 8);
        };

        $write(60, 16, 'OFFICIAL ACADEMIC TRANSCRIPT');
        $write(60, 10, 'Student: ' . (string)($student['name'] ?? '-'));
        $write(60, 10, 'Admission No: ' . (string)($student['admission_number'] ?? '-'));
        $write(60, 10, 'Programme: ' . (string)($student['programme_name'] ?? '-'));
        $write(60, 10, 'Generated: ' . date('F j, Y'));
        $y -= 12;
        $write(60, 11, 'Results');

        foreach (array_slice(array_values($transcript['rows'] ?? []), 0, 30) as $row) {
            $marks = array_values(array_filter($row['exam_marks'] ?? [], static fn($mark): bool => $mark !== null && $mark !== ''));
            $total = $marks !== [] ? (string)end($marks) : '-';
            $write(60, 8, trim((string)($row['course_code'] ?? '-') . '  ' . (string)($row['course_title'] ?? '-') . '  Total: ' . $total . '  Grade: ' . (string)($row['grade'] ?? '-')));
            if ($y < 80) {
                break;
            }
        }

        return $this->assemblePdf([$content], $pageWidth, $pageHeight);
    }

    private function buildTranscriptAcademicSummary(array $rows): array
    {
        $registered = count($rows);
        $completed = 0;
        $passed = 0;
        $totalScore = 0.0;
        $scoreCount = 0;
        $gradeCounts = [];
        $gradePoints = ['A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'F' => 0.0];
        $gpaTotal = 0.0;
        $gpaCount = 0;

        foreach ($rows as $row) {
            $numericMarks = array_values(array_filter($row['exam_marks'] ?? [], static fn($mark): bool => $mark !== null && $mark !== '' && is_numeric($mark)));
            if ($numericMarks !== []) {
                $completed++;
                $score = (float)end($numericMarks);
                $totalScore += $score;
                $scoreCount++;
                if ($score >= 50) {
                    $passed++;
                }
            }

            $grade = strtoupper(trim((string)($row['grade'] ?? '')));
            if ($grade !== '') {
                $gradeCounts[$grade] = ($gradeCounts[$grade] ?? 0) + 1;
                if (array_key_exists($grade, $gradePoints)) {
                    $gpaTotal += $gradePoints[$grade];
                    $gpaCount++;
                }
            }
        }

        arsort($gradeCounts);
        $meanGrade = $gradeCounts !== [] ? (string)array_key_first($gradeCounts) : '-';

        return [
            'registered' => $registered,
            'completed' => $completed,
            'passed' => $passed,
            'average' => $scoreCount > 0 ? (string)((int)round($totalScore / $scoreCount)) : '-',
            'mean_grade' => $meanGrade,
            'gpa' => $gpaCount > 0 ? number_format($gpaTotal / $gpaCount, 2) : 'N/A',
        ];
    }



    private function pdfWrapText(string $value, int $maxChars, int $maxLines): array
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        if ($value === '') {
            return ['-'];
        }

        $words = preg_split('/\s+/', $value) ?: [];
        $lines = [];
        $current = '';
        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;
            if (strlen($candidate) <= $maxChars) {
                $current = $candidate;
                continue;
            }
            if ($current !== '') {
                $lines[] = $current;
            }
            $current = strlen($word) > $maxChars ? substr($word, 0, max(1, $maxChars - 1)) . '…' : $word;
            if (count($lines) >= $maxLines) {
                break;
            }
        }
        if ($current !== '' && count($lines) < $maxLines) {
            $lines[] = $current;
        }
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
        }
        if ($lines !== [] && count($lines) === $maxLines && strlen(end($lines)) >= $maxChars) {
            $last = array_key_last($lines);
            $lines[$last] = substr($lines[$last], 0, max(1, $maxChars - 1)) . '…';
        }
        return $lines === [] ? ['-'] : $lines;
    }

    private function truncatePdfText(string $value, int $maxLength): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        if ($maxLength <= 0 || strlen($value) <= $maxLength) {
            return $value;
        }
        return substr($value, 0, max(1, $maxLength - 1)) . '…';
    }

    private function prepareTranscriptPdfLogo(array $settings): ?array
    {
        $logoPath = trim((string)($settings['site_logo'] ?? $settings['logo_path'] ?? $settings['admin_reply_email_logo_url'] ?? ''));
        if ($logoPath === '') {
            $logoPath = 'assets/images/logo.png';
        }

        if (preg_match('#^https?://#i', $logoPath)) {
            $path = parse_url($logoPath, PHP_URL_PATH) ?: '';
            $logoPath = ltrim($path, '/');
        }

        $candidates = [];
        if ($logoPath !== '') {
            $candidates[] = __DIR__ . '/../../public/' . ltrim($logoPath, '/');
            $candidates[] = __DIR__ . '/../../' . ltrim($logoPath, '/');
        }
        $candidates[] = __DIR__ . '/../../public/assets/images/logo.png';

        foreach ($candidates as $candidate) {
            $realPath = realpath($candidate);
            if ($realPath === false || !is_readable($realPath)) {
                continue;
            }
            if (filesize($realPath) !== false && filesize($realPath) > 1024 * 1024) {
                continue;
            }

            $info = @getimagesize($realPath);
            if (!is_array($info)) {
                continue;
            }

            $mime = (string)($info['mime'] ?? '');
            $data = null;
            if ($mime === 'image/jpeg') {
                $data = file_get_contents($realPath);
            } elseif ($mime === 'image/png' && function_exists('imagecreatefrompng') && function_exists('imagejpeg')) {
                $image = @imagecreatefrompng($realPath);
                if ($image !== false) {
                    ob_start();
                    imagejpeg($image, null, 90);
                    $data = ob_get_clean();
                    imagedestroy($image);
                }
            }

            if (is_string($data) && $data !== '') {
                return [
                    'data' => $data,
                    'width' => max(1, (int)($info[0] ?? 1)),
                    'height' => max(1, (int)($info[1] ?? 1)),
                ];
            }
        }

        return null;
    }

    private function pdfEscape(string $value): string
    {
        $value = str_replace(["\r", "\n"], ' ', $value);
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }

    private function assemblePdf(array $pageContents, int $pageWidth, int $pageHeight, array $images = []): string
    {
        if ($pageContents === []) {
            $pageContents = [''];
        }

        $objects = [
            1 => '',
            2 => '',
            3 => "<< /Type /Font /Subtype /Type1 /BaseFont /Times-Roman >>",
            4 => "<< /Type /Font /Subtype /Type1 /BaseFont /Times-Bold >>",
            5 => "<< /Type /Font /Subtype /Type1 /BaseFont /Times-Italic >>",
            6 => "<< /Type /Font /Subtype /Type1 /BaseFont /Times-Roman >>",
            7 => "<< /Type /Font /Subtype /Type1 /BaseFont /Times-Bold >>",
        ];
        $pageObjectIds = [];
        $imageObjectIds = [];
        $nextObjectId = 8;

        foreach ($images as $name => $image) {
            $imageObjectId = $nextObjectId++;
            $imageObjectIds[(string)$name] = $imageObjectId;
            $imageData = (string)($image['data'] ?? '');
            $imageWidth = max(1, (int)($image['width'] ?? 1));
            $imageHeight = max(1, (int)($image['height'] ?? 1));
            $objects[$imageObjectId] = "<< /Type /XObject /Subtype /Image /Width {$imageWidth} /Height {$imageHeight} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen($imageData) . " >>\nstream\n" . $imageData . "\nendstream";
        }

        foreach ($pageContents as $pageContent) {
            $contentObjectId = $nextObjectId++;
            $objects[$contentObjectId] = "<< /Length " . strlen($pageContent) . " >>\nstream\n" . $pageContent . "endstream";

            $pageObjectId = $nextObjectId++;
            $pageObjectIds[] = $pageObjectId;
            $xObjectResources = '';
            if ($imageObjectIds !== []) {
                $entries = [];
                foreach ($imageObjectIds as $name => $objectId) {
                    $entries[] = '/' . preg_replace('/[^A-Za-z0-9_]/', '', $name) . ' ' . $objectId . ' 0 R';
                }
                $xObjectResources = ' /XObject << ' . implode(' ', $entries) . ' >>';
            }
            $objects[$pageObjectId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$pageWidth} {$pageHeight}] /Resources << /Font << /F1 3 0 R /F2 4 0 R /F3 5 0 R /F4 6 0 R /F5 7 0 R >>{$xObjectResources} >> /Contents {$contentObjectId} 0 R >>";
        }

        $kids = implode(' ', array_map(static fn(int $id): string => $id . ' 0 R', $pageObjectIds));
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Kids [' . $kids . '] /Count ' . count($pageObjectIds) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0 => 0];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $body . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    public function submitSupportTicket(): void
    {
        $student = $this->requireStudent();
        $subject = trim($_POST['subject'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($subject === '' || $message === '') {
            flash('error', 'Please provide a subject and issue details.');
            $this->redirect('portal/support');
        }

        $model = new StudentPortalModel($this->config);
        $ok = $model->createSupportTicket([
            'student_id' => (int)$student['id'],
            'name' => (string)($student['name'] ?? 'Student'),
            'email' => (string)($student['email'] ?? ''),
            'admission_number' => (string)($student['admission_number'] ?? ''),
            'subject' => $subject,
            'category' => $category,
            'message' => $message,
        ]);
        if (!$ok) {
            flash('error', 'Unable to submit ticket right now. Please try again.');
            $this->redirect('portal/support');
        }

        flash('success', 'Support ticket submitted. The admin team will respond soon.');
        $this->redirect('portal/support');
    }

    // Account Section
    public function profile(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);

        // Fetch programme information
        $programmeName = '';
        if (!empty($student['programme_id'])) {
            $stmt = $this->pdo->prepare('SELECT name FROM programmes WHERE id = ? LIMIT 1');
            $stmt->execute([(int)$student['programme_id']]);
            $programme = $stmt->fetch(PDO::FETCH_ASSOC);
            $programmeName = $programme['name'] ?? '';
        }

        $this->view('student/profile', [
            'metaTitle' => 'Student Portal - Profile',
            'student' => $student,
            'programmeName' => $programmeName,
            'editMode' => false,
        ], 'student');
    }

    public function editProfile(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);

        // Fetch programme information
        $programmeName = '';
        if (!empty($student['programme_id'])) {
            $stmt = $this->pdo->prepare('SELECT name FROM programmes WHERE id = ? LIMIT 1');
            $stmt->execute([(int)$student['programme_id']]);
            $programme = $stmt->fetch(PDO::FETCH_ASSOC);
            $programmeName = $programme['name'] ?? '';
        }

        $this->view('student/profile', [
            'metaTitle' => 'Student Portal - Edit Profile',
            'student' => $student,
            'programmeName' => $programmeName,
            'editMode' => true,
        ], 'student');
    }

    public function updateProfile(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);

        // Get and validate input
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $nationalId = trim($_POST['national_id'] ?? '');
        $county = trim($_POST['county'] ?? '');
        $subCounty = trim($_POST['sub_county'] ?? '');

        // Validation
        $errors = [];

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email address is required.';
        }

        if ($phone === '') {
            $errors[] = 'Phone number is required.';
        }

        if ($nationalId === '') {
            $errors[] = 'National ID is required.';
        }

        if ($county === '') {
            $errors[] = 'County is required.';
        }

        if ($subCounty === '') {
            $errors[] = 'Sub County is required.';
        }

        // Check if email is already used by another student
        if ($email !== $student['email']) {
            $existing = $model->findStudentByEmail($email);
            if ($existing && $existing['id'] != $student['id']) {
                $errors[] = 'This email address is already registered.';
            }
        }

        // Check if national ID is already used by another student
        if ($nationalId !== $student['national_id']) {
            $existing = $model->findStudentByNationalId($nationalId);
            if ($existing && $existing['id'] != $student['id']) {
                $errors[] = 'This National ID is already registered.';
            }
        }

        if (!empty($errors)) {
            flash('error', implode(' ', $errors));
            $this->redirect('portal/profile/edit');
            return;
        }

        // Update profile
        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'national_id' => $nationalId,
            'county' => $county,
            'sub_county' => $subCounty,
        ];

        if ($model->updateStudentProfile((int)$student['id'], $data)) {
            // Update session data
            $_SESSION['student_name'] = $name;
            $_SESSION['student_email'] = $email;

            flash('success', 'Profile updated successfully.');
            $this->redirect('portal/profile');
        } else {
            flash('error', 'Failed to update profile. Please try again.');
            $this->redirect('portal/profile/edit');
        }
    }

    public function settings(): void
    {
        $student = $this->requireStudent();
        $this->view('student/settings', ['metaTitle' => 'Student Portal - Settings'], 'student');
    }
}
