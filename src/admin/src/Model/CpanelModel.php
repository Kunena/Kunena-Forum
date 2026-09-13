<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Cpanel Model for Kunena
 *
 * @since   Kunena 2.0
 */
class CpanelModel extends AdminModel
{
    /**
     * @inheritDoc
     *
     * @param   array    $data      data
     * @param   boolean  $loadData  load data
     *
     * @return void
     *
     * @since  Kunena 6.0
     */
    public function getForm($data = [], $loadData = true)
    {
        // TODO: Implement getForm() method. 
    }
    
    /**
     * Get number of mails queues not yet send to be displayed in cpanel
     * 
     * @since   Kunena 7.1
     */
    public function numberOfMailsqueues() :int
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery();
        
        $query->select('COUNT(*)');
        
        $query->from($db->quoteName('#__kunena_notifications_mailsqueue', 'a'));
        
        $query->where($db->quoteName('a.send') . ' = ' . 1);
        
        $result = $db->loadResult();
        
        return $result;
    }
    
    /**
     * Set value of sample data flag
     *
     * @since   Kunena 7.1
     */
    function setSampleDataFlag($value = 1)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        
        $query->update($db->quoteName('#__kunena_version'))
        ->set($db->quoteName('sampleData') . ' = ' . $db->quote($value));
        
        $db->setQuery($query);
        
        try {
            return $db->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}
