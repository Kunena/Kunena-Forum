<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

class Com_KunenaInstallerScript {

	function install($parent) {
		$app = JFactory::getApplication();
		$app->setUserState('com_kunena.install.step', 0);
	}

	function update($parent) {
		self::install($parent);
	}

	function uninstall($parent) {
		require_once (JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_kunena' .DS. 'api.php');
		$lang = JFactory::getLanguage();
		$lang->load('com_kunena.install');
		$lang->load('com_kunena.install',KPATH_ADMIN);

		require_once(KPATH_ADMIN . '/install/model.php');
		$installer = new KunenaModelInstall();
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
		$parent->getParent()->set('redirect_url', JURI::base () . 'index.php?option=com_kunena&view=install');
	}
}