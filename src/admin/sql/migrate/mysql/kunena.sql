-- Table migration from Kunena 1.0 and 1.5

-- NOTE: if you run the script manually some queries may fail on older versions
-- but it doesn't matter as update queries will add the missing tables..

-- Kunena 1.0.6 - 1.5.14
CREATE TABLE `#__kunena_announcement`  SELECT *
                                       FROM `#__fb_announcement`;
ALTER TABLE `#__kunena_announcement`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_attachments`    SELECT *
                                        FROM `#__fb_attachments`;
ALTER TABLE `#__kunena_attachments`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_categories`    SELECT *
                                       FROM `#__fb_categories`;
ALTER TABLE `#__kunena_categories`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_favorites`    SELECT *
                                      FROM `#__fb_favorites`;
ALTER TABLE `#__kunena_favorites`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_groups`      SELECT *
                                     FROM `#__fb_groups`;
ALTER TABLE `#__kunena_groups`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_messages`    SELECT *
                                     FROM `#__fb_messages`;
ALTER TABLE `#__kunena_messages`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_messages_text`  SELECT *
                                        FROM `#__fb_messages_text`;
ALTER TABLE `#__kunena_messages_text`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_moderation`    SELECT *
                                       FROM `#__fb_moderation`;
ALTER TABLE `#__kunena_moderation`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_ranks`      SELECT *
                                    FROM `#__fb_ranks`;
ALTER TABLE `#__kunena_ranks`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_sessions`    SELECT *
                                     FROM `#__fb_sessions`;
ALTER TABLE `#__kunena_sessions`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_smileys`      SELECT *
                                      FROM `#__fb_smileys`;
ALTER TABLE `#__kunena_smileys`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_subscriptions`  SELECT *
                                        FROM `#__fb_subscriptions`;
ALTER TABLE `#__kunena_subscriptions`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_users`      SELECT *
                                    FROM `#__fb_users`;
ALTER TABLE `#__kunena_users`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_version`      SELECT *
                                      FROM `#__fb_version`;
ALTER TABLE `#__kunena_version`
  CHARACTER SET utf8;

CREATE TABLE `#__kunena_whoisonline`    SELECT *
                                        FROM `#__fb_whoisonline`;
ALTER TABLE `#__kunena_whoisonline`
  CHARACTER SET utf8;
