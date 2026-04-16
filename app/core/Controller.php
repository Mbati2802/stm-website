<?php
class Controller
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo 'View not found.';
            return;
        }

        $layout = str_starts_with($view, 'admin/') ? 'admin.php' : 'app.php';
        include __DIR__ . '/../views/layouts/' . $layout;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . base_url($path));
        exit;
    }
}
