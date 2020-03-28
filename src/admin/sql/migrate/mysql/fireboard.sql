-- Table migration from FireBoard (any version)

-- NOTE: if you run the script manually some queries may fail on older versions
-- but it doesn't matter as update queries will add the missing tables..

-- FireBoard 1.0.0+
CREATE TABLE `#__kunena_announcement`  SELECT *
                                       FROM `#__fb_announcement`;
ALTER TABLE `#__kunena_announcement`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_attachments`    SELECT *
                                        FROM `#__fb_attachments`;
ALTER TABLE `#__kunena_attachments`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_categories`    SELECT *
                                       FROM `#__fb_categories`;
ALTER TABLE `#__kunena_categories`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_favorites`    SELECT *
                                      FROM `#__fb_favorites`;
ALTER TABLE `#__kunena_favorites`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_messages`    SELECT *
                                     FROM `#__fb_messages`;
ALTER TABLE `#__kunena_messages`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_messages_text`  SELECT *
                                        FROM `#__fb_messages_text`;
ALTER TABLE `#__kunena_messages_text`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_moderation`    SELECT *
                                       FROM `#__fb_moderation`;
ALTER TABLE `#__kunena_moderation`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_sessions`    SELECT *
                                     FROM `#__fb_sessions`;
ALTER TABLE `#__kunena_sessions`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_smileys`      SELECT *
                                      FROM `#__fb_smileys`;
ALTER TABLE `#__kunena_smileys`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_subscriptions`  SELECT *
                                        FROM `#__fb_subscriptions`;
ALTER TABLE `#__kunena_subscriptions`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_users`      SELECT *
                                    FROM `#__fb_users`;
ALTER TABLE `#__kunena_users`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `#__kunena_whoisonline`    SELECT *
                                        FROM `#__fb_whoisonline`;
ALTER TABLE `#__kunena_whoisonline`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- FireBoard 1.0.1+
CREATE TABLE `#__kunena_groups`      SELECT *
                                     FROM `#__fb_groups`;
ALTER TABLE `#__kunena_groups`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- FireBoard 1.0.3+
CREATE TABLE `#__kunena_ranks`      SELECT *
                                    FROM `#__fb_ranks`;
ALTER TABLE `#__kunena_ranks`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- FireBoard 1.0.5RC1+
CREATE TABLE `#__kunena_version`      SELECT *
                                      FROM `#__fb_version`;
ALTER TABLE `#__kunena_version`
   CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
