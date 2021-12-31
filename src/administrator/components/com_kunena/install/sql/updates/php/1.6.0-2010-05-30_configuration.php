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

// Kunena 1.6.0: Convert deprecated configuration options
/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena
 */
function kunena_160_2010_05_30_configuration($parent)
{
	$config = KunenaFactory::getConfig();

	// Switch to default template
	$config->set('template', 'blue_eagle');

	// Keep integration settings
	$integration = array('jomsocial' => 'jomsocial', 'cb' => 'communitybuilder', 'uddeim' => 'uddeim', 'aup' => 'alphauserpoints', 'none' => 'none');

	if (!$config->get('allowavatar'))
	{
		$config->set('allowavatar', 1);
		$config->set('integration_avatar', 'none');
	}
	else
	{
		if ($config->get('avatar_src'))
		{
			if (isset($integration [$config->get('avatar_src')]))
			{
				$config->set('integration_avatar', $integration [$config->get('avatar_src')]);
			}
			else
			{
				$config->set('integration_avatar', 'kunena');
			}

			unset($config->avatar_src);
		}
	}

	if ($config->get('fb_profile'))
	{
		if (isset($integration [$config->get('fb_profile')]))
		{
			$profile = $integration [$config->get('fb_profile')];
			$config->set('integration_access', $profile);
			$config->set('integration_login', $profile);
			$config->set('integration_profile', $profile);
			$config->set('integration_activity', $profile);
		}
		else
		{
			$config->set('integration_access', 'joomla');
			$config->set('integration_login', 'joomla');
			$config->set('integration_profile', 'kunena');
			$config->set('integration_activity', 'none');
		}

		unset($config->fb_profile);
	}

	if ($config->get('js_actstr_integration'))
	{
		$config->set('integration_activity', 'jomsocial');
		unset($config->js_actstr_integration);
	}
	else
	{
		if ($config->get('integration_activity') == 'jomsocial')
		{
			$config->set('integration_activity', 'none');
		}
	}

	if ($config->get('pm_component'))
	{
		if (isset($integration [$config->get('pm_component')]))
		{
			$config->set('integration_private', $integration [$config->get('pm_component')]);
		}
		else
		{
			$config->set('integration_private', 'none');
		}

		unset($config->pm_component);
	}

	// Save configuration
	$config->save();

	return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_160_CONFIGURATION'), 'success' => true);
}
