<?php
/**
 * Router script for PHP's built-in server.
 *
 * Run from the project root:
 *   php -S 127.0.0.1:8000 server.php
 *
 * This emulates the `public/.htaccess` rewrite to `public/index.php`
 * while still allowing existing static files under `public/` to be served directly.
 */

$publicDir = __DIR__ . DIRECTORY_SEPARATOR . 'public';
$publicDirReal = realpath($publicDir) ?: $publicDir;
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// Serve existing files (css/js/images/etc.) directly.
$candidate = $publicDirReal . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $uriPath);
if ($uriPath !== '/' && is_file($candidate)) {
    $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
    $mimeByExt = [
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'eot' => 'application/vnd.ms-fontobject',
        'json' => 'application/json; charset=utf-8',
        'txt' => 'text/plain; charset=utf-8',
        'map' => 'application/json; charset=utf-8',
    ];

    $mime = $mimeByExt[$ext] ?? null;

    // Best-effort MIME detection when we don't have a known mapping.
    if ($mime === null && function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $detected = finfo_file($finfo, $candidate);
            finfo_close($finfo);
            if (is_string($detected) && $detected !== '') {
                $mime = $detected;
            }
        }
    }

    $mime = $mime ?? 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . (string)filesize($candidate));
    readfile($candidate);
    exit;
}

// Make the app think it's running as `public/index.php` (not `server.php`).
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require $publicDirReal . DIRECTORY_SEPARATOR . 'index.php';

