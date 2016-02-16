<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Installer
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

// Kunena 2.0.0: Update menu items
function kunena_200_2012_04_13_menu($parent)
{
	$app    = JFactory::getApplication();
	$legacy = KunenaMenuFix::getLegacy();
	$errors = KunenaMenuFix::fixLegacy();
	if ($errors)
	{
		foreach ($errors as $error)
		{
			$app->enqueueMessage($error, 'error');
		}
	}

	if (!empty($legacy))
	{
		return array('action' => '', 'name' => JText::sprintf('COM_KUNENA_INSTALL_200_MENU', count($legacy)), 'success' => !$errors);
	}

	return null;
}
