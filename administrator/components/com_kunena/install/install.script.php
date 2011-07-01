<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class Com_KunenaInstallerScript {

	function install($parent) {}

	function update($parent) {}

	function uninstall($parent) {
		require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
		$lang = JFactory::getLanguage();
		$lang->load('com_kunena.install',JPATH_ADMINISTRATOR);

		require_once(KPATH_ADMIN . '/install/model.php');
		$installer = new KunenaModelInstall();
		$installer->uninstallPlugin('system', 'kunena');
		$installer->deleteMenu();
	}

	function preflight($type, $parent) {
		// Remove deprecated manifest.xml (K1.5)
		$manifest = JPATH_ADMINISTRATOR . '/components/com_kunena/manifest.xml';
		if (is_file($manifest)) {
			jimport( 'joomla.filesystem.file' );
			JFile::delete($manifest);
		}
	}

	function postflight($type, $parent) {
		$installer = $parent->getParent();
		$installer->set('redirect_url', JURI::base () . 'index.php?option=com_kunena&view=install&task=prepare&'.JUtility::getToken().'=1');
	}
}