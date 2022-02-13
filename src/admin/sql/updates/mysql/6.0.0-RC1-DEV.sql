CREATE TABLE IF NOT EXISTS `#__kunena_private` (
    `id`          int                                 NOT NULL AUTO_INCREMENT,
    `parent_id`   int                                 NOT NULL DEFAULT '0',
    `author_id`   int                                 NOT NULL DEFAULT '0',
    `created_at`  datetime                            NOT NULL,
    `attachments` tinyint                             NOT NULL DEFAULT '0',
    `subject`     tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
    `body`        text COLLATE utf8mb4_unicode_ci     NOT NULL,
    `params`      text COLLATE utf8mb4_unicode_ci     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `parent_id` (`parent_id`),
    KEY `author_id` (`author_id`),
    KEY `created_at` (`created_at`)
);
CREATE TABLE IF NOT EXISTS `#__kunena_private_attachment_map` (
    `private_id`    int NOT NULL,
    `attachment_id` int NOT NULL,
    PRIMARY KEY (`private_id`, `attachment_id`),
    KEY `attachment_id` (`attachment_id`)
);
CREATE TABLE IF NOT EXISTS `#__kunena_private_post_map` (
    `private_id` int NOT NULL,
    `message_id` int NOT NULL,
    PRIMARY KEY (`private_id`, `message_id`),
    KEY `message_id` (`message_id`)
);
CREATE TABLE IF NOT EXISTS `#__kunena_private_user_map` (
    `private_id` int      NOT NULL,
    `user_id`    int      NOT NULL,
    `read_at`    datetime NOT NULL,
    `replied_at` datetime NOT NULL,
    `deleted_at` datetime NOT NULL,
    PRIMARY KEY (`private_id`, `user_id`),
    KEY `user_id` (`user_id`)
);
