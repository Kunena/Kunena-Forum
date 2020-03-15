ALTER TABLE `#__kunena_announcement`
  ADD `publish_up` datetime NOT NULL default '0000-00-00 00:00:00'
  AFTER `published`;
ALTER TABLE `#__kunena_announcement`
  ADD `publish_down` datetime NOT NULL default '0000-00-00 00:00:00'
  AFTER `publish_up`;
