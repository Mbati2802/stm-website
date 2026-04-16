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
        $pdo = Database::getInstance($this->config['db']);
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($pdo, $email, $password)) {
            $this->redirect('admin');
        }

        flash('error', 'Invalid credentials.');
        $this->redirect('admin/login');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('admin/login');
    }
}
