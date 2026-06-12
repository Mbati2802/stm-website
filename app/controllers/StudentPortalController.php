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

        $pdf = $this->buildTranscriptPdf($transcript);

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
        $margin = 32;
        $bottomMargin = 58;
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
        $serialNumber = 'SR-' . date('Ymd') . '-' . str_pad((string)((int)($student['id'] ?? 0)), 5, '0', STR_PAD_LEFT);
        $transcriptNumber = 'TR-' . date('Ymd-His') . '-' . strtoupper(substr(sha1((string)($student['admission_number'] ?? '') . microtime(true)), 0, 6));
        $appName = (string)($this->config['app_name'] ?? 'St. Mary\'s Mother and Child Hospital Medical Training College');
        $collegeName = $appName !== '' ? $appName : 'St. Mary\'s Mother and Child Hospital Medical Training College';
        $collegeAddress = trim((string)($settings['address'] ?? $settings['location'] ?? ''));
        $collegeContacts = trim(implode(' | ', array_filter([
            (string)($settings['phone'] ?? ''),
            (string)($settings['email'] ?? ''),
        ])));
        $logoImage = $this->prepareTranscriptPdfLogo($settings);
        $pdfImages = $logoImage !== null ? ['Logo' => $logoImage] : [];

        $drawLogo = function (float $x, float $logoY, float $size) use (&$content, $logoImage, $primary, $white, $fillRect, $strokeRect, $text): void {
            if ($logoImage !== null) {
                $content .= sprintf("q %.2f 0 0 %.2f %.2f %.2f cm /Logo Do Q\n", $size, $size, $x, $logoY);
                return;
            }
            $fillRect($x, $logoY, $size, $size, $white);
            $strokeRect($x, $logoY, $size, $size, $primary, 1.0);
            $text($x + 8, $logoY + ($size / 2) - 4, 12, 'STM', $primary, 'F2');
        };

        $drawPageHeader = function (bool $firstPage) use (&$y, &$pageNumber, $pageWidth, $pageHeight, $margin, $primary, $secondary, $dark, $muted, $line, $text, $wrappedText, $drawLogo, $collegeName, $collegeAddress, $collegeContacts, $issuedAt, $serialNumber, $transcriptNumber): void {
            $pageNumber++;
            $y = $pageHeight - $margin;
            $drawLogo($margin, $pageHeight - 78, 46);
            $drawLogo($pageWidth - $margin - 46, $pageHeight - 78, 46);
            $wrappedText($margin + 66, $pageHeight - 38, 14, strtoupper($collegeName), $pageWidth - (($margin + 66) * 2), 2, $primary, 'F5');
            if ($collegeAddress !== '') {
                $wrappedText($margin + 74, $pageHeight - 62, 8, $collegeAddress, $pageWidth - (($margin + 74) * 2), 1, $dark, 'F4');
            }
            if ($collegeContacts !== '') {
                $wrappedText($margin + 74, $pageHeight - 74, 8, $collegeContacts, $pageWidth - (($margin + 74) * 2), 1, $dark, 'F4');
            }
            $line($margin, $pageHeight - 92, $pageWidth - $margin, $pageHeight - 92, $primary, 1.4);
            $line($margin, $pageHeight - 96, $pageWidth - $margin, $pageHeight - 96, $secondary, 0.8);
            $y = $pageHeight - 116;

            if ($firstPage) {
                $wrappedText($margin, $y, 16, 'OFFICIAL ACADEMIC TRANSCRIPT', $pageWidth - ($margin * 2), 1, $primary, 'F5');
                $text($margin, $y - 20, 8, 'Serial No: ' . $serialNumber, $dark, 'F4');
                $text($pageWidth - $margin - 190, $y - 20, 8, 'Transcript No: ' . $transcriptNumber, $dark, 'F4');
                $text($margin, $y - 34, 8, 'Generated: ' . $issuedAt, $muted, 'F4');
                $line($margin, $y - 42, $pageWidth - $margin, $y - 42, $secondary, 1.2);
                $y -= 58;
            } else {
                $text($margin, $y, 9, 'Continuation for ' . (string)($student['name'] ?? 'Student'), $muted, 'F2');
                $line($margin, $y - 7, $pageWidth - $margin, $y - 7, $secondary, 0.8);
                $y -= 20;
            }
        };

        $drawFooter = function () use ($pageWidth, $margin, $primary, $muted, $line, $text, $issuedAt): void {
            $line($margin, 42, $pageWidth - $margin, 42, $primary, 0.8);
            $text($margin, 28, 7, 'Generated from official student portal records.', $muted, 'F4');
            $text($pageWidth - 145, 28, 7, 'Generated: ' . $issuedAt, $muted, 'F4');
        };

        $drawMeta = function () use (&$y, $pageWidth, $margin, $primary, $dark, $muted, $border, $light, $fillRect, $strokeRect, $text, $wrappedText, $student, $transcript): void {
            $boxWidth = ($pageWidth - ($margin * 2) - 12) / 2;
            $boxHeight = 96;
            $leftX = $margin;
            $rightX = $margin + $boxWidth + 12;
            $boxY = $y - $boxHeight;
            $fillRect($leftX, $boxY, $boxWidth, $boxHeight, $light);
            $fillRect($rightX, $boxY, $boxWidth, $boxHeight, $light);
            $strokeRect($leftX, $boxY, $boxWidth, $boxHeight, $border);
            $strokeRect($rightX, $boxY, $boxWidth, $boxHeight, $border);
            $text($leftX + 10, $y - 17, 10, 'Student Details', $primary, 'F2');
            $text($rightX + 10, $y - 17, 10, 'Academic Details', $primary, 'F2');

            $metaY = $y - 34;
            $text($leftX + 10, $metaY, 8, 'Name:', $muted, 'F2');
            $wrappedText($leftX + 78, $metaY, 8, (string)($student['name'] ?? '-'), $boxWidth - 88, 2, $dark);
            $metaY -= 18;
            $text($leftX + 10, $metaY, 8, 'Admission No:', $muted, 'F2');
            $text($leftX + 78, $metaY, 8, (string)($student['admission_number'] ?? '-'), $dark);
            $metaY -= 18;
            $text($leftX + 10, $metaY, 8, 'Email:', $muted, 'F2');
            $wrappedText($leftX + 78, $metaY, 8, (string)($student['email'] ?? '-'), $boxWidth - 88, 1, $dark);

            $metaY = $y - 34;
            $text($rightX + 10, $metaY, 8, 'Programme:', $muted, 'F2');
            $wrappedText($rightX + 78, $metaY, 8, (string)($student['programme_name'] ?? '-'), $boxWidth - 88, 2, $dark);
            $metaY -= 18;
            $text($rightX + 10, $metaY, 8, 'Term:', $muted, 'F2');
            $text($rightX + 78, $metaY, 8, (string)($transcript['term_name'] ?? '-'), $dark);
            $metaY -= 18;
            $text($rightX + 10, $metaY, 8, 'Session:', $muted, 'F2');
            $text($rightX + 78, $metaY, 8, (string)($transcript['session_name'] ?? '-'), $dark);
            $metaY -= 18;
            $text($rightX + 10, $metaY, 8, 'Issued:', $muted, 'F2');
            $text($rightX + 78, $metaY, 8, date('F j, Y'), $dark);
            $y = $boxY - 24;
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

        $drawTableHeader = function () use (&$y, $margin, $columns, $primary, $white, $border, $fillRect, $strokeRect, $wrappedText): void {
            $rowHeight = 28;
            $x = $margin;
            foreach ($columns as $column) {
                $width = (float)$column['width'];
                $fillRect($x, $y - $rowHeight, $width, $rowHeight, $primary);
                $strokeRect($x, $y - $rowHeight, $width, $rowHeight, $border, 0.35);
                $wrappedText($x + 3, $y - 10, 6, (string)$column['label'], $width - 6, 2, $white, 'F2');
                $x += $width;
            }
            $y -= $rowHeight;
        };

        $drawPageHeader(true);
        $drawMeta();
        $text($margin, $y, 11, 'Results Summary', $primary, 'F2');
        $y -= 12;
        $drawTableHeader();

        if ($rows === []) {
            $text($margin, $y - 18, 9, 'No grades are available to include in this transcript yet.', $muted);
        } else {
            foreach ($rows as $rowIndex => $row) {
                $rowHeight = 30;
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
                    $strokeRect($x, $y - $rowHeight, $width, $rowHeight, $border, 0.35);
                    $font = ($column['key'] ?? '') === 'grade' ? 'F2' : 'F1';
                    $size = isset($column['exam_id']) || ($column['key'] ?? '') === 'grade' ? 7 : 6;
                    $textX = ($column['align'] ?? 'left') === 'center' ? $x + ($width / 2) - (strlen($value) * $size * 0.14) : $x + 3;
                    $wrappedText(max($x + 3, $textX), $y - 11, $size, $value, $width - 6, 2, $dark, $font);
                    $x += $width;
                }
                $y -= $rowHeight;
            }
        }

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
                $gradingKey[] = $grade . ': ' . $min . '-' . $max;
            }
            if ($gradingKey !== []) {
                break;
            }
        }

        if ($gradingKey !== []) {
            if ($y - 54 < $bottomMargin) {
                $drawFooter();
                $finishPage();
                $drawPageHeader(false);
            }
            $y -= 20;
            $text($margin, $y, 9, 'Grading System', $primary, 'F5');
            $y -= 14;
            $wrappedText($margin, $y, 8, implode('    ', $gradingKey), $contentWidth, 3, $dark, 'F4');
            $y -= 32;
        }

        if ($y - 112 < $bottomMargin) {
            $drawFooter();
            $finishPage();
            $drawPageHeader(false);
        }

        $y -= 22;
        $text($margin, $y, 9, 'Certification', $primary, 'F5');
        $y -= 18;
        $wrappedText($margin, $y, 8, 'This is to certify that the results appearing on this Result Slip are a true and accurate record of the student\'s academic performance as maintained in the official records of the institution.', $contentWidth, 4, $dark, 'F3');
        $y -= 42;
        $wrappedText($margin, $y, 8, 'Issued without alteration and for official purposes only.', $contentWidth, 2, $dark, 'F3');
        $y -= 34;
        $line($margin, $y, $margin + 150, $y, $dark, 0.6);
        $line($pageWidth - $margin - 150, $y, $pageWidth - $margin, $y, $dark, 0.6);
        $text($margin, $y - 14, 8, 'Registrar / Authorized Officer', $muted);
        $text($pageWidth - $margin - 150, $y - 14, 8, 'College Stamp / Date', $muted);

        $drawFooter();
        $finishPage();

        return $this->assemblePdf($pages, $pageWidth, $pageHeight, $pdfImages);
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
            3 => "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>",
            4 => "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>",
            5 => "<< /Type /Font /Subtype /Type1 /BaseFont /Times-Roman >>",
            6 => "<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>",
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
