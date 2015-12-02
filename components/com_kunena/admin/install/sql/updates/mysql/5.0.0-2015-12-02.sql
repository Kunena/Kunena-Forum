ALTER TABLE `#__kunena_users` CHANGE COLUMN `msn` `microsoft` varchar(50) NULL;
ALTER TABLE `#__kunena_users` CHANGE COLUMN `gtalk` `google` varchar(50) NULL;
ALTER TABLE `#__kunena_users`
				ADD `telegram` VARCHAR( 50 ) NULL AFTER `microsoft` ,
				ADD `vk` VARCHAR( 50 ) NULL AFTER `telegram` ,
				ADD `instagram` VARCHAR( 50 ) NULL AFTER `digg` ,
				ADD `qq` VARCHAR( 50 ) NULL AFTER `instagram` ,
				ADD `qzone` VARCHAR( 50 ) NULL AFTER `qq` ,
				ADD `weibo` VARCHAR( 50 ) NULL AFTER `qzone` ,
				ADD `wechat` VARCHAR( 50 ) NULL AFTER `weibo` ,
				ADD `apple` VARCHAR( 50 ) NULL AFTER `wechat` ;
