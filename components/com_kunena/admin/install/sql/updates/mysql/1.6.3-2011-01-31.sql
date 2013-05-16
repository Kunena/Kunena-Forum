-- Kunena 1.6.0+ => 1.6.3

ALTER TABLE	`#__kunena_categories`		ADD `accesstype` varchar(20) NOT NULL default 'none' AFTER `moderators`;
ALTER TABLE	`#__kunena_categories`		ADD `access` int(11) NOT NULL default '0' AFTER `accesstype`;
ALTER TABLE	`#__kunena_categories`		ADD KEY `category_access` (accesstype,access);

ALTER TABLE	`#__kunena_whoisonline`		DROP INDEX `userip`;
ALTER TABLE	`#__kunena_whoisonline`		MODIFY `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE	`#__kunena_whoisonline`		MODIFY `userid` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE	`#__kunena_whoisonline`		ADD INDEX `userid_userip` ( `userid` , `userip` );
ALTER TABLE	`#__kunena_whoisonline`		ADD INDEX `func` ( `func` );
ALTER TABLE	`#__kunena_whoisonline`		ADD INDEX `time` ( `time` );
