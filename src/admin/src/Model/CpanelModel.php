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

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use RuntimeException;

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
     * @return false  This model has no form.
     *
     * @since  Kunena 6.0
     */
    public function getForm($data = [], $loadData = true)
    {
        // No form: this model does not use one (the method is kept for the callers of getForm()).
        return false;
    }

    /**
     * The number of e-mails of the mail queue which wait to be sent, for the dashboard. The queue keeps the sent ones too
     * (send = 1): they are not counted.
     *
     * @return  integer
     *
     * @since   Kunena 7.1
     */
    public function numberOfMailsqueues(): int
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_notifications_mailsqueue', 'a'))
            ->where($db->quoteName('a.send') . ' = 0');

        try {
            $db->setQuery($query);

            return (int) $db->loadResult();
        } catch (RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

            return 0;
        }
    }

    /**
     * Set value of sample data flag
     *
     * @param   integer  $value  The value of the flag.
     *
     * @return  boolean  True on success.
     *
     * @since   Kunena 7.1
     */
    public function setSampleDataFlag($value = 1)
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();

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
