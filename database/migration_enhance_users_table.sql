-- Alter users table to support enhanced audit tracking
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `device_fingerprint` VARCHAR(255) AFTER `password`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `last_login_ip` VARCHAR(45) AFTER `updated_at`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `failed_login_attempts` INT DEFAULT 0 AFTER `last_login_ip`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `last_failed_attempt` DATETIME AFTER `failed_login_attempts`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `locked_until` DATETIME AFTER `last_failed_attempt`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `password_changed_at` DATETIME AFTER `active`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `force_password_change` BOOLEAN DEFAULT FALSE AFTER `password_changed_at`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `created_by` INT AFTER `force_password_change`;

-- Create index for faster lookups
CREATE INDEX IF NOT EXISTS `idx_users_role` ON `users` (`role`);
CREATE INDEX IF NOT EXISTS `idx_users_active` ON `users` (`active`);
CREATE INDEX IF NOT EXISTS `idx_users_created_by` ON `users` (`created_by`);
