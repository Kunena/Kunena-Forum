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
	UNIQUE KEY `catid` (`id`),
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
	KEY `id` (`id`),
	KEY `parent` (`parent`),
	KEY `catid` (`catid`),
	KEY `ip` (`ip`),
	KEY `userid` (`userid`)
) AUTO_INCREMENT=1;

ALTER TABLE `#__sb_messages` ADD INDEX ( `time`, `hold` );

CREATE TABLE IF NOT EXISTS `#__sb_messages_text` (
	`mesid` int(11) NOT NULL default '0',
	`message` text NOT NULL,
	UNIQUE KEY `mesid` (`mesid`)
);

CREATE TABLE IF NOT EXISTS `#__sb_moderation` (
	`catid` int(11) NOT NULL default '0',
	`userid` int(11) NOT NULL default '0',
	`future1` tinyint(4) default '0',
	`future2` int(11) default '0',
	PRIMARY KEY	(`catid`,`userid`),
	KEY `catid` (`catid`)
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
	`filelocation` text NOT NULL
);

ALTER TABLE `#__sb_attachments` ADD INDEX ( `mesid` );

CREATE TABLE IF NOT EXISTS `#__sb_smileys` (
	`id` int(4) NOT NULL auto_increment,
	`code` varchar(12) NOT NULL default '',
	`location` varchar(50) NOT NULL default '',
	`greylocation` varchar(60) NOT NULL default '',
	`emoticonbar` tinyint(4) NOT NULL default '0',
	PRIMARY KEY	(`id`)
) AUTO_INCREMENT=1;

TRUNCATE #__sb_smileys;

INSERT INTO `#__sb_smileys` VALUES (1, 'B)', 'cool.png', 'cool-grey.png', 1),
	(8, ';)', 'wink.png', 'wink-grey.png', 1),
	(3, ':)', 'smile.png', 'smile-grey.png', 1),
	(10, ':P', 'tongue.png', 'tongue-grey.png', 1),
	(6, ':laugh:', 'laughing.png', 'laughing-grey.png', 1),
	(17, ':ohmy:', 'shocked.png', 'shocked-grey.png', 1),
	(22, ':sick:', 'sick.png', 'sick-grey.png', 1),
	(14, ':angry:', 'angry.png', 'angry-grey.png', 1),
	(25, ':blink:', 'blink.png', 'blink-grey.png', 1),
	(2, ':(', 'sad.png', 'sad-grey.png', 1),
	(16, ':unsure:', 'unsure.png', 'unsure-grey.png', 1),
	(27, ':kiss:', 'kissing.png', 'kissing-grey.png', 1),
	(29, ':woohoo:', 'w00t.png', 'w00t-grey.png', 1),
	(21, ':lol:', 'grin.png', 'grin-grey.png', 1),
	(23, ':silly:', 'silly.png', 'silly-grey.png', 1),
	(35, ':pinch:', 'pinch.png', 'pinch-grey.png', 1),
	(30, ':side:', 'sideways.png', 'sideways-grey.png', 1),
	(34, ':whistle:', 'whistling.png', 'whistling-grey.png', 1),
	(33, ':evil:', 'devil.png', 'devil-grey.png', 1),
	(31, ':S', 'dizzy.png', 'dizzy-grey.png', 1),
	(26, ':blush:', 'blush.png', 'blush-grey.png', 1),
	(7, ':cheer:', 'cheerful.png', 'cheerful-grey.png', 1),
	(18, ':huh:', 'wassat.png', 'wassat-grey.png', 1),
	(19, ':dry:', 'ermm.png', 'ermm-grey.png', 1),
	(4, ':-)', 'smile.png', 'smile-grey.png', 0),
	(5, ':-(', 'sad.png', 'sad-grey.png', 0),
	(9, ';-)', 'wink.png', 'wink-grey.png', 0),
	(37, ':D', 'laughing.png', 'laughing-grey.png', 0),
	(12, ':X', 'sick.png', 'sick-grey.png', 0),
	(13, ':x', 'sick.png', 'sick-grey.png', 0),
	(15, ':mad:', 'angry.png', 'angry-grey.png', 0),
	(20, ':ermm:', 'ermm.png', 'ermm-grey.png', 0),
	(24, ':y32b4:', 'silly.png', 'silly-grey.png', 0),
	(28, ':rolleyes:', 'blink.png', 'blink-grey.png', 0),
	(32, ':s', 'dizzy.png', 'dizzy-grey.png', 0),
	(36, ':p', 'tongue.png', 'tongue-grey.png', 0);
