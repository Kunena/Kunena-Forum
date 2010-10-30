<?php
/**
 * @version $Id$
 * Kunena Forum Importer Component
 * @package com_kunenaimporter
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2009 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
defined ( '_JEXEC' ) or die ();

// Initialize the database
$db = & JFactory::getDBO ();
$update_queries = array ("CREATE TABLE IF NOT EXISTS `#__kunenaimporter_users` (
  `extid` int(11) NOT NULL auto_increment,
  `extusername` varchar(150) NOT NULL default '',
  `id` int(11),
  `name` varchar(255) default '',
  `username` varchar(150) default '',
  `email` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `usertype` varchar(25) NOT NULL default '',
  `block` tinyint(4) NOT NULL default '0',
  `gid` tinyint(3) unsigned NOT NULL default '1',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  `conflict` text,
  `error` text,
  PRIMARY KEY  (`extid`),
  KEY `extusername` (`extusername`),
  KEY `id` (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) DEFAULT CHARSET=utf8;", "DROP TABLE IF EXISTS `#__knimporter_user`", "DROP TABLE IF EXISTS `#__knimport_extuser`", "DROP TABLE IF EXISTS `#__knimporter_extuser`" );

// Perform all queries - we don't care if it fails
foreach ( $update_queries as $query ) {
	$db->setQuery ( $query );
	$db->query ();
	if ($db->getErrorNum ())
		die ( "<br />Invalid query:<br />$query<br />" . $db->getErrorMsg () );
}