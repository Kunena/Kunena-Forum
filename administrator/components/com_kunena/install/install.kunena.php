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

// This isn't called because of redirect
$this->parent->copyManifest();

function com_install() {
	if (version_compare ( phpversion (), '5.0.0', '<' )) {
		// Installer would fail to load, so let's output an error.
		echo "ERROR: PHP 5.2 REQUIRED!";
		return false;
	}

	if (version_compare(JVERSION, '1.6','>')) {
		echo "ERROR: WRONG MANIFEST FILE LOADED, PLEASE TRY AGAIN WITH THE LATEST VERSION OF JOOMLA!";
		return false;
	}

	// Emulate J1.6 installer
	include_once(dirname(__FILE__).'/install.script.php');
	Com_KunenaInstallerScript::preflight( 'update', null );
	Com_KunenaInstallerScript::install ( null );
	$redirect_url = Com_KunenaInstallerScript::postflight( 'update', null );

	// Redirect to Kunena Installer
	header ( "HTTP/1.1 303 See Other" );
	header ( "Location: {$redirect_url}" );
}