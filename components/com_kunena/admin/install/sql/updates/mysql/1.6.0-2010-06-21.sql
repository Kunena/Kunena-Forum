-- Kunena 1.6.0 => 1.6.0

ALTER TABLE	`#__kunena_users`			ADD `banned` DATETIME NULL DEFAULT NULL AFTER `moderator`;

DROP TABLE IF EXISTS `#__kunena_banned_users`;
