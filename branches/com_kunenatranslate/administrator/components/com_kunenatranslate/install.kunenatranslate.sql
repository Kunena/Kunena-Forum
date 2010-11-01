-- @version $Id$
-- Kunena Translate Component
-- 
-- @package	Kunena Translate
-- @Copyright (C) 2010 www.kunena.com All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.kunena.com

CREATE TABLE `#__kunenatranslate_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sheme` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sheme` (`sheme`)
)

CREATE TABLE `#__kunenatranslate_translation` (
  `labelid` int(11) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `translation` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `shemeid` (`labelid`,`lang`)
)
