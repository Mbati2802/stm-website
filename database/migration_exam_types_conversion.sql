-- Migration: Add marks conversion support to exam_types
-- Allows setting maximum marks per exam type and display mode

-- Add max_marks column to exam_types (default 100 for backward compatibility)
ALTER TABLE `exam_types` 
ADD COLUMN `max_marks` DECIMAL(5,2) NOT NULL DEFAULT 100.00 AFTER `parent_exam_ids`,
ADD COLUMN `display_mode` ENUM('percentage','converted','both') DEFAULT 'converted' AFTER `max_marks`,
ADD INDEX `idx_max_marks` (`max_marks`);

-- Add marks_percentage column to course_grades to store original percentage
ALTER TABLE `course_grades`
ADD COLUMN `marks_percentage` DECIMAL(5,2) NULL AFTER `marks`,
ADD INDEX `idx_marks_percentage` (`marks_percentage`);

-- Update existing exam types with sensible defaults
-- These can be adjusted per institution needs
UPDATE `exam_types` SET `max_marks` = 100.00 WHERE `type` = 'single' AND `max_marks` = 100.00;

-- Note: After migration, admin should configure:
-- 1. CW (Coursework) = 10-30 marks
-- 2. EX (Exam) = 40-70 marks  
-- 3. AS (Assignment) = 10-20 marks
-- 4. TOTAL (Consolidated) = 100 marks (sum of above)
