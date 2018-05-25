-- Kunena 1.6.0 => 1.6.0

ALTER TABLE `#__kunena_messages`
  DROP INDEX `catid`;
ALTER TABLE `#__kunena_messages`
  DROP INDEX `parent`;
ALTER TABLE `#__kunena_messages`
  ADD INDEX `catid_parent` (`catid`, `parent`);
