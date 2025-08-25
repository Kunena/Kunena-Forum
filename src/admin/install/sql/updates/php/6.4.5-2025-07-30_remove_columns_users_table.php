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
 * @since Kunena 6.4.5
 */
function kunena_645_2025_07_30_remove_columns_users_table($parent) {
    $db     = Factory::getContainer()->get('DatabaseDriver');
    
    $listOfColumns = ['qq', 'karma_time'];
    
    foreach ($listOfColumns as $column) {
        $query = "SHOW COLUMNS FROM " . $db->quoteName('#__kunena_users') . " LIKE '{$column}';";
        $db->setQuery($query);
        $column = $db->loadResult();
        
        if ($column) {
            $query = 'ALTER TABLE ' . $db->quoteName('#__kunena_users') . ' DROP ' . $column . ';';
            $db->setQuery($query);
            $db->execute();
        }
    }    
    
    return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_645_REMOVE_COLUMNS_USERS_TABLE'), 'success' => true);
}