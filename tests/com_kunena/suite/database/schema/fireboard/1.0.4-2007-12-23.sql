-- FireBoard 1.0.4

CREATE TABLE IF NOT EXISTS `#__fb_announcement` (
	`id` int(3) NOT NULL auto_increment,
	`title` tinytext,
	`sdescription` text NOT NULL,
	`description` text NOT NULL,
	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	`published` tinyint(1) NOT NULL default '0',
	`ordering` tinyint(4) NOT NULL default '0',
	`showdate` tinyint(1) NOT NULL default '1',
	PRIMARY KEY	(`id`)
);

CREATE TABLE IF NOT EXISTS `#__fb_attachments` (
	`mesid` int(11) NOT NULL default '0',
	`filelocation` text NOT NULL,
	KEY `mesid` (`mesid`)
);

CREATE TABLE IF NOT EXISTS `#__fb_categories` (
	`id` int(11) NOT NULL auto_increment,
	`parent` int(11) default '0',
	`name` tinytext,
	`cat_emoticon` tinyint(4) NOT NULL default '0',
	`locked` tinyint(4) NOT NULL default '0',
	`alert_admin` tinyint(4) NOT NULL default '0',
	`moderated` tinyint(4) NOT NULL default '1',
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
	`headerdesc` text NOT NULL,
	`class_sfx` varchar(20) NOT NULL,
	`id_last_msg` int(10) NOT NULL default '0',
	`numTopics` mediumint(8) NOT NULL default '0',
	`numPosts` mediumint(8) NOT NULL default '0',
	`time_last_msg` int(11) default NULL,
	PRIMARY KEY	(`id`),
	KEY `parent` (`parent`),
	KEY `published_pubaccess_id` (`published`,`pub_access`,`id`),
	KEY `msg_id` (`id_last_msg`)
);

CREATE TABLE IF NOT EXISTS `#__fb_favorites` (
	`thread` int(11) NOT NULL default '0',
	`userid` int(11) NOT NULL default '0',
	KEY `thread` (`thread`),
	KEY `userid` (`userid`)
);

CREATE TABLE IF NOT EXISTS `#__fb_groups` (
	`id` int(4) NOT NULL auto_increment,
	`title` varchar(255) default NULL,
	PRIMARY KEY	(`id`)
);

INSERT INTO `#__fb_groups`(`id`,`title`) values (1,'Registered User');

CREATE TABLE IF NOT EXISTS `#__fb_messages` (
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
	`modified_by` int(7) default NULL,
	`modified_time` int(11) default NULL,
	`modified_reason` tinytext,
	PRIMARY KEY	(`id`),
	KEY `thread` (`thread`),
	KEY `parent` (`parent`),
	KEY `catid` (`catid`),
	KEY `ip` (`ip`),
	KEY `userid` (`userid`),
	KEY `time` (`time`),
	KEY `locked` (`locked`),
	KEY `hold_time` (`hold`,`time`)
);

CREATE TABLE IF NOT EXISTS `#__fb_messages_text` (
	`mesid` int(11) NOT NULL default '0',
	`message` text NOT NULL,
	PRIMARY KEY	(`mesid`)
);

CREATE TABLE IF NOT EXISTS `#__fb_moderation` (
	`catid` int(11) NOT NULL default '0',
	`userid` int(11) NOT NULL default '0',
	`future1` tinyint(4) default '0',
	`future2` int(11) default '0',
	PRIMARY KEY	(`catid`,`userid`)
);

CREATE TABLE IF NOT EXISTS `#__fb_sessions` (
	`userid` int(11) NOT NULL default '0',
	`allowed` text,
	`lasttime` int(11) NOT NULL default '0',
	`readtopics` text,
	`currvisit` int(11) NOT NULL default '0',
	PRIMARY KEY	(`userid`)
);

CREATE TABLE	IF NOT EXISTS `#__fb_smileys` (
	`id` int(4) NOT NULL auto_increment,
	`code` varchar(12) NOT NULL default '',
	`location` varchar(50) NOT NULL default '',
	`greylocation` varchar(60) NOT NULL default '',
	`emoticonbar` tinyint(4) NOT NULL default '0',
	PRIMARY KEY	(`id`)
);

