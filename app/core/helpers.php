<?php
function base_url(string $path = ''): string
{
    global $config;
    $base = trim((string)($config['base_url'] ?? ''));
    if ($base !== '') {
        $base = preg_replace('#^https?://https?://#i', 'https://', $base) ?? $base;
        if (!preg_match('#^https?://#i', $base)) {
            $base = '';
        }
    }

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

function admin_login_path(): string
{
    global $config;
    $slug = trim((string)($config['admin_login_slug'] ?? 'admin/login'));
    $slug = trim($slug, '/');
    return $slug !== '' ? $slug : 'admin/login';
}

function admin_login_url(): string
{
    return base_url(admin_login_path());
}

function admin_login_allowed_ips(): array
{
    global $config;
    $raw = trim((string)($config['admin_login_allow_ips'] ?? ''));
    if ($raw === '') {
        return [];
    }

    $lines = preg_split('/[\r\n,;]+/', $raw) ?: [];
    return array_values(array_filter(array_map(static fn($item) => trim((string)$item), $lines)));
}

function admin_login_ip_allowed(): bool
{
    $allowedIps = admin_login_allowed_ips();
    if ($allowedIps === []) {
        return true;
    }

    $remoteIp = trim((string)($_SERVER['REMOTE_ADDR'] ?? ''));
    if ($remoteIp === '') {
        return false;
    }

    foreach ($allowedIps as $entry) {
        if ($entry === $remoteIp) {
            return true;
        }

        if (str_contains($entry, '/')) {
            [$subnet, $mask] = explode('/', $entry, 2) + ['', ''];
            $mask = (int)$mask;
            if ($mask < 0 || $mask > 32) {
                continue;
            }
            if (filter_var($remoteIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipLong = ip2long($remoteIp);
                $subnetLong = ip2long($subnet);
                $maskLong = $mask === 0 ? 0 : ((0xFFFFFFFF << (32 - $mask)) & 0xFFFFFFFF);
                if (($ipLong & $maskLong) === ($subnetLong & $maskLong)) {
                    return true;
                }
            }
        }
    }

    return false;
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

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return '';
    }
    $token = (string)($_SESSION['_csrf'] ?? '');
    if ($token !== '') {
        return $token;
    }
    try {
        $token = bin2hex(random_bytes(32));
    } catch (Throwable) {
        $token = bin2hex(pack('N', (int)(microtime(true) * 1000000))) . bin2hex(pack('N', random_int(1, PHP_INT_MAX)));
    }
    $_SESSION['_csrf'] = $token;
    return $token;
}

function csrf_field(): string
{
    $token = csrf_token();
    if ($token === '') {
        return '';
    }
    return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
}

function csrf_validate(?string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }
    $expected = (string)($_SESSION['_csrf'] ?? '');
    if ($expected === '' || $token === null || $token === '') {
        return false;
    }
    return hash_equals($expected, (string)$token);
}

function rate_limit_check(string $key, int $maxAttempts, int $windowSeconds): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return true;
    }
    $bucket = (string)($_SESSION['_rate_limit'][$key] ?? '');
    if ($bucket === '') {
        return true;
    }
    $parts = explode('|', $bucket);
    $count = (int)($parts[0] ?? 0);
    $startedAt = (int)($parts[1] ?? 0);
    $now = time();
    if ($startedAt <= 0 || ($now - $startedAt) > $windowSeconds) {
        return true;
    }
    return $count < $maxAttempts;
}

function rate_limit_increment(string $key): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    $bucket = (string)($_SESSION['_rate_limit'][$key] ?? '');
    $parts = explode('|', $bucket);
    $count = (int)($parts[0] ?? 0);
    $startedAt = (int)($parts[1] ?? 0);
    $now = time();
    if ($startedAt <= 0 || ($now - $startedAt) > 3600) {
        $count = 0;
        $startedAt = $now;
    }
    $_SESSION['_rate_limit'][$key] = ($count + 1) . '|' . $startedAt;
}

function rate_limit_clear(string $key): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    unset($_SESSION['_rate_limit'][$key]);
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

