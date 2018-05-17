-- Kunena 1.0.6+ => 1.0.8

UPDATE `#__kunena_users`
SET `view` = 'flat';

UPDATE `#__kunena_messages_text`
SET `message` = REPLACE(`message`, "com_fireboard", "com_kunena");

UPDATE `#__kunena_users`
SET `signature` = REPLACE(`signature`, "com_fireboard", "com_kunena");

-- Fix favorites table (remove duplicate rows and guests).
ALTER TABLE `#__kunena_favorites`
  DROP INDEX `thread`;
ALTER IGNORE TABLE `#__kunena_favorites`
  ADD UNIQUE `thread`(`thread`, `userid`);
DELETE
FROM `#__kunena_favorites`
WHERE `userid` = 0;

-- Fix subscriptions table (remove duplicate rows and guests).
ALTER TABLE `#__kunena_subscriptions`
  DROP INDEX `thread`;
ALTER IGNORE TABLE `#__kunena_subscriptions`
  ADD UNIQUE `thread`(`thread`, `userid`);
DELETE
FROM `#__kunena_subscriptions`
WHERE `userid` = 0;
