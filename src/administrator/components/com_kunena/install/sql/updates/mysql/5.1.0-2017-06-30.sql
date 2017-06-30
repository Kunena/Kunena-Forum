ALTER TABLE `#__kunena_categories` ADD `topictemplate` MEDIUMINT NOT NULL;
ALTER TABLE `#__kunena_categories` MODIFY COLUMN `topictemplate` MEDIUMINT NOT NULL AFTER `headerdesc`;
