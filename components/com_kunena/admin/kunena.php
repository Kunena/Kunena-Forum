<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_kunena'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Check if installation hasn't been completed.
if (is_file(__DIR__ . '/install.php'))
{
	require_once __DIR__ . '/install.php';

	if (class_exists('KunenaControllerInstall'))
	{
		return;
	}
}

$app = JFactory::getApplication();

// Safety check to prevent fatal error if 'System - Kunena Forum' plug-in has been disabled.
if ($app->input->getCmd('view') == 'install' || !class_exists('KunenaForum') || !KunenaForum::isCompatible('4.0'))
{
	// Run installer instead..
	require_once __DIR__ . '/install/controller.php';

	$controller = new KunenaControllerInstall();

	// TODO: execute special task that checks what's wrong
	$controller->execute($app->input->getCmd('task'));
	$controller->redirect();

	return;
}

if ($app->input->getCmd('view') == 'uninstall')
{
	$allowed = $app->getUserState('com_kunena.uninstall.allowed');

	if ($allowed)
	{
		require_once __DIR__ . '/install/controller.php';
		$controller = new KunenaControllerInstall;
		$controller->execute('uninstall');
		$controller->redirect();

		$app->setUserState('com_kunena.uninstall.allowed', null);

		return;
	}
}

// Initialize Kunena Framework.
KunenaForum::setup();

// Initialize custom error handlers.
KunenaError::initialize();

// Kunena has been successfully installed: Load our main controller.
$controller = KunenaController::getInstance();
$controller->execute($app->input->getCmd('task'));
$controller->redirect();

// Remove custom error handlers.
KunenaError::cleanup();
