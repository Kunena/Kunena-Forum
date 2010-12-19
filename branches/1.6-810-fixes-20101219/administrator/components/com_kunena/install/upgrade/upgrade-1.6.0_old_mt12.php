<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Kunena 1.6.0: remove old non-standard Mootools 1.2 library
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

function kunena_upgrade_160_old_mt12($parent) {
	// Only needed for K1.6.0 ALPHA releases:

	jimport ( 'joomla.version' );
	$jversion = new JVersion ();
	if ($jversion->RELEASE == 1.5 && is_dir ( JPATH_ROOT . '/plugins/system/mootools12' )) {
		$query = "SELECT id FROM #__plugins WHERE element='mootools12'";
		$parent->db->setQuery ( $query );
		$id = $parent->db->loadResult ();
		if ($id) {
			jimport ( 'joomla.installer.installer' );
			$installer = new JInstaller ();
			$installer->uninstall ( 'plugin', $id );
			return array ('action' => '', 'name' => JText::_ ( 'COM_KUNENA_INSTALL_160_OLD_MT12' ), 'success' => true );
		}
	}
}