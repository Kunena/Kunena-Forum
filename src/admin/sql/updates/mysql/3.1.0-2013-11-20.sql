-- Change #__kunena_user_categories.datetime to integer (safe to be run more than once).

ALTER TABLE `#__kunena_user_categories`
  ADD `allreadtime_new` INT NOT NULL DEFAULT '0'
  AFTER `allreadtime`;
UPDATE `#__kunena_user_categories`
SET `allreadtime_new` = if(UNIX_TIMESTAMP(`allreadtime`), UNIX_TIMESTAMP(`allreadtime`), `allreadtime`);
ALTER TABLE `#__kunena_user_categories`
  DROP `allreadtime`;
ALTER TABLE `#__kunena_user_categories`
  CHANGE `allreadtime_new` `allreadtime` INT(11) NOT NULL DEFAULT '0';

-- Reset all categories read time to last visit time for all users.
UPDATE `#__kunena_user_categories` AS c INNER JOIN `#__kunena_sessions` AS s ON c.user_id = s.userid
SET c.allreadtime = s.lasttime
WHERE c.category_id = 0
  AND c.allreadtime < s.lasttime;
