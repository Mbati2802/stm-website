-- Add admission-related fields to student_accounts table
ALTER TABLE `student_accounts` 
ADD COLUMN `national_id` VARCHAR(50) NULL AFTER `admission_number`,
ADD COLUMN `gender` ENUM('Male', 'Female') NULL AFTER `national_id`,
ADD COLUMN `date_of_birth` DATE NULL AFTER `gender`,
ADD COLUMN `phone` VARCHAR(20) NULL AFTER `date_of_birth`,
ADD COLUMN `county` VARCHAR(100) NULL AFTER `phone`,
ADD COLUMN `sub_county` VARCHAR(100) NULL AFTER `county`,
ADD COLUMN `guardian_name` VARCHAR(255) NULL AFTER `sub_county`,
ADD COLUMN `guardian_relationship` ENUM('Parent', 'Guardian', 'Sponsor') NULL AFTER `guardian_name`,
ADD COLUMN `guardian_phone` VARCHAR(20) NULL AFTER `guardian_relationship`,
ADD COLUMN `guardian_email` VARCHAR(255) NULL AFTER `guardian_phone`,
ADD COLUMN `previous_school` VARCHAR(255) NULL AFTER `guardian_email`,
ADD COLUMN `kcse_year` YEAR NULL AFTER `previous_school`,
ADD COLUMN `kcse_grade` VARCHAR(5) NULL AFTER `kcse_year`,
ADD COLUMN `kcse_index` VARCHAR(50) NULL AFTER `kcse_grade`,
ADD COLUMN `programme_id` INT NULL AFTER `kcse_index`,
ADD COLUMN `preferred_intake` VARCHAR(50) NULL AFTER `programme_id`,
ADD COLUMN `disability_status` ENUM('None', 'Physical', 'Visual', 'Hearing', 'Other') DEFAULT 'None' NULL AFTER `preferred_intake`,
ADD COLUMN `referral_source` VARCHAR(255) NULL AFTER `disability_status`,
ADD COLUMN `additional_notes` TEXT NULL AFTER `referral_source`;

-- Add foreign key for programme_id
ALTER TABLE `student_accounts` 
ADD CONSTRAINT `fk_student_accounts_programme` 
FOREIGN KEY (`programme_id`) REFERENCES `programmes`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;
