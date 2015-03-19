-- Migration from JoomlaBoard (unknown version) to FireBoard 1.0.3, SQL was found and modified from FireBoard 1.0.3 installation package.
-- Code has not been tested, but is included to allow people to play with it..

CREATE TABLE	`#__kunena_attachments`		SELECT * FROM #__sb_attachments;
ALTER TABLE		`#__kunena_attachments`		CHARACTER SET utf8
ALTER TABLE		`#__kunena_attachments`		ADD KEY `mesid` (`mesid`);

CREATE TABLE	`#__kunena_categories`		SELECT * FROM #__sb_categories;
ALTER TABLE		`#__kunena_categories`		CHARACTER SET utf8
ALTER TABLE		`#__kunena_categories`		CHANGE `moderated` `moderated` TINYINT( 4 ) NOT NULL DEFAULT '1';
ALTER TABLE		`#__kunena_categories`		ADD `id_last_msg` int(10) NOT NULL DEFAULT '0';
ALTER TABLE		`#__kunena_categories`		ADD `numTopics` mediumint(8) NOT NULL DEFAULT '0';
ALTER TABLE		`#__kunena_categories`		ADD `numPosts` mediumint(8) NOT NULL DEFAULT '0';
ALTER TABLE		`#__kunena_categories`		ADD `time_last_msg` int(11) DEFAULT NULL;
ALTER TABLE		`#__kunena_categories`		ADD PRIMARY KEY (`id`);
ALTER TABLE		`#__kunena_categories`		CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE		`#__kunena_categories`		ADD KEY `parent` (`parent`);
ALTER TABLE		`#__kunena_categories`		ADD KEY `published_pubaccess_id` (`published`,`pub_access`,`id`);
ALTER TABLE		`#__kunena_categories`		ADD KEY `msg_id` (`id_last_msg`);

ALTER TABLE		`#__kunena_categories`		ADD `headerdesc` text NOT NULL  AFTER `description`;
ALTER TABLE		`#__kunena_categories`		ADD `class_sfx` varchar(20) NOT NULL  AFTER `headerdesc`;

CREATE TABLE	`#__kunena_messages`		SELECT * FROM #__sb_messages;
ALTER TABLE		`#__kunena_messages`		CHARACTER SET utf8
ALTER TABLE		`#__kunena_messages`		ADD `modified_by` int(7) NULL;
ALTER TABLE		`#__kunena_messages`		ADD `modified_time` int(11) NULL;
ALTER TABLE		`#__kunena_messages`		ADD `modified_reason` tinytext NULL;
ALTER TABLE		`#__kunena_messages`		ADD PRIMARY KEY (`id`);
ALTER TABLE		`#__kunena_messages`		CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE		`#__kunena_messages`		ADD KEY `thread` (`thread`);
ALTER TABLE		`#__kunena_messages`		ADD KEY `parent` (`parent`);
ALTER TABLE		`#__kunena_messages`		ADD KEY `catid` (`catid`);
ALTER TABLE		`#__kunena_messages`		ADD KEY `ip` (`ip`);
ALTER TABLE		`#__kunena_messages`		ADD KEY `userid` (`userid`);
ALTER TABLE		`#__kunena_messages`		ADD KEY `time` (`time`);
ALTER TABLE		`#__kunena_messages`		ADD KEY `locked` (`locked`);
ALTER TABLE		`#__kunena_messages`		ADD KEY `hold_time` (`hold`, `time`);

CREATE TABLE	`#__kunena_messages_text`	SELECT * FROM #__sb_messages_text;
ALTER TABLE		`#__kunena_messages_text`	CHARACTER SET utf8
ALTER TABLE		`#__kunena_messages_text`	ADD PRIMARY KEY (`mesid`);

CREATE TABLE	`#__kunena_moderation`		SELECT * FROM #__sb_moderation;
ALTER TABLE		`#__kunena_moderation`		CHARACTER SET utf8
ALTER TABLE		`#__kunena_moderation`		ADD PRIMARY KEY (`catid`,`userid`);

CREATE TABLE	`#__kunena_sessions`		SELECT * FROM #__sb_sessions;
ALTER TABLE		`#__kunena_sessions`		CHARACTER SET utf8
ALTER TABLE		`#__kunena_sessions`		ADD PRIMARY KEY (`userid`);

CREATE TABLE	`#__kunena_smileys`			SELECT * FROM #__sb_smileys;
ALTER TABLE		`#__kunena_smileys`			CHARACTER SET utf8
ALTER TABLE		`#__kunena_smileys`			ADD PRIMARY KEY (`id`);
ALTER TABLE		`#__kunena_smileys`			CHANGE `id` `id` INT( 4 ) NOT NULL AUTO_INCREMENT;

CREATE TABLE	`#__kunena_subscriptions`	SELECT * FROM #__sb_subscriptions;
ALTER TABLE		`#__kunena_subscriptions`	CHARACTER SET utf8
ALTER TABLE		`#__kunena_subscriptions`	ADD KEY `thread` (`thread`);
ALTER TABLE		`#__kunena_subscriptions`	ADD KEY `userid` (`userid`);

