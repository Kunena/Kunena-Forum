<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Kunena 6.0.0: Update default value for banned users
/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena
 */
function kunena_600_2019_05_31_banneddatetimedefault($parent)
{
	$db  = Factory::getDbo();

	$query    = "UPDATE `#___kunena_users` SET banned='9999-12-31 23:59:59' WHERE banned='0000-00-00 00:00:00'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query    = "UPDATE `#___kunena_users` SET banned='1000-01-01 00:00:00' WHERE banned='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query    = "ALTER TABLE `#__kunena_users` MODIFY COLUMN `banned` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_600_BANNED_DATETIME_DEFAULT'), 'success' => true);
}
