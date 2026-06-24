<?php

class AccessMatrix
{
    private static $db;
    private static $cache = [];

    public function __construct()
    {
        self::$db = Database::getInstance();
    }

    /**
     * Check if user has permission to perform action on resource
     * 
     * @param int $user_id - User ID
     * @param string $resource - Resource name (e.g., 'students', 'courses', 'users')
     * @param string $action - Action (e.g., 'view', 'create', 'edit', 'delete', 'export')
     * @param array $context - Additional context for conditional checks
     */
    public static function hasPermission($user_id, $resource, $action, $context = [])
    {
        // Get user's role
        $stmt = self::$db->prepare("SELECT role FROM users WHERE id = ? AND active = 1");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        $role_id = $user['role'];

        // Check if permission exists in access matrix
        $stmt = self::$db->prepare("
            SELECT id, conditions FROM access_matrix 
            WHERE role_id = ? AND resource = ? AND action = ?
            ORDER BY priority DESC
            LIMIT 1
        ");
        $stmt->execute([$role_id, $resource, $action]);
        $permission = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$permission) {
            return false;
        }

        // Evaluate conditions if any
        if ($permission['conditions']) {
            $conditions = json_decode($permission['conditions'], true);
            return self::evaluateConditions($conditions, $context);
        }

        return true;
    }

