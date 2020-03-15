-- FireBoard 1.0.3 => 1.0.4

ALTER TABLE `#__kunena_sessions`
  ADD `currvisit` int(11) NOT NULL default '0'
  AFTER `readtopics`;
