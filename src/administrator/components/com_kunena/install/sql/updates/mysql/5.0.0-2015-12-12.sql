ALTER TABLE	`#__kunena_categories`	ADD `allow_ratings` tinyint(4) 	 NOT NULL default '0' AFTER `last_post_time`;
CREATE TABLE IF NOT EXISTS `#__kunena_rate` (
	`topicid` int(11) NULL,
	`userid` int(11) NULL,
	`rate` mediumint(8) unsigned NOT NULL default '0',
	`time`  DATETIME NOT NULL,
	UNIQUE KEY  `topicid` (topicid,userid) ) DEFAULT CHARACTER SET utf8;