function build_structured_notification_email(string $headline, array $rows, string $note = ''): string
{
    global $config;
    $appName = (string)($config['app_name'] ?? 'College');
    $safeHeadline = e($headline);
    $safeNote = e($note);
    $tableRows = '';
    foreach ($rows as $label => $value) {
        $tableRows .= '<tr>'
            . '<td style="padding:10px 12px;border:1px solid #d9e3f2;background:#f6f9ff;font-weight:700;width:180px;">' . e((string)$label) . '</td>'
            . '<td style="padding:10px 12px;border:1px solid #d9e3f2;">' . nl2br(e((string)$value)) . '</td>'
            . '</tr>';
    }
    return '<!doctype html><html><body style="margin:0;padding:0;background:#ffffff;">'
        . '<div style="max-width:760px;margin:20px auto;padding:0 12px;">'
        . '<div style="background:#f5f6fb;border-top:4px solid #5fc7e7;border-bottom:4px solid #5fc7e7;">'
        . '<div style="padding:24px 32px 14px;text-align:center;">'
        . '<h1 style="margin:0;color:#1f2a44;font-family:Arial,sans-serif;font-size:32px;line-height:1.1;">' . $safeHeadline . '</h1>'
        . '<p style="margin:8px 0 0;color:#6e7381;font-family:Arial,sans-serif;font-size:14px;">Notification from ' . e($appName) . '</p>'
        . '</div>'
        . '<div style="padding:0 28px 24px;">'
        . '<table role="presentation" style="width:100%;border-collapse:collapse;font-family:Arial,sans-serif;color:#1f2a44;">'
        . $tableRows
        . '</table>'
        . ($safeNote !== '' ? '<p style="margin:14px 0 0;color:#334155;font-family:Arial,sans-serif;font-size:13px;">' . $safeNote . '</p>' : '')
        . '</div>'
        . '<div style="background:#2c3653;padding:16px 28px;color:#b9e7ff;text-align:center;font-family:Arial,sans-serif;font-size:12px;">'
        . e($appName) . ' automated notification'
        . '</div>'
        . '</div></div></body></html>';
}

function email_delivery_log(string $status, array $context = []): void
{
    global $config;
    $safeContext = [];
    foreach ($context as $key => $value) {
        if (is_scalar($value) || $value === null) {
            $safeContext[(string)$key] = $value;
        }
    }
    $safeContext['time'] = date('Y-m-d H:i:s');
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['email_delivery_last'] = [
            'status' => $status,
            'context' => $safeContext,
        ];
    }

    try {
        if (is_array($config ?? null) && isset($config['db']) && is_array($config['db'])) {
            $pdo = Database::getInstance($config['db']);
            $stmt = $pdo->prepare('
                INSERT INTO email_logs(status, recipient_email, subject, error_message, context_json, created_at)
                VALUES(:status, :recipient_email, :subject, :error_message, :context_json, NOW())
            ');
            $stmt->execute([
                'status' => substr((string)$status, 0, 60),
                'recipient_email' => substr((string)($safeContext['to'] ?? ''), 0, 190),
                'subject' => substr((string)($safeContext['subject'] ?? ''), 0, 255),
                'error_message' => substr((string)($safeContext['error'] ?? ''), 0, 1000),
                'context_json' => json_encode($safeContext, JSON_UNESCAPED_SLASHES),
            ]);
        }
    } catch (Throwable) {
        // Keep email sending non-blocking even if logging storage fails.
    }
    @file_put_contents(
        (defined('LOG_DIR') ? LOG_DIR : __DIR__ . '/../../logs') . '/email_delivery.log',
        '[' . date('Y-m-d H:i:s') . '] [email:' . $status . '] ' . json_encode($safeContext, JSON_UNESCAPED_SLASHES) . "\n",
        FILE_APPEND | LOCK_EX
    );
}

function email_delivery_last_status(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return [];
    }
    $last = $_SESSION['email_delivery_last'] ?? null;
    return is_array($last) ? $last : [];
}

