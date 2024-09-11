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
 * Smileys Model for Kunena
 *
 * @since  3.0
 */
class SmiliesModel extends ListModel
{
    /**
     * @param   array  $config  config
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id',
                'code',
                'location',
                'emoticonbar',
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
     * @param   string  $ordering   ordering
     * @param   string  $direction  direction
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
        $layout        = $app->input->get('layout');
        $this->context = 'com_kunena.admin.smilies';

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
     * @since   Kunena 6.0
     */
    protected function getStoreId($id = ''): string
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected function getListQuery(): QueryInterface
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery();

        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.code, a.location, a.emoticonbar'
            )
        );

        $query->from($db->quoteName('#__kunena_smileys', 'a'));

        // Filter by search.
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where('(a.location LIKE ' . $search . ' OR ' . $search . ' OR a.emoticonbar LIKE ' . $search . ' OR a.code LIKE ' . $search . ' OR a.id LIKE ' . $search . ')');
            }
        }

        $filter = $this->getState('filter.code');

        if (!empty($filter)) {
            $code = $db->quote('%' . $db->escape($filter, true) . '%');
            $query->where('(a.code LIKE ' . $code . ')');
        }

        $filter = $this->getState('filter.location');

        if (!empty($filter)) {
            $location = $db->quote('%' . $db->escape($filter, true) . '%');
            $query->where('(a.location LIKE ' . $location . ')');
        }

        // Add the list ordering clause.
        $direction = strtoupper($this->state->get('list.direction'));

        switch ($this->state->get('list.ordering')) {
            case 'code':
                $query->order('a.code ' . $direction);
                break;
            case 'location':
                $query->order('a.location ' . $direction);
                break;
            case 'emoticonbar':
                $query->order('a.emoticonbar ' . $direction);
                break;
            default:
                $query->order('a.id ' . $direction);
        }

        return $query;
    }
}
