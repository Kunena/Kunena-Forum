<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Administrator
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_kunena'))
{
	throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 401);
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

// Display time it took to create the entire page in the footer.
$kunena_profiler = KunenaProfiler::instance('Kunena');
$kunena_profiler->start('Total Time');
KUNENA_PROFILER ? $kunena_profiler->mark('afterLoad') : null;

$app = Factory::getApplication();

// Safety check to prevent fatal error if 'System - Kunena Forum' plug-in has been disabled.
if ($app->input->getCmd('view') == 'install' || !class_exists('KunenaForum') || !KunenaForum::isCompatible('4.0'))
{
	// Run installer instead..
	require_once __DIR__ . '/install/controller.php';

	$controller = new KunenaControllerInstall;

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

// Display profiler information.
$kunena_profiler->stop('Total Time');

if (KUNENA_PROFILER)
{
	echo '<div class="kprofiler">';
	echo "<h3>Kunena Profile Information</h3>";

	foreach ($kunena_profiler->getAll() as $item)
	{
		echo sprintf("Kunena %s: %0.3f / %0.3f seconds (%d calls)<br/>", $item->name, $item->getInternalTime(),
			$item->getTotalTime(), $item->calls
		);
	}

	echo '</div>';
}
