ALTER TABLE `#__kunena_categories` CHANGE `checked_out_time` `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_announcement` CHANGE `created` `created` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_announcement` CHANGE `publish_up` `publish_up` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_announcement` CHANGE `publish_down` `publish_down` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_polls_users` CHANGE `lasttime` `lasttime` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_rate` CHANGE `time` `time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_thankyou` CHANGE `time` `time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users` CHANGE `banned` `banned` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_users_banned` CHANGE `created_time` `created_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__kunena_topics` CHANGE `params` `params` TEXT NULL;

UPDATE `#__kunena_categories` SET `checked_out_time` = NULL
WHERE `checked_out_time` = '0000-00-00 00:00:00'
   or `checked_out_time` = '1000-01-01 00:00:00';

UPDATE `#__kunena_announcement` SET `created` = CASE WHEN `created` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `created` END;
UPDATE `#__kunena_announcement` SET `publish_up` = CASE WHEN `publish_up` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `publish_up` END;
UPDATE `#__kunena_announcement` SET `publish_down` = CASE WHEN `publish_down` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `publish_down` END;
UPDATE `#__kunena_polls` SET `polltimetolive` = CASE WHEN `polltimetolive` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `polltimetolive` END;
UPDATE `#__kunena_polls_users` SET `lasttime` = CASE WHEN `lasttime` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `lasttime` END;
UPDATE `#__kunena_rate` SET `time` = CASE WHEN `time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `time` END;
UPDATE `#__kunena_thankyou` SET `time` = CASE WHEN `time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `time` END;
UPDATE `#__kunena_users_banned` SET `expiration` = CASE WHEN `expiration` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `expiration` END;
UPDATE `#__kunena_users_banned` SET `created_time` = CASE WHEN `created_time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `created_time` END;
UPDATE `#__kunena_users_banned` SET `modified_time` = CASE WHEN `modified_time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `modified_time` END;
