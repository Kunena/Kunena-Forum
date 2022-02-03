ALTER TABLE `#__kunena_topics` CHANGE `params` `params` TEXT NULL;
ALTER TABLE `#__kunena_user_topics` CHANGE `params` `params` text NULL;
ALTER TABLE `#__kunena_announcement` CHANGE `created` `created` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_announcement` CHANGE `publish_up` `publish_up` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_announcement` CHANGE `publish_down` `publish_down` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `checked_out_time` `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `ordering` `ordering` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_polls` CHANGE `polltimetolive` `polltimetolive` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_polls_users` CHANGE `lasttime` `lasttime` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_rate` CHANGE `time` `time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_thankyou` CHANGE `time` `time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users` CHANGE `banned` `banned` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users_banned` CHANGE `expiration` `expiration` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users_banned` CHANGE `created_time` `created_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users_banned` CHANGE `modified_time` `modified_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `allow_anonymous` `allowAnonymous` tinyint DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `post_anonymous` `postAnonymous` tinyint DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `allow_polls` `allowPolls` tinyint DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `parent_id` `parentid` int DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `pub_recurse` `pubRecurse` tinyint DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `admin_recurse` `adminRecurse` tinyint DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `pub_access` `pubAccess` int DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `admin_access` `adminAccess` int DEFAULT NULL;
ALTER TABLE `#__kunena_categories` CHANGE `topic_ordering` `topicOrdering` varchar(16);
ALTER TABLE `#__kunena_categories` CHANGE `allow_ratings` `allowRatings` tinyint DEFAULT NULL;
ALTER TABLE `#__kunena_ranks` CHANGE `rank_title` `rankTitle` varchar(255);
ALTER TABLE `#__kunena_ranks` CHANGE `rank_image` `rankImage` varchar(255);
ALTER TABLE `#__kunena_ranks` CHANGE `rank_special` `rankSpecial` tinyint(1);
ALTER TABLE `#__kunena_ranks` CHANGE `rank_min` `rankMin` mediumint DEFAULT NULL;
ALTER TABLE `#__kunena_ranks` CHANGE `rank_id` `rankId` mediumint NOT NULL;
ALTER TABLE `#__kunena_version` ADD `sampleData` boolean not null default 1 AFTER `versionname`;
ALTER TABLE `#__kunena_users` ADD `pinterest` VARCHAR(75) NULL AFTER `yim`;
ALTER TABLE `#__kunena_users` ADD `reddit` VARCHAR(75) NULL AFTER `pinterest`;
UPDATE `#__kunena_announcement` SET `created` = CASE WHEN `created` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `created` END;
UPDATE `#__kunena_announcement` SET `publish_up` = CASE WHEN `publish_up` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `publish_up` END;
UPDATE `#__kunena_announcement` SET `publish_down` = CASE WHEN `publish_down` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `publish_down` END;
UPDATE `#__kunena_categories` SET `checked_out_time` = CASE WHEN `checked_out_time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `checked_out_time` END;
UPDATE `#__kunena_polls` SET `polltimetolive` = CASE WHEN `polltimetolive` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `polltimetolive` END;
UPDATE `#__kunena_polls_users` SET `lasttime` = CASE WHEN `lasttime` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `lasttime` END;
UPDATE `#__kunena_rate` SET `time` = CASE WHEN `time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `time` END;
UPDATE `#__kunena_thankyou` SET `time` = CASE WHEN `time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `time` END;
UPDATE `#__kunena_users` SET `banned` = CASE WHEN `banned` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `banned` END;
UPDATE `#__kunena_users_banned` SET `expiration` = CASE WHEN `expiration` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `expiration` END;
UPDATE `#__kunena_users_banned` SET `created_time` = CASE WHEN `created_time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `created_time` END;
UPDATE `#__kunena_users_banned` SET `modified_time` = CASE WHEN `modified_time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `modified_time` END;
INSERT INTO `#__mail_templates` (`template_id`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.reply', '', 'COM_CONFIG_SENDMAIL_SUBJECT', 'COM_CONFIG_SENDMAIL_BODY', '', '',
        '{"tags":["mail", "subject", "message", "messageUrl", "once"]}');
INSERT INTO `#__mail_templates` (`template_id`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.replymoderator', '', 'COM_CONFIG_SENDMAIL_SUBJECT', 'COM_CONFIG_SENDMAIL_BODY', '', '',
        '{"tags":["mail", "subject", "message", "messageUrl", "once"]}');
INSERT INTO `#__mail_templates` (`template_id`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.report', '', 'COM_CONFIG_SENDMAIL_SUBJECT', 'COM_CONFIG_SENDMAIL_BODY', '', '',
        '{"tags":["mail", "subject", "message", "messageUrl", "once"]}');
