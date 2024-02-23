<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Models
 *
 * @copyright     Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

/**
 * Logs Model for Kunena
 *
 * @since 5.0
 */
class LogsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @throws  Exception
     * @since   Kunena 6.0
     *
     * @see     JController
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id',
                'type',
                'user',
                'category',
                'topic',
                'target_user',
                'ip',
                'time',
                'time_start',
                'time_stop',
                'operation',
            ];
        }

        $app      = Factory::getApplication();
        $this->me = $app->getIdentity();

        parent::__construct($config);
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
        $id .= ':' . $this->getState('filter.id');
        $id .= ':' . $this->getState('filter.type');
        $id .= ':' . $this->getState('filter.user');
        $id .= ':' . $this->getState('filter.category');
        $id .= ':' . $this->getState('filter.topic');
        $id .= ':' . $this->getState('filter.target_user');
        $id .= ':' . $this->getState('filter.ip');
        $id .= ':' . $this->getState('filter.time_start');
        $id .= ':' . $this->getState('filter.time_stop');
        $id .= ':' . $this->getState('filter.operation');
        $id .= ':' . $this->getState('filter.usertypes');
        $id .= ':' . json_encode($this->getState('group'));

        return parent::getStoreId($id);
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
    protected function populateState($ordering = 'id', $direction = 'desc')
    {
        $app = Factory::getApplication();

        // Adjust the context to support modal layouts.
        $layout        = $app->input->get('layout');
        $this->context = 'com_kunena.admin.logs';

        if ($layout) {
            $this->context .= '.' . $layout;
        }

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $id = $this->getUserStateFromRequest($this->context . '.filter.id', 'filter_id', '');
        $this->setState('filter.id', $id);

        $type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '');
        $this->setState('filter.type', $type);

        $user = $this->getUserStateFromRequest($this->context . '.filter.user', 'filter_user', '');
        $this->setState('filter.user', $user);

        $category = $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '');
        $this->setState('filter.category', $category);

        $topic = $this->getUserStateFromRequest($this->context . '.filter.topic', 'filter_topic', '');
        $this->setState('filter.topic', $topic);

        $target_user = $this->getUserStateFromRequest($this->context . '.filter.target_user', 'filter_target_user', '');
        $this->setState('filter.target_user', $target_user);

        $ip = $this->getUserStateFromRequest($this->context . '.filter.ip', 'filter_ip', '');
        $this->setState('filter.ip', $ip);

        $time_start = $this->getUserStateFromRequest($this->context . '.filter.time_start', 'filter_time_start', '');
        $this->setState('filter.time_start', $time_start);

        $time_stop = $this->getUserStateFromRequest($this->context . '.filter.time_stop', 'filter_time_stop', '');
        $this->setState('filter.time_stop', $time_stop);

        $operation = $this->getUserStateFromRequest($this->context . '.filter.operation', 'filter_operation', '');
        $this->setState('filter.operation', $operation);

        $usertypes = $this->getUserStateFromRequest($this->context . '.filter.usertypes', 'filter_usertypes', '');
        $this->setState('filter.usertypes', $usertypes);

        $userfields = $this->getUserStateFromRequest($this->context . '.filter.userfields', 'filter_userfields', '');
        $this->setState('filter.userfields', $userfields);

        $group = [];

        if ($this->getUserStateFromRequest($this->context . '.group.type', 'group_type', false, 'bool')) {
            $group['type'] = 'a.type';
        }

        if ($this->getUserStateFromRequest($this->context . '.group.user', 'group_user', false, 'bool')) {
            $group['user'] = 'a.user_id';
        }

        if ($this->getUserStateFromRequest($this->context . '.group.category', 'group_category', false, 'bool')) {
            $group['category'] = 'a.category_id';
        }

        if ($this->getUserStateFromRequest($this->context . '.group.topic', 'group_topic', false, 'bool')) {
            $group['topic'] = 'a.topic_id';
        }

        if ($this->getUserStateFromRequest($this->context . '.group.target_user', 'group_target_user', false, 'bool')) {
            $group['target_user'] = 'a.target_user';
        }

        if ($this->getUserStateFromRequest($this->context . '.group.ip', 'group_ip', false, 'bool')) {
            $group['ip'] = 'a.ip';
        }

        if ($this->getUserStateFromRequest($this->context . '.group.operation', 'group_operation', false, 'bool')) {
            $group['operation'] = 'a.operation';
        }

        $this->setState('group', $group);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 6.3.0-BETA3
     */
    protected function getListQuery(): QueryInterface
    {
        // Create a new query object.
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'kl.*, u.name AS user_name, u.username AS user_username, tu.name AS targetuser_name, tu.username AS targetuser_username,
                kc.name AS category_name, kt.subject AS topic_subject'
            )
        );
        $query->from($db->quoteName('#__kunena_logs', 'kl'));

        // Join over the users for the linked user.
        $query->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = kl.user_id');

        // Join over the users for the linked target_user.
        $query->join('LEFT', $db->quoteName('#__users', 'tu') . ' ON tu.id = kl.target_user');

        // Join over the categories for the linked category
        $query->join('LEFT', $db->quoteName('#__kunena_categories', 'kc') . ' ON kc.id = kl.category_id');

        // Join over the topics for the linked topic
        $query->join('LEFT', $db->quoteName('#__kunena_topics', 'kt') . ' ON kt.id = kl.topic_id');


        // Filter by search.
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('kl.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where('(u.username LIKE ' . $search
                    . ' OR u.name LIKE ' . $search
                    . ' OR tu.name LIKE ' . $search
                    . ' OR tu.username LIKE ' . $search
                    . ' OR kc.name LIKE ' . $search
                    . ' OR kt.subject LIKE ' . $search
                    . ' OR kl.ip LIKE ' . $search
                    . ')');
            }
        }

        // Filter by type.
        $type = $this->getState('filter.type');

        if (!empty($type)) {
            $type = $db->quote('%' . $db->escape($type, true) . '%');
            $query->where('kl.type LIKE ' . $type);
        }

        // Filter by operation.
        $operation = $this->getState('filter.operation');

        if (!empty($operation)) {
            $operation = $db->quote('%' . $db->escape($operation, true) . '%');
            $query->where('kl.operation LIKE ' . $operation);
        }

        // Filter by user.
        $user = $this->getState('filter.user');

        if ($user !== '') {
            $query->where('kl.user_id = ' . (int) $user);
        }

        // Filter by target_user.
        $target_user = $this->getState('filter.target_user');

        if ($target_user !== '') {
            $query->where('kl.target_user = ' . (int) $target_user);
        }

        // Filter by category.
        $category = $this->getState('filter.category');

        if ($category !== '') {
            $query->where('kl.category_id = ' . (int) $category);
        }

        // Filter by topic.
        $topic = $this->getState('filter.topic');

        if ($topic !== '') {
            $query->where('kl.topic_id = ' . (int) $topic);
        }

        // Filter by time_start state.
        $time_start = $this->getState('filter.time_start');

        if ($time_start !== '') {
            $query->where("kl.time > {$db->quote(strtotime($time_start))}");
        }

        // Filter by time_stop state.
        $time_stop = $this->getState('filter.time_stop');

        if ($time_stop !== '') {
            $query->where("kl.time < {$db->quote(strtotime($time_stop))}");
        }
        // Add the list ordering clause.
        $direction = strtoupper($this->state->get('list.direction'));

        switch ($this->state->get('list.ordering')) {
            case 'id':
                $query->order('kl.id ' . $direction);
                break;
            case 'time':
                $query->order('kl.time ' . $direction);
                break;
            case 'type':
                $query->order('kl.type ' . $direction);
                break;
            case 'operation':
                $query->order('kl.operation ' . $direction);
                break;
            case 'user':
                $query->order('user_username ' . $direction);
                break;
            case 'category':
                $query->order('category_name ' . $direction);
                break;
            case 'topic':
                $query->order('topic_subject ' . $direction);
                break;
            case 'target_user':
                $query->order('targetuser_username ' . $direction);
                break;
            case 'ip':
                $query->order('kl.ip ' . $direction);
                break;
        }

        return $query;
    }
}
