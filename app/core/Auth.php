<?php
class Auth
{
    private const ROLE_SUPER_ADMIN = 'super_admin';
    private const ROLE_JUNIOR_ADMIN = 'junior_admin';
    private const ROLE_TEACHER = 'teacher';

    public static function check(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    public static function attempt(PDO $pdo, string $email, string $password): bool
    {
        $stmt = $pdo->prepare('SELECT id, name, password, role, status FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (
            $user &&
            ($user['status'] ?? 'active') === 'active' &&
            password_verify($password, $user['password'])
        ) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = (string)($user['role'] ?? self::ROLE_SUPER_ADMIN);
            self::loadRolePermissions($pdo);
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function role(): string
    {
        return (string)($_SESSION['admin_role'] ?? self::ROLE_SUPER_ADMIN);
    }

    public static function isSuperAdmin(): bool
    {
        return self::role() === self::ROLE_SUPER_ADMIN;
    }

    public static function isJuniorAdmin(): bool
    {
        return self::role() === self::ROLE_JUNIOR_ADMIN;
    }

    public static function isTeacher(): bool
    {
        return self::role() === self::ROLE_TEACHER;
    }

    public static function canManageRole(string $targetRole): bool
    {
        $target = strtolower(trim($targetRole));
        if (self::isSuperAdmin()) {
            return in_array($target, [self::ROLE_SUPER_ADMIN, self::ROLE_JUNIOR_ADMIN, self::ROLE_TEACHER], true);
        }
        if (self::isJuniorAdmin()) {
            return $target === self::ROLE_TEACHER;
        }
        return false;
    }

    public static function canManageEntity(string $entity): bool
    {
        $entity = strtolower(trim($entity));
        if (self::isSuperAdmin()) {
            return true;
        }

        if (self::isJuniorAdmin()) {
            $defaults = [
                'programmes',
                'departments',
                'news',
                'careers',
                'tenders',
                'events',
                'gallery',
                'library_resources',
                'faqs',
                'pages',
                'messages',
                'media_assets',
                'media',
                'students',
                'portal_courses',
                'programme_timetables',
                'course_grades',
                'course_assignments',
                'study_materials',
                'users',
                'grading_schemes',
            ];
            return in_array($entity, self::configuredPermissions('junior_admin_permissions', $defaults), true);
        }

        if (self::isTeacher()) {
            $defaults = [
                'portal_courses',
                'programme_timetables',
                'course_grades',
                'course_assignments',
                'study_materials',
                'library_resources',
            ];
            return in_array($entity, self::configuredPermissions('teacher_permissions', $defaults), true);
        }

        return false;
    }

    public static function requireAdmin(): void
    {
        if (!self::check()) {
            header('Location: ' . base_url('admin/login'));
            exit;
        }
    }

    private static function loadRolePermissions(PDO $pdo): void
    {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('junior_admin_permissions','teacher_permissions')");
            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                $key = (string)($row['setting_key'] ?? '');
                $value = (string)($row['setting_value'] ?? '');
                if ($key !== '') {
                    $_SESSION[$key] = $value;
                }
            }
        } catch (PDOException) {
            // Ignore if settings table is not ready yet.
        }
    }

    private static function configuredPermissions(string $sessionKey, array $defaults): array
    {
        $raw = trim((string)($_SESSION[$sessionKey] ?? ''));
        if ($raw === '') {
            return $defaults;
        }
        $parsed = array_values(array_filter(array_map(
            static fn($entity) => strtolower(trim((string)$entity)),
            explode(',', $raw)
        )));
        return $parsed === [] ? $defaults : $parsed;
    }
}
