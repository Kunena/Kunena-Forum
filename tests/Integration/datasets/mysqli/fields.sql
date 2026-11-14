-- The custom fields of Joomla, as small as they can be (the columns which the code of Kunena reads or writes): the nickname of a
-- user is a custom field of the Joomla users.
CREATE TABLE IF NOT EXISTS `#__fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `context` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__fields_values` (
  `field_id` int(10) unsigned NOT NULL DEFAULT 0,
  `item_id` varchar(255) NOT NULL DEFAULT '',
  `value` mediumtext,
  KEY `idx_field_id` (`field_id`),
  KEY `idx_item_id` (`item_id`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
