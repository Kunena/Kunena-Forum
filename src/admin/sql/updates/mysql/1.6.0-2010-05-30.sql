-- Kunena 1.0.8+ => 1.6.0

ALTER TABLE `#__kunena_users`
  ADD `TWITTER` VARCHAR(50) NULL
  AFTER `SKYPE`;
ALTER TABLE `#__kunena_users`
  ADD `FACEBOOK` VARCHAR(50) NULL
  AFTER `TWITTER`;
ALTER TABLE `#__kunena_users`
  ADD `MYSPACE` VARCHAR(50) NULL
  AFTER `GTALK`;
ALTER TABLE `#__kunena_users`
  ADD `LINKEDIN` VARCHAR(50) NULL
  AFTER `MYSPACE`;
ALTER TABLE `#__kunena_users`
  ADD `DELICIOUS` VARCHAR(50) NULL
  AFTER `LINKEDIN`;
ALTER TABLE `#__kunena_users`
  ADD `FRIENDFEED` VARCHAR(50) NULL
  AFTER `DELICIOUS`;
ALTER TABLE `#__kunena_users`
  ADD `DIGG` VARCHAR(50) NULL
  AFTER `FRIENDFEED`;
ALTER TABLE `#__kunena_users`
  ADD `BLOGSPOT` VARCHAR(50) NULL
  AFTER `DIGG`;
ALTER TABLE `#__kunena_users`
  ADD `FLICKR` VARCHAR(50) NULL
  AFTER `BLOGSPOT`;
ALTER TABLE `#__kunena_users`
  ADD `BEBO` VARCHAR(50) NULL
  AFTER `FLICKR`;

ALTER TABLE `#__kunena_categories`
  ADD `allow_polls` tinyint(4) NOT NULL default '0'
  AFTER `class_sfx`;
ALTER TABLE `#__kunena_categories`
  ADD `allow_anonymous` TINYINT NOT NULL DEFAULT '0'
  AFTER `review`;
ALTER TABLE `#__kunena_categories`
  ADD `post_anonymous` TINYINT NOT NULL DEFAULT '0'
  AFTER `allow_anonymous`;

ALTER TABLE `#__kunena_sessions`
  ADD KEY `currvisit` (`currvisit`);

UPDATE `#__kunena_messages`
SET thread = id
WHERE parent = 0;
