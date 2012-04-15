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

jimport( 'joomla.filesystem.file' );

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

	// Initialise Kunena installer
	require_once(JPATH_ADMINISTRATOR . '/components/com_kunena/install/model.php');
	$installer = new KunenaModelInstall();
	$installer->install();

	// Remove deprecated manifest.xml (K1.5) and kunena.j16.xml (K1.7)
	$manifest = JPATH_ADMINISTRATOR . '/components/com_kunena/manifest.xml';
	if (JFile::exists($manifest)) JFile::delete($manifest);
	$manifest = JPATH_ADMINISTRATOR . '/components/com_kunena/kunena.j16.xml';
	if (JFile::exists($manifest)) JFile::delete($manifest);

	// Redirect to Kunena Installer
	$redirect_url = JURI::base () . 'index.php?option=com_kunena&view=install';
	header ( "HTTP/1.1 303 See Other" );
	header ( "Location: {$redirect_url}" );
}