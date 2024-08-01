<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 6.3.4
 */
function kunena_634_2024_08_01_update_configuration_to_x($parent) {
	$config = KunenaFactory::getConfig();

	$config->XConsumerKey = $config->twitterConsumerKey;
	
	unset($config->twitterConsumerKey);
	
	$config->XConsumerSecret = $config->twitterConsumerSecret;
	
	unset($config->twitterConsumerSecret);

	// Save configuration
	$config->save();	

	return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_634_UPDATE_CONFIGURATION'), 'success' => true);
}
