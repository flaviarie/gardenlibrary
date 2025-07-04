-- Add status field to users table for account suspension/activation
-- This migration adds a status field to track whether users are active or suspended

-- Add status column to users table
ALTER TABLE `users` 
ADD COLUMN `status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active' 
AFTER `role`;

-- Update existing users to have active status
UPDATE `users` SET `status` = 'active' WHERE `status` IS NULL;

-- Add index on status for better performance
ALTER TABLE `users` ADD INDEX `idx_user_status` (`status`);

-- Add comment to document the field
ALTER TABLE `users` 
MODIFY COLUMN `status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active' 
COMMENT 'User account status - active or suspended';
