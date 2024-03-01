<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

/**
 * Trash Model for Kunena
 *
 * @since   Kunena 2.0
 */
class TrashsModel extends ListModel
{
    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected $internalItems = false;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected $internalItemsOrder = false;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected $internalObject = false;

    /**
     * Constructor
     *
     * @param   array                $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param   MVCFactoryInterface  $factory  The factory.
     *
     * @since   Kunena 6.3.0-BETA3
     * @throws  Exception
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id',
                'title',
                'topic',
                'category',
                'author',
                'ip',
                'time',
            ];
        }

        parent::__construct($config, $factory);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string  $id  A prefix for the store id.
     *
     * @return  string  A store id.
     *
     * @since   Kunena 6.0
     */
    protected function getStoreId($id = ''): string
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.title');
        $id .= ':' . $this->getState('filter.topic');
        $id .= ':' . $this->getState('filter.category');
        $id .= ':' . $this->getState('filter.author');
        $id .= ':' . $this->getState('filter.ip');
        $id .= ':' . $this->getState('filter.time');
        $id .= ':' . $this->context;

        return parent::getStoreId($id);
    }

    /**
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
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   null  $ordering   ordering
     * @param   null  $direction  direction
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function populateState($ordering = 'id', $direction = 'asc')
    {
        $app = Factory::getApplication();

        // Adjust the context to support modal layouts.
        $layout        = $app->input->get('layout', 'messages');
        $this->context = 'com_kunena.admin.trashs';

        if ($layout) {
            $this->context .= '.' . $layout;
        }

        // Set the filtername to get the correct filter layout on the view
        $this->filterFormName = 'filter_trashs_' . $layout;

        $this->context .= '.' . $layout;
        // $this->setState('com_kunena.admin.trashs.layout', $layout);

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected function getListQuery(): QueryInterface
    {
        // Create a new query object.
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $app    = Factory::getApplication();
        $layout = $app->input->get('layout', 'messages');

        if ($layout == 'messages') {
            // Select the required fields from the table.
            $query->select(
                $this->getState(
                    'list.select',
                    'km.id AS id, km.subject AS title, kt.subject AS topic, kc.name AS category, u.name AS author, km.ip AS ip, km.time AS time,
                    km.catid AS category_id, km.thread AS topic_id'
                )
            );
            $query->from($db->quoteName('#__kunena_messages', 'km'));

            // Join over the users for the linked user.
            $query->join('LEFT', $db->quoteName('#__users', 'u') . ' ON km.userid = u.id');

            // Join over the topics.
            $query->join('LEFT', $db->quoteName('#__kunena_topics', 'kt') . ' ON km.thread = kt.id');

            // Join over the category.
            $query->join('LEFT', $db->quoteName('#__kunena_categories', 'kc') . ' ON km.catid = kc.id');

            // Only deleted messages
            $query->where($db->quoteName('km.hold') . ' IN (2,3)');
        } else {
            // Select the required fields from the table.
            $query->select(
                $this->getState(
                    'list.select',
                    'kt.id AS id, kt.subject AS title, kc.name AS category, u.name AS author, km.ip AS ip, kt.first_post_time AS time,
                    kt.category_id AS category_id, kt.id AS topic_id'
                )
            );
            $query->from($db->quoteName('#__kunena_topics', 'kt'));

            // Join over the users for the linked user.
            $query->join('LEFT', $db->quoteName('#__users', 'u') . ' ON kt.first_post_userid = u.id');

            // Join over the messages.
            $query->join('LEFT', $db->quoteName('#__kunena_messages', 'km') . ' ON kt.first_post_id = km.id');

            // Join over the category.
            $query->join('LEFT', $db->quoteName('#__kunena_categories', 'kc') . ' ON kt.category_id = kc.id');

            // Only deleted messages
            $query->where($db->quoteName('kt.hold') . ' IN (2,3)');
        }

        // Filter by search.
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(km.subject LIKE ' . $search . ' OR kt.subject LIKE ' . $search . ' OR kc.name LIKE ' . $search . ' OR u.name LIKE ' . $search . ' OR km.ip LIKE ' . $search . ')');
        }

        // Add the list ordering clause.
        $direction = strtoupper($this->getState('list.direction'));
        $ordering  = strtoupper($this->getState('list.ordering'));
        $query->order($ordering . ' ' . $direction);

        return $query;
    }
}
