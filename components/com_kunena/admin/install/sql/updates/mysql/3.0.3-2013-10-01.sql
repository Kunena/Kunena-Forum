-- Kunena 2.0.3 => 3.0.3

-- Optimize counting of daily activity statistics.
ALTER TABLE `#__kunena_messages` DROP INDEX `time`;
ALTER TABLE `#__kunena_messages` DROP INDEX `hold_time`;
ALTER TABLE `#__kunena_messages` ADD INDEX `time_hold` ( `time` , `hold` );
ALTER TABLE `#__kunena_messages` ADD INDEX `hold` ( `hold` );
