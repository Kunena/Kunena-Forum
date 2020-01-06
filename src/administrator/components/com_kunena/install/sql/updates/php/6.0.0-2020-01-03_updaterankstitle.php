<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Kunena 6.0.0: Update ranks title value to have string which can be translated
/**
 * @param   string  $parent parent
 *
 * @return  array
 *
 * @since   Kunena 6.0
 *
 * @throws  Exception
 */
function kunena_600_2020_01_03_updaterankstitle($parent)
{
	$db = Factory::getDbo();

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK1')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 1'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK2')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 2'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK3')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 3'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK4')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 4'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK5')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 5'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK6')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 6'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_ADMIN')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 7'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_MODERATOR')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 8'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_SPAMMER')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 9'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = [
		$db->quoteName('title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_BANNED')
	];

	// Conditions for which records should be updated.
	$conditions = [
		$db->quoteName('id') . ' = 10'
	];

	$query->update($db->quoteName('#__kunena_ranks'))->set($fields)->where($conditions);
	$db->setQuery($query);

	try
	{
		$db->execute();
	}
	catch (Exception $e)
	{
		throw new KunenaInstallerException($e->getMessage(), $e->getCode());
	}

	return ['action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_600_RANKS_TITLE_VALUE_IN_TABLES'), 'success' => true];
}
