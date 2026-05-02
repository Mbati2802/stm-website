-- Rename course_interest to program_interest in CRM leads table
-- Run this in the CRM database (stmarys2_crm_college)

ALTER TABLE leads CHANGE COLUMN course_interest program_interest VARCHAR(100);
