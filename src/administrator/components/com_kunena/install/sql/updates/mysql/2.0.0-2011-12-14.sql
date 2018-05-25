-- Kunena 2.0

ALTER TABLE `#__kunena_categories`
  DROP `moderated`;
ALTER TABLE `#__kunena_categories`
  DROP `last_topic_subject`;
ALTER TABLE `#__kunena_categories`
  DROP `last_topic_posts`;
ALTER TABLE `#__kunena_categories`
  DROP `last_post_userid`;
ALTER TABLE `#__kunena_categories`
  DROP `last_post_message`;
ALTER TABLE `#__kunena_categories`
  DROP `last_post_guest_name`;
ALTER TABLE `#__kunena_categories`
  ADD `alias` varchar(255) NOT NULL
  AFTER `name`;


DROP TABLE IF EXISTS `#__kunena_config`;
DROP TABLE IF EXISTS `#__kunena_config_backup`;
DROP TABLE IF EXISTS `#__kunena_moderation`;
DROP TABLE IF EXISTS `#__kunena_whoisonline`;
