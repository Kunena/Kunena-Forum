<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Kunena 1.6.0: remove old non-standard Mootools 1.2 library
function kunena_upgrade_160_old_mt12($parent) {
	if (version_compare(JVERSION, '1.6','<') && is_dir ( JPATH_ROOT . '/plugins/system/mootools12' )) {
		// Joomla 1.5: Only needed for K1.6.0 ALPHA releases
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