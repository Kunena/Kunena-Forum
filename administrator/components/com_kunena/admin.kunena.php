<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Access check.
if (version_compare(JVERSION, '1.6', '>')) {
	if (!JFactory::getUser()->authorise('core.manage', 'com_kunena')) {
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

// Get view and task
$view = JRequest::getCmd ( 'view' );
$task = JRequest::getCmd ( 'task' );

// Akeeba Live Update
if($view == 'liveupdate') {
	require_once KPATH_ADMIN.'/liveupdate/liveupdate.php';
	LiveUpdate::handleRequest();
	return;
}

// Initialize Kunena (if Kunena System Plugin isn't enabled).
$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
if (file_exists($api)) require_once $api;

if ($view == 'install') {
	// Run Kunena installer.
	require_once (KPATH_ADMIN . '/install/controller.php');
	$controller = new KunenaControllerInstall();
	$controller->execute( $task );
	$controller->redirect();
	return;

} elseif (!class_exists('KunenaForum') || !KunenaForum::isCompatible('2.0') || !KunenaForum::installed()) {
	// Prapare installation if Kunena hasn't been fully installed.
	JFactory::getApplication ()->redirect(JURI::root(true).'/administrator/index.php?option=com_kunena&view=install&task=prepare&'.JUtility::getToken().'=1');

} else {
	// Check if latest version of Kunena has been installed. If not, prepare installation.
	require_once(KPATH_ADMIN.'/install/version.php');
	$kversion = new KunenaVersion();
	if (!$kversion->checkVersion()) {
		JFactory::getApplication ()->redirect(JURI::root(true).'/administrator/index.php?option=com_kunena&view=install&task=prepare&'.JUtility::getToken().'=1');
	}
}

// Load language files
KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
KunenaFactory::loadLanguage('com_kunena.install', 'admin');
KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');
KunenaFactory::loadLanguage('com_kunena.models', 'admin');
KunenaFactory::loadLanguage('com_kunena.views', 'admin');
// Load last to get deprecated language files to work
KunenaFactory::loadLanguage('com_kunena', 'site');
KunenaFactory::loadLanguage('com_kunena', 'admin');

KunenaForum::setup();

// Initialize error handlers
KunenaError::initialize ();

// Create Kunena user if current user doesn't have one
$kuser = KunenaUserHelper::getMyself ();
if (! $kuser->exists ()) {
	$kuser->save ();
}

// Set default view
if (!$view) JRequest::setVar( 'view', 'cpanel' );

// Kunena has been successfully installed: Load our main controller
$controller = KunenaController::getInstance();
$controller->execute( $task );
$controller->redirect();

KunenaError::cleanup ();
