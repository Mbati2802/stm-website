<?php

class CRMAuth
{
    private static ?array $user = null;
    private static string $sessionKey = 'crm_user_id';

    public static function login(int $userId, string $username, string $role): void
    {
        $_SESSION[self::$sessionKey] = $userId;
        $_SESSION['crm_username'] = $username;
        $_SESSION['crm_role'] = $role;
        $_SESSION['crm_login_time'] = time();
    }

    public static function logout(): void
    {
        unset($_SESSION[self::$sessionKey]);
        unset($_SESSION['crm_username']);
        unset($_SESSION['crm_role']);
        unset($_SESSION['crm_login_time']);
    }

    public static function check(): bool
    {
        return isset($_SESSION[self::$sessionKey]);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /crm/login');
            exit;
        }
    }

    public static function user(): ?array
    {
        if (self::$user === null) {
            if (!self::check()) {
                return null;
            }
            
            $config = require __DIR__ . '/../../config/crm_config.php';
            $pdo = new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
                $config['db']['user'],
                $config['db']['pass'],
                $config['db']['options']
            );

            $stmt = $pdo->prepare('SELECT * FROM crm_users WHERE id = ? AND status = "active"');
            $stmt->execute([$_SESSION[self::$sessionKey]]);
            self::$user = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return self::$user;
    }

    public static function id(): ?int
    {
        return $_SESSION[self::$sessionKey] ?? null;
    }

    public static function username(): ?string
    {
        return $_SESSION['crm_username'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['crm_role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isOfficer(): bool
    {
        return self::role() === 'officer';
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
