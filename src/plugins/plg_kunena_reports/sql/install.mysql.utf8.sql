CREATE TABLE IF NOT EXISTS `#__kunena_reports`
(
    `message_id`   int(11) NOT NULL,
    `topic_id`     int(11) NOT NULL DEFAULT 0,
    `reports`      int(11) NOT NULL DEFAULT 1,
    `reporters`    text    NOT NULL,
    `created_time` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`message_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
