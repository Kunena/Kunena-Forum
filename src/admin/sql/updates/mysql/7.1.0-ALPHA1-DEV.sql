CREATE TABLE IF NOT EXISTS `#__kunena_plg_task_mailsqueue`
(
    `id`            int(11)    NOT NULL auto_increment,
    `subject`       tinytext   NOT NULL,
    `messageId`     int(11)    NOT NULL default '0',
    `url`           text       NOT NULL,
    `emailListJson` text       NOT NULL,
    `categoryName`  text       NOT NULL,
    `once`          text       NOT NULL,
    `send`          tinyint(4) NOT NULL default '0',
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