CREATE TABLE	`#__kunena_users`			SELECT * FROM #__sb_users;
ALTER TABLE		`#__kunena_users`			CHARACTER SET utf8
ALTER TABLE		`#__kunena_users`			ADD PRIMARY KEY (`userid`);
ALTER TABLE		`#__kunena_users`			ADD `group_id` int(4) default '1';
ALTER TABLE		`#__kunena_users`			ADD `uhits` int(11) default '0';
ALTER TABLE		`#__kunena_users`			ADD `personalText` tinytext;
ALTER TABLE		`#__kunena_users`			ADD `gender` tinyint(4) NOT NULL default '0';
ALTER TABLE		`#__kunena_users`			ADD `birthdate` date NOT NULL default '0001-01-01';
ALTER TABLE		`#__kunena_users`			ADD `location` varchar(50) default NULL;
ALTER TABLE		`#__kunena_users`			ADD `ICQ` varchar(50) default NULL;
ALTER TABLE		`#__kunena_users`			ADD `AIM` varchar(50) default NULL;
ALTER TABLE		`#__kunena_users`			ADD `YIM` varchar(50) default NULL;
ALTER TABLE		`#__kunena_users`			ADD `MSN` varchar(50) default NULL;
ALTER TABLE		`#__kunena_users`			ADD `SKYPE` varchar(50) default NULL;
ALTER TABLE		`#__kunena_users`			ADD `GTALK` varchar(50) default NULL;
ALTER TABLE		`#__kunena_users`			ADD `websitename` varchar(50) default NULL;
ALTER TABLE		`#__kunena_users`			ADD `websiteurl` varchar(50) default NULL;
ALTER TABLE		`#__kunena_users`			ADD `rank` tinyint(4) NOT NULL default '0';
ALTER TABLE		`#__kunena_users`			ADD `hideEmail` tinyint(1) NOT NULL default '1';
ALTER TABLE		`#__kunena_users`			ADD `showOnline` tinyint(1) NOT NULL default '1';
ALTER TABLE		`#__kunena_users`			ADD KEY `group_id` (`group_id`);
UPDATE			`#__kunena_users`			SET `rank`=8 WHERE `moderator`=1 AND `rank`=0;

CREATE TABLE `#__kunena_announcement` (
	`id` int(3) NOT NULL auto_increment,
	`title` tinytext,
	`sdescription` text NOT NULL,
	`description` text NOT NULL,
	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	`published` tinyint(1) NOT NULL default '0',
	`ordering` tinyint(4) NOT NULL default '0',
	`showdate` tinyint(1) NOT NULL default '1',
	PRIMARY KEY (`id`) ) DEFAULT CHARACTER SET utf8;

CREATE TABLE `#__kunena_favorites` (
	`thread` int(11) NOT NULL default '0',
	`userid` int(11) NOT NULL default '0',
	KEY `thread` (`thread`),
	KEY `userid` (`userid`) ) DEFAULT CHARACTER SET utf8;

CREATE TABLE `#__kunena_whoisonline` (
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
	PRIMARY KEY (`id`) ) DEFAULT CHARACTER SET utf8;

CREATE TABLE `#__kunena_groups`(
	`id` int(4) NOT NULL  auto_increment,
	`title` varchar(255) NULL,
	PRIMARY KEY (id) ) DEFAULT CHARACTER SET utf8;

CREATE TABLE `#__kunena_ranks` (
	`rank_id` mediumint(8) unsigned NOT NULL auto_increment,
	`rank_title` varchar(255) NOT NULL default '',
	`rank_min` mediumint(8) unsigned NOT NULL default '0',
	`rank_special` tinyint(1) unsigned NOT NULL default '0',
	`rank_image` varchar(255) NOT NULL default '',
	PRIMARY KEY (`rank_id`) ) DEFAULT CHARACTER SET utf8;

INSERT INTO	`#__kunena_groups` VALUES ('1', 'Registered User');

INSERT INTO `#__kunena_ranks` (`rank_id`, `rank_title`, `rank_min`, `rank_special`, `rank_image`) VALUES
	(1, 'Fresh Boarder', 0, 0, 'rank1.gif'),
	(2, 'Junior Boarder', 20, 0, 'rank2.gif'),
	(3, 'Senior Boarder', 40, 0, 'rank3.gif'),
	(4, 'Expert Boarder', 80, 0, 'rank4.gif'),
	(5, 'Gold Boarder', 160, 0, 'rank5.gif'),
	(6, 'Platinum Boarder', 320, 0, 'rank6.gif'),
	(7, 'Administrator', 0, 1, 'rankadmin.gif'),
	(8, 'Moderator', 0, 1, 'rankmod.gif'),
	(9, 'Spammer', 0, 1, 'rankspammer.gif');
