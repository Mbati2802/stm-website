<?php
class Router
{
    private array $routes = [];

    public function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = trim($uri, '/');

        // Debug logging - will be written to error log
        error_log('ROUTER DEBUG: Method=' . $method . ', URI=' . $uri);
        error_log('ROUTER DEBUG: Routes count=' . count($this->routes));

        foreach ($this->routes as $index => $route) {
            error_log('ROUTER DEBUG: Checking route ' . $index . ': ' . $route['method'] . ' ' . $route['path']);
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '([^/]+)', trim($route['path'], '/')) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                error_log('ROUTER DEBUG: MATCH FOUND for route ' . $index . ', controller=' . $route['handler'][0] . ', action=' . $route['handler'][1]);
                array_shift($matches);
                [$controller, $action] = $route['handler'];
                $instance = new $controller($GLOBALS['config']);
                if (strtoupper($method) === 'GET') {
                    try {
                        $path = '/' . trim($uri, '/');
                        if ($path === '/') {
                            $path = '/';
                        }
                        (new ContentModel($GLOBALS['config']))->logPageVisit($path, str_starts_with($path, '/admin'));
                    } catch (Throwable) {
                        // no-op
                    }
                }
                if (strtoupper($method) === 'POST') {
                    $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
                    if (!csrf_validate(is_string($token) ? $token : '')) {
                        http_response_code(419);
                        flash('error', 'Your session expired. Please try again.');
                        $back = $_SERVER['HTTP_REFERER'] ?? base_url('');
                        header('Location: ' . $back);
                        exit;
                    }
                }
                call_user_func_array([$instance, $action], $matches);
                return;
            }
        }

        error_log('ROUTER DEBUG: NO MATCH FOUND - returning 404');
        http_response_code(404);
        echo 'Page not found';
    }
}
