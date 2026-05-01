-- Create grading_systems table for managing different grading schemes
CREATE TABLE IF NOT EXISTS `grading_systems` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `exam_type_id` INT NOT NULL,
  `description` TEXT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_exam_type_id` (`exam_type_id`),
  INDEX `idx_is_default` (`is_default`),
  INDEX `idx_is_active` (`is_active`),
  FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create grade_ranges table for defining grade ranges within grading systems
CREATE TABLE IF NOT EXISTS `grade_ranges` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `grading_system_id` INT NOT NULL,
  `grade_letter` VARCHAR(10) NOT NULL,
  `min_marks` DECIMAL(5,2) NOT NULL,
  `max_marks` DECIMAL(5,2) NOT NULL,
  `remarks` VARCHAR(255) NULL,
  `gpa_value` DECIMAL(3,2) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_grading_system_id` (`grading_system_id`),
  INDEX `idx_grade_letter` (`grade_letter`),
  FOREIGN KEY (`grading_system_id`) REFERENCES `grading_systems`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
