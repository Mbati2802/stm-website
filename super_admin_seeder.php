#!/usr/bin/env php
<?php
/**
 * Super Admin Setup Script
 * 
 * Usage: php super_admin_seeder.php
 * 
 * This script sets up the super admin system:
 * 1. Runs database migrations
 * 2. Creates the initial super admin user
 * 3. Sets up access matrix permissions
 * 4. Initializes system settings
 */

require_once __DIR__ . '/bootstrap.php';

$config = require __DIR__ . '/config/config.php';

// Connect to database
try {
    $db = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']}",
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n╔═══════════════════════════════════════════════════════════╗\n";
echo "║         SUPER ADMIN SYSTEM INITIALIZATION                  ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

// Step 1: Run migrations
echo "📦 Step 1: Running database migrations...\n";

$migrations = [
    'migration_super_admin.sql',
    'migration_enhance_users_table.sql'
];

foreach ($migrations as $migration) {
    $migration_file = __DIR__ . "/database/$migration";
    
    if (!file_exists($migration_file)) {
        echo "⚠️  Migration file not found: $migration_file\n";
        continue;
    }
    
    $sql = file_get_contents($migration_file);
    
    try {
        // Execute each statement separately
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $db->exec($statement);
            }
        }
        
        echo "✅ Executed: $migration\n";
    } catch (Exception $e) {
        echo "❌ Error executing $migration: " . $e->getMessage() . "\n";
    }
}

// Step 2: Create super admin user
echo "\n👤 Step 2: Setting up Super Admin user...\n";

// Check if super admin already exists
$stmt = $db->prepare("SELECT COUNT(*) FROM super_admin");
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count > 0) {
    echo "⚠️  Super admin user already exists.\n";
    echo "Do you want to reset it? (y/n): ";
    $input = trim(fgets(STDIN));
    
    if (strtolower($input) !== 'y') {
        echo "Skipping super admin creation.\n";
    } else {
        $db->prepare("DELETE FROM super_admin")->execute();
        $db->prepare("DELETE FROM two_fa_otp")->execute();
        echo "✅ Existing super admin records deleted.\n";
    }
}

if ($count === 0 || (isset($input) && strtolower($input) === 'y')) {
    echo "\n📋 Enter Super Admin Details:\n";
    
    echo "Full Name: ";
    $name = trim(fgets(STDIN));
    
    echo "Email Address: ";
    $email = trim(fgets(STDIN));
    
    echo "Password: ";
    $password = trim(fgets(STDIN));
    
    echo "Confirm Password: ";
    $password_confirm = trim(fgets(STDIN));
    
    if ($password !== $password_confirm) {
        echo "❌ Passwords do not match!\n";
        exit(1);
    }
    
    if (strlen($password) < 12) {
        echo "❌ Password must be at least 12 characters long!\n";
        exit(1);
    }
    
    // Create super admin
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    try {
        $stmt = $db->prepare("
            INSERT INTO super_admin (name, email, password_hash, two_fa_enabled, created_at)
            VALUES (?, ?, ?, 1, NOW())
        ");
        $stmt->execute([$name, $email, $password_hash]);
        
        $super_admin_id = $db->lastInsertId();
        echo "✅ Super admin user created successfully!\n";
        echo "   ID: $super_admin_id\n";
        echo "   Email: $email\n";
    } catch (Exception $e) {
        echo "❌ Failed to create super admin: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Step 3: Set up access matrix
echo "\n🔑 Step 3: Setting up Access Matrix...\n";

$default_permissions = [
    'super_admin' => [
        'users' => ['view', 'create', 'edit', 'delete', 'export'],
        'students' => ['view', 'create', 'edit', 'delete', 'export'],
        'courses' => ['view', 'create', 'edit', 'delete'],
        'programmes' => ['view', 'create', 'edit', 'delete'],
        'grades' => ['view', 'create', 'edit', 'delete', 'export'],
        'reports' => ['view', 'export'],
        'settings' => ['view', 'edit'],
        'admin_logs' => ['view', 'export'],
    ],
    'junior_admin' => [
        'users' => ['view', 'create', 'edit'],
        'students' => ['view', 'create', 'edit', 'export'],
        'courses' => ['view', 'create', 'edit'],
        'programmes' => ['view', 'edit'],
        'grades' => ['view', 'create', 'edit', 'export'],
    ],
    'editor' => [
        'courses' => ['view', 'create', 'edit'],
        'programmes' => ['view', 'edit'],
    ],
    'viewer' => [
        'users' => ['view'],
        'students' => ['view'],
        'courses' => ['view'],
        'programmes' => ['view'],
        'grades' => ['view'],
    ]
];

$permissions_count = 0;

foreach ($default_permissions as $role => $resources) {
    // Get role ID from users table
    $stmt = $db->prepare("SELECT id FROM users WHERE role = ? LIMIT 1");
    $stmt->execute([$role]);
    $role_result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$role_result && $role !== 'super_admin') {
        continue; // Skip if role doesn't exist in users table
    }
    
    $role_id = $role_result['id'] ?? null;
    
    foreach ($resources as $resource => $actions) {
        foreach ($actions as $action) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO access_matrix (role_id, resource, action, created_by, created_at)
                    VALUES (?, ?, ?, 1, NOW())
                    ON DUPLICATE KEY UPDATE role_id = VALUES(role_id)
                ");
                $stmt->execute([$role_id, $resource, $action]);
                $permissions_count++;
            } catch (Exception $e) {
                // Skip if fails
            }
        }
    }
}

echo "✅ Access matrix initialized with $permissions_count permissions\n";

// Step 4: Initialize system settings
echo "\n⚙️  Step 4: Initializing system settings...\n";

$settings = [
    'maintenance_mode' => '0',
    'portal_lockdown' => '0',
    'super_admin_initialized' => '1',
    'initialization_date' => date('Y-m-d H:i:s')
];

$settings_count = 0;

foreach ($settings as $key => $value) {
    try {
        $stmt = $db->prepare("
            INSERT INTO settings (key, value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE value = VALUES(value)
        ");
        $stmt->execute([$key, $value]);
        $settings_count++;
    } catch (Exception $e) {
        // Continue
    }
}

echo "✅ System settings initialized\n";

// Final Summary
echo "\n╔═══════════════════════════════════════════════════════════╗\n";
echo "║                   ✅ SETUP COMPLETE!                       ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

echo "📍 Next Steps:\n\n";
echo "1. Access the Super Admin Panel:\n";
echo "   🌐 URL: https://yourdomain.com/super-admin/login\n\n";

echo "2. Login Credentials:\n";
echo "   📧 Email: $email\n";
echo "   🔐 Password: (the password you entered)\n\n";

echo "3. Security Reminders:\n";
echo "   ✓ Keep your password secure and change it regularly\n";
echo "   ✓ Enable 2FA with a strong authenticator\n";
echo "   ✓ Whitelist your IP address for extra security\n";
echo "   ✓ Review audit logs regularly\n";
echo "   ✓ Monitor suspicious activity alerts\n\n";

echo "4. Quick Actions:\n";
echo "   • Create admin users: /super-admin/users\n";
echo "   • Manage permissions: /super-admin/access-matrix\n";
echo "   • View audit logs: /super-admin/audit-logs\n";
echo "   • Emergency controls: /super-admin/emergency-mode\n\n";

echo "📚 Documentation:\n";
echo "   For more info, check the database migrations and controller code\n\n";

exit(0);
