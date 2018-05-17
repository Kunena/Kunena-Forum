-- Kunena 2.0

ALTER TABLE `#__kunena_categories`
  CHANGE `pub_access` `pub_access` int(11) NOT NULL default '1';
ALTER TABLE `#__kunena_categories`
  CHANGE `admin_access` `admin_access` int(11) NOT NULL default '1';
