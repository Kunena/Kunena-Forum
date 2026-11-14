ALTER TABLE `#__kunena_aliases` DROP INDEX `alias`, ADD PRIMARY KEY (`alias`);
DELETE FROM `#__kunena_polls_users` WHERE `pollid` IS NULL OR `userid` IS NULL;
ALTER TABLE `#__kunena_polls_users` MODIFY `pollid` int(11) NOT NULL, MODIFY `userid` int(11) NOT NULL, DROP INDEX `pollid`, ADD PRIMARY KEY (`pollid`, `userid`);
ALTER TABLE `#__kunena_thankyou` DROP INDEX `postid`, ADD PRIMARY KEY (`postid`, `userid`);
ALTER TABLE `#__kunena_user_read` DROP INDEX `user_topic_id`, ADD PRIMARY KEY (`user_id`, `topic_id`);
ALTER TABLE `#__kunena_user_topics` DROP INDEX `user_topic_id`, ADD PRIMARY KEY (`user_id`, `topic_id`);
CREATE TABLE IF NOT EXISTS `#__kunena_drafts` (
    `id`            int(11)      NOT NULL AUTO_INCREMENT,
    `user_id`       int(11)      NOT NULL,
    `catid`         int(11)      NOT NULL DEFAULT 0,
    `parent_id`     int(11)      NOT NULL DEFAULT 0,
    `topic_id`      int(11)      NOT NULL DEFAULT 0,
    `message_id`    int(11)      NOT NULL DEFAULT 0,
    `subject`       varchar(255) NOT NULL DEFAULT '',
    `message`       mediumtext   NOT NULL,
    `fields`        mediumtext   NOT NULL,
    `created_time`  int(11)      NOT NULL DEFAULT 0,
    `modified_time` int(11)      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `user_modified` (`user_id`, `modified_time`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `#__kunena_user_ignore` (
    `user_id`      int(11) NOT NULL,
    `ignored_id`   int(11) NOT NULL,
    `created_time` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `ignored_id`),
    KEY `ignored_id` (`ignored_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `#__kunena_user_follow` (
    `user_id`      int(11) NOT NULL,
    `followed_id`  int(11) NOT NULL,
    `created_time` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `followed_id`),
    KEY `followed_id` (`followed_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `#__kunena_categories_translations` (
    `category_id` int(11)      NOT NULL,
    `language`    varchar(7)   NOT NULL,
    `name`        tinytext     NULL,
    `description` text         NULL,
    `headerdesc`  text         NULL,
    PRIMARY KEY (`category_id`, `language`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;

-- The social websites which are shut down (Skype, Yahoo Messenger, Google+) are not in the default list any more.
UPDATE `#__kunena_users` SET `socials` = JSON_REMOVE(`socials`, '$.skype', '$.yim', '$.google') WHERE `socials` IS NOT NULL AND JSON_VALID(`socials`);
INSERT IGNORE INTO `#__mail_templates` (`template_id`, `extension`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.subscription', 'com_kunena', '', 'COM_KUNENA_MAILTPL_NOTIFICATION_SUBJECT', 'COM_KUNENA_MAILTPL_NOTIFICATION_BODY', 'COM_KUNENA_MAILTPL_NOTIFICATION_HTMLBODY', '', '{"tags":["sitename","boardtitle","heading","subject","topic","category","author","url","message","more","footer","unsubscribe"]}');
INSERT IGNORE INTO `#__mail_templates` (`template_id`, `extension`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.moderator', 'com_kunena', '', 'COM_KUNENA_MAILTPL_NOTIFICATION_SUBJECT', 'COM_KUNENA_MAILTPL_NOTIFICATION_BODY', 'COM_KUNENA_MAILTPL_NOTIFICATION_HTMLBODY', '', '{"tags":["sitename","boardtitle","heading","subject","topic","category","author","url","message","more","footer","unsubscribe"]}');
INSERT IGNORE INTO `#__mail_templates` (`template_id`, `extension`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.mention', 'com_kunena', '', 'COM_KUNENA_MAILTPL_MENTION_SUBJECT', 'COM_KUNENA_MAILTPL_MENTION_BODY', 'COM_KUNENA_MAILTPL_MENTION_HTMLBODY', '', '{"tags":["sitename","author","topic","url"]}');
INSERT IGNORE INTO `#__mail_templates` (`template_id`, `extension`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.reportmessage', 'com_kunena', '', 'COM_KUNENA_MAILTPL_REPORT_SUBJECT', 'COM_KUNENA_MAILTPL_REPORT_BODY', 'COM_KUNENA_MAILTPL_REPORT_HTMLBODY', '', '{"tags":["sitename","boardtitle","title","sender","reason","report","poster","subject","message","url"]}');
-- One rating of a user for a topic: the duplicates of a race of two requests are removed, the key keeps it so.
DELETE r1 FROM `#__kunena_rate` AS r1 INNER JOIN `#__kunena_rate` AS r2 ON r1.`topic_id` = r2.`topic_id` AND r1.`userid` = r2.`userid` AND r1.`id` > r2.`id`;
ALTER TABLE `#__kunena_rate` ADD UNIQUE KEY `topic_user` (`topic_id`, `userid`);
-- The smilies get a manual order: the current order (by id) stays the same, a second run keeps an order set since.
ALTER TABLE `#__kunena_smileys` ADD COLUMN `ordering` int(11) NOT NULL default '0' AFTER `emoticonbar`;
UPDATE `#__kunena_smileys` SET `ordering` = `id` WHERE `ordering` = 0;
-- A locked topic can have a reason (closed, solved, sold...): the topics which are already locked have none.
ALTER TABLE `#__kunena_topics` ADD COLUMN `lock_reason` varchar(255) NOT NULL default '' AFTER `locked`;
-- The topic keeps a copy of its first and last post: as long as a post (mediumtext), else a recount fails on a long post.
ALTER TABLE `#__kunena_topics` MODIFY `first_post_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, MODIFY `last_post_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;
-- A reply can be scheduled: published by the scheduler task at its time, unless it was held for review.
ALTER TABLE `#__kunena_messages` ADD COLUMN `scheduled_time` int(11) NOT NULL default '0' AFTER `modified_reason`;
ALTER TABLE `#__kunena_messages` ADD COLUMN `scheduled_review` tinyint(4) NOT NULL default '0' AFTER `scheduled_time`;
-- The title and the image of a rank are 255 characters, as in the schema: the install file of the first versions of 6.x made them 250.
ALTER TABLE `#__kunena_ranks` MODIFY `rankTitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__kunena_ranks` MODIFY `rankImage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;
