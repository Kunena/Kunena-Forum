CREATE TABLE IF NOT EXISTS `#__sb_categories` (
	`id` int(11) NOT NULL auto_increment,
	`parent` int(11) default '0',
	`name` tinytext,
	`cat_emoticon` tinyint(4) NOT NULL default '0',
	`locked` tinyint(4) NOT NULL default '0',
	`alert_admin` tinyint(4) NOT NULL default '0',
	`moderated` tinyint(4) NOT NULL default '0',
	`moderators` varchar(15) default NULL,
	`pub_access` tinyint(4) default '1',
	`pub_recurse` tinyint(4) default '1',
	`admin_access` tinyint(4) default '0',
	`admin_recurse` tinyint(4) default '1',
	`ordering` tinyint(4) NOT NULL default '0',
	`future2` int(11) default '0',
	`published` tinyint(4) NOT NULL default '0',
	`checked_out` tinyint(4) NOT NULL default '0',
	`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`review` tinyint(4) NOT NULL default '0',
	`hits` int(11) NOT NULL default '0',
	`description` text NOT NULL,
	PRIMARY KEY `id` (`id`),
	INDEX `published_pubaccess_id` ( `published` , `pub_access` , `id` ),
	KEY `catparent` (`parent`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__sb_messages` (
	`id` int(11) NOT NULL auto_increment,
	`parent` int(11) default '0',
	`thread` int(11) default '0',
	`catid` int(11) NOT NULL default '0',
	`name` tinytext,
	`userid` int(11) NOT NULL default '0',
	`email` tinytext,
	`subject` tinytext,
	`time` int(11) NOT NULL default '0',
	`ip` varchar(15) default NULL,
	`topic_emoticon` int(11) NOT NULL default '0',
	`locked` tinyint(4) NOT NULL default '0',
	`hold` tinyint(4) NOT NULL default '0',
	`ordering` int(11) default '0',
	`hits` int(11) default '0',
	`moved` tinyint(4) default '0',
	PRIMARY KEY	(`id`),
	KEY `thread` (`thread`),
	KEY `parent` (`parent`),
	KEY `catid` (`catid`),
	KEY `ip` (`ip`),
	KEY `userid` (`userid`),
	INDEX `hold_time` ( `hold` , `time` )
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__sb_messages_text` (
	`mesid` int(11) NOT NULL default '0',
	`message` text NOT NULL,
	PRIMARY KEY `mesid` (`mesid`)
);

CREATE TABLE IF NOT EXISTS `#__sb_moderation` (
	`catid` int(11) NOT NULL default '0',
	`userid` int(11) NOT NULL default '0',
	`future1` tinyint(4) default '0',
	`future2` int(11) default '0',
	PRIMARY KEY	(`catid`,`userid`)
);

CREATE TABLE IF NOT EXISTS `#__sb_subscriptions` (
	`thread` int(11) NOT NULL default '0',
	`userid` int(11) NOT NULL default '0',
	`future1` int(11) default '0',
	KEY `thread` (`thread`),
	KEY `userid` (`userid`)
);

CREATE TABLE IF NOT EXISTS `#__sb_users` (
	`userid` int(11) NOT NULL default '0',
	`view` varchar(8) NOT NULL default 'flat',
	`signature` text default NULL,
	`moderator` int(11) default '0',
	`ordering` int(11) default '0',
	`posts` int(11) default '0',
	`avatar` varchar(50) default NULL,
	`karma` integer(11) default '0',
	`karma_time` integer(11) default '0',
	PRIMARY KEY	(`userid`)
);

CREATE TABLE IF NOT EXISTS `#__sb_sessions` (
	`userid` int(11) NOT NULL default '0',
	`allowed` text,
	`lasttime` int(11) NOT NULL default '0',
	`readtopics` text,
	PRIMARY KEY	(`userid`)
);

CREATE TABLE IF NOT EXISTS `#__sb_attachments` (
	`mesid` int( 11 ) NOT NULL ,
	`filelocation` text NOT NULL,
	KEY `mesid` (`mesid`)
);

CREATE TABLE IF NOT EXISTS `#__sb_config` (
	`jbkey` tinytext NOT NULL,
	`jbvalue` tinytext NOT NULL
);

CREATE TABLE IF NOT EXISTS `#__sb_smileys` (
	`id` int(4) NOT NULL auto_increment,
	`code` varchar(12) NOT NULL default '',
	`location` varchar(50) NOT NULL default '',
	`greylocation` varchar(60) NOT NULL default '',
	`emoticonbar` tinyint(4) NOT NULL default '0',
	PRIMARY KEY	(`id`)
) AUTO_INCREMENT=1;
