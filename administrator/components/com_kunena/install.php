<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**************************/
/* KUNENA FORUM INSTALLER */
/**************************/

$view = JRequest::getCmd ( 'view' );
$task = JRequest::getCmd ( 'task' );

// Special case for developer versions.
if ($view != 'install' && class_exists('KunenaForum') && KunenaForum::isDev()) {
	// Developer version found: Check if latest version of Kunena has been installed. If not, prepare installation.
	require_once __DIR__ . '/install/version.php';
	$kversion = new KunenaVersion();
	if (!$kversion->checkVersion()) {
		JFactory::getApplication()->redirect(JUri::base(true).'/index.php?option=com_kunena&view=install');
	}
	return;
}

// Run the installer...
require_once __DIR__ . '/install/controller.php';
$controller = new KunenaControllerInstall();
$controller->execute( $task );
$controller->redirect();
