<?php

class SuperAdminController extends Controller
{
    private $auditLogger;
    private $accessMatrix;
    private $db;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Login page and form handler
     */
    public function login()
    {
        // Allow login without authentication
        SuperAdminAuth::initSession();
        
        // If already logged in, redirect to dashboard
        if (SuperAdminAuth::isAuthenticated()) {
            header('Location: /super-admin/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $result = SuperAdminAuth::attempt($email, $password);

            if ($result['success']) {
                if ($result['requires_2fa'] ?? false) {
                    return $this->jsonResponse($result);
                } else {
                    AuditLogger::logLogin(SuperAdminAuth::getId(), 'super_admin');
                    return $this->jsonResponse(['success' => true, 'message' => 'Login successful']);
                }
            } else {
                AuditLogger::logFailedLogin($email, 'super_admin');
                return $this->jsonResponse(['success' => false, 'message' => $result['message']], 401);
            }
        }

        return $this->view('super-admin/login', []);
    }

    /**
     * Verify 2FA code
     */
    public function verify2FA()
    {
        SuperAdminAuth::initSession();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $otp_code = $_POST['otp'] ?? '';
        $result = SuperAdminAuth::verify2FA($otp_code);

        if ($result['success']) {
            AuditLogger::logLogin(SuperAdminAuth::getId(), 'super_admin');
        }

        return $this->jsonResponse($result);
    }

    /**
     * Logout
     */
    public function logout()
    {
        SuperAdminAuth::initSession();
        $admin_id = SuperAdminAuth::getId();
        
        if ($admin_id) {
            AuditLogger::init($admin_id, 'super_admin');
            AuditLogger::logLogout($admin_id, 'super_admin');
        }

        SuperAdminAuth::logout();
        header('Location: /super-admin/login');
        exit;
    }

    /**
     * Require authentication for all other methods
     */
    private function requireAuth()
    {
        SuperAdminAuth::initSession();
        SuperAdminAuth::requireAuth();
    }

    /**
     * Initialize common properties
     */
    private function init()
    {
        $this->requireAuth();
        $this->db = Database::getInstance();
        $this->auditLogger = new AuditLogger();
        $this->accessMatrix = new AccessMatrix();
        AuditLogger::init(SuperAdminAuth::getId(), 'super_admin');
    }

    /**
     * Dashboard - Main overview
     */
    public function dashboard()
    {
        $this->init();
        $data = [];

        // Get activity stats
        $data['activity_stats'] = AuditLogger::getActivityStats(7);

        // Get system overview
        $data['admin_count'] = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $data['student_count'] = $this->db->query("SELECT COUNT(*) FROM student_accounts")->fetchColumn();
        $data['crm_users_count'] = $this->db->query("SELECT COUNT(*) FROM crm_users")->fetchColumn();

        // Get suspicious alerts
        $data['suspicious_alerts'] = AuditLogger::getSuspiciousAlerts(10);

        // Get recent activity
        $stmt = $this->db->prepare("
            SELECT * FROM audit_logs 
            ORDER BY timestamp DESC 
            LIMIT 20
        ");
        $stmt->execute();
        $data['recent_activity'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get active sessions
        $stmt = $this->db->prepare("
            SELECT * FROM user_sessions 
            WHERE is_active = TRUE 
            ORDER BY last_activity DESC 
            LIMIT 15
        ");
        $stmt->execute();
        $data['active_sessions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get active sessions count by user type
        $stmt = $this->db->prepare("
            SELECT user_type, COUNT(*) as count 
            FROM user_sessions 
            WHERE is_active = TRUE
            GROUP BY user_type
        ");
        $stmt->execute();
        $data['sessions_by_type'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->view('super-admin/dashboard', $data);
    }

    /**
     * Users Management - List all users
     */
    public function users()
    {
        $this->init();
        $page = $_GET['page'] ?? 1;
        $limit = 25;
        $offset = ($page - 1) * $limit;
        $filter_role = $_GET['role'] ?? null;
        $search = $_GET['search'] ?? null;

        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if ($filter_role) {
            $sql .= " AND role = ?";
            $params[] = $filter_role;
        }

        if ($search) {
            $sql .= " AND (name LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Get total count
        $count_stmt = $this->db->prepare(str_replace('SELECT *', 'SELECT COUNT(*) as count', $sql));
        $count_stmt->execute($params);
        $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get paginated results
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get available roles
        $roles = $this->db->query("SELECT DISTINCT role FROM users ORDER BY role")->fetchAll(PDO::FETCH_COLUMN);

        $data = [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'roles' => $roles,
            'filter_role' => $filter_role,
            'search' => $search
        ];

        return $this->view('super-admin/users', $data);
    }

    /**
     * Create new user
     */
    public function createUser()
    {
        $this->init();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'editor';

            // Validation
            if (empty($name) || empty($email) || empty($password)) {
                return $this->jsonResponse(['success' => false, 'message' => 'All fields required']);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->jsonResponse(['success' => false, 'message' => 'Invalid email']);
            }

            // Check if email exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return $this->jsonResponse(['success' => false, 'message' => 'Email already exists']);
            }

            // Create user
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, role, active, created_by, created_at)
                VALUES (?, ?, ?, ?, 1, ?, NOW())
            ");

            if ($stmt->execute([$name, $email, $password_hash, $role, SuperAdminAuth::getId()])) {
                $user_id = $this->db->lastInsertId();

                AuditLogger::logUserCreation($user_id, $email, $role);

                return $this->jsonResponse([
                    'success' => true,
                    'message' => "User '$name' created successfully",
                    'user_id' => $user_id
                ]);
            }

            return $this->jsonResponse(['success' => false, 'message' => 'Failed to create user']);
        }

        $roles = $this->db->query("SELECT DISTINCT role FROM users ORDER BY role")->fetchAll(PDO::FETCH_COLUMN);
        return $this->view('super-admin/create-user', ['roles' => $roles]);
    }

    /**
     * Edit user
     */
    public function editUser($user_id)
    {
        $this->init();
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return $this->jsonResponse(['success' => false, 'message' => 'User not found'], 404);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old_user = $user;
            $name = $_POST['name'] ?? $user['name'];
            $email = $_POST['email'] ?? $user['email'];
            $role = $_POST['role'] ?? $user['role'];
            $active = isset($_POST['active']) ? 1 : 0;

            // Validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->jsonResponse(['success' => false, 'message' => 'Invalid email']);
            }

            // Check email uniqueness
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                return $this->jsonResponse(['success' => false, 'message' => 'Email already exists']);
            }

            // Update user
            $stmt = $this->db->prepare("
                UPDATE users 
                SET name = ?, email = ?, role = ?, active = ?, updated_at = NOW()
                WHERE id = ?
            ");

            if ($stmt->execute([$name, $email, $role, $active, $user_id])) {
                $new_user = ['name' => $name, 'email' => $email, 'role' => $role, 'active' => $active];

                if ($old_user['role'] != $role) {
                    AuditLogger::logPermissionChange($user_id, $old_user['role'], $role);
                }

                AuditLogger::log(
                    'edit',
                    'user',
                    $user_id,
                    $email,
                    $old_user,
                    $new_user
                );

                return $this->jsonResponse(['success' => true, 'message' => 'User updated successfully']);
            }

            return $this->jsonResponse(['success' => false, 'message' => 'Failed to update user']);
        }

        $roles = $this->db->query("SELECT DISTINCT role FROM users ORDER BY role")->fetchAll(PDO::FETCH_COLUMN);
        return $this->view('super-admin/edit-user', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Delete user
     */
    public function deleteUser($user_id)
    {
        $this->init();
        if ($user_id == SuperAdminAuth::getId()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Cannot delete yourself']);
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return $this->jsonResponse(['success' => false, 'message' => 'User not found'], 404);
        }

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$user_id])) {
            AuditLogger::logUserDeletion($user_id, $user['email']);

            return $this->jsonResponse(['success' => true, 'message' => 'User deleted successfully']);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Failed to delete user']);
    }

    /**
     * Audit Logs - View all audit logs
     */
    public function auditLogs()
    {
        $this->init();
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $filters = [
            'user_type' => $_GET['user_type'] ?? null,
            'action' => $_GET['action'] ?? null,
            'entity_type' => $_GET['entity_type'] ?? null,
            'status' => $_GET['status'] ?? null,
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null
        ];

        $filters = array_filter($filters, fn($v) => $v !== null);

        $logs = AuditLogger::getLogs($filters, $limit, $offset);
        $total = AuditLogger::getLogCount($filters);

        $data = [
            'logs' => $logs,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'filters' => $filters
        ];

        return $this->view('super-admin/audit-logs', $data);
    }

    /**
     * Suspicious Activity Alerts
     */
    public function suspiciousAlerts()
    {
        $this->init();
        $status = $_GET['status'] ?? 'new';
        $limit = 50;

        $alerts = AuditLogger::getSuspiciousAlerts($limit, $status);

        $data = [
            'alerts' => $alerts,
            'current_status' => $status
        ];

        return $this->view('super-admin/suspicious-alerts', $data);
    }

    /**
     * Review suspicious alert
     */
    public function reviewAlert($alert_id)
    {
        $this->init();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_status = $_POST['status'] ?? 'reviewed';
            $action_taken = $_POST['action_taken'] ?? null;

            if (AuditLogger::reviewAlert($alert_id, $new_status, $action_taken, SuperAdminAuth::getId())) {
                return $this->jsonResponse(['success' => true, 'message' => 'Alert updated']);
            }

            return $this->jsonResponse(['success' => false, 'message' => 'Failed to update alert']);
        }
    }

    /**
     * Access Matrix - Permission Management
     */
    public function accessMatrix()
    {
        $this->init();
        $matrix = AccessMatrix::getPermissionMatrix();
        $resources = AccessMatrix::getAvailableResources();

        $data = [
            'matrix' => $matrix,
            'resources' => $resources
        ];

        return $this->view('super-admin/access-matrix', $data);
    }

    /**
     * Update access matrix
     */
    public function updateAccessMatrix()
    {
        $this->init();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $matrix_data = json_decode($_POST['matrix_data'] ?? '{}', true);

        $changes = AccessMatrix::bulkUpdateMatrix($matrix_data);

        return $this->jsonResponse([
            'success' => true,
            'message' => "Updated $changes permissions",
            'changes' => $changes
        ]);
    }

    /**
     * User Permissions - View permissions for specific user
     */
    public function userPermissions($user_id)
    {
        $this->init();
        $stmt = $this->db->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return $this->jsonResponse(['success' => false, 'message' => 'User not found'], 404);
        }

        $permissions = AccessMatrix::getUserPermissions($user_id);
        $resources = AccessMatrix::getAvailableResources();

        $data = [
            'user' => $user,
            'permissions' => $permissions,
            'resources' => $resources
        ];

        return $this->view('super-admin/user-permissions', $data);
    }

    /**
     * Sessions Management - View active sessions
     */
    public function sessions()
    {
        $this->init();
        $stmt = $this->db->prepare("
            SELECT * FROM user_sessions 
            WHERE is_active = TRUE
            ORDER BY last_activity DESC
        ");
        $stmt->execute();
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = ['sessions' => $sessions];

        return $this->view('super-admin/sessions', $data);
    }

    /**
     * Force logout a user
     */
    public function forceLogout($session_id)
    {
        $this->init();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $stmt = $this->db->prepare("
            UPDATE user_sessions 
            SET is_active = FALSE 
            WHERE id = ?
        ");

        if ($stmt->execute([$session_id])) {
            AuditLogger::log('force_logout', 'user_session', $session_id, "Session forced logout");

            return $this->jsonResponse(['success' => true, 'message' => 'User logged out']);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Failed to logout user']);
    }

    /**
     * Force logout all users
     */
    public function forceLogoutAll()
    {
        $this->init();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $stmt = $this->db->prepare("
            UPDATE user_sessions 
            SET is_active = FALSE 
            WHERE user_type != 'super_admin'
        ");

        if ($stmt->execute()) {
            $affected = $stmt->rowCount();
            AuditLogger::logBulkOperation('force_logout', 'user_session', $affected);

            return $this->jsonResponse([
                'success' => true,
                'message' => "Logged out $affected users"
            ]);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Failed to logout users']);
    }

    /**
     * Emergency Maintenance Mode
     */
    public function emergencyMode()
    {
        $this->init();
        $is_maintenance = (bool) $this->db->query("SELECT value FROM settings WHERE key = 'maintenance_mode'")->fetchColumn();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enable = $_POST['enable'] ?? false;
            $message = $_POST['message'] ?? 'System undergoing maintenance. Please try again later.';

            $stmt = $this->db->prepare("
                INSERT INTO settings (key, value) VALUES ('maintenance_mode', ?)
                ON DUPLICATE KEY UPDATE value = VALUES(value)
            ");
            $stmt->execute([$enable ? '1' : '0']);

            $stmt = $this->db->prepare("
                INSERT INTO settings (key, value) VALUES ('maintenance_message', ?)
                ON DUPLICATE KEY UPDATE value = VALUES(value)
            ");
            $stmt->execute([$message]);

            $action = $enable ? 'enabled' : 'disabled';
            AuditLogger::log(
                'maintenance_mode',
                'system',
                null,
                "Maintenance mode $action",
                ['enabled' => !$enable],
                ['enabled' => $enable]
            );

            return $this->jsonResponse([
                'success' => true,
                'message' => "Maintenance mode $action"
            ]);
        }

        $stmt = $this->db->prepare("SELECT value FROM settings WHERE key = 'maintenance_message'");
        $stmt->execute();
        $maintenance_message = $stmt->fetchColumn();

        $data = [
            'is_maintenance' => $is_maintenance,
            'message' => $maintenance_message
        ];

        return $this->view('super-admin/emergency-mode', $data);
    }

    /**
     * Lock down portal (disable all logins except super admin)
     */
    public function lockdownPortal()
    {
        $this->init();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $enable = $_POST['enable'] ?? false;

        $stmt = $this->db->prepare("
            INSERT INTO settings (key, value) VALUES ('portal_lockdown', ?)
            ON DUPLICATE KEY UPDATE value = VALUES(value)
        ");
        $stmt->execute([$enable ? '1' : '0']);

        $action = $enable ? 'enabled' : 'disabled';
        AuditLogger::log(
            'portal_lockdown',
            'system',
            null,
            "Portal lockdown $action",
            ['lockdown' => !$enable],
            ['lockdown' => $enable],
            'CRITICAL: Portal lockdown ' . $action
        );

        return $this->jsonResponse([
            'success' => true,
            'message' => "Portal lockdown $action"
        ]);
    }

    /**
     * System Status Overview
     */
    public function systemStatus()
    {
        $this->init();
        $data = [
            'admin_users' => $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'active_sessions' => $this->db->query("SELECT COUNT(*) FROM user_sessions WHERE is_active = TRUE")->fetchColumn(),
            'students' => $this->db->query("SELECT COUNT(*) FROM student_accounts")->fetchColumn(),
            'audit_logs' => $this->db->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn(),
            'alerts' => $this->db->query("SELECT COUNT(*) FROM suspicious_activity_alerts WHERE status = 'new'")->fetchColumn(),
        ];

        return $this->view('super-admin/system-status', $data);
    }

    /**
     * JSON response helper
     */
    protected function jsonResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
