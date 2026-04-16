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

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '([^/]+)', trim($route['path'], '/')) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                [$controller, $action] = $route['handler'];
                $instance = new $controller($GLOBALS['config']);
                call_user_func_array([$instance, $action], $matches);
                return;
            }
        }

        http_response_code(404);
        echo 'Page not found';
    }
}
