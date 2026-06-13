<?php
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $paths = [
        __DIR__ . '/app/core/' . $class . '.php',
        __DIR__ . '/app/controllers/' . $class . '.php',
        __DIR__ . '/app/controllers/admin/' . $class . '.php',
        __DIR__ . '/app/models/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/app/core/helpers.php';

// Temporary shutdown handler to capture fatal errors and write to logs for debugging 500s
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
register_shutdown_function(function() use ($logDir) {
    $err = error_get_last();
    if ($err !== null) {
        $payload = [
            'time' => date('c'),
            'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
            'error' => $err,
            'server' => [
                'php_sapi' => PHP_SAPI,
                'php_version' => phpversion(),
            ],
        ];
        $msg = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n\n";
        @file_put_contents($logDir . '/fatal_errors.log', $msg, FILE_APPEND | LOCK_EX);
    }
});
