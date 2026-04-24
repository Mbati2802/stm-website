<?php
class AdminAuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('admin');
        }
        $this->view('admin/login', ['metaTitle' => 'Admin Login']);
    }

    public function authenticate(): void
    {
        $limitKey = 'admin_login_' . md5((string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        if (!rate_limit_check($limitKey, 5, 15 * 60)) {
            flash('error', 'Too many login attempts. Please wait 15 minutes and try again.');
            $this->redirect('admin/login');
        }

        $pdo = Database::getInstance($this->config['db']);
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($pdo, $email, $password)) {
            rate_limit_clear($limitKey);
            $this->redirect('admin');
        }

        rate_limit_increment($limitKey);
        flash('error', 'Invalid credentials.');
        $this->redirect('admin/login');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('admin/login');
    }
}
