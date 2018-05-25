-- Add real filename and comment fields to attachments.

ALTER TABLE `#__kunena_attachments`
  ADD `protected` TINYINT(4) NOT NULL DEFAULT '0'
  AFTER `userid`;
ALTER TABLE `#__kunena_attachments`
  ADD `filename_real` VARCHAR(255) NOT NULL DEFAULT ''
COMMENT 'Filename for downloads.';
ALTER TABLE `#__kunena_attachments`
  ADD `caption` VARCHAR(255) NOT NULL DEFAULT 'Caption text.';
UPDATE `#__kunena_attachments`
SET `filename_real` = `filename`
WHERE `filename_real` = '';
ALTER TABLE `#__kunena_attachments`
  ADD INDEX `filename_real` (`filename_real`);
