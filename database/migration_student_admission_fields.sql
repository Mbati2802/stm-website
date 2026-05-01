-- Add admission-related fields to student_accounts table
-- Note: programme_id column already exists, so we skip adding it

ALTER TABLE `student_accounts` ADD COLUMN `national_id` VARCHAR(50) NULL AFTER `admission_number`;
ALTER TABLE `student_accounts` ADD COLUMN `gender` ENUM('Male', 'Female') NULL AFTER `national_id`;
ALTER TABLE `student_accounts` ADD COLUMN `date_of_birth` DATE NULL AFTER `gender`;
ALTER TABLE `student_accounts` ADD COLUMN `phone` VARCHAR(20) NULL AFTER `date_of_birth`;
ALTER TABLE `student_accounts` ADD COLUMN `county` VARCHAR(100) NULL AFTER `phone`;
ALTER TABLE `student_accounts` ADD COLUMN `sub_county` VARCHAR(100) NULL AFTER `county`;
ALTER TABLE `student_accounts` ADD COLUMN `guardian_name` VARCHAR(255) NULL AFTER `sub_county`;
ALTER TABLE `student_accounts` ADD COLUMN `guardian_relationship` ENUM('Parent', 'Guardian', 'Sponsor') NULL AFTER `guardian_name`;
ALTER TABLE `student_accounts` ADD COLUMN `guardian_phone` VARCHAR(20) NULL AFTER `guardian_relationship`;
ALTER TABLE `student_accounts` ADD COLUMN `guardian_email` VARCHAR(255) NULL AFTER `guardian_phone`;
ALTER TABLE `student_accounts` ADD COLUMN `previous_school` VARCHAR(255) NULL AFTER `guardian_email`;
ALTER TABLE `student_accounts` ADD COLUMN `kcse_year` YEAR NULL AFTER `previous_school`;
ALTER TABLE `student_accounts` ADD COLUMN `kcse_grade` VARCHAR(5) NULL AFTER `kcse_year`;
ALTER TABLE `student_accounts` ADD COLUMN `kcse_index` VARCHAR(50) NULL AFTER `kcse_grade`;
ALTER TABLE `student_accounts` ADD COLUMN `preferred_intake` VARCHAR(50) NULL AFTER `kcse_index`;
ALTER TABLE `student_accounts` ADD COLUMN `disability_status` ENUM('None', 'Physical', 'Visual', 'Hearing', 'Other') DEFAULT 'None' NULL AFTER `preferred_intake`;
ALTER TABLE `student_accounts` ADD COLUMN `referral_source` VARCHAR(255) NULL AFTER `disability_status`;
ALTER TABLE `student_accounts` ADD COLUMN `additional_notes` TEXT NULL AFTER `referral_source`;

-- Foreign key constraint already exists, skipping

