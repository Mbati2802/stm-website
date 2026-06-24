<?php

class AuditLogger
{
    private static $db;
    private static $current_user = null;
    private static $current_user_type = null;

    public function __construct()
    {
        self::$db = Database::getInstance();
    }

    /**
     * Initialize audit logger with current user context
     */
    public static function init($user_id, $user_type = 'admin')
    {
        self::$current_user = $user_id;
        self::$current_user_type = $user_type;
    }

    /**
     * Log an action
     * 
     * @param string $action - Action performed (create, edit, delete, login, logout, export, etc)
     * @param string $entity_type - Type of entity affected (user, student, course, etc)
     * @param int $entity_id - ID of entity affected
     * @param string $entity_name - Name/label of entity
     * @param array $old_values - Previous values (for edit/delete)
     * @param array $new_values - New values (for create/edit)
     * @param string $description - Additional context
     * @param string $status - success|failed|suspicious
     */
    public static function log($action, $entity_type, $entity_id = null, $entity_name = null, 
                              $old_values = null, $new_values = null, $description = null, $status = 'success')
    {
        if (!self::$current_user) {
            return false;
        }

        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $device_fingerprint = self::generateDeviceFingerprint();

        $old_values_json = $old_values ? json_encode($old_values) : null;
        $new_values_json = $new_values ? json_encode($new_values) : null;

        try {
            $stmt = self::$db->prepare("
                INSERT INTO audit_logs 
                (user_id, user_type, action, entity_type, entity_id, entity_name, 
                 old_values, new_values, ip_address, device_fingerprint, user_agent, 
                 description, status, timestamp) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                self::$current_user,
                self::$current_user_type,
                $action,
                $entity_type,
                $entity_id,
                $entity_name,
                $old_values_json,
                $new_values_json,
                $ip_address,
                $device_fingerprint,
                $user_agent,
                $description,
                $status
            ]);

            // Check if this is suspicious activity and create alert
            if ($status === 'suspicious' || self::isSuspiciousAction($action, $entity_type)) {
                self::createSuspiciousAlert(
                    self::$current_user,
                    $action,
                    $entity_type,
                    $description,
                    $ip_address,
                    $device_fingerprint
                );
            }

            return true;
        } catch (Exception $e) {
            error_log("AuditLogger error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log login
     */
    public static function logLogin($user_id, $user_type = 'admin')
    {
        self::log(
            'login',
            'authentication',
            $user_id,
            "$user_type user login",
            null,
            null,
            "User logged in from IP: " . $_SERVER['REMOTE_ADDR']
        );
    }

    /**
     * Log logout
     */
    public static function logLogout($user_id, $user_type = 'admin')
    {
        self::log(
            'logout',
            'authentication',
            $user_id,
            "$user_type user logout",
            null,
            null,
            "User logged out"
        );
    }

    /**
     * Log failed login attempt
     */
    public static function logFailedLogin($email_or_username, $user_type = 'admin')
    {
        try {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

            $stmt = self::$db->prepare("
                INSERT INTO audit_logs 
                (user_type, action, entity_type, entity_name, ip_address, 
                 user_agent, description, status, timestamp) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'failed', NOW())
            ");

            $stmt->execute([
                $user_type,
                'login',
                'authentication',
                "Failed login for: $email_or_username",
                $ip_address,
                $user_agent,
                "Failed login attempt for $email_or_username"
            ]);

            return true;
        } catch (Exception $e) {
            error_log("AuditLogger error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log data export
     */
    public static function logExport($export_type, $entity_type, $record_count, $description = null)
    {
        $details = "$export_type export of $record_count records from $entity_type";
        
        self::log(
            'export',
            $entity_type,
            null,
            $details,
            null,
            ['export_type' => $export_type, 'record_count' => $record_count],
            $description ?? $details
        );
    }

    /**
     * Log bulk operation
     */
    public static function logBulkOperation($operation, $entity_type, $affected_count, $description = null)
    {
        $details = "Bulk $operation on $affected_count records in $entity_type";
        
        self::log(
            "bulk_$operation",
            $entity_type,
            null,
            $details,
            null,
            ['affected_count' => $affected_count],
            $description ?? $details
        );
    }

    /**
     * Log permission change
     */
    public static function logPermissionChange($target_user_id, $old_role, $new_role, $description = null)
    {
        self::log(
            'permission_change',
            'user',
            $target_user_id,
            "Role change",
            ['role' => $old_role],
            ['role' => $new_role],
            $description ?? "Role changed from $old_role to $new_role"
        );
    }

    /**
     * Log user creation
     */
    public static function logUserCreation($user_id, $email, $role, $description = null)
    {
        self::log(
            'create',
            'user',
            $user_id,
            $email,
            null,
            ['email' => $email, 'role' => $role],
            $description ?? "New user created: $email with role $role"
        );
    }

    /**
     * Log user deletion
     */
    public static function logUserDeletion($user_id, $email, $reason = null)
    {
        self::log(
            'delete',
            'user',
            $user_id,
            $email,
            ['email' => $email, 'status' => 'deleted'],
            null,
            $reason ?? "User deleted: $email",
            'success'
        );
    }

    /**
     * Log system setting change
     */
    public static function logSettingChange($setting_key, $old_value, $new_value, $description = null)
    {
        // Don't log sensitive settings
        $sensitive_settings = ['database_password', 'api_key', 'email_password', 'smtp_password'];
        if (in_array($setting_key, $sensitive_settings)) {
            $old_value = '***REDACTED***';
            $new_value = '***REDACTED***';
        }

        self::log(
            'edit',
            'setting',
            null,
            $setting_key,
            ['value' => $old_value],
            ['value' => $new_value],
            $description ?? "System setting changed: $setting_key"
        );
    }

    /**
     * Retrieve audit logs
     */
    public static function getLogs($filters = [], $limit = 100, $offset = 0)
    {
        $sql = "SELECT * FROM audit_logs WHERE 1=1";
        $params = [];

        // Filter by user
        if (isset($filters['user_id'])) {
            $sql .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }

        // Filter by user type
        if (isset($filters['user_type'])) {
            $sql .= " AND user_type = ?";
            $params[] = $filters['user_type'];
        }

        // Filter by action
        if (isset($filters['action'])) {
            $sql .= " AND action = ?";
            $params[] = $filters['action'];
        }

        // Filter by entity type
        if (isset($filters['entity_type'])) {
            $sql .= " AND entity_type = ?";
            $params[] = $filters['entity_type'];
        }

        // Filter by status
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        // Filter by date range
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $sql .= " AND DATE(timestamp) BETWEEN ? AND ?";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        } elseif (isset($filters['start_date'])) {
            $sql .= " AND DATE(timestamp) >= ?";
            $params[] = $filters['start_date'];
        }

        // Filter by IP address
        if (isset($filters['ip_address'])) {
            $sql .= " AND ip_address = ?";
            $params[] = $filters['ip_address'];
        }

        $sql .= " ORDER BY timestamp DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get audit log count with filters
     */
    public static function getLogCount($filters = [])
    {
        $sql = "SELECT COUNT(*) as count FROM audit_logs WHERE 1=1";
        $params = [];

        if (isset($filters['user_id'])) {
            $sql .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (isset($filters['user_type'])) {
            $sql .= " AND user_type = ?";
            $params[] = $filters['user_type'];
        }

        if (isset($filters['action'])) {
            $sql .= " AND action = ?";
            $params[] = $filters['action'];
        }

        if (isset($filters['entity_type'])) {
            $sql .= " AND entity_type = ?";
            $params[] = $filters['entity_type'];
        }

        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    }

    /**
     * Check if action is suspicious
     */
    private static function isSuspiciousAction($action, $entity_type)
    {
        $suspicious_patterns = [
            'delete' => ['user', 'student', 'course'],
            'edit' => ['setting', 'role'],
            'bulk_delete' => ['*'],
            'bulk_edit' => ['*'],
            'export' => ['student', 'user', 'grades']
        ];

        return isset($suspicious_patterns[$action]) && 
               (in_array($entity_type, $suspicious_patterns[$action]) || 
                in_array('*', $suspicious_patterns[$action]));
    }

    /**
     * Create suspicious activity alert
     */
    private static function createSuspiciousAlert($user_id, $action, $entity_type, $description, $ip_address, $device_fingerprint)
    {
        try {
            $stmt = self::$db->prepare("
                INSERT INTO suspicious_activity_alerts 
                (user_id, user_type, alert_type, description, ip_address, device_fingerprint, severity, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'high', 'new')
            ");

            $stmt->execute([
                $user_id,
                self::$current_user_type,
                "$action:$entity_type",
                $description,
                $ip_address,
                $device_fingerprint
            ]);
        } catch (Exception $e) {
            error_log("Failed to create suspicious alert: " . $e->getMessage());
        }
    }

    /**
     * Generate device fingerprint
     */
    private static function generateDeviceFingerprint()
    {
        $fingerprint_data = [
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            'accept_encoding' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
        ];

        return hash('sha256', json_encode($fingerprint_data));
    }

    /**
     * Get suspicious activity alerts
     */
    public static function getSuspiciousAlerts($limit = 50, $status = 'new')
    {
        $stmt = self::$db->prepare("
            SELECT * FROM suspicious_activity_alerts 
            WHERE status = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$status, $limit]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Review suspicious alert
     */
    public static function reviewAlert($alert_id, $status, $action_taken = null, $reviewed_by = null)
    {
        $stmt = self::$db->prepare("
            UPDATE suspicious_activity_alerts 
            SET status = ?, action_taken = ?, reviewed_by = ?, reviewed_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([$status, $action_taken, $reviewed_by, $alert_id]);
    }

    /**
     * Get activity statistics
     */
    public static function getActivityStats($days = 7)
    {
        $start_date = date('Y-m-d', strtotime("-$days days"));

        // Action breakdown
        $stmt = self::$db->prepare("
            SELECT action, COUNT(*) as count 
            FROM audit_logs 
            WHERE DATE(timestamp) >= ?
            GROUP BY action
            ORDER BY count DESC
        ");
        $stmt->execute([$start_date]);
        $action_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // User type breakdown
        $stmt = self::$db->prepare("
            SELECT user_type, COUNT(*) as count 
            FROM audit_logs 
            WHERE DATE(timestamp) >= ?
            GROUP BY user_type
        ");
        $stmt->execute([$start_date]);
        $user_type_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Failed actions
        $stmt = self::$db->prepare("
            SELECT COUNT(*) as count 
            FROM audit_logs 
            WHERE DATE(timestamp) >= ? AND status = 'failed'
        ");
        $stmt->execute([$start_date]);
        $failed_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Suspicious activities
        $stmt = self::$db->prepare("
            SELECT COUNT(*) as count 
            FROM suspicious_activity_alerts 
            WHERE DATE(created_at) >= ? AND status = 'new'
        ");
        $stmt->execute([$start_date]);
        $suspicious_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return [
            'action_breakdown' => $action_stats,
            'user_type_breakdown' => $user_type_stats,
            'failed_actions' => $failed_count,
            'suspicious_alerts' => $suspicious_count,
            'period_days' => $days
        ];
    }
}
