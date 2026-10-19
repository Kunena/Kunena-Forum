ALTER TABLE `#__kunena_aliases` DROP INDEX `alias`, ADD PRIMARY KEY (`alias`);
DELETE FROM `#__kunena_polls_users` WHERE `pollid` IS NULL OR `userid` IS NULL;
ALTER TABLE `#__kunena_polls_users` MODIFY `pollid` int(11) NOT NULL, MODIFY `userid` int(11) NOT NULL, DROP INDEX `pollid`, ADD PRIMARY KEY (`pollid`, `userid`);
ALTER TABLE `#__kunena_thankyou` DROP INDEX `postid`, ADD PRIMARY KEY (`postid`, `userid`);
ALTER TABLE `#__kunena_user_read` DROP INDEX `user_topic_id`, ADD PRIMARY KEY (`user_id`, `topic_id`);
ALTER TABLE `#__kunena_user_topics` DROP INDEX `user_topic_id`, ADD PRIMARY KEY (`user_id`, `topic_id`);
CREATE TABLE IF NOT EXISTS `#__kunena_drafts`
(
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
CREATE TABLE IF NOT EXISTS `#__kunena_user_ignore`
(
    `user_id`      int(11) NOT NULL,
    `ignored_id`   int(11) NOT NULL,
    `created_time` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `ignored_id`),
    KEY `ignored_id` (`ignored_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `#__kunena_user_follow`
(
    `user_id`      int(11) NOT NULL,
    `followed_id`  int(11) NOT NULL,
    `created_time` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `followed_id`),
    KEY `followed_id` (`followed_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `#__kunena_categories_translations`
(
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
