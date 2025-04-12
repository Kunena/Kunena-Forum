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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 6.4.0
 */
function kunena_640_2024_10_21_remove_delicious_socialshare($parent) {   
    $db     = Factory::getContainer()->get('DatabaseDriver');
    
    $query = "SHOW COLUMNS FROM " . $db->quoteName('#__kunena_users') . " LIKE 'delicious';";
    $db->setQuery($query);
    $column = $db->loadResult();
    
    if ($column) {
        $query = 'ALTER TABLE ' . $db->quoteName('#__kunena_users') . ' DROP `delicious`;';
        $db->setQuery($query);
        $db->execute();
    }
    
    $query = "SHOW COLUMNS FROM " . $db->quoteName('#__kunena_users') . " LIKE 'socialshare';";
    $db->setQuery($query);
    $column = $db->loadResult();
    
    if ($column) {
        $query = 'ALTER TABLE ' . $db->quoteName('#__kunena_users') . ' DROP `socialshare`;';
        $db->setQuery($query);
        $db->execute();
    }
    
    return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_640_REMOVE_DELICIOUS_SOCIALSHARE'), 'success' => true);
}