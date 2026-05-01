-- Add suspended status to student_accounts
ALTER TABLE `student_accounts` ADD COLUMN `is_suspended` TINYINT(1) DEFAULT 0 NULL AFTER `additional_notes`;
