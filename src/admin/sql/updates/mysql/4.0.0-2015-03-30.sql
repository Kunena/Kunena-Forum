ALTER TABLE `#__kunena_categories`
  ADD COLUMN `iconset` varchar(255) NOT NULL  DEFAULT ''
  AFTER `topicOrdering`;