function email_delivery_recent_logs(int $limit = 20): array
{
    global $config;
    $resolvedLimit = max(1, min(100, $limit));
    try {
        if (!is_array($config ?? null) || !isset($config['db']) || !is_array($config['db'])) {
            return [];
        }
        $pdo = Database::getInstance($config['db']);
        $stmt = $pdo->prepare('
            SELECT status, recipient_email, subject, error_message, created_at
            FROM email_logs
            ORDER BY id DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', $resolvedLimit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    } catch (Throwable) {
        return [];
    }
}

function email_last_error_set(string $message): void
{
    $GLOBALS['__email_last_error'] = $message;
}

function email_last_error_get(): string
{
    return (string)($GLOBALS['__email_last_error'] ?? '');
}

function send_notification_email(string $to, string $subject, string $message, ?string $htmlMessage = null): bool
{
    global $config;
    email_last_error_set('');
    $to = trim($to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        email_last_error_set('Invalid recipient email address.');
        email_delivery_log('invalid-recipient', ['to' => $to]);
        return false;
    }

    $subject = trim($subject) !== '' ? trim($subject) : 'Website Notification';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $fromDomain = preg_replace('/^www\./i', '', (string)$host) ?: 'localhost';
    $fallbackFrom = 'no-reply@' . $fromDomain;
    if (!filter_var($fallbackFrom, FILTER_VALIDATE_EMAIL)) {
        $fallbackFrom = 'no-reply@localhost.localdomain';
    }

    $smtpFrom = trim((string)($config['smtp_from_email'] ?? ''));
    $smtpUser = trim((string)($config['smtp_username'] ?? ''));
    $fromAddress = filter_var($smtpFrom, FILTER_VALIDATE_EMAIL)
        ? $smtpFrom
        : (filter_var($smtpUser, FILTER_VALIDATE_EMAIL) ? $smtpUser : $fallbackFrom);

    $replyTo = trim((string)($config['notification_email'] ?? ''));
    if (!filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $replyTo = '';
    }

    $plainBody = (string)$message;
    $htmlBody = $htmlMessage !== null ? trim((string)$htmlMessage) : '';
    $useHtml = $htmlBody !== '';

    $boundary = null;
    $body = $plainBody;

    // When SMTP is configured, smtp_send_email() will generate the From header.
    // Including a second From header here can cause external providers to reject the message.
    $baseHeaders = ['MIME-Version: 1.0'];
    if ($useHtml) {
        try {
            $boundary = '=_Alt_' . bin2hex(random_bytes(12));
        } catch (Throwable) {
            $boundary = '=_Alt_' . md5((string)microtime(true) . $to . $subject);
        }
        $baseHeaders[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

        $body = '';
        $body .= '--' . $boundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $plainBody . "\r\n\r\n";
        $body .= '--' . $boundary . "\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $htmlBody . "\r\n\r\n";
        $body .= '--' . $boundary . "--\r\n";
    } else {
        $baseHeaders[] = 'Content-type: text/plain; charset=UTF-8';
    }
    if ($replyTo !== '') {
        $baseHeaders[] = 'Reply-To: ' . $replyTo;
    }

    $smtpSent = smtp_send_email($to, $subject, $body, implode("\r\n", $baseHeaders), $boundary, $config ?? []);
    if ($smtpSent !== null) {
        email_delivery_log($smtpSent ? 'smtp-success' : 'smtp-failed', [
            'to' => $to,
            'subject' => $subject,
            'error' => email_last_error_get(),
        ]);
        return $smtpSent;
    }

    try {
        $mailHeaders = $baseHeaders;
        $mailHeaders[] = 'From: ' . $fromAddress;
        $sent = @mail($to, $subject, $body, implode("\r\n", $mailHeaders));
        if (!$sent && email_last_error_get() === '') {
            email_last_error_set('PHP mail() returned false.');
        }
        email_delivery_log($sent ? 'mail-success' : 'mail-failed', [
            'to' => $to,
            'subject' => $subject,
            'error' => email_last_error_get(),
        ]);
        return $sent;
    } catch (Throwable) {
        email_last_error_set('PHP mail() threw an exception.');
        email_delivery_log('mail-exception', ['to' => $to, 'subject' => $subject]);
        return false;
    }
}

function send_notification_email_with_attachment(
    string $to,
    string $subject,
    string $message,
    string $attachmentName,
    string $attachmentContent,
    string $attachmentMime = 'application/octet-stream',
    ?string $htmlMessage = null
): bool {
    global $config;
    $to = trim($to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $subject = trim($subject) !== '' ? trim($subject) : 'Website Notification';
    try {
        $boundary = '=_Part_' . bin2hex(random_bytes(12));
    } catch (Throwable) {
        $boundary = '=_Part_' . md5((string)microtime(true) . $to . $subject);
    }
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $fromDomain = preg_replace('/^www\./i', '', (string)$host) ?: 'localhost';
    $fallbackFrom = 'no-reply@' . $fromDomain;
    if (!filter_var($fallbackFrom, FILTER_VALIDATE_EMAIL)) {
        $fallbackFrom = 'no-reply@localhost.localdomain';
    }

    $smtpFrom = trim((string)($config['smtp_from_email'] ?? ''));
    $smtpUser = trim((string)($config['smtp_username'] ?? ''));
    $from = filter_var($smtpFrom, FILTER_VALIDATE_EMAIL)
        ? $smtpFrom
        : (filter_var($smtpUser, FILTER_VALIDATE_EMAIL) ? $smtpUser : $fallbackFrom);

    $replyTo = trim((string)($config['notification_email'] ?? ''));
    if (!filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $replyTo = '';
    }

    $headers = ['MIME-Version: 1.0', 'Content-Type: multipart/mixed; boundary="' . $boundary . '"'];
    if ($replyTo !== '') {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    $htmlBody = $htmlMessage !== null ? trim((string)$htmlMessage) : '';
    $useHtml = $htmlBody !== '';
    $altBoundary = '';
    if ($useHtml) {
        try {
            $altBoundary = '=_Alt_' . bin2hex(random_bytes(10));
        } catch (Throwable) {
            $altBoundary = '=_Alt_' . md5((string)microtime(true) . $subject . $to);
        }
    }

    $body = '';
    $body .= '--' . $boundary . "\r\n";
    if ($useHtml) {
        $body .= 'Content-Type: multipart/alternative; boundary="' . $altBoundary . '"' . "\r\n\r\n";
        $body .= '--' . $altBoundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $message . "\r\n\r\n";
        $body .= '--' . $altBoundary . "\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $htmlBody . "\r\n\r\n";
        $body .= '--' . $altBoundary . "--\r\n";
    } else {
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $message . "\r\n\r\n";
    }
    $body .= '--' . $boundary . "\r\n";
    $body .= 'Content-Type: ' . $attachmentMime . '; name="' . $attachmentName . '"' . "\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= 'Content-Disposition: attachment; filename="' . $attachmentName . '"' . "\r\n\r\n";
    $body .= chunk_split(base64_encode($attachmentContent)) . "\r\n";
    $body .= '--' . $boundary . "--\r\n";

    $smtpSent = smtp_send_email($to, $subject, $body, implode("\r\n", $headers), $boundary, $config ?? []);
    if ($smtpSent !== null) {
        return $smtpSent;
    }

    try {
        $mailHeaders = $headers;
        $mailHeaders[] = 'From: ' . $from;
        return @mail($to, $subject, $body, implode("\r\n", $mailHeaders));
    } catch (Throwable) {
        return false;
    }
}

function smtp_send_email(string $to, string $subject, string $body, string $headers, ?string $boundary, array $config): ?bool
{
    $smtpHost = trim((string)($config['smtp_host'] ?? ''));
    if ($smtpHost === '') {
        return null;
    }

    $smtpPort = (int)($config['smtp_port'] ?? 587);
    $smtpUser = trim((string)($config['smtp_username'] ?? ''));
    $smtpPass = (string)($config['smtp_password'] ?? '');
    $smtpSecure = strtolower(trim((string)($config['smtp_encryption'] ?? 'tls')));
    $smtpFrom = trim((string)($config['smtp_from_email'] ?? ''));
    $smtpFromName = trim((string)($config['smtp_from_name'] ?? ($config['app_name'] ?? 'Website')));

    if ($smtpFrom === '' || !filter_var($smtpFrom, FILTER_VALIDATE_EMAIL)) {
        $smtpFrom = $smtpUser;
    }
    if ($smtpFrom === '' || !filter_var($smtpFrom, FILTER_VALIDATE_EMAIL)) {
        email_last_error_set('SMTP From address is invalid.');
        return false;
    }

    $transport = $smtpHost;
    if ($smtpSecure === 'ssl') {
        $transport = 'ssl://' . $smtpHost;
    }

    $socket = @stream_socket_client($transport . ':' . $smtpPort, $errno, $errstr, 20);
    if (!$socket) {
        email_last_error_set('SMTP connection failed: ' . $errstr . ' (' . $errno . ').');
        return false;
    }
    stream_set_timeout($socket, 20);

    $ok = smtp_expect($socket, [220]);
    $ok = $ok && smtp_command($socket, 'EHLO localhost', [250]);

    if ($ok && $smtpSecure === 'tls') {
        $ok = smtp_command($socket, 'STARTTLS', [220]) && @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $ok = $ok && smtp_command($socket, 'EHLO localhost', [250]);
    }

    if ($ok && $smtpUser !== '') {
        $ok = smtp_command($socket, 'AUTH LOGIN', [334]);
        $ok = $ok && smtp_command($socket, base64_encode($smtpUser), [334]);
        $ok = $ok && smtp_command($socket, base64_encode($smtpPass), [235]);
    }

    $ok = $ok && smtp_command($socket, 'MAIL FROM:<' . $smtpFrom . '>', [250]);
    $ok = $ok && smtp_command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
    $ok = $ok && smtp_command($socket, 'DATA', [354]);
    if (!$ok) {
        if (email_last_error_get() === '') {
            email_last_error_set('SMTP handshake failed before DATA transmission.');
        }
        @fwrite($socket, "QUIT\r\n");
        @fclose($socket);
        return false;
    }

    $mailHeaders = [];
    $mailHeaders[] = 'From: ' . ($smtpFromName !== '' ? ('"' . addslashes($smtpFromName) . '" <' . $smtpFrom . '>') : $smtpFrom);
    $mailHeaders[] = 'To: <' . $to . '>';
    $mailHeaders[] = 'Subject: ' . $subject;
    $mailHeaders[] = $headers;
    $mailHeaders[] = 'Date: ' . date('r');
    $mailHeaders[] = 'Message-ID: <' . md5((string)microtime(true) . $to) . '@' . preg_replace('/^.*@/', '', $smtpFrom) . '>';
    $mailData = implode("\r\n", $mailHeaders) . "\r\n\r\n" . $body;
    $mailData = str_replace(["\r\n.", "\n."], ["\r\n..", "\n.."], $mailData);
    @fwrite($socket, $mailData . "\r\n.\r\n");

    $ok = smtp_expect($socket, [250]);
    if (!$ok && email_last_error_get() === '') {
        email_last_error_set('SMTP server rejected the message body.');
    }
    @fwrite($socket, "QUIT\r\n");
    @fclose($socket);
    return $ok;
}

function smtp_command($socket, string $command, array $expectedCodes): bool
{
    @fwrite($socket, $command . "\r\n");
    return smtp_expect($socket, $expectedCodes);
}

function smtp_expect($socket, array $expectedCodes): bool
{
    $line = '';
    $code = 0;
    while (!feof($socket)) {
        $line = fgets($socket, 515) ?: '';
        if ($line === '') {
            break;
        }
        if (preg_match('/^(\d{3})([\s-])/', $line, $m)) {
            $code = (int)$m[1];
            if ($m[2] === ' ') {
                break;
            }
        } else {
            break;
        }
    }
    $isExpected = in_array($code, $expectedCodes, true);
    if (!$isExpected) {
        $lineSummary = trim($line);
        email_last_error_set('SMTP response ' . $code . ' not expected. Last line: ' . $lineSummary);
    }
    return $isExpected;
}

function generate_program_abbreviation(string $programName): string
{
    // Words to exclude from abbreviation
    $excludeWords = ['in', 'of', 'and', 'for', 'the', 'with', 'a', 'an', 'on', 'at', 'to', 'by'];
    
    // Split by spaces and filter out excluded words
    $words = preg_split('/\s+/', trim($programName)) ?: [];
    $significantWords = array_filter($words, static fn($word) => 
        !in_array(strtolower(trim($word)), $excludeWords, true)
    );
    
    // Take first letter of each significant word and convert to uppercase
    $abbreviation = '';
    foreach ($significantWords as $word) {
        $word = trim($word);
        if ($word !== '') {
            $abbreviation .= strtoupper($word[0]);
        }
    }
    
    // Fallback: if abbreviation is too short, use first 3-4 letters of first word
    if (strlen($abbreviation) < 3 && !empty($words)) {
        $firstWord = trim($words[0]);
        $abbreviation = strtoupper(substr($firstWord, 0, 4));
    }
    
    // Limit to 6 characters max
    return substr($abbreviation, 0, 6);
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
