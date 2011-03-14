<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Kunena 1.6.0: Convert database timezone from (local+board_offset) to UTC
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

kimport ('factory');

function kunena_upgrade_160_timezone($parent) {
	$result = null;
	$config = KunenaFactory::getConfig ();
	// We need to fix all timestamps to UTC (if not already done)
	if ($config->board_ofset != '0.00') {
		$timeshift = ( float ) date ( 'Z' ) + (( float ) $config->board_ofset * 3600);

		$parent->db->setQuery ( "UPDATE #__kunena_categories SET time_last_msg = time_last_msg - {$timeshift}" );
		$parent->db->query ();
		if ($parent->db->getErrorNum ())
			throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

		$parent->db->setQuery ( "UPDATE #__kunena_sessions SET lasttime = lasttime - {$timeshift}, currvisit  = currvisit  - {$timeshift}" );
		$parent->db->query ();
		if ($parent->db->getErrorNum ())
			throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

		$parent->db->setQuery ( "UPDATE #__kunena_whoisonline SET time = time - {$timeshift}" );
		$parent->db->query ();
		if ($parent->db->getErrorNum ())
			throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

		$parent->db->setQuery ( "UPDATE #__kunena_messages SET time = time - {$timeshift}, modified_time = modified_time - {$timeshift}" );
		$parent->db->query ();
		if ($parent->db->getErrorNum ())
			throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

		$config->board_ofset = '0.00';
		$result = array('action'=>'', 'name'=>JText::sprintf ( 'COM_KUNENA_INSTALL_160_TIMEZONE', sprintf('%+d:%02d', $timeshift/3600, ($timeshift/60)%60)), 'success'=>true);
	}

	// Save configuration
	$config->remove ();
	$config->create ();
	return $result;
}