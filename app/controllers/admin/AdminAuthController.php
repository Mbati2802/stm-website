<?php
class AdminAuthController extends Controller
{
    public function login(): void
    {
        if (!admin_login_ip_allowed()) {
            http_response_code(404);
            echo 'Page not found';
            exit;
        }

        if (Auth::check()) {
            $this->redirect('admin');
        }
        $this->view('admin/login', ['metaTitle' => 'Admin Login']);
    }

    public function authenticate(): void
    {
        if (!admin_login_ip_allowed()) {
            http_response_code(404);
            echo 'Page not found';
            exit;
        }

        $limitKey = 'admin_login_' . md5((string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        $securityModel = new ContentModel($this->config);

        if (!rate_limit_check($limitKey, 5, 15 * 60)) {
            $securityModel->logPageVisit('/admin/login-rate-limited', true);
            flash('error', 'Too many login attempts. Please wait 15 minutes and try again.');
            $this->redirect(admin_login_path());
        }

        $pdo = Database::getInstance($this->config['db']);
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($pdo, $email, $password)) {
            rate_limit_clear($limitKey);
            $securityModel->logPageVisit('/admin/login-success', true);
            $this->redirect('admin');
        }

        rate_limit_increment($limitKey);
        $securityModel->logPageVisit('/admin/login-failed', true);
        flash('error', 'Invalid credentials.');
        $this->redirect(admin_login_path());
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect(admin_login_path());
    }
}
