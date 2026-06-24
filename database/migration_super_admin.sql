-- Create Super Admin table
CREATE TABLE IF NOT EXISTS `super_admin` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `ip_whitelist` TEXT,
    `two_fa_enabled` BOOLEAN DEFAULT TRUE,
    `two_fa_secret` VARCHAR(255),
    `two_fa_backup_codes` JSON,
    `last_login` DATETIME,
    `last_login_ip` VARCHAR(45),
    `failed_login_attempts` INT DEFAULT 0,
    `last_failed_attempt` DATETIME,
    `locked_until` DATETIME,
    `session_timeout` INT DEFAULT 900,
    `force_password_change` BOOLEAN DEFAULT FALSE,
    `password_changed_at` DATETIME,
    `status` ENUM('active', 'suspended', 'archived') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Audit Logs table
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `user_type` ENUM('admin', 'crm_admin', 'student', 'super_admin') DEFAULT 'admin',
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(100) NOT NULL,
    `entity_id` INT,
    `entity_name` VARCHAR(255),
    `old_values` JSON,
    `new_values` JSON,
    `ip_address` VARCHAR(45),
    `device_fingerprint` VARCHAR(255),
    `user_agent` TEXT,
    `description` TEXT,
    `status` ENUM('success', 'failed', 'suspicious') DEFAULT 'success',
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id_timestamp` (`user_id`, `timestamp`),
    INDEX `idx_entity_type` (`entity_type`),
    INDEX `idx_action` (`action`),
    INDEX `idx_timestamp` (`timestamp`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Access Matrix table
CREATE TABLE IF NOT EXISTS `access_matrix` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `role_id` INT NOT NULL,
    `resource` VARCHAR(100) NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `conditions` JSON,
    `priority` INT DEFAULT 0,
    `created_by` INT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_role_resource_action` (`role_id`, `resource`, `action`),
    INDEX `idx_role_id` (`role_id`),
    INDEX `idx_resource_action` (`resource`, `action`),
    FOREIGN KEY (`role_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create User Sessions table (enhanced)
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `user_type` ENUM('admin', 'crm_admin', 'student', 'super_admin') DEFAULT 'admin',
    `session_token` VARCHAR(255) UNIQUE NOT NULL,
    `ip_address` VARCHAR(45),
    `device_fingerprint` VARCHAR(255),
    `user_agent` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `last_activity` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `expires_at` DATETIME,
    `is_active` BOOLEAN DEFAULT TRUE,
    INDEX `idx_session_token` (`session_token`),
    INDEX `idx_user_id_type` (`user_id`, `user_type`),
    INDEX `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create 2FA OTP table
CREATE TABLE IF NOT EXISTS `two_fa_otp` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `super_admin_id` INT NOT NULL,
    `otp_code` VARCHAR(6),
    `otp_type` ENUM('email', 'sms') DEFAULT 'email',
    `attempts` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME,
    `verified_at` DATETIME,
    `is_used` BOOLEAN DEFAULT FALSE,
    INDEX `idx_super_admin_id` (`super_admin_id`),
    INDEX `idx_expires_at` (`expires_at`),
    FOREIGN KEY (`super_admin_id`) REFERENCES `super_admin` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Suspicious Activity Alerts table
CREATE TABLE IF NOT EXISTS `suspicious_activity_alerts` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `user_type` VARCHAR(20),
    `alert_type` VARCHAR(100),
    `description` TEXT,
    `ip_address` VARCHAR(45),
    `device_fingerprint` VARCHAR(255),
    `severity` ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    `status` ENUM('new', 'reviewed', 'resolved', 'false_alarm') DEFAULT 'new',
    `reviewed_by` INT,
    `reviewed_at` DATETIME,
    `action_taken` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_severity` (`severity`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
