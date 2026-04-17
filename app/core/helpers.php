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

function send_notification_email_with_attachment(
    string $to,
    string $subject,
    string $message,
    string $attachmentName,
    string $attachmentContent,
    string $attachmentMime = 'application/octet-stream'
): bool {
    $to = trim($to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $subject = trim($subject) !== '' ? trim($subject) : 'Website Notification';
    $boundary = '=_Part_' . bin2hex(random_bytes(12));
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $from = 'no-reply@' . preg_replace('/^www\./', '', $host);

    $headers = [
        'MIME-Version: 1.0',
        'From: ' . $from,
        'Content-Type: multipart/mixed; boundary="' . $boundary . '"',
    ];

    $body = '';
    $body .= '--' . $boundary . "\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $message . "\r\n\r\n";
    $body .= '--' . $boundary . "\r\n";
    $body .= 'Content-Type: ' . $attachmentMime . '; name="' . $attachmentName . '"' . "\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= 'Content-Disposition: attachment; filename="' . $attachmentName . '"' . "\r\n\r\n";
    $body .= chunk_split(base64_encode($attachmentContent)) . "\r\n";
    $body .= '--' . $boundary . "--\r\n";

    return @mail($to, $subject, $body, implode("\r\n", $headers));
}

function generate_simple_pdf(array $lines): string
{
    $safeLines = array_values(array_filter(array_map(
        static fn($line) => trim((string)$line),
        $lines
    ), static fn($line) => $line !== ''));

    if ($safeLines === []) {
        $safeLines = ['Admission Letter'];
    }

    $objects = [];

    $content = "BT\n/F1 12 Tf\n50 800 Td\n";
    $first = true;
    foreach ($safeLines as $line) {
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
        if ($first) {
            $content .= '(' . $escaped . ") Tj\n";
            $first = false;
        } else {
            $content .= "0 -16 Td\n(" . $escaped . ") Tj\n";
        }
    }
    $content .= "ET";

    $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
    $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
    $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
    $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
    $objects[] = "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n";

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objects as $object) {
        $offsets[] = strlen($pdf);
        $pdf .= $object;
    }

    $xrefPos = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    for ($i = 1; $i <= count($objects); $i++) {
        $pdf .= str_pad((string)$offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
    }
    $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n" . $xrefPos . "\n%%EOF";

    return $pdf;
}
