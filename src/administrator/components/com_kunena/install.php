<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

/*
 ************************/
/*
  KUNENA FORUM INSTALLER */

$app  = \Joomla\CMS\Factory::getApplication();
$view = $app->input->getCmd('view');
$task = $app->input->getCmd('task');

// Special case for developer versions.
if ($view != 'install' && class_exists('KunenaForum') && KunenaForum::isDev())
{
	// Developer version found: Check if latest version of Kunena has been installed. If not, prepare installation.
	require_once __DIR__ . '/install/version.php';

	$kversion = new KunenaVersion;

	if (!$kversion->checkVersion())
	{
		\Joomla\CMS\Factory::getApplication()->redirect(\Joomla\CMS\Uri\Uri::base(true) . '/index.php?option=com_kunena&view=install');
	}

	return;
}

// Run the installer...
require_once __DIR__ . '/install/controller.php';

$controller = new KunenaControllerInstall;
$controller->execute($task);
$controller->redirect();
