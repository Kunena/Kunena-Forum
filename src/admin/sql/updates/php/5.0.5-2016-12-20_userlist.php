<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright  (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

use Kunena\Forum\Libraries\Factory\KunenaFactory;

defined('_JEXEC') or die();

// Kunena 5.0.5: Update setting allow guest to see userlist
/**
 * @return  void
 *
 * @since   Kunena 6.0
 */
function kunena_505_2016_12_20_userlist()
{
	$config = KunenaFactory::getConfig();

	if ($config->userlistAllowed)
	{
		$config->userlistAllowed = 0;
	}
	else
	{
		$config->userlistAllowed = 1;
	}

	// Save configuration
	$config->save();
}
