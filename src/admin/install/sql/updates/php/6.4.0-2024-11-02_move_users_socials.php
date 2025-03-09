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
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\User\KunenaUserSocials;

/**
 *  Handle the data by group of 100 lines
 * 
 * @param array $array
 * @param integer $numRows
 * @param integer $size
 */
function chunk($array, $numRows, $size) {
    $result = array();
    for ($i = 0; $i < $numRows; $i += $size) {
        $result[] = array_slice($array, $i, $size);
    }
    
    return $result['0'];
}

/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 6.4.0
 */
function kunena_640_2024_11_02_move_users_socials($parent) {   
    $db     = Factory::getContainer()->get('DatabaseDriver');
    
    // Check if some columns of socials are missing in the table #__kunena_users
    $query = $db->getQuery(true);
    $db->setQuery("SHOW COLUMNS FROM `#__kunena_users`");
    $columnsKunenaUsers = $db->loadObjectList();
    
    $columsSocialsByDefault = ['x_social', 'facebook', 'myspace', 'linkedin', 'linkedin_company', 'digg', 'skype', 'yim', 'google', 'github', 'microsoft', 'blogspot', 'flickr', 'bebo', 'instagram', 'qqsocial', 'qzone', 'weibo', 'wechat', 'vk', 'telegram', 'apple', 'vimeo', 'whatsapp', 'youtube', 'ok', 'pinterest', 'reddit', 'bluesky_app'];
    $columsToAvoid = ['userid', 'status', 'status_text', 'view', 'signature', 'moderator', 'banned', 'ordering', 'posts', 'avatar', 'timestamp', 'karma', 'group_id', 'uhits', 'personalText', 'gender', 'birthdate', 'location', 'websitename', 'websiteurl', 'rank', 'hideEmail', 'showOnline', 'canSubscribe', 'userListtime', 'thankyou', 'ip', 'socials'];
    $columsSocialsInTable = [];
    
    foreach($columnsKunenaUsers as $column) {
        if (array_search($column->Field, $columsToAvoid) === false) {
            $columsSocialsInTable[] = $column->Field;
        }
    }
    
    if(count($columsSocialsInTable) > 0 ) {
        $listSocialsColumns = $columsSocialsInTable;
    } else {
        $listSocialsColumns = $columsSocialsByDefault;
    }

    // Get the number of lines in table #__kunena_users    
    $query  = $db->createQuery()
        ->select(array('userid', 'banned'))
        ->from($db->quoteName('#__kunena_users'))
        ->where($db->quoteName('banned') . '= ' . $db->quote('1000-01-01 00:00:00')
    );

    $db->setQuery($query);
    $db->execute();    
    $numRows = $db->getNumRows(); 
    
    if ($numRows > 0) {    
        $listSocialsColumns[] = 'userid'; 
            
        $query  = $db->createQuery()
            ->select($db->quoteName($listSocialsColumns))
            ->from($db->quoteName('#__kunena_users'))
            ->where($db->quoteName('banned') . '= ' . $db->quote('1000-01-01 00:00:00')
        );
    
        $db->setQuery($query);
        $db->execute();
        $dataResults = (array) $db->loadAssocList();
            
        foreach (chunk($dataResults, $numRows, 100) as $line) {
            $result = ArrayHelper::toObject($line);
            $socials = KunenaUserSocials::getInstance($result->userid, false);
            
            foreach ($columsSocialsByDefault as $socialColumn) {
                if (isset($result->$socialColumn) && !empty($result->$socialColumn)) {
                    $socials->$socialColumn->value = $result->$socialColumn;
                }
            }
                
            $socials->save();
        }
    }
    
    return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_640_UPDATE_USERS_SOCIALS'), 'success' => true);
}
