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
use Kunena\Forum\Libraries\Factory\KunenaFactory;

// Kunena 6.0.0: Set Aurelia as default template in config when update
/**
 * @param   string  $parent  parent
 *
 * @return  array
 *
 * @throws  Exception
 * @since   Kunena 6.0
 */
function kunena_600_2019_05_18_configuration($parent)
{
	$config = KunenaFactory::getConfig();

	if (isset($config->template))
	{
		if ($config->template == 'crypsis' || $config->template == 'crypsisb4')
		{
			$config->set('template', 'aurelia');
		}
	}

	// Save configuration
	$config->save();

	return ['action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_600_CONFIGURATION'), 'success' => true];
}
