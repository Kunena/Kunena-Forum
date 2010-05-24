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

jimport ( 'joomla.version' );
$jversion = new JVersion ();
if ($jversion->RELEASE == 1.5) {
	function com_install() {
		// Redirect to Kunena Installer
		ignore_user_abort ( true );
		header ( "HTTP/1.1 303 See Other" );
		header ( "Location: " . JURI::base () . "index.php?option=com_kunena&view=install" );
		Com_KunenaInstallerScript::install ( null );
	}

	function com_uninstall() {
		Com_KunenaInstallerScript::uninstall ( null );
	}
}

class Com_KunenaInstallerScript {

	function install($parent) {
		$app = JFactory::getApplication();
		$app->setUserState('com_kunena.install.step', 0);
	}

	function update($parent) {
		self::install($parent);
	}

	function uninstall($parent) {
		// Kunena wide defines
		require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');
		require_once (KUNENA_PATH .DS. 'class.kunena.php');

		$lang = JFactory::getLanguage();
		$lang->load('com_kunena', JPATH_COMPONENT);
		if ($jversion->RELEASE == 1.5) {
			CKunenaTools::DeleteMenuJ15();
		} else {
			CKunenaTools::DeleteMenuJ16();
		}
	}

	function preflight($type, $parent) {}

	function postflight($type, $parent) {
		$parent->getParent()->set('redirect_url', JURI::base () . 'index.php?option=com_kunena&view=install');
	}
}