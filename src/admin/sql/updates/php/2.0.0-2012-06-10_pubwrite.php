<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

// Kunena 2.0.0: Update menu items
/**
 * @param   string  $parent  parent
 *
 * @return  array
 *
 * @throws  Exception
 * @since   Kunena 6.0
 */
function kunena_200_2012_06_10_pubWrite($parent)
{
	$config = KunenaFactory::getConfig();

	if ($config->pubWrite)
	{
		$db     = Factory::getDbo();
		$params = '{"access_post":["1"],"access_reply":["1"]}';
		$query  = "UPDATE `#__kunena_categories` SET params={$db->quote($params)} WHERE accesstype LIKE 'joomla.%' AND params=''";
		$db->setQuery($query);
		$success = (bool) $db->execute();

		return ['action' => '', 'name' => Text::sprintf('COM_KUNENA_INSTALL_200_PUBWRITE'), 'success' => $success];
	}
}
