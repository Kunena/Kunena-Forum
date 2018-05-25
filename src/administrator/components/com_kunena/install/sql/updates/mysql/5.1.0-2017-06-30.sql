ALTER TABLE `#__kunena_categories`
  ADD `topictemplate` MEDIUMTEXT NOT NULL;
ALTER TABLE `#__kunena_categories`
  MODIFY COLUMN `topictemplate` MEDIUMTEXT NOT NULL
  AFTER `headerdesc`;
