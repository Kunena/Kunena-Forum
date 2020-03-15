-- Kunena 1.6.0 => 1.6.0

ALTER TABLE `#__kunena_users`
  ADD KEY `banned` (`banned`);

ALTER TABLE `#__kunena_polls`
  MODIFY `polltimetolive` DATETIME NULL DEFAULT NULL;

ALTER TABLE `#__kunena_polls_options`
  MODIFY `text` varchar(100) NULL;

ALTER TABLE `#__kunena_thankyou`
  MODIFY `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `#__kunena_users_banned`
  MODIFY `expiration` DATETIME NULL;
ALTER TABLE `#__kunena_users_banned`
  MODIFY `created_time` DATETIME NOT NULL;
ALTER TABLE `#__kunena_users_banned`
  MODIFY `modified_time` DATETIME NULL;
