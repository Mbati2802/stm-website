-- Add programme_id to student_accounts for admission number generation
ALTER TABLE `student_accounts` ADD COLUMN `programme_id` INT NULL AFTER `admission_number`;

-- Add foreign key constraint
ALTER TABLE `student_accounts` 
ADD CONSTRAINT fk_student_accounts_programme 
FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE SET NULL;

-- Add index for performance
ALTER TABLE `student_accounts` ADD INDEX idx_student_accounts_programme (programme_id);
