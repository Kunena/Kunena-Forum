<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Kunena 1.6.0: Convert attachments table to support new multi file attachments
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

function kunena_upgrade_160_attachments($parent) {
	// First check if attachments table has legacy field
	$fields = array_pop ( $parent->db->getTableFields ( '#__kunena_attachments' ) );
	if (! isset ( $fields ['filelocation'] )) {
		// Already converted, there is nothing to do
		return;
	}

	//Import filesystem libraries.
	jimport ( 'joomla.filesystem.folder' );

	$query = "DROP TABLE IF EXISTS `#__kunena_attachments_bak`";
	$parent->db->setQuery ( $query );
	$parent->db->query ();
	if ($parent->db->getErrorNum ())
		throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

	// Attachments table has filelocation - assume we have to convert attachments
	// hash and size ommited -> NULL
	$query = "RENAME TABLE `#__kunena_attachments` TO `#__kunena_attachments_bak`";
	$parent->db->setQuery ( $query );
	$parent->db->query ();
	if ($parent->db->getErrorNum ())
		throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

	$collation = $parent->db->getCollation ();
	if (!strstr($collation, 'utf8')) $collation = 'utf8_general_ci';
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
					KEY `filename` (`filename`) ) DEFAULT CHARACTER SET utf8 COLLATE {$collation};";
	$parent->db->setQuery ( $query );
	$parent->db->query ();
	if ($parent->db->getErrorNum ())
		throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

	$query = "INSERT INTO #__kunena_attachments (mesid, userid, folder, filetype, filename)
				SELECT a.mesid, m.userid,
					SUBSTRING_INDEX(SUBSTRING_INDEX(a.filelocation, '/', -4), '/', 3) AS folder,
					SUBSTRING_INDEX(a.filelocation, '.', -1) AS filetype,
					SUBSTRING_INDEX(a.filelocation, '/', -1) AS filename
				FROM #__kunena_attachments_bak AS a
				JOIN #__kunena_messages AS m ON a.mesid = m.id";
	$parent->db->setQuery ( $query );
	$parent->db->query ();
	if ($parent->db->getErrorNum ())
		throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

	if (JDEBUG == 1 && defined ( 'JFIREPHP' )) {
		FB::log ( $query, 'Attachment Upgrade' );
	}

	// By now the old attachmets table has been converted to the new Kunena 1.6 format
	// with the exception of file size and file hash that cannot be calculated inside
	// the database. Both of these columns are set to null. As we could be dealing with
	// thousands of medium to large size images, we cannot afford to iterate over all
	// of them to calculate this values. A seperate maintenance task will have to be
	// created and executed outside of the upgrade itself.

	return array('action'=>'', 'name'=>JText::_ ( 'COM_KUNENA_INSTALL_160_ATTACHMENTS' ), 'success'=>true);
}