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

// Kunena 5.2.0: Update ranks rank_title value to have string which can be translated
/**
 * @param $parent
 *
 * @return array
 * @since Kunena
 * @throws Exception
 */
function kunena_520_2020_08_20_updaterankstitle($parent)
{
	$db = Factory::getDbo();

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK1')
	);

	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 1'
	);

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
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK2')
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 2'
	);
	
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
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK3')
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 3'
	);
	
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
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK4')
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 4'
	);
	
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
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK5')
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 5'
	);
	
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
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK6')
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 6'
	);
	
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
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_ADMIN')
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 7'
	);
	
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
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_MODERATOR')
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 8'
	);
	
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
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_SPAMMER')
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 9'
	);
	
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
	$fields = array(
		$db->quoteName('rank_title') . ' = ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_BANNED')
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('rank_id') . ' = 10'
	);
	
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

	return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_520_RANKS_TITLE_VALUE_IN_TABLES'), 'success' => true);
}
