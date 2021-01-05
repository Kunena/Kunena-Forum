-- Kunena 2.0

ALTER TABLE `#__kunena_categories`
  CHANGE `pubAccess` `pubAccess` int(11) NOT NULL default '1';
ALTER TABLE `#__kunena_categories`
  CHANGE `adminAccess` `adminAccess` int(11) NOT NULL default '1';
