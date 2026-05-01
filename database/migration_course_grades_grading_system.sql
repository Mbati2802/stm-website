-- Add grading_system_id column to course_grades table to use new grading system
ALTER TABLE `course_grades` 
ADD COLUMN `grading_system_id` INT NULL AFTER `grading_scheme_id`,
ADD INDEX `idx_grading_system_id` (`grading_system_id`),
ADD FOREIGN KEY (`grading_system_id`) REFERENCES `grading_systems`(`id`) ON DELETE SET NULL;
