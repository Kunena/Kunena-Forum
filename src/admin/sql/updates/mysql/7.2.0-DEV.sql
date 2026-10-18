ALTER TABLE `#__kunena_aliases` DROP INDEX `alias`, ADD PRIMARY KEY (`alias`);
DELETE FROM `#__kunena_polls_users` WHERE `pollid` IS NULL OR `userid` IS NULL;
ALTER TABLE `#__kunena_polls_users` MODIFY `pollid` int(11) NOT NULL, MODIFY `userid` int(11) NOT NULL, DROP INDEX `pollid`, ADD PRIMARY KEY (`pollid`, `userid`);
ALTER TABLE `#__kunena_thankyou` DROP INDEX `postid`, ADD PRIMARY KEY (`postid`, `userid`);
ALTER TABLE `#__kunena_user_read` DROP INDEX `user_topic_id`, ADD PRIMARY KEY (`user_id`, `topic_id`);
ALTER TABLE `#__kunena_user_topics` DROP INDEX `user_topic_id`, ADD PRIMARY KEY (`user_id`, `topic_id`);
