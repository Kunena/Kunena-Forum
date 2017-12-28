<?php
/**
 * Kunena Component
 *
 * @package    Kunena.Installer
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die();

// Kunena 1.6.0: Convert database timezone from (local+board_offset) to UTC
/**
 * @param $parent
 *
 * @return array|null
 * @throws KunenaInstallerException
 */
function kunena_160_2010_05_30_timezone($parent)
{
	$result = null;
	$config = KunenaFactory::getConfig();
	$db     = JFactory::getDbo();

	// We need to fix all timestamps to UTC (if not already done)
	if ($config->get('board_ofset', '0.00') != '0.00')
	{
		$timeshift = (float) date('Z') + ((float) $config->get('board_ofset') * 3600);

		$db->setQuery("UPDATE #__kunena_categories SET time_last_msg = time_last_msg - {$timeshift}");
		$db->query();

		if ($db->getErrorNum())
		{
			throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
		}

		$db->setQuery("UPDATE #__kunena_sessions SET lasttime = lasttime - {$timeshift}, currvisit  = currvisit  - {$timeshift}");
		$db->query();

		if ($db->getErrorNum())
		{
			throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
		}

		$db->setQuery("UPDATE #__kunena_whoisonline SET time = time - {$timeshift}");
		$db->query();

		if ($db->getErrorNum())
		{
			throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
		}

		$db->setQuery("UPDATE #__kunena_messages SET time = time - {$timeshift}, modified_time = modified_time - {$timeshift}");
		$db->query();

		if ($db->getErrorNum())
		{
			throw new KunenaInstallerException($db->getErrorMsg(), $db->getErrorNum());
		}

		unset($config->board_ofset);
		$result = array('action' => '', 'name' => JText::sprintf('COM_KUNENA_INSTALL_160_TIMEZONE', sprintf('%+d:%02d', $timeshift / 3600, ($timeshift / 60) % 60)), 'success' => true);
	}

	// Save configuration
	$config->save();

	return $result;
}
