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
function kunena_640_2025_03_09_add_threads_to_socials($parent) {   
    $db     = Factory::getContainer()->get('DatabaseDriver');
    
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
        $query  = $db->createQuery()
            ->select($db->quoteName(array('userid', 'socials')))
            ->from($db->quoteName('#__kunena_users'))
            ->where($db->quoteName('banned') . '= ' . $db->quote('1000-01-01 00:00:00')
        );
        
        $db->setQuery($query);
        $db->execute();
        $dataResults = (array) $db->loadAssocList();
        
        foreach (chunk($dataResults, $numRows, 100) as $line) {
            $result = ArrayHelper::toObject($line);
            $socials = KunenaUserSocials::getInstance($result->userid, false);
            
            $threads = new \stdClass();
            $threads->value = '';
            $threads->url = 'https://www.threads.net/@##VALUE##';
            $threads->title = 'COM_KUNENA_MYPROFILE_THREADS_APP';
            $threads->nourl = '0';
            $threads->fa = 'fa-brands fa-threads';
            $socials->threads = $threads;
            
            $socials->save();
        }
    }
    
    return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_640_ADD_THREADS_IN_USERS_SOCIALS'), 'success' => true);
}
