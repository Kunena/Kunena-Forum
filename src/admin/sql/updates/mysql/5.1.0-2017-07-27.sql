ALTER TABLE `#__kunena_attachments`
  ADD `inline` tinyint(4) NOT NULL default '0'
  AFTER `caption`;
