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
-- The reply which the author or a moderator accepted as the answer of the topic: the topics which are there have none.
ALTER TABLE `#__kunena_topics` ADD COLUMN `answer_id` int(11) NOT NULL default '0' AFTER `lock_reason`;
-- The warnings of the users: points which a moderator gave, which expire, and lead to a ban at a threshold.
CREATE TABLE IF NOT EXISTS `#__kunena_warnings` (
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `user_id`      int(11)      NOT NULL,
    `moderator_id` int(11)      NOT NULL DEFAULT 0,
    `message_id`   int(11)      NOT NULL DEFAULT 0,
    `points`       tinyint(4)   NOT NULL DEFAULT 1,
    `reason`       varchar(255) NOT NULL DEFAULT '',
    `created_time` int(11)      NOT NULL DEFAULT 0,
    `expires_time` int(11)      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
-- The messages which a user bookmarked (My bookmarks).
CREATE TABLE IF NOT EXISTS `#__kunena_bookmarks` (
    `user_id`      int(11) NOT NULL,
    `message_id`   int(11) NOT NULL,
    `created_time` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `message_id`),
    KEY `message_id` (`message_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
-- The posts which wait for the daily or weekly digest of a user (instead of an e-mail for each post).
CREATE TABLE IF NOT EXISTS `#__kunena_digest` (
    `user_id`      int(11) NOT NULL,
    `message_id`   int(11) NOT NULL,
    `created_time` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `message_id`),
    KEY `message_id` (`message_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
-- The earlier versions of the messages: the subject and the text before each edit, with who edited, when and why.
CREATE TABLE IF NOT EXISTS `#__kunena_messages_history` (
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `message_id`  int(11)      NOT NULL,
    `edited_by`   int(11)      NOT NULL DEFAULT 0,
    `edited_time` int(11)      NOT NULL DEFAULT 0,
    `reason`      varchar(255) NOT NULL DEFAULT '',
    `subject`     varchar(255) NOT NULL DEFAULT '',
    `message`     mediumtext   NOT NULL,
    PRIMARY KEY (`id`),
    KEY `message_id` (`message_id`),
    KEY `edited_by` (`edited_by`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
-- The reactions (an emoji) of the users on the messages: one per user and message. The emoji is binary: compared byte by byte,
-- as the collation of the forum compares many emoji as equal.
CREATE TABLE IF NOT EXISTS `#__kunena_reactions` (
    `message_id`   int(11)       NOT NULL,
    `user_id`      int(11)       NOT NULL,
    `reaction`     varbinary(64) NOT NULL,
    `created_time` int(11)       NOT NULL DEFAULT 0,
    PRIMARY KEY (`message_id`, `user_id`),
    KEY `user_id` (`user_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
-- The topic keeps a copy of its first and last post: as long as a post (mediumtext), else a recount fails on a long post.
ALTER TABLE `#__kunena_topics` MODIFY `first_post_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, MODIFY `last_post_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;
-- A reply can be scheduled: published by the scheduler task at its time, unless it was held for review.
ALTER TABLE `#__kunena_messages` ADD COLUMN `scheduled_time` int(11) NOT NULL default '0' AFTER `modified_reason`;
ALTER TABLE `#__kunena_messages` ADD COLUMN `scheduled_review` tinyint(4) NOT NULL default '0' AFTER `scheduled_time`;
-- A message whose subscribers were told about it: approved again after an edit, it does not tell them a second time.
ALTER TABLE `#__kunena_messages` ADD COLUMN `notified` tinyint(4) NOT NULL default '0' AFTER `scheduled_review`;
UPDATE `#__kunena_messages` SET `notified` = 1 WHERE `hold` = 0;
-- A message in the trash without the time of its delete (deleted before the column was there, or by a tool which did not set it) gets the time of the upgrade: the task which empties the trash removed it at once, whatever its age.
UPDATE `#__kunena_messages` SET `deleted_time` = UNIX_TIMESTAMP() WHERE `hold` IN (2, 3) AND `deleted_time` = 0;
-- The private posts whose post no longer exists (a post deleted for good left its private post behind), with their recipients and links.
DELETE a FROM `#__kunena_private` AS a WHERE NOT EXISTS (SELECT 1 FROM `#__kunena_private_post_map` AS pm INNER JOIN `#__kunena_messages` AS m ON m.`id` = pm.`message_id` WHERE pm.`private_id` = a.`id`);
DELETE a FROM `#__kunena_private_post_map` AS a LEFT JOIN `#__kunena_private` AS p ON p.`id` = a.`private_id` WHERE p.`id` IS NULL;
DELETE a FROM `#__kunena_private_user_map` AS a LEFT JOIN `#__kunena_private` AS p ON p.`id` = a.`private_id` WHERE p.`id` IS NULL;
DELETE a FROM `#__kunena_private_attachment_map` AS a LEFT JOIN `#__kunena_private` AS p ON p.`id` = a.`private_id` WHERE p.`id` IS NULL;
-- The title and the image of a rank are 255 characters, as in the schema: the install file of the first versions of 6.x made them 250.
ALTER TABLE `#__kunena_ranks` MODIFY `rankTitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__kunena_ranks` MODIFY `rankImage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;
-- The language of a category and of a topic: in the install only, an upgraded site had no column (errors in the lists of a multilingual site, the language of a topic not saved).
ALTER TABLE `#__kunena_categories` ADD COLUMN `language` varchar(7) NOT NULL DEFAULT '*' AFTER `allowRatings`;
ALTER TABLE `#__kunena_topics` ADD COLUMN `language` varchar(7) NOT NULL DEFAULT '*' AFTER `params`;
ALTER TABLE `#__kunena_topics` ADD INDEX `language` (`language`);
-- The choice of the age and the birthdate of a profile: a forum installed with 7.1 had no column, and every save of a profile was an error.
ALTER TABLE `#__kunena_users` ADD COLUMN `displayAgeBirthdate` tinyint(1) NOT NULL default '1' AFTER `showOnline`;
