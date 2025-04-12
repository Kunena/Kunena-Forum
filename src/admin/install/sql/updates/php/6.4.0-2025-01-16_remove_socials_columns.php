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
function kunena_640_2025_01_16_remove_socials_columns($parent) {
    $db     = Factory::getContainer()->get('DatabaseDriver');
    
    $listOfColumns = ['icq', 'x_social', 'facebook', 'myspace', 'linkedin', 'linkedin_company', 'digg', 'skype', 'yim', 'google', 'friendfeed', 'github', 'microsoft', 'blogspot', 'flickr', 'bebo', 'instagram', 
        'qqsocial', 'qzone', 'weibo', 'wechat', 'vk', 'telegram', 'apple', 'vimeo', 'whatsapp', 'youtube', 'ok', 'pinterest', 'reddit', 'bluesky_app'];
    
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
    
    return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_640_REMOVE_SOCIALS_COLUMNS'), 'success' => true);
}