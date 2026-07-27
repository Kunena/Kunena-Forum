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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

/**
 * Mailsqueues Model for Kunena
 *
 * @since   Kunena 7.1
 */
class MailsqueuesModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id',
                'subject',
                'messageId',
                'url',
                'categoryName',
                'once',
                'send',
            ];
        }

        parent::__construct($config);
    }

    /**
     * @param   array    $data      data
     * @param   boolean  $loadData  load data
     *
     * @return void
     *
     * @since  Kunena 7.1
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_kunena.mailsqueues', 'filter_mailsqueues', ['control' => '', 'load_data' => $loadData]);
        
        if (empty($form)) {
            return false;
        }
        
        return $form;
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   ordering
     * @param   string  $direction  direction
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    protected function populateState($ordering = 'id', $direction = 'desc')
    {
        $app = Factory::getApplication();

        // Adjust the context to support modal layouts.
        $layout        = $app->getInput()->get('layout');
        $this->context = 'com_kunena.admin.mailsqueues';

        if ($layout) {
            $this->context .= '.' . $layout;
        }

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * @param   string  $id  id
     *
     * @return  string
     *
     * @since   Kunena 7.1
     */
    protected function getStoreId($id = ''): string
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 7.1
     */
    protected function getListQuery(): QueryInterface
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery();

        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.subject, a.messageId, a.url, a.emailListJson, a.categoryName, a.once, a.send'
            )
        );

        $query->from($db->quoteName('#__kunena_notifications_mailsqueue', 'a'));

        // Filter by search.
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where('(a.subject LIKE ' . $search . ' OR a.categoryName LIKE ' . $search . ' OR a.url LIKE ' . $search . ')');
            }
        }

        // Add the list ordering clause.
        $direction = strtoupper($this->state->get('list.direction'));
        $direction = \in_array($direction, ['ASC', 'DESC'], true) ? $direction : 'DESC';

        switch ($this->state->get('list.ordering')) {
            case 'subject':
                $query->order('a.subject ' . $direction);
                break;
            case 'messageId':
                $query->order('a.messageId ' . $direction);
                break;
            case 'categoryName':
                $query->order('a.categoryName ' . $direction);
                break;
            case 'send':
                $query->order('a.send ' . $direction);
                break;
            default:
                $query->order('a.id ' . $direction);
        }

        return $query;
    }
}
