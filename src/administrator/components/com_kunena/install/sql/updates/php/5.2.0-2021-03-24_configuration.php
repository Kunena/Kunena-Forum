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

use Joomla\CMS\Language\Text;

// Kunena 5.2.0: Convert configuration options which was wrong default value
/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 5.2.4
 */
function kunena_520_2021_03_24_configuration($parent)
{
	$config = KunenaFactory::getConfig();

	$latestcategory = $config->latestcategory;

	if (!is_array($latestcategory))
	{
		unset($config->latestcategory);

		$config->latestcategory = 0;

		// Save configuration
		$config->save();

		return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_520_UPDATE_CONFIGURATION'), 'success' => true);
	}
}
