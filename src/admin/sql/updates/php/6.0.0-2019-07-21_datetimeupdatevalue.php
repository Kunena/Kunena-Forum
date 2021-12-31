<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Install\KunenaInstallerException;

// Kunena 6.0.0: Update value of type datetime in all tables to changes default value
/**
 * @param   string  $parent  parent
 *
 * @return  array
 *
 * @throws  Exception
 * @since   Kunena 6.0
 */
function kunena_600_2019_07_21_datetimeupdatevalue($parent)
{
	$db = Factory::getContainer()->get('DatabaseDriver');

	$query = "UPDATE `#__kunena_announcement` SET created='1000-01-01 00:00:00' WHERE created='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_announcement` MODIFY COLUMN `created` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "UPDATE `#__kunena_announcement` SET  publish_up='1000-01-01 00:00:00' WHERE publish_up='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_announcement` MODIFY COLUMN `publish_up` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "UPDATE `#__kunena_announcement` SET publish_down='1000-01-01 00:00:00' WHERE publish_down='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_announcement` MODIFY COLUMN `publish_down` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "UPDATE `#__kunena_categories` SET checked_out_time='1000-01-01 00:00:00' WHERE checked_out_time='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_categories` MODIFY COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "UPDATE `#__kunena_polls` SET polltimetolive='1000-01-01 00:00:00' WHERE polltimetolive='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_polls` MODIFY COLUMN `polltimetolive` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "UPDATE `#__kunena_polls_users` SET lasttime='1000-01-01 00:00:00' WHERE lasttime='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_polls_users` MODIFY COLUMN `lasttime` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "UPDATE `#__kunena_rate` SET time='1000-01-01 00:00:00' WHERE time='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_rate` MODIFY COLUMN `time` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "UPDATE `#__kunena_users_banned` SET expiration='1000-01-01 00:00:00' WHERE expiration='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_users_banned` MODIFY COLUMN `expiration` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "UPDATE `#__kunena_users_banned` SET created_time='1000-01-01 00:00:00' WHERE created_time='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_users_banned` MODIFY COLUMN `created_time` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$query .= "UPDATE `#__kunena_users_banned` SET modified_time='1000-01-01 00:00:00' WHERE modified_time='null'";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = "ALTER TABLE `#__kunena_users_banned` MODIFY COLUMN `modified_time` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	return ['action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_600_DATETIME_VALUE_IN_TABLES'), 'success' => true];
}
