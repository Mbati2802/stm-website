<?php
class Controller
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function view(string $view, array $data = [], ?string $layoutOverride = null): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo 'View not found.';
            return;
        }

        $layout = 'app.php';
        if ($layoutOverride !== null && $layoutOverride !== '') {
            $candidate = trim($layoutOverride) . '.php';
            if (file_exists(__DIR__ . '/../views/layouts/' . $candidate)) {
                $layout = $candidate;
            }
        } elseif (str_starts_with($view, 'admin/')) {
            $layout = 'admin.php';
        } elseif (str_starts_with($view, 'super-admin/')) {
            $layout = 'super-admin.php';
        } elseif (str_starts_with($view, 'pages/portal_')) {
            $layout = 'portal.php';
        } elseif (str_starts_with($view, 'student/')) {
            $layout = 'student.php';
        }
        include __DIR__ . '/../views/layouts/' . $layout;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . base_url($path));
        exit;
    }
}