REPLACE INTO `#__fb_smileys`(`id`,`code`,`location`,`greylocation`,`emoticonbar`) values 
	(1,'B)','cool.png','cool-grey.png',1),
	(2,':(','sad.png','sad-grey.png',1),
	(3,':)','smile.png','smile-grey.png',1),
	(4,':-)','smile.png','smile-grey.png',0),
	(5,':-(','sad.png','sad-grey.png',0),
	(6,':laugh:','laughing.png','laughing-grey.png',1),
	(7,':cheer:','cheerful.png','cheerful-grey.png',1),
	(8,';)','wink.png','wink-grey.png',1),
	(9,';-)','wink.png','wink-grey.png',0),
	(10,':P','tongue.png','tongue-grey.png',1),
	(12,':X','sick.png','sick-grey.png',0),
	(13,':x','sick.png','sick-grey.png',0),
	(14,':angry:','angry.png','angry-grey.png',1),
	(15,':mad:','angry.png','angry-grey.png',0),
	(16,':unsure:','unsure.png','unsure-grey.png',1),
	(17,':ohmy:','shocked.png','shocked-grey.png',1),
	(18,':huh:','wassat.png','wassat-grey.png',1),
	(19,':dry:','ermm.png','ermm-grey.png',1),
	(20,':ermm:','ermm.png','ermm-grey.png',0),
	(21,':lol:','grin.png','grin-grey.png',1),
	(22,':sick:','sick.png','sick-grey.png',1),
	(23,':silly:','silly.png','silly-grey.png',1),
	(24,':y32b4:','silly.png','silly-grey.png',0),
	(25,':blink:','blink.png','blink-grey.png',1),
	(26,':blush:','blush.png','blush-grey.png',1),
	(27,':kiss:','kissing.png','kissing-grey.png',1),
	(28,':rolleyes:','blink.png','blink-grey.png',0),
	(29,':woohoo:','w00t.png','w00t-grey.png',1),
	(30,':side:','sideways.png','sideways-grey.png',1),
	(31,':S','dizzy.png','dizzy-grey.png',1),
	(32,':s','dizzy.png','dizzy-grey.png',0),
	(33,':evil:','devil.png','devil-grey.png',1),
	(34,':whistle:','whistling.png','whistling-grey.png',1),
	(35,':pinch:','pinch.png','pinch-grey.png',1),
	(36,':p','tongue.png','tongue-grey.png',0),
	(37,':D','laughing.png','laughing-grey.png',0);

CREATE TABLE	IF NOT EXISTS `#__fb_subscriptions` (
	`thread` int(11) NOT NULL default '0',
	`userid` int(11) NOT NULL default '0',
	`future1` int(11) default '0',
	KEY `thread` (`thread`),
	KEY `userid` (`userid`)
);

CREATE TABLE	IF NOT EXISTS `#__fb_users` (
	`userid` int(11) NOT NULL default '0',
	`view` varchar(8) NOT NULL default 'flat',
	`signature` text,
	`moderator` int(11) default '0',
	`ordering` int(11) default '0',
	`posts` int(11) default '0',
	`avatar` varchar(50) default NULL,
	`karma` int(11) default '0',
	`karma_time` int(11) default '0',
	`group_id` int(4) default '1',
	`uhits` int(11) default '0',
	`personalText` tinytext,
	`gender` tinyint(4) NOT NULL default '0',
	`birthdate` date NOT NULL default '0001-01-01',
	`location` varchar(50) default NULL,
	`ICQ` varchar(50) default NULL,
	`AIM` varchar(50) default NULL,
	`YIM` varchar(50) default NULL,
	`MSN` varchar(50) default NULL,
	`SKYPE` varchar(50) default NULL,
	`GTALK` varchar(50) default NULL,
	`websitename` varchar(50) default NULL,
	`websiteurl` varchar(50) default NULL,
	`rank` tinyint(4) NOT NULL default '0',
	`hideEmail` tinyint(1) NOT NULL default '1',
	`showOnline` tinyint(1) NOT NULL default '1',
	PRIMARY KEY	(`userid`),
	KEY `group_id` (`group_id`)
);

CREATE TABLE	IF NOT EXISTS `#__fb_whoisonline` (
	`id` int(6) NOT NULL auto_increment,
	`userid` int(7) NOT NULL default '0',
	`time` varchar(14) NOT NULL default '0',
	`item` int(6) default '0',
	`what` varchar(255) default '0',
	`func` varchar(50) default NULL,
	`do` varchar(50) default NULL,
	`task` varchar(50) default NULL,
	`link` text,
	`userip` varchar(20) NOT NULL default '',
	`user` tinyint(2) NOT NULL default '0',
	PRIMARY KEY	(`id`),
	KEY `userid` (`userid`)
);

CREATE TABLE `#__fb_ranks` (
	`rank_id` mediumint(8) unsigned NOT NULL auto_increment,
	`rank_title` varchar(255) NOT NULL default '',
	`rank_min` mediumint(8) unsigned NOT NULL default '0',
	`rank_special` tinyint(1) unsigned NOT NULL default '0',
	`rank_image` varchar(255) NOT NULL default '',
	PRIMARY KEY	(`rank_id`)
);

INSERT INTO `#__fb_ranks` (`rank_id`, `rank_title`, `rank_min`, `rank_special`, `rank_image`) VALUES
	(1, 'Fresh Boarder', 0, 0, 'rank1.gif'),
	(2, 'Junior Boarder', 20, 0, 'rank2.gif'),
	(3, 'Senior Boarder', 40, 0, 'rank3.gif'),
	(4, 'Expert Boarder', 80, 0, 'rank4.gif'),
	(5, 'Gold Boarder', 160, 0, 'rank5.gif'),
	(6, 'Platinum Boarder', 320, 0, 'rank6.gif'),
	(7, 'Administrator', 0, 1, 'rankadmin.gif'),
	(8, 'Moderator', 0, 1, 'rankmod.gif'),
	(9, 'Spammer', 0, 1, 'rankspammer.gif');
