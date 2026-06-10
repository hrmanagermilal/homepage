-- ===============================================
-- Migration: Add hero_image column to departments table
-- ===============================================

-- Add hero_image column to departments table
ALTER TABLE departments 
ADD COLUMN hero_image VARCHAR(500) NULL AFTER heading_title;

-- Verify the column was added
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'departments' 
  AND COLUMN_NAME = 'hero_image';
