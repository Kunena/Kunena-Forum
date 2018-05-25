-- FireBoard 1.0.1 => 1.0.2

ALTER TABLE `#__kunena_categories`
  MODIFY `moderated` tinyint(4) NOT NULL default '1'
  AFTER `alert_admin`;
ALTER TABLE `#__kunena_categories`
  ADD `id_last_msg` int(10) NOT NULL DEFAULT '0'
  AFTER `description`;
ALTER TABLE `#__kunena_categories`
  ADD `numTopics` mediumint(8) NOT NULL DEFAULT '0'
  AFTER `id_last_msg`;
ALTER TABLE `#__kunena_categories`
  ADD `numPosts` mediumint(8) NOT NULL DEFAULT '0'
  AFTER `numTopics`;
ALTER TABLE `#__kunena_categories`
  ADD `time_last_msg` int(11) NULL
  AFTER `numPosts`;
ALTER TABLE `#__kunena_categories`
  ADD KEY `msg_id`(`id_last_msg`);
UPDATE `#__kunena_categories`
SET `moderated` = '1';

ALTER TABLE `#__kunena_messages`
  ADD `modified_by` int(7) NULL
  AFTER `moved`;
ALTER TABLE `#__kunena_messages`
  ADD `modified_time` int(11) NULL
  AFTER `modified_by`;
ALTER TABLE `#__kunena_messages`
  ADD `modified_reason` tinytext NULL
  AFTER `modified_time`;

ALTER TABLE `#__kunena_users`
  ADD `personalText` tinytext NULL
  AFTER `uhits`;
ALTER TABLE `#__kunena_users`
  ADD `gender` tinyint(4) NOT NULL DEFAULT '0'
  AFTER `personalText`;
ALTER TABLE `#__kunena_users`
  ADD `birthdate` date NOT NULL DEFAULT '0001-01-01'
  AFTER `gender`;
ALTER TABLE `#__kunena_users`
  ADD `location` varchar(50) NULL
  AFTER `birthdate`;
ALTER TABLE `#__kunena_users`
  ADD `ICQ` varchar(50) NULL
  AFTER `location`;
ALTER TABLE `#__kunena_users`
  ADD `AIM` varchar(50) NULL
  AFTER `ICQ`;
ALTER TABLE `#__kunena_users`
  ADD `YIM` varchar(50) NULL
  AFTER `AIM`;
ALTER TABLE `#__kunena_users`
  ADD `MSN` varchar(50) NULL
  AFTER `YIM`;
ALTER TABLE `#__kunena_users`
  ADD `SKYPE` varchar(50) NULL
  AFTER `MSN`;
ALTER TABLE `#__kunena_users`
  ADD `hideEmail` tinyint(1) NOT NULL DEFAULT '1'
  AFTER `SKYPE`;
ALTER TABLE `#__kunena_users`
  ADD `showOnline` tinyint(1) NOT NULL DEFAULT '1'
  AFTER `hideEmail`;
ALTER TABLE `#__kunena_users`
  ADD `rank` tinyint(4) NOT NULL DEFAULT '0'
  AFTER `showOnline`;
ALTER TABLE `#__kunena_users`
  ADD `GTALK` varchar(50) NULL
  AFTER `rank`;
ALTER TABLE `#__kunena_users`
  ADD `websitename` varchar(50) NULL
  AFTER `GTALK`;
ALTER TABLE `#__kunena_users`
  ADD `websiteurl` varchar(50) NULL
  AFTER `websitename`;
