<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_kunena')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Check if installation hasn't been completed.
if (file_exists(__DIR__ . '/install.php')) {
	require_once __DIR__ . '/install.php';
	if (class_exists('KunenaControllerInstall')) return;
}

// Safety check to prevent fatal error if 'System - Kunena Forum' plug-in has been disabled.
if (JRequest::getCmd('view') == 'install' || !class_exists('KunenaForum') || !KunenaForum::isCompatible('3.0')) {
	// Run installer instead..
	require_once __DIR__ . '/install/controller.php';
	$controller = new KunenaControllerInstall();
	// TODO: execute special task that checks what's wrong
	$controller->execute(JRequest::getCmd('task'));
	$controller->redirect();
	return;
}

// Initialize Kunena Framework.
KunenaForum::setup();

// Initialize custom error handlers.
KunenaError::initialize();

// Kunena has been successfully installed: Load our main controller.
$controller = KunenaController::getInstance();
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

// Remove custom error handlers.
KunenaError::cleanup();
