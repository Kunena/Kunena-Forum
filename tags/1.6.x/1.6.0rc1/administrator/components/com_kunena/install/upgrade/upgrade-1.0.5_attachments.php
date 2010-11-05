<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Kunena 1.0.5: convert old attachments from FB < 1.0.2
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

function kunena_upgrade_105_attachments($parent) {
	// First check if attachments table has legacy field
	$fields = array_pop ( $parent->db->getTableFields ( '#__kunena_attachments' ) );
	if (! isset ( $fields ['filelocation'] )) {
		// Already converted, there is nothing to do
		return;
	}
	$root = strtr ( JPATH_ROOT, "\\", "/" );
	// now lets do some checks and upgrades to 1.0.2 version of attachment table
	$parent->db->setQuery ( "SELECT COUNT(*) FROM #__kunena_attachments WHERE filelocation LIKE '%com_fireboard/uploaded%'", 0, 1 );

	// if >0 then it means we are on fb version below 1.0.2
	$is_101_version = $parent->db->loadResult ();
	if ($parent->db->getErrorNum ())
		throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

	$done = false;
	if ($is_101_version) {
		// now do the upgrade
		$parent->db->setQuery ( "UPDATE #__kunena_attachments SET filelocation = REPLACE(filelocation,'{$root}/components/com_fireboard/uploaded','/images/fbfiles');" );
		if ($parent->db->query ()) {
			$done = true;
		} else {
			if ($parent->db->getErrorNum ())
				throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
		}

		$parent->db->setQuery ( "UPDATE #__kunena_messages_text SET message = REPLACE(message,'/components/com_fireboard/uploaded','/images/fbfiles');" );
		if ($parent->db->query ()) {
			$done = true;
		} else {
			if ($parent->db->getErrorNum ())
				throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
		}
	}
	if ($done) {
		return array('action'=>'', 'name'=>JText::_ ( 'COM_KUNENA_INSTALL_105_ATTACHMENTS' ), 'success'=>true);
	}
}