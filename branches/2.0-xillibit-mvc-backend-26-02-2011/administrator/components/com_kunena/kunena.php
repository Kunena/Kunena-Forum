<?php
/**
 * @version $Id: kunena.php 4381 2011-02-05 20:55:31Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// no direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

$view = JRequest::getCmd ( 'view' );
$task = JRequest::getCmd ( 'task' );

require_once(KPATH_ADMIN.'/install/version.php');
$kversion = new KunenaVersion();
if ($view != 'install' && !$kversion->checkVersion()) {
	$app = JFactory::getApplication ();
	$app->redirect(JURI::root().'administrator/index.php?option=com_kunena&view=install');
}

if ($view) {
	if ($view == 'install') {
		require_once (KPATH_ADMIN . '/install/controller.php');
		$controller = new KunenaControllerInstall();
	} else {
		kimport ('kunena.controller');
		$controller = KunenaController::getInstance();
	}
	$controller->execute( $task );
	$controller->redirect();
	return;
} else {
	$view = JRequest::setVar( 'view', 'cpanel' );
	kimport ('kunena.controller');
	$controller = KunenaController::getInstance();
	$controller->execute( $task );
	$controller->redirect();
}