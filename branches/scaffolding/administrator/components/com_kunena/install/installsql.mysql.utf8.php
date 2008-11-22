-- <?php /* $Id$ */ defined('_JEXEC') or die('Invalid Request.') ?>;

--
-- Table structure for table `#__kunena_announcements`
--

CREATE TABLE IF NOT EXISTS `#__kunena` (
  `id` int(10) NOT NULL auto_increment,
  `version` varchar(16) NOT NULL COMMENT 'Version number',
  `installed_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Date-time modified or installed',
  `log` mediumtext,
  PRIMARY KEY  USING BTREE (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Extension version history';

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_announcements`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_announcements` (
  `id` INT(10) unsigned NOT NULL auto_increment COMMENT 'Primary Key',
  `title` VARCHAR(255) default NULL,
  `summary` TEXT NOT NULL,
  `body` TEXT NOT NULL,
  `created_date` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `ordering` INT(11) NOT NULL default '0',
  `published` INT(1) NOT NULL default '0',
  `show_date` INT(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_attachments`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_attachments` (
  `post_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__kunena_posts.id',
  `file_path` VARCHAR(255) NOT NULL default '',
  KEY `idx_post_id` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_categories`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_categories` (
  `id` INT(10) unsigned NOT NULL auto_increment COMMENT 'Primary Key',
  `parent_id` INT(10) unsigned default '0',
  `title` VARCHAR(100) default NULL,
  `alias` VARCHAR(100) default NULL,
  `path` VARCHAR(255) default NULL COMMENT 'URL path from root',

  `icon` INT(10) unsigned default '0',
  `summary` TEXT NOT NULL,
  `description` TEXT NOT NULL,
  `ordering` INT(11) default '0',
  `published` INT(1) default '0',
  `class_sfx` VARCHAR(20) default NULL,

  `locked` INT(1) NOT NULL default '0',
  `moderated` INT(1) NOT NULL default '1',
  `alert_admin` INT(1) NOT NULL default '0',

  `access` INT(11) default '1',
  `admin_access` INT(11) default '0',

  `checked_out` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',
  `checked_out_time` DATETIME NOT NULL default '0000-00-00 00:00:00',

  `review` INT(4) NOT NULL default '0',
  `hits` INT(10) unsigned default '0',

  `total_threads` INT(10) unsigned NOT NULL default '0',
  `total_posts` INT(10) unsigned NOT NULL default '0',

  `last_post_id` INT(10) unsigned NOT NULL default '0',
  `last_post_time` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `last_post_icon` INT(11) NOT NULL default '0',
  `last_post_subject` VARCHAR(100) default NULL,
  `last_post_name` VARCHAR(100) default NULL,
  `last_post_email` VARCHAR(100) default NULL,
  `last_post_user_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',

  `left_id` INT(10) unsigned NOT NULL COMMENT 'Nested Set Reference Id',
  `right_id` INT(10) unsigned NOT NULL COMMENT 'Nested Set Reference Id',
  PRIMARY KEY  (`id`),
  KEY `idx_category_nested_set` (`parent_id`,`left_id`,`right_id`,`access`,`published`),
  KEY `idx_category_route_lookup` (`path`),
  KEY `idx_category_hits` (`hits`,`access`,`published`),
  KEY `idx_category_threads` (`total_threads`,`access`,`published`),
  KEY `idx_category_posts` (`total_posts`,`access`,`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `jos_kunena_categories` (`id`, `parent_id`, `title`, `alias`, `path`, `icon`, `summary`, `description`, `ordering`, `published`, `class_sfx`, `locked`, `moderated`, `alert_admin`, `access`, `admin_access`, `checked_out`, `checked_out_time`, `review`, `hits`, `last_post_id`, `last_post_time`, `total_threads`, `total_posts`, `left_id`, `right_id`) VALUES
(1, 0, 'None', NULL, 'root', 0, '', '', 0, 0, NULL, 0, 1, 0, 1, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_favorite_threads`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_favorite_threads` (
  `thread_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__kunena_threads.id',
  `user_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',
  KEY `idx_favorite_user_thread` (`user_id`,`thread_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_members`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_members` (
  `user_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',
  `personal_text` VARCHAR (50) default NULL,
  `avatar` VARCHAR(50) default NULL,
  `signature` TEXT default NULL,
  `gender` INT(1) NOT NULL default '0',
  `birth_date` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `location` VARCHAR(50) NULL,
  `alias_icq` VARCHAR(50) default NULL,
  `alias_aim` VARCHAR(50) default NULL,
  `alias_yim` VARCHAR(50) default NULL,
  `alias_msn` VARCHAR(50) default NULL,
  `alias_skype` VARCHAR(50) default NULL,
  `alias_gtalk` VARCHAR(50) default NULL,
  `website_name` VARCHAR(50) default NULL,
  `website_url` VARCHAR(50) default NULL,
  `karma` INT(11) default '0',
  `karma_time` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `total_posts` INT(10) unsigned default '0',
  `hits` INT(10) unsigned default '0',
  `rank_id` INT(10) NOT NULL default '0' COMMENT 'Foreign Key to #__kunena_member_ranks.id',
  `global_moderator` INT(1) NOT NULL default '0',
  `show_email` INT(1) NOT NULL default '0',
  `show_online` INT(1) NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  KEY `idx_member_birth_date` (`birth_date`),
  KEY `idx_member_rank` (`rank_id`),
  KEY `idx_member_total_posts` (`total_posts`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_member_ranks`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_member_ranks` (
  `id` INT(10) unsigned NOT NULL auto_increment COMMENT 'Primary Key',
  `title` VARCHAR(100) NOT NULL default '',
  `alias` VARCHAR(100) NOT NULL default '',
  `minimum_posts` INT(10) unsigned NOT NULL default '0',
  `special` INT(1) unsigned NOT NULL default '0',
  `image_path` VARCHAR(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `idx_rank_alias` (`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_moderators`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_moderators` (
  `user_id` INT(10) unsigned NOT NULL COMMENT 'Foreign Key to #__users.id',
  `category_id` INT(10) unsigned NOT NULL COMMENT 'Foreign Key to #__kunena_categories.id',
  KEY `idx_moderator_user_category` (`user_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_monitor`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_monitor` (
  `user_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',
  `user_ip` INT(20) unsigned default NULL,
  `time` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `link` VARCHAR(255) default NULL COMMENT 'Link to currently viewed page',
  `task` VARCHAR(50) default NULL,
  `what` VARCHAR(100) default NULL,
  `where` VARCHAR(100) default NULL,
  KEY `idx_monitor_user` (`user_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_posts`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_posts` (
  `id` INT(10) unsigned NOT NULL auto_increment COMMENT 'Primary Key',
  `parent_id` INT(10) unsigned default '0',
  `category_id` INT (10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__kunena_categories.id',
  `thread_id` INT(10) unsigned default '0' COMMENT 'Foreign Key to #__kunena_threads.id',
  `user_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',
  `icon` INT(11) NOT NULL default '0',
  `subject` VARCHAR(100) default NULL,
  `message` TEXT NOT NULL,
  `created_time` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `user_ip` INT(20) unsigned default NULL,
  `hits` INT(10) unsigned default '0',
  `name` VARCHAR(100) default NULL,
  `email` VARCHAR(100) default NULL,
  `published` INT(1) NOT NULL default '0',
  `moved` INT(4) default '0',
  `modified_user_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',
  `modified_time` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `modified_reason` VARCHAR(100) default NULL,
  `left_id` INT(10) unsigned default '0' COMMENT 'Nested Set Reference Id',
  `right_id` INT(10) unsigned default '0' COMMENT 'Nested Set Reference Id',
  PRIMARY KEY  (`id`),
  KEY `idx_post_list` (`category_id`,`thread_id`,`published`,`created_time`),
  KEY `idx_post_nested_set` (`parent_id`,`left_id`,`right_id`),
  KEY `idx_post_ip` (`user_ip`),
  KEY `idx_post_user` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `#__kunena_smileys`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_smileys` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'Primary Key',
  `code` VARCHAR(16) NOT NULL,
  `file_path` VARCHAR(100) NOT NULL default '',
  `file_path_grey` VARCHAR(100) NOT NULL default '',
  `palette` INT(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_subscriptions`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_subscriptions` (
  `thread_id` INT(10) unsigned default '0' COMMENT 'Foreign Key to #__kunena_threads.id',
  `user_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',
  `category_id` INT(10) unsigned default '0' COMMENT 'Foreign Key to #__kunena_categories.id',
  KEY `idx_user_category` (`user_id`,`category_id`),
  KEY `idx_user_thread` (`user_id`,`thread_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__kunena_threads`
--

CREATE TABLE IF NOT EXISTS `jos_kunena_threads` (
  `id` INT(10) unsigned NOT NULL auto_increment COMMENT 'Primary Key',
  `category_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__kunena_categories.id',
  `user_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',
  `user_ip` INT(20) unsigned default NULL,
  `created_time` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `icon` INT(11) NOT NULL default '0',
  `published` INT(1) NOT NULL default '0',
  `locked` INT(1) NOT NULL default '0',
  `ordering` INT(11) default '0',
  `moved` INT(4) default '0',
  `hits` INT(10) unsigned default '0',
  `total_posts` INT(10) unsigned NOT NULL default '0',
  `last_post_id` INT(10) unsigned NOT NULL default '0',
  `last_post_time` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `last_post_icon` INT(11) NOT NULL default '0',
  `last_post_subject` VARCHAR(100) default NULL,
  `last_post_name` VARCHAR(100) default NULL,
  `last_post_email` VARCHAR(100) default NULL,
  `last_post_user_id` INT(10) unsigned NOT NULL default '0' COMMENT 'Foreign Key to #__users.id',
  PRIMARY KEY  (`id`),
  KEY `idx_thread_category_published_created` (`category_id`,`published`,`created_time`),
  KEY `idx_thread_ip` (`user_ip`),
  KEY `idx_thread_user` (`user_id`),
  KEY `idx_thread_created` (`created_time`),
  KEY `idx_thread_hits` (`hits`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
