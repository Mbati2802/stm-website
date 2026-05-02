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
        
        // Fetch student's courses based on their programme
        $courses = [];
        if (!empty($student['programme_id'])) {
            try {
                $stmt = $this->pdo->prepare('
                    SELECT pc.*, p.name AS programme_name, u.name AS teacher_name
                    FROM portal_courses pc
                    LEFT JOIN programmes p ON p.id = pc.programme_id
                    LEFT JOIN users u ON u.id = pc.teacher_id
                    WHERE pc.programme_id = ?
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
        $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $this->view('student/courses', [
            'metaTitle' => 'Student Portal - My Courses',
            'courses' => $model->allPortalCourses(),
        ], 'student');
    }

    public function grades(): void
    {
        $student = $this->requireStudent();
        $model = new StudentPortalModel($this->config);
        $this->view('student/grades', [
            'metaTitle' => 'Student Portal - Grades',
            'grades' => array_values(array_filter($model->allCourseGrades(), fn($row) => (int)($row['student_id'] ?? 0) === (int)($student['id'] ?? 0))),
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
        ], 'student');
    }

    public function settings(): void
    {
        $student = $this->requireStudent();
        $this->view('student/settings', ['metaTitle' => 'Student Portal - Settings'], 'student');
    }
}
