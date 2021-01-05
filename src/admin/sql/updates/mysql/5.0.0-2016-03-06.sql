ALTER TABLE `#__kunena_categories`
  ADD `allowRatings` tinyint(4) NOT NULL default '0'
  AFTER `last_post_time`;
