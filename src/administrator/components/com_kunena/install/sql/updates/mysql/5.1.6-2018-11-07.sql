ALTER TABLE `#__kunena_announcement` MODIFY COLUMN `created` datetime NOT NULL default
  '1000-01-01 00:00:00';

UPDATE `#__kunena_announcement`
SET created='1000-01-01 00:00:00'
WHERE created = '0000-00-00 00:00:00';

ALTER TABLE `#__kunena_announcement` MODIFY COLUMN `publish_up` datetime NOT NULL default
  '1000-01-01 00:00:00';

UPDATE `#__kunena_announcement`
SET publish_up='1000-01-01 00:00:00'
WHERE publish_up = '0000-00-00 00:00:00';

ALTER TABLE `#__kunena_announcement` MODIFY COLUMN `publish_down` datetime NOT NULL default
  '1000-01-01 00:00:00';

UPDATE `#__kunena_announcement`
SET publish_down='1000-01-01 00:00:00'
WHERE publish_down = '0000-00-00 00:00:00';

ALTER TABLE `#__kunena_categories` MODIFY COLUMN `checked_out_time` datetime NOT NULL default
  '1000-01-01 00:00:00';

UPDATE `#__kunena_announcement`
SET checked_out_time='1000-01-01 00:00:00'
WHERE checked_out_time = '0000-00-00 00:00:00'

ALTER TABLE `#__kunena_polls_users` MODIFY COLUMN `lasttime` datetime NOT NULL default
  '1000-01-01 00:00:00';

UPDATE `#__kunena_announcement`
SET lasttime='1000-01-01 00:00:00'
WHERE lasttime = '0000-00-00 00:00:00';
