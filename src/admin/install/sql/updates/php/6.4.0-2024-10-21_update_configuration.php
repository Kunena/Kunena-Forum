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
 * @since Kunena 6.4.0
 */
function kunena_640_2024_10_21_update_configuration($parent) {
    $config = KunenaFactory::getConfig();

    unset($config->social);

    // Save configuration
    $config->save();

    return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_640_UPDATE_CONFIGURATION'), 'success' => true);
}
