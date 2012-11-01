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

	// Check requirements.
	require_once dirname(__FILE__) . '/install.script.php';
	$installer = new Com_KunenaInstallerScript();
	$version = $installer_object->manifest->getElementByPath('version');
	if (!$installer->checkRequirements($version->data())) return false;

	$adminpath = JPATH_ADMINISTRATOR . '/components/com_kunena';

	// Remove old manifest files.
	$manifests = array('manifest.xml', 'kunena.j16.xml');
	foreach ($manifests as $filename) {
		if (JFile::exists("{$adminpath}/{$filename}")) JFile::delete("{$adminpath}/{$filename}");
	}

	// Create new bootstrap file for the installer and keep backup from the old version.
	if (JFile::exists("{$adminpath}/admin.kunena.php")) {
		$backuppath = "{$adminpath}/bak";
		if (JFolder::exists($backuppath)) JFolder::delete($backuppath);
		if (!JFolder::create($backuppath)) return false;
		$contents = file_get_contents("{$adminpath}/admin.kunena.php");
		if (!strstr($contents, '/* KUNENA FORUM INSTALLER */')) {
			JFile::move("{$adminpath}/admin.kunena.php", "{$backuppath}/admin.kunena.php");
		}
	}
	$success = JFile::copy("{$adminpath}/install/entrypoints/admin.kunena.php", "{$adminpath}/admin.kunena.php");
	if (!$success) return false;

	// Reset installer state.
	$app = JFactory::getApplication();
	$app->setUserState('kunena-old', 0);

	// This isn't called because of redirect.
	$installer_object->parent->copyManifest();

	// Redirect to Kunena Installer.
	$redirect_url = JURI::base () . 'index.php?option=com_kunena';
	header ( "HTTP/1.1 303 See Other" );
	header ( "Location: {$redirect_url}" );
}