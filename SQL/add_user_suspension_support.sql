-- Add account status management columns to users table
-- This allows administrators to properly suspend accounts

-- Add status column with default 'active'
ALTER TABLE `users` 
ADD COLUMN `status` ENUM('active', 'suspended', 'inactive') DEFAULT 'active' 
AFTER `role`;

-- Add suspension reason column
ALTER TABLE `users` 
ADD COLUMN `suspension_reason` TEXT NULL 
AFTER `status`;

-- Add suspended date column for tracking
ALTER TABLE `users` 
ADD COLUMN `suspended_at` TIMESTAMP NULL 
AFTER `suspension_reason`;

-- Update existing users to have active status
UPDATE `users` SET `status` = 'active' WHERE `status` IS NULL;

-- Example: To suspend a user account
-- UPDATE users SET status = 'suspended', suspension_reason = 'Books overdue for more than 30 days', suspended_at = NOW() WHERE user_id = ?;

-- Example: To reactivate a user account  
-- UPDATE users SET status = 'active', suspension_reason = NULL, suspended_at = NULL WHERE user_id = ?;
