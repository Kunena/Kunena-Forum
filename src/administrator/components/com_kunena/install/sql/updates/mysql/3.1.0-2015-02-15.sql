ALTER TABLE `#__kunena_users`
  ADD `canSubscribe` tinyint(1) NOT NULL DEFAULT '1'
  AFTER `showOnline`;
ALTER TABLE `#__kunena_users`
  ADD `userListtime` int(11) NOT NULL DEFAULT '-2'
  AFTER `canSubscribe`;
