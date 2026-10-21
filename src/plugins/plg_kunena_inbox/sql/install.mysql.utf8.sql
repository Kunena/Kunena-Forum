CREATE TABLE IF NOT EXISTS `#__kunena_inbox`
(
    `id`                int(11)      NOT NULL AUTO_INCREMENT,
    `user_id`           int(11)      NOT NULL,
    `sender_id`         int(11)      NOT NULL DEFAULT 0,
    `type`              varchar(20)  NOT NULL DEFAULT 'message',
    `conversation_id`   int(11)      NOT NULL DEFAULT 0,
    `subject`           varchar(255) NOT NULL DEFAULT '',
    `body`              mediumtext   NOT NULL,
    `topic_id`          int(11)      NOT NULL DEFAULT 0,
    `message_id`        int(11)      NOT NULL DEFAULT 0,
    `created_time`      int(11)      NOT NULL DEFAULT 0,
    `read_time`         int(11)      NOT NULL DEFAULT 0,
    `deleted_recipient` tinyint(4)   NOT NULL DEFAULT 0,
    `deleted_sender`    tinyint(4)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `user_read` (`user_id`, `read_time`),
    KEY `sender` (`sender_id`),
    KEY `conversation` (`conversation_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `#__kunena_inbox_attachments`
(
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `inbox_id`     int(11)      NOT NULL,
    `user_id`      int(11)      NOT NULL DEFAULT 0,
    `filename`     varchar(255) NOT NULL DEFAULT '',
    `stored`       varchar(64)  NOT NULL DEFAULT '',
    `size`         int(11)      NOT NULL DEFAULT 0,
    `mime`         varchar(100) NOT NULL DEFAULT '',
    `created_time` int(11)      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `inbox` (`inbox_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `#__kunena_inbox_blocks`
(
    `user_id`      int(11) NOT NULL,
    `blocked_id`   int(11) NOT NULL,
    `created_time` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`user_id`, `blocked_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
