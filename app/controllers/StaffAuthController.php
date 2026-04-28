<?php
class StaffAuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            if (($_SESSION['login_origin'] ?? '') === 'staff') {
                $this->redirect('staff/dashboard');
            }
            $this->redirect('admin');
        }
        $this->view('pages/portal_staff_login', ['metaTitle' => 'Staff Portal Login'], 'portal_staff');
    }

    public function authenticate(): void
    {
        $limitKey = 'staff_login_' . md5((string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        $securityModel = new ContentModel($this->config);

        if (!rate_limit_check($limitKey, 5, 15 * 60)) {
            $securityModel->logPageVisit('/staff/login-rate-limited', true);
            flash('error', 'Too many login attempts. Please wait 15 minutes and try again.');
            $this->redirect('staff/login');
        }

        $pdo = Database::getInstance($this->config['db']);
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($pdo, $email, $password)) {
            $_SESSION['login_origin'] = 'staff';
            rate_limit_clear($limitKey);
            $securityModel->logPageVisit('/staff/login-success', true);
            $this->redirect('staff/dashboard');
        }

        rate_limit_increment($limitKey);
        $securityModel->logPageVisit('/staff/login-failed', true);
        flash('error', 'Invalid credentials.');
        $this->redirect('staff/login');
    }

    public function dashboard(): void
    {
        if (!Auth::check()) {
            $this->redirect('staff/login');
        }
        $this->view('pages/staff_dashboard', ['metaTitle' => 'Staff Portal Dashboard'], 'portal_staff');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('staff/login');
    }
}