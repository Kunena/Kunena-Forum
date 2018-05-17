-- FireBoard 1.0.2 => 1.0.3

ALTER TABLE `#__kunena_categories`
  ADD `headerdesc` text NOT NULL
  AFTER `description`;
ALTER TABLE `#__kunena_categories`
  ADD `class_sfx` varchar(20) NOT NULL
  AFTER `headerdesc`;
