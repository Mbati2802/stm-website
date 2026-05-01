-- Add abbreviation column to programmes table
ALTER TABLE `programmes` ADD COLUMN `abbreviation` VARCHAR(10) NULL AFTER `name`;

-- Run PHP script to update existing programmes with correct abbreviations
-- Command: php database/update_programme_abbreviations.php

-- Make abbreviation column NOT NULL with default
ALTER TABLE `programmes` MODIFY COLUMN `abbreviation` VARCHAR(10) NOT NULL DEFAULT '';
