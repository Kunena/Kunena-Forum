ALTER TABLE `#__kunena_users`
  ADD `socialshare` tinyint(1) NOT NULL default '1'
  AFTER `ip`