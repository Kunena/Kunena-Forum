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

/**************************/
/* KUNENA FORUM INSTALLER */
/**************************/

$view = JRequest::getCmd ( 'view' );
$task = JRequest::getCmd ( 'task' );

clearstatcache();
if (file_exists(JPATH_COMPONENT.'/new/install/entrypoints/controller.php')) {
	// Pre-install controller (if there's existing installation)
	require_once (JPATH_COMPONENT.'/new/install/entrypoints/controller.php');

} elseif (file_exists(JPATH_COMPONENT.'/install/entrypoints/controller.php')) {
	// Pre-install controller (for new installations)
	require_once (JPATH_COMPONENT.'/install/entrypoints/controller.php');

} else {
	// Install controller
	require_once (JPATH_COMPONENT.'/install/controller.php');

}
$controller = new KunenaControllerInstall();
$controller->execute( $task );
$controller->redirect();
