-- Kunena 2.0.1 => 2.0.2

ALTER TABLE `#__kunena_users`
  MODIFY `view` varchar(8) NOT NULL default ''
  AFTER `userid`;
UPDATE `#__kunena_users`
SET view = '';
