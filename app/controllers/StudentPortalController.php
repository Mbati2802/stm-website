<?php
class StudentPortalController extends Controller
{
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
        $admissionNumber = strtoupper(trim($_POST['admission_number'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $model = new StudentPortalModel($this->config);
        $student = $model->findStudentByAdmissionNumber($admissionNumber);

        if ($student === null || !password_verify($password, (string)$student['password'])) {
            flash('error', 'Invalid admission number or password.');
            $this->redirect('portal/login');
        }

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
        $this->view('student/dashboard', [
            'metaTitle' => 'Student Portal - Dashboard',
            'student' => $student,
            'timetables' => $model->latestTimetables(8),
            'announcements' => $model->latestAnnouncements(8),
        ], 'student');
    }

    public function forgotPasswordForm(): void
    {
        $this->view('pages/portal_forgot_password', ['metaTitle' => 'Student Portal - Forgot Password']);
    }

    public function sendResetCode(): void
    {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please provide a valid email address.');
            $this->redirect('portal/forgot-password');
        }

        $model = new StudentPortalModel($this->config);
        $student = $model->findStudentByEmail($email);
        if ($student === null) {
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
        $this->view('student/events', ['metaTitle' => 'Student Portal - Campus Events'], 'student');
    }

    public function clubs(): void
    {
        $student = $this->requireStudent();
        $this->view('student/clubs', ['metaTitle' => 'Student Portal - Clubs & Societies'], 'student');
    }

    public function announcements(): void
    {
        $student = $this->requireStudent();
        $this->view('student/announcements', ['metaTitle' => 'Student Portal - Announcements'], 'student');
    }

    // Services Section
    public function fees(): void
    {
        $student = $this->requireStudent();
        $this->view('student/fees', ['metaTitle' => 'Student Portal - Fee Statement'], 'student');
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
        $this->view('student/support', ['metaTitle' => 'Student Portal - IT Support'], 'student');
    }

    // Account Section
    public function profile(): void
    {
        $student = $this->requireStudent();
        $this->view('student/profile', ['metaTitle' => 'Student Portal - Profile'], 'student');
    }

    public function settings(): void
    {
        $student = $this->requireStudent();
        $this->view('student/settings', ['metaTitle' => 'Student Portal - Settings'], 'student');
    }
}
