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

include_once (KUNENA_PATH .DS. "class.kunena.php");

// Add custom upgrade code here
// Most or all sql statements should be covered within comupgrade.xml

//Import filesystem libraries.
jimport ( 'joomla.filesystem.folder' );

$templatedeprecatedlist = array ('default_ex', 'default_green', 'default_red', 'default_gray' );

$kunena_db = & JFactory::getDBO ();
$kunena_db->setQuery ( "SELECT template FROM #__fb_config" );
$kactualtemplate = $kunena_db->loadResult ();
if ($kunena_db->getErrorNum() != 0)
{
	if (in_array ( $kactualtemplate, $templatedeprecatedlist )) {
		$kunena_db->setQuery ( "UPDATE #__fb_config SET template='default',templateimagepath='default'" );
		$kunena_db->query ();
	}
}
foreach ( $templatedeprecatedlist as $template ) {
	if (file_exists ( KUNENA_PATH_TEMPLATE . DS . $template )) {
		JFolder::delete ( KUNENA_PATH_TEMPLATE . DS . $template );
	}
}

// Convert attachments table to support new multi file attachments

// First check if new attachments table is empty. This either means this is the first
// time the upgrade is executed, or the table has been manually reset to force another
// upgrade of the old structure.
$query = 'SELECT count(*) FROM #__kunena_attachments;';
$kunena_db->setQuery ( $query );
$attachcount = (int) $kunena_db->loadResult ();

if ($attachcount==0){
	// New attachments table is empty - assume we have to convert attachments

	// hash and size ommited -> NULL
	$query = "INSERT INTO #__kunena_attachments (mesid, userid, folder, filetype, filename)
				SELECT a.mesid, m.userid,
					SUBSTRING_INDEX(SUBSTRING_INDEX(a.filelocation, '/', -4), '/', 3) AS folder,
					SUBSTRING_INDEX(a.filelocation, '.', -1) AS filetype,
					SUBSTRING_INDEX(a.filelocation, '/', -1) AS filename
				FROM #__fb_attachments AS a
				JOIN #__fb_messages AS m ON a.mesid = m.id";

	if(JDEBUG == 1 && defined('JFIREPHP')){
		FB::log($query, 'Attachment Upgrade');
	}

	$kunena_db->setQuery ( $query );
	$kunena_db->query();

	// By now the old attachmets table has been converted to the new Kunena 1.6 format
	// with the exception of file size and file hash that cannot be calculated inside
	// the database. Both of these columns are set to null. As we could be dealing with
	// thousands of medium to large size images, we cannot afford to iterate over all
	// of them to calculate this values. A seperate maintenance task will have to be
	// created and executed outside of the upgrade itself.
}

