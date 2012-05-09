<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport( 'joomla.filesystem.file' );

global $installer_object;
if (isset($this)) $installer_object = $this;

function com_install() {
	global $installer_object;

	if (!isset($installer_object) || version_compare(JVERSION, '1.6','>')) {
		JFactory::getApplication()->enqueueMessage('Oops, Joomla! 1.5 manifest file loaded! Aborting installation.', 'notice');
		return false;
	}

	// Check requirements
	require_once dirname(__FILE__) . '/install.script.php';
	$installer = new Com_KunenaInstallerScript();
	$version = $installer_object->manifest->getElementByPath('version');
	if (!$installer->checkRequirements($version->data())) return false;

	// Initialise Kunena installer
	$installer->install();

	// Remove deprecated manifest.xml (K1.5) and kunena.j16.xml (K1.7)
	$manifest = JPATH_ADMINISTRATOR . '/components/com_kunena/manifest.xml';
	if (JFile::exists($manifest)) JFile::delete($manifest);
	$manifest = JPATH_ADMINISTRATOR . '/components/com_kunena/kunena.j16.xml';
	if (JFile::exists($manifest)) JFile::delete($manifest);

	// This isn't called because of redirect
	$installer_object->parent->copyManifest();

	// Redirect to Kunena Installer
	$redirect_url = JURI::base () . 'index.php?option=com_kunena&view=install';
	header ( "HTTP/1.1 303 See Other" );
	header ( "Location: {$redirect_url}" );
}