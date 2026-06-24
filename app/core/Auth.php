<?php
class Auth
{
    private const ROLE_SUPER_ADMIN = 'super_admin';
    private const ROLE_JUNIOR_ADMIN = 'junior_admin';
    private const ROLE_EDITOR = 'editor';
    private const ROLE_VIEWER = 'viewer';
    private const ROLE_REGISTRAR = 'registrar';
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

    public static function isEditor(): bool
    {
        return self::role() === self::ROLE_EDITOR;
    }

    public static function isViewer(): bool
    {
        return self::role() === self::ROLE_VIEWER;
    }

    public static function isRegistrar(): bool
    {
        return self::role() === self::ROLE_REGISTRAR;
    }

    public static function canManageRole(string $targetRole): bool
    {
        $target = strtolower(trim($targetRole));
        if (self::isSuperAdmin()) {
            return in_array($target, [self::ROLE_SUPER_ADMIN, self::ROLE_JUNIOR_ADMIN, self::ROLE_EDITOR, self::ROLE_VIEWER, self::ROLE_REGISTRAR, self::ROLE_TEACHER], true);
        }
        if (self::isJuniorAdmin()) {
            return in_array($target, [self::ROLE_EDITOR, self::ROLE_VIEWER, self::ROLE_REGISTRAR, self::ROLE_TEACHER], true);
        }
        return false;
    }

    public static function canViewEntity(string $entity): bool
    {
        $entity = strtolower(trim($entity));
        if (self::isSuperAdmin()) {
            return true;
        }
        
        // Check AccessMatrix first (new system)
        if (class_exists('AccessMatrix') && isset($_SESSION['admin_id'])) {
            if (AccessMatrix::hasPermission($_SESSION['admin_id'], $entity, 'view')) {
                return true;
            }
        }
        
        // Fallback to CSV permissions (legacy system)
        $role = self::role();
        $defaults = self::roleDefaultViewPermissions($role);
        return in_array($entity, self::configuredPermissions(self::roleViewSessionKey($role), $defaults), true);
    }

    public static function canManageEntity(string $entity): bool
    {
        $entity = strtolower(trim($entity));
        if (self::isSuperAdmin()) {
            return true;
        }
        
        // Check AccessMatrix first (new system)
        if (class_exists('AccessMatrix') && isset($_SESSION['admin_id'])) {
            if (AccessMatrix::hasPermission($_SESSION['admin_id'], $entity, 'edit')) {
                return true;
            }
        }
        
        // Fallback to CSV permissions (legacy system)
        $role = self::role();
        $defaults = self::roleDefaultManagePermissions($role);
        return in_array($entity, self::configuredPermissions(self::roleManageSessionKey($role), $defaults), true);
    }

    public static function requireAdmin(): void
    {
        if (!self::check()) {
            header('Location: ' . admin_login_url());
            exit;
        }
    }

    private static function loadRolePermissions(PDO $pdo): void
    {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN (
                'junior_admin_permissions',
                'junior_admin_view_permissions',
                'junior_admin_manage_permissions',
                'teacher_permissions',
                'teacher_view_permissions',
                'teacher_manage_permissions',
                'editor_view_permissions',
                'editor_manage_permissions',
                'viewer_view_permissions',
                'viewer_manage_permissions',
                'registrar_view_permissions',
                'registrar_manage_permissions'
            )");
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

    private static function roleViewSessionKey(string $role): string
    {
        return match ($role) {
            self::ROLE_JUNIOR_ADMIN => 'junior_admin_view_permissions',
            self::ROLE_EDITOR => 'editor_view_permissions',
            self::ROLE_VIEWER => 'viewer_view_permissions',
            self::ROLE_REGISTRAR => 'registrar_view_permissions',
            self::ROLE_TEACHER => 'teacher_view_permissions',
            default => 'junior_admin_view_permissions',
        };
    }

    private static function roleManageSessionKey(string $role): string
    {
        return match ($role) {
            self::ROLE_JUNIOR_ADMIN => 'junior_admin_manage_permissions',
            self::ROLE_EDITOR => 'editor_manage_permissions',
            self::ROLE_VIEWER => 'viewer_manage_permissions',
            self::ROLE_REGISTRAR => 'registrar_manage_permissions',
            self::ROLE_TEACHER => 'teacher_manage_permissions',
            default => 'junior_admin_manage_permissions',
        };
    }

    private static function roleDefaultViewPermissions(string $role): array
    {
        return match ($role) {
            self::ROLE_JUNIOR_ADMIN => [
                'programmes','departments','news','careers','tenders','events','gallery','library_resources','faqs','pages','messages','media','students','portal_courses','programme_timetables','course_grades','course_assignments','study_materials','users','grading_schemes','testimonials','social_updates'
            ],
            self::ROLE_EDITOR => [
                'programmes','departments','news','careers','tenders','events','gallery','library_resources','faqs','pages','testimonials','social_updates','media'
            ],
            self::ROLE_VIEWER => [
                'programmes','departments','news','careers','tenders','events','gallery','library_resources','faqs','pages','messages','students','portal_courses','programme_timetables','course_grades','course_assignments','study_materials','users','grading_schemes','testimonials','social_updates','media'
            ],
            self::ROLE_REGISTRAR => [
                'programmes','departments','events','messages','students','portal_courses','programme_timetables','course_grades','course_assignments','study_materials','library_resources'
            ],
            self::ROLE_TEACHER => [
                'portal_courses','programme_timetables','course_grades','course_assignments','study_materials','library_resources'
            ],
            default => [],
        };
    }

    private static function roleDefaultManagePermissions(string $role): array
    {
        return match ($role) {
            self::ROLE_JUNIOR_ADMIN => [
                'programmes','departments','news','careers','tenders','events','gallery','library_resources','faqs','pages','messages','media','students','portal_courses','programme_timetables','course_grades','course_assignments','study_materials','users','grading_schemes','testimonials','social_updates'
            ],
            self::ROLE_EDITOR => [
                'programmes','departments','news','careers','tenders','events','gallery','library_resources','faqs','pages','testimonials','social_updates','media'
            ],
            self::ROLE_VIEWER => [],
            self::ROLE_REGISTRAR => [
                'students','messages','events','portal_courses','programme_timetables','course_grades','course_assignments','study_materials'
            ],
            self::ROLE_TEACHER => [
                'portal_courses','programme_timetables','course_grades','course_assignments','study_materials','library_resources'
            ],
            default => [],
        };
    }
}
