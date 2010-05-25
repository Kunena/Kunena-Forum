<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Kunena Delete deprecated template for 1.6.0
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

//Import filesystem libraries.
jimport ( 'joomla.filesystem.folder' );

$templatedeprecatedlist = array ('default_ex', 'default_green', 'default_red', 'default_gray' );

$kunena_db = JFactory::getDBO ();
$kunena_db->setQuery ( "SELECT template FROM #__kunena_config" );
$kactualtemplate = $kunena_db->loadResult ();
if ($kunena_db->getErrorNum () != 0) {
	if (in_array ( $kactualtemplate, $templatedeprecatedlist )) {
		$kunena_db->setQuery ( "UPDATE #__kunena_config SET template='default',templateimagepath='default'" );
		$kunena_db->query ();
	}
}
foreach ( $templatedeprecatedlist as $template ) {
	if (file_exists ( KUNENA_PATH_TEMPLATE . DS . $template )) {
		JFolder::delete ( KUNENA_PATH_TEMPLATE . DS . $template );
	}
}

// Convert attachments table to support new multi file attachments

// First check if attachments table has legacy field
$fields = array_pop ( $kunena_db->getTableFields ( '#__kunena_attachments' ) );
if (isset ( $fields ['filelocation'] )) {
	// Attachments table has filelocation - assume we have to convert attachments
	// hash and size ommited -> NULL
	$query = "RENAME TABLE `#__kunena_attachments` TO `#__kunena_attachments_bak`";
	$kunena_db->setQuery ( $query );
	$kunena_db->query ();
	$query = "CREATE TABLE IF NOT EXISTS `#__kunena_attachments` (
				`id` int(11) NOT NULL auto_increment,
				`mesid` int(11) NOT NULL default '0',
				`userid` int(11) NOT NULL default '0',
				`hash` char(32) NULL,
				`size` int(11) NULL,
				`folder` varchar(255) NOT NULL,
				`filetype` varchar(20) NOT NULL,
				`filename` varchar(255) NOT NULL,
					PRIMARY KEY (`id`),
					KEY `mesid` (`mesid`),
					KEY `userid` (`userid`),
					KEY `hash` (`hash`),
					KEY `filename` (`filename`) ) DEFAULT CHARSET=utf8;";
	$kunena_db->setQuery ( $query );
	$kunena_db->query ();
	$query = "INSERT INTO #__kunena_attachments (mesid, userid, folder, filetype, filename)
				SELECT a.mesid, m.userid,
					SUBSTRING_INDEX(SUBSTRING_INDEX(a.filelocation, '/', -4), '/', 3) AS folder,
					SUBSTRING_INDEX(a.filelocation, '.', -1) AS filetype,
					SUBSTRING_INDEX(a.filelocation, '/', -1) AS filename
				FROM #__kunena_attachments_bak AS a
				JOIN #__kunena_messages AS m ON a.mesid = m.id";

	if (JDEBUG == 1 && defined ( 'JFIREPHP' )) {
		FB::log ( $query, 'Attachment Upgrade' );
	}

	$kunena_db->setQuery ( $query );
	$kunena_db->query ();

// By now the old attachmets table has been converted to the new Kunena 1.6 format
// with the exception of file size and file hash that cannot be calculated inside
// the database. Both of these columns are set to null. As we could be dealing with
// thousands of medium to large size images, we cannot afford to iterate over all
// of them to calculate this values. A seperate maintenance task will have to be
// created and executed outside of the upgrade itself.
}