    /**
     * Get all permissions for a user
     */
    public static function getUserPermissions($user_id)
    {
        // Check cache first
        if (isset(self::$cache["user_$user_id"])) {
            return self::$cache["user_$user_id"];
        }

        $stmt = self::$db->prepare("
            SELECT u.id, u.role, am.resource, am.action, am.conditions
            FROM users u
            LEFT JOIN access_matrix am ON u.role = am.role_id
            WHERE u.id = ? AND u.active = 1
        ");
        $stmt->execute([$user_id]);
        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format permissions by resource
        $formatted = [];
        foreach ($permissions as $perm) {
            if (!$perm['resource']) continue;
            
            if (!isset($formatted[$perm['resource']])) {
                $formatted[$perm['resource']] = [];
            }
            
            $formatted[$perm['resource']][] = [
                'action' => $perm['action'],
                'conditions' => $perm['conditions'] ? json_decode($perm['conditions'], true) : null
            ];
        }

        // Cache the result
        self::$cache["user_$user_id"] = $formatted;

        return $formatted;
    }

    /**
     * Get all permissions for a role
     */
    public static function getRolePermissions($role_id)
    {
        $stmt = self::$db->prepare("
            SELECT resource, action, conditions, priority
            FROM access_matrix 
            WHERE role_id = ?
            ORDER BY resource, action, priority DESC
        ");
        $stmt->execute([$role_id]);

        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode conditions JSON
        foreach ($permissions as &$perm) {
            if ($perm['conditions']) {
                $perm['conditions'] = json_decode($perm['conditions'], true);
            }
        }

        return $permissions;
    }

    /**
     * Grant permission (create or update)
     */
    public static function grantPermission($role_id, $resource, $action, $conditions = null, $priority = 0)
    {
        $conditions_json = $conditions ? json_encode($conditions) : null;

        $stmt = self::$db->prepare("
            INSERT INTO access_matrix 
            (role_id, resource, action, conditions, priority, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
                conditions = VALUES(conditions),
                priority = VALUES(priority),
                updated_at = NOW()
        ");

        $created_by = SuperAdminAuth::getId() ?? Auth::getId();
        $result = $stmt->execute([$role_id, $resource, $action, $conditions_json, $priority, $created_by]);

        if ($result) {
            self::clearCache($role_id);
            AuditLogger::log(
                'permission_grant',
                'access_matrix',
                $role_id,
                "$resource:$action",
                null,
                ['resource' => $resource, 'action' => $action, 'conditions' => $conditions],
                "Permission granted to role $role_id for $resource:$action"
            );
        }

        return $result;
    }

    /**
     * Revoke permission
     */
    public static function revokePermission($role_id, $resource, $action)
    {
        $stmt = self::$db->prepare("
            DELETE FROM access_matrix 
            WHERE role_id = ? AND resource = ? AND action = ?
        ");
        $result = $stmt->execute([$role_id, $resource, $action]);

        if ($result && $stmt->rowCount() > 0) {
            self::clearCache($role_id);
            AuditLogger::log(
                'permission_revoke',
                'access_matrix',
                $role_id,
                "$resource:$action",
                ['resource' => $resource, 'action' => $action],
                null,
                "Permission revoked from role $role_id for $resource:$action"
            );
        }

        return $result;
    }

    /**
     * Bulk grant permissions to a role
     */
    public static function grantBulkPermissions($role_id, $permissions)
    {
        $created_by = SuperAdminAuth::getId() ?? Auth::getId();
        $count = 0;

        self::$db->beginTransaction();
        try {
            foreach ($permissions as $perm) {
                $conditions_json = isset($perm['conditions']) ? json_encode($perm['conditions']) : null;

                $stmt = self::$db->prepare("
                    INSERT INTO access_matrix 
                    (role_id, resource, action, conditions, priority, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE 
                        conditions = VALUES(conditions),
                        priority = VALUES(priority),
                        updated_at = NOW()
                ");

                $stmt->execute([
                    $role_id,
                    $perm['resource'],
                    $perm['action'],
                    $conditions_json,
                    $perm['priority'] ?? 0,
                    $created_by
                ]);

                $count++;
            }

            self::$db->commit();
            self::clearCache($role_id);

            AuditLogger::logBulkOperation(
                'permission_grant',
                'access_matrix',
                $count,
                "Bulk permissions granted to role $role_id"
            );

            return $count;
        } catch (Exception $e) {
            self::$db->rollBack();
            error_log("AccessMatrix bulk grant error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Duplicate role permissions (clone from one role to another)
     */
    public static function cloneRolePermissions($source_role_id, $target_role_id)
    {
        $stmt = self::$db->prepare("
            SELECT resource, action, conditions, priority
            FROM access_matrix 
            WHERE role_id = ?
        ");
        $stmt->execute([$source_role_id]);
        $source_perms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $created_by = SuperAdminAuth::getId() ?? Auth::getId();
        $count = 0;

        self::$db->beginTransaction();
        try {
            foreach ($source_perms as $perm) {
                $stmt = self::$db->prepare("
                    INSERT INTO access_matrix 
                    (role_id, resource, action, conditions, priority, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");

                $stmt->execute([
                    $target_role_id,
                    $perm['resource'],
                    $perm['action'],
                    $perm['conditions'],
                    $perm['priority'],
                    $created_by
                ]);

                $count++;
            }

            self::$db->commit();
            self::clearCache($target_role_id);

            AuditLogger::log(
                'role_clone',
                'access_matrix',
                $target_role_id,
                "Role permissions cloned",
                ['source_role_id' => $source_role_id],
                ['target_role_id' => $target_role_id],
                "Permissions from role $source_role_id cloned to role $target_role_id"
            );

            return $count;
        } catch (Exception $e) {
            self::$db->rollBack();
            error_log("AccessMatrix clone error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all available resources
     */
    public static function getAvailableResources()
    {
        $stmt = self::$db->prepare("
            SELECT DISTINCT resource 
            FROM access_matrix 
            ORDER BY resource
        ");
        $stmt->execute();
        
        $resources = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Add common resources if not in database
        $default_resources = [
            'users', 'students', 'courses', 'programmes', 'departments',
            'grades', 'timetables', 'events', 'news', 'gallery', 'messages',
            'settings', 'reports', 'analytics', 'admin_logs'
        ];

        $resources = array_unique(array_merge($resources, $default_resources));
        sort($resources);

        return $resources;
    }

    /**
     * Get available actions for a resource
     */
    public static function getAvailableActions($resource)
    {
        $default_actions = ['view', 'create', 'edit', 'delete', 'export', 'manage'];

        $stmt = self::$db->prepare("
            SELECT DISTINCT action 
            FROM access_matrix 
            WHERE resource = ?
        ");
        $stmt->execute([$resource]);

        $db_actions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $actions = array_unique(array_merge($db_actions, $default_actions));
        sort($actions);

        return $actions;
    }

    /**
     * Evaluate conditions against context
     */
    private static function evaluateConditions($conditions, $context)
    {
        if (!$conditions) {
            return true;
        }

        // AND conditions (all must be true)
        if (isset($conditions['AND'])) {
            foreach ($conditions['AND'] as $condition) {
                if (!self::evaluateCondition($condition, $context)) {
                    return false;
                }
            }
            return true;
        }

        // OR conditions (at least one must be true)
        if (isset($conditions['OR'])) {
            foreach ($conditions['OR'] as $condition) {
                if (self::evaluateCondition($condition, $context)) {
                    return true;
                }
            }
            return false;
        }

        // Single condition
        return self::evaluateCondition($conditions, $context);
    }

    /**
     * Evaluate single condition
     */
    private static function evaluateCondition($condition, $context)
    {
        if (!isset($condition['field']) || !isset($condition['operator'])) {
            return false;
        }

        $field = $condition['field'];
        $operator = $condition['operator'];
        $value = $condition['value'] ?? null;

        $context_value = $context[$field] ?? null;

        switch ($operator) {
            case '==':
            case 'equals':
                return $context_value == $value;
            case '!=':
            case 'not_equals':
                return $context_value != $value;
            case '>':
                return $context_value > $value;
            case '<':
                return $context_value < $value;
            case '>=':
                return $context_value >= $value;
            case '<=':
                return $context_value <= $value;
            case 'in':
                return in_array($context_value, (array)$value);
            case 'not_in':
                return !in_array($context_value, (array)$value);
            case 'contains':
                return strpos($context_value, $value) !== false;
            case 'exists':
                return isset($context[$field]);
            default:
                return false;
        }
    }

    /**
     * Clear cache for a role
     */
    private static function clearCache($role_id)
    {
        // Clear user caches for users with this role
        foreach (self::$cache as $key => $value) {
            if (strpos($key, 'user_') === 0) {
                unset(self::$cache[$key]);
            }
        }
    }

    /**
     * Get permission matrix visualization data
     */
    public static function getPermissionMatrix()
    {
        // Get all roles
        $roles = self::$db->query("
            SELECT DISTINCT role as id, role as name FROM users 
            UNION 
            SELECT DISTINCT role_id as id, role_id as name FROM access_matrix
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Get all resources and actions
        $resources = self::getAvailableResources();
        
        $matrix = [];

        foreach ($roles as $role) {
            $matrix[$role['id']] = [];
            
            foreach ($resources as $resource) {
                $actions = self::getAvailableActions($resource);
                $matrix[$role['id']][$resource] = [];

                foreach ($actions as $action) {
                    $stmt = self::$db->prepare("
                        SELECT id FROM access_matrix 
                        WHERE role_id = ? AND resource = ? AND action = ?
                    ");
                    $stmt->execute([$role['id'], $resource, $action]);
                    
                    $matrix[$role['id']][$resource][$action] = $stmt->fetch(PDO::FETCH_ASSOC) ? 1 : 0;
                }
            }
        }

        return $matrix;
    }

    /**
     * Bulk update permission matrix
     */
    public static function bulkUpdateMatrix($matrix_data)
    {
        $created_by = SuperAdminAuth::getId() ?? Auth::getId();
        $changes = 0;

        self::$db->beginTransaction();
        try {
            foreach ($matrix_data as $role_id => $resources) {
                foreach ($resources as $resource => $actions) {
                    foreach ($actions as $action => $granted) {
                        $stmt = self::$db->prepare("
                            SELECT id FROM access_matrix 
                            WHERE role_id = ? AND resource = ? AND action = ?
                        ");
                        $stmt->execute([$role_id, $resource, $action]);
                        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($granted && !$exists) {
                            // Grant permission
                            $stmt = self::$db->prepare("
                                INSERT INTO access_matrix 
                                (role_id, resource, action, created_by, created_at)
                                VALUES (?, ?, ?, ?, NOW())
                            ");
                            $stmt->execute([$role_id, $resource, $action, $created_by]);
                            $changes++;
                        } elseif (!$granted && $exists) {
                            // Revoke permission
                            $stmt = self::$db->prepare("
                                DELETE FROM access_matrix 
                                WHERE role_id = ? AND resource = ? AND action = ?
                            ");
                            $stmt->execute([$role_id, $resource, $action]);
                            $changes++;
                        }
                    }

                    self::clearCache($role_id);
                }
            }

            self::$db->commit();

            AuditLogger::logBulkOperation(
                'matrix_update',
                'access_matrix',
                $changes,
                "Bulk permission matrix updated"
            );

            return $changes;
        } catch (Exception $e) {
            self::$db->rollBack();
            error_log("AccessMatrix bulk update error: " . $e->getMessage());
            return 0;
        }
    }
}
