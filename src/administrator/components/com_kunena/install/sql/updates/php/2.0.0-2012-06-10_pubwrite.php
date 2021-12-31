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

// Kunena 2.0.0: Update menu items
/**
 * @param $parent
 *
 * @return array|null
 * @throws Exception
 * @since Kunena
 */
function kunena_200_2012_06_10_pubwrite($parent)
{
	$config = KunenaFactory::getConfig();

	if ($config->pubwrite)
	{
		$db     = Factory::getDbo();
		$params = '{"access_post":["1"],"access_reply":["1"]}';
		$query  = "UPDATE `#__kunena_categories` SET params={$db->quote($params)} WHERE accesstype LIKE 'joomla.%' AND params=''";
		$db->setQuery($query);
		$success = (bool) $db->execute();

		return array('action' => '', 'name' => Text::sprintf('COM_KUNENA_INSTALL_200_PUBWRITE'), 'success' => $success);
	}

	return null;
}
