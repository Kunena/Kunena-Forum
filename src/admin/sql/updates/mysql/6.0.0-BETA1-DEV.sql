ALTER TABLE `#__kunena_categories` CHANGE `allow_anonymous` `allowAnonymous` tinyint NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_categories` CHANGE `post_anonymous` `postAnonymous` tinyint NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_categories` CHANGE `allow_polls` `allowPolls` tinyint NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_categories` CHANGE `parent_id` `parentid` int DEFAULT '0';
ALTER TABLE `#__kunena_categories` CHANGE `pub_recurse` `pubRecurse` tinyint DEFAULT '1';
ALTER TABLE `#__kunena_categories` CHANGE `admin_recurse` `adminRecurse` tinyint DEFAULT '1';
ALTER TABLE `#__kunena_categories` CHANGE `pub_access` `pubAccess` int NOT NULL DEFAULT '1';
ALTER TABLE `#__kunena_categories` CHANGE `admin_access` `adminAccess` int NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_categories` CHANGE `topic_ordering` `topicOrdering` varchar(16);
ALTER TABLE `#__kunena_categories` CHANGE `allow_ratings` `allowRatings` tinyint NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_categories` CHANGE `checked_out_time` `checked_out_time` datetime NOT NULL DEFAULT '1000-01-01 00:00:00';
ALTER TABLE `#__kunena_categories` CHANGE `ordering` `ordering` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_ranks` CHANGE `rank_title` `rankTitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__kunena_ranks` CHANGE `rank_image` `rankImage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;
ALTER TABLE `#__kunena_ranks` CHANGE `rank_special` `rankSpecial` tinyint UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_ranks` CHANGE `rank_min` `rankMin` mediumint UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_ranks` CHANGE `rank_id` `rankId` mediumint UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__kunena_announcement` CHANGE `created` `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00';
ALTER TABLE `#__kunena_announcement` CHANGE `publish_up` `publish_up` datetime NOT NULL DEFAULT '1000-01-01 00:00:00';
ALTER TABLE `#__kunena_announcement` CHANGE `publish_down` `publish_down` datetime NOT NULL DEFAULT '1000-01-01 00:00:00';
ALTER TABLE `#__kunena_polls` CHANGE `polltimetolive` `polltimetolive` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_polls_users` CHANGE `lasttime` `lasttime` datetime NOT NULL DEFAULT '1000-01-01 00:00:00';
ALTER TABLE `#__kunena_rate` CHANGE `time` `time` datetime NOT NULL;
ALTER TABLE `#__kunena_thankyou` CHANGE `time` `time` datetime NOT NULL;
ALTER TABLE `#__kunena_users` CHANGE `banned` `banned` datetime DEFAULT '1000-01-01 00:00:00';
ALTER TABLE `#__kunena_users` ADD `pinterest` VARCHAR(75) NULL AFTER `yim`;
ALTER TABLE `#__kunena_users` ADD `reddit` VARCHAR(75) NULL AFTER `pinterest`;
ALTER TABLE `#__kunena_users_banned` CHANGE `expiration` `expiration` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users_banned` CHANGE `created_time` `created_time` datetime NOT NULL;
ALTER TABLE `#__kunena_users_banned` CHANGE `modified_time` `modified_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_topics` CHANGE `params` `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;
ALTER TABLE `#__kunena_user_topics` CHANGE `params` `params` text NULL;
INSERT IGNORE INTO `#__mail_templates` (`template_id`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.reply', '', 'COM_KUNENA_SENDMAIL_REPLY_SUBJECT', 'COM_KUNENA_SENDMAIL_BODY', '', '',
        '{"tags":["mail", "subject", "message", "messageUrl", "once"]}');
INSERT IGNORE INTO `#__mail_templates` (`template_id`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.replymoderator', '', 'COM_KUNENA_SENDMAIL_REPLYMODERATOR_SUBJECT', 'COM_KUNENA_SENDMAIL_BODY', '', '',
        '{"tags":["mail", "subject", "message", "messageUrl", "once"]}');
INSERT IGNORE INTO `#__mail_templates` (`template_id`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.report', '', 'COM_KUNENA_SENDMAIL_REPORT_SUBJECT', 'COM_KUNENA_SENDMAIL_BODY_REPORTMODERATOR', '', '',
        '{"tags":["mail", "subject", "message", "messageUrl", "once"]}');
