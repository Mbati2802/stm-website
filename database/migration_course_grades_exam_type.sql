-- Add exam_type_id to course_grades table to link marks to exam types
ALTER TABLE `course_grades` 
ADD COLUMN `exam_type_id` INT NULL AFTER `grading_system_id`,
ADD COLUMN `academic_session_id` INT NULL AFTER `exam_type_id`,
ADD COLUMN `term_id` INT NULL AFTER `academic_session_id`,
ADD INDEX `idx_exam_type_id` (`exam_type_id`),
ADD INDEX `idx_academic_session_id` (`academic_session_id`),
ADD INDEX `idx_term_id` (`term_id`),
ADD FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types`(`id`) ON DELETE SET NULL,
ADD FOREIGN KEY (`academic_session_id`) REFERENCES `academic_years`(`id`) ON DELETE SET NULL,
ADD FOREIGN KEY (`term_id`) REFERENCES `terms`(`id`) ON DELETE SET NULL;
