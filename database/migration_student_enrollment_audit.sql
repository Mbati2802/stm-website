-- Migration: create student_enrollment_audit table
CREATE TABLE IF NOT EXISTS `student_enrollment_audit` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `enrollment_id` INT NULL,
  `action` ENUM('created','updated','deleted') NOT NULL,
  `changed_by` INT NULL,
  `old_data` JSON NULL,
  `new_data` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_student` (`student_id`),
  INDEX `idx_enrollment` (`enrollment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
