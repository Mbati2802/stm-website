<?php
function base_url(string $path = ''): string
{
    global $config;
    $base = trim((string)($config['base_url'] ?? ''));

    if ($base === '') {
        $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = ($scriptDir === '/' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/');
        $base = $scheme . '://' . $host . $scriptDir;
    }

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function plain_text(?string $value): string
{
    $decoded = (string)$value;
    for ($i = 0; $i < 3; $i++) {
        $next = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($next === $decoded) {
            break;
        }
        $decoded = $next;
    }
    $clean = strip_tags($decoded);
    return trim(preg_replace('/\s+/', ' ', $clean) ?? '');
}

function plain_text_multiline(?string $value): string
{
    $decoded = (string)$value;
    for ($i = 0; $i < 3; $i++) {
        $next = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($next === $decoded) {
            break;
        }
        $decoded = $next;
    }
    $withBreaks = preg_replace('/<\s*br\s*\/?\s*>/i', "\n", $decoded) ?? $decoded;
    $withBreaks = preg_replace('/<\s*\/p\s*>/i', "\n\n", $withBreaks) ?? $withBreaks;
    $withBreaks = preg_replace('/<\s*p[^>]*>/i', '', $withBreaks) ?? $withBreaks;
    $clean = strip_tags($withBreaks);
    $normalized = str_replace(["\r\n", "\r"], "\n", $clean);
    $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized) ?? $normalized;
    return trim($normalized);
}

function old(string $key, string $default = ''): string
{
    return $_SESSION['old'][$key] ?? $default;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $message;
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim((string)$text, '-');
}

function safe_html(?string $html, array $allowedTags = ['p','br','strong','b','em','i','u','ul','ol','li','a','h2','h3','h4','blockquote','code','pre','span']): string
{
    $raw = (string)$html;
    if ($raw === '') {
        return '';
    }

    $allowed = '<' . implode('><', $allowedTags) . '>';
    $clean = strip_tags($raw, $allowed);

    // Remove inline event handlers and javascript: URLs.
    $clean = preg_replace('/\son\w+\s*=\s*(\"[^\"]*\"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? $clean;
    $clean = preg_replace('/\s(href|src)\s*=\s*(\"|\')\s*javascript:[^\"\']*\2/i', '', $clean) ?? $clean;

    // Prevent style injections.
    $clean = preg_replace('/\sstyle\s*=\s*(\"[^\"]*\"|\'[^\']*\')/i', '', $clean) ?? $clean;

    return $clean;
}

function send_notification_email(string $to, string $subject, string $message): bool
{
    $to = trim($to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $subject = trim($subject) !== '' ? trim($subject) : 'Website Notification';
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/plain; charset=UTF-8',
        'From: no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
    ];

    return @mail($to, $subject, $message, implode("\r\n", $headers));
}
