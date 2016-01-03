ALTER TABLE `#__kunena_categories` CHANGE COLUMN `iconset` `iconset` varchar(255) NOT NULL default 'default';
UPDATE `#__kunena_categories` SET iconset='default' WHERE iconset='';
