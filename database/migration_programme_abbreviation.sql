-- Add abbreviation column to programmes table
ALTER TABLE `programmes` ADD COLUMN `abbreviation` VARCHAR(10) NULL AFTER `name`;

-- Update existing programmes with auto-generated abbreviations
UPDATE `programmes` SET `abbreviation` = UPPER(SUBSTRING(`name`, 1, 4)) WHERE `abbreviation` IS NULL;

-- Make abbreviation column NOT NULL with default
ALTER TABLE `programmes` MODIFY COLUMN `abbreviation` VARCHAR(10) NOT NULL DEFAULT '';
