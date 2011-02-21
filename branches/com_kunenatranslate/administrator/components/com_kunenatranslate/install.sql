-- @version $Id$
-- Kunena Translate Component
-- 
-- @package	Kunena Translate
-- @Copyright (C) 2010 www.kunena.com All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.kunena.com

CREATE TABLE IF NOT EXISTS `#__kunenatranslate_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(80) NOT NULL,
  `client` varchar(30) NOT NULL,
  `extension` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `label` (`label`),
  KEY `client` (`client`),
  KEY `extension` (`extension`)
)ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__kunenatranslate_translation` (
  `labelid` int(11) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `translation` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `labelid` (`labelid`,`lang`)
)ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__kunenatranslate_extension` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `filename` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM;

--
-- Daten für Tabelle `#__kunenatranslate_extension`
--

INSERT INTO `#__kunenatranslate_extension` (`id`, `name`, `filename`) VALUES
(1, 'Kunena', 'kunena.xml');
