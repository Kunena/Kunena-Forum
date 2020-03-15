-- Kunena 2.0.0

ALTER TABLE `#__kunena_categories`
  MODIFY `accesstype` varchar(20) NOT NULL default 'joomla.level'
  AFTER `locked`;
UPDATE `#__kunena_categories`
SET accesstype = 'joomla.group'
WHERE accesstype = 'none';
