<?php

/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

/**
 * Convert columns in Kunena tables to collation in utf8mb4
 *
 * @param   string  $parent  parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 6.2.4
 */
function kunena_624_2022_01_24_change_collation_columns_on_tables($parent)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);;

    // Change to varchar 250 the columns which are in varchar 255 because it blocks then the conversion to utf8mb4
    $listKunenaTables = [$db->getPrefix().'kunena_aliases', $db->getPrefix().'kunena_announcement', $db->getPrefix().'kunena_attachments', $db->getPrefix().'kunena_categories', $db->getPrefix().'kunena_configuration',
         $db->getPrefix().'kunena_karma', $db->getPrefix().'kunena_topics', $db->getPrefix().'kunena_messages', $db->getPrefix().'kunena_messages_text', $db->getPrefix().'kunena_polls', $db->getPrefix().'kunena_polls_options',
         $db->getPrefix().'kunena_polls_users', $db->getPrefix().'kunena_private', $db->getPrefix().'kunena_private_attachment_map', $db->getPrefix().'kunena_private_post_map', $db->getPrefix().'kunena_private_user_map', 
         $db->getPrefix().'kunena_ranks', $db->getPrefix().'kunena_rate', $db->getPrefix().'kunena_sessions', $db->getPrefix().'kunena_smileys', $db->getPrefix().'kunena_thankyou', $db->getPrefix().'kunena_user_categories', 
         $db->getPrefix().'kunena_user_read', $db->getPrefix().'kunena_user_topics', $db->getPrefix().'kunena_users', $db->getPrefix().'kunena_users_banned', $db->getPrefix().'kunena_logs', $db->getPrefix().'kunena_version'];

        // Get collations from all Kunena tables et convert the column when needed
        foreach ($listKunenaTables as $kunenatable) {
            // Fisrt check if the table exist
            $db->setQuery("SHOW TABLES LIKE {$db->quote($kunenatable)}");
            if ($db->loadResult() == $kunenatable) {
                $query = 'SHOW FULL COLUMNS FROM '.$db->quoteName($kunenatable);
                $db->setQuery($query);
    
                $tableColumns = $db->loadobjectList();
    
                // Check column and set to ut8_mb4 when needed
                foreach ($tableColumns as $column) {
                    if ($column->Collation == 'utf8_general_ci' || $column->Collation == 'utf8mb3_general_ci' || $column->Collation == 'utf8_unicode_ci' || $column->Collation == 'utf8mb3_unicode_ci') {
                        if ($column->Type == 'VARCHAR(255)') {
                        
                        $query = 'ALTER TABLE ' . $db->quoteName($kunenatable) . ' CHANGE ' . $column->Field . ' ' . $column->Field . ' ' . $column->Type . ' VARCHAR(250);';
                     $db->setQuery($query);
    
                     $db->execute();
                        } 
                        
                        $query = 'ALTER TABLE ' . $db->quoteName($kunenatable) . ' CHANGE ' . $column->Field . ' ' . $column->Field . ' ' . $column->Type . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';
                     $db->setQuery($query);
    
                     $db->execute();                    
                   }
                }
            }
        }

    return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_624_CHANGE_COLLATION_ON_COLUMNS_TO_UTF8MB4'), 'success' => true);    
}
