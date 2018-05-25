-- Kunena 2.0

ALTER TABLE `#__kunena_categories`
  MODIFY `ordering` smallint(6) NOT NULL default '0'
  AFTER `admin_recurse`; -- Also new in Kunena 1.6.4
ALTER TABLE `#__kunena_categories`
  DROP `alert_admin`;
ALTER TABLE `#__kunena_categories`
  DROP `moderators`;
ALTER TABLE `#__kunena_categories`
  DROP `future2`;
ALTER TABLE `#__kunena_categories`
  DROP `id_last_msg`;
ALTER TABLE `#__kunena_categories`
  DROP `time_last_msg`;
ALTER TABLE `#__kunena_categories`
  DROP INDEX `parent`;
ALTER TABLE `#__kunena_categories`
  DROP INDEX `msg_id`;
ALTER TABLE `#__kunena_categories`
  CHANGE `parent` `parent_id` INT(11) NULL DEFAULT '0';
ALTER TABLE `#__kunena_categories`
  CHANGE `cat_emoticon` `icon_id` TINYINT(4) NOT NULL DEFAULT '0';
ALTER TABLE `#__kunena_categories`
  ADD `last_topic_id` int(11) NOT NULL default '0'
  AFTER `numPosts`;
ALTER TABLE `#__kunena_categories`
  ADD `last_post_id` int(11) NOT NULL default '0'
  AFTER `last_topic_id`;
ALTER TABLE `#__kunena_categories`
  ADD `last_post_time` int(11) NOT NULL default '0'
  AFTER `last_post_id`;
ALTER TABLE `#__kunena_categories`
  ADD `params` text NOT NULL
  AFTER `last_post_time`;
ALTER TABLE `#__kunena_categories`
  ADD `channels` TEXT NULL DEFAULT NULL
  AFTER `published`;
ALTER TABLE `#__kunena_categories`
  ADD `topic_ordering` VARCHAR(16) NOT NULL default 'lastpost'
  AFTER `allow_polls`;
ALTER TABLE `#__kunena_categories`
  ADD KEY `parent_id` (parent_id);

ALTER TABLE `#__kunena_topics`
  ADD INDEX `posts` (`posts`);

ALTER TABLE `#__kunena_messages`
  MODIFY `ip` varchar(128) NULL
  AFTER `time`;

ALTER TABLE `#__kunena_thankyou`
  MODIFY `time` datetime NOT NULL;

ALTER TABLE `#__kunena_users`
  ADD INDEX `moderator` (`moderator`); -- Also new in Kunena 1.6.4
ALTER TABLE `#__kunena_users`
  CHANGE `ICQ` `icq` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `AIM` `aim` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `YIM` `yim` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `MSN` `msn` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `SKYPE` `skype` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `TWITTER` `twitter` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `FACEBOOK` `facebook` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `GTALK` `gtalk` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `MYSPACE` `myspace` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `LINKEDIN` `linkedin` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `DELICIOUS` `delicious` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `FRIENDFEED` `friendfeed` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `DIGG` `digg` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `BLOGSPOT` `blogspot` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `FLICKR` `flickr` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users`
  CHANGE `BEBO` `bebo` VARCHAR(50) NULL DEFAULT NULL;

ALTER TABLE `#__kunena_user_read`
  MODIFY `time` datetime NOT NULL;


INSERT IGNORE INTO `#__kunena_user_topics` (user_id, topic_id, category_id, posts, last_post_id, owner)
SELECT userid                    AS user_id,
       thread                    AS topic_id,
       catid                     AS category_id,
       COUNT(*)                  AS posts,
       MAX(id)                   AS last_post_id,
       MAX(IF(parent = 0, 1, 0)) AS owner
FROM `#__kunena_messages`
WHERE userid > 0
  AND moved = 0
  AND hold = 0
GROUP BY user_id, topic_id;

INSERT INTO `#__kunena_user_topics` (user_id, topic_id, category_id, favorite)
SELECT f.userid AS user_id,
       m.thread AS topic_id,
       m.catid  AS category_id,
       1        AS favorite
FROM `#__kunena_favorites` AS f
  INNER JOIN `#__kunena_messages` AS m ON m.id = f.thread
WHERE f.userid > 0
ON DUPLICATE KEY UPDATE favorite = 1;

INSERT INTO `#__kunena_user_topics` (user_id, topic_id, category_id, subscribed)
SELECT s.userid AS user_id,
       m.thread AS topic_id,
       m.catid  AS category_id,
       1        AS subscribed
FROM `#__kunena_subscriptions` AS s
  INNER JOIN `#__kunena_messages` AS m ON m.id = s.thread
WHERE s.userid > 0
ON DUPLICATE KEY UPDATE subscribed = 1;

INSERT INTO `#__kunena_user_categories` (user_id, category_id, subscribed)
SELECT userid AS user_id,
       catid  AS category_id,
       1      AS subscribed
FROM `#__kunena_subscriptions_categories`
WHERE userid > 0
ON DUPLICATE KEY UPDATE subscribed = VALUES(subscribed);

UPDATE `#__kunena_user_categories` AS c INNER JOIN `#__kunena_sessions` AS s ON c.user_id = s.userid
SET c.allreadtime = FROM_UNIXTIME(s.lasttime);

UPDATE `#__kunena_messages`
SET hold = 3
WHERE parent = 0
  AND hold = 2;

INSERT IGNORE INTO `#__kunena_topics` (id, category_id, subject, icon_id, locked, hold, ordering, hits, poll_id,
                          moved_id)
SELECT a.id,
       a.catid,
       a.subject,
       a.topic_emoticon,
       a.locked,
       a.hold,
       a.ordering,
       a.hits,
       p.id AS poll_id,
       0    as moved_id
FROM `#__kunena_messages` AS a
  LEFT JOIN `#__kunena_polls` AS p ON p.threadid = a.id
WHERE a.parent = 0
  AND a.moved = 0
  AND a.id = a.thread
GROUP BY a.id;


DROP TABLE IF EXISTS `#__kunena_attachments_bak`;
DROP TABLE IF EXISTS `#__kunena_favorites`;
DROP TABLE IF EXISTS `#__kunena_groups`;
DROP TABLE IF EXISTS `#__kunena_subscriptions`;
DROP TABLE IF EXISTS `#__kunena_subscriptions_categories`;
