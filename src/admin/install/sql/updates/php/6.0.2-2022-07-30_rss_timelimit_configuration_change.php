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

/**
 * Convert Kunena configuration to update changes of setting of RSS timelimit
 * 
 * @param   string  $parent  parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 6.0.2
 */
function kunena_602_2022_07_30_rss_timelimit_configuration_change($parent)
{
	$config = KunenaFactory::getConfig();

	$rssTimeLimit = $config->rssTimeLimit;

	if ($rssTimeLimit == 'month')
	{
		$config->rssTimeLimit = '1 month';
	}
	elseif ($rssTimeLimit == 'week')
	{
		$config->rssTimeLimit = '1 week';
	}
	else 
	{
		$config->rssTimeLimit = '1 year';
	}

	if ($rssTimeLimit == 'month' || $rssTimeLimit == 'week' || $rssTimeLimit == 'year')
	{
		unset($config->rssTimeLimit);

		// Save configuration
		$config->save();

		return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_602_UPDATE_CONFIGURATION_RSS_TIMELIMIT'), 'success' => true);
	}
}