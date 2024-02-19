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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

/**
 * Ranks Model for Kunena
 *
 * @since 3.0
 */
class RanksModel extends ListModel
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
                'rankId',
                'rankTitle',
                'rankMin',
                'rankSpecial',
                'rankImage',
            ];
        }

        parent::__construct($config);
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
    protected function populateState($ordering = 'rankId', $direction = 'asc')
    {
        // For some reason kunena sets option to com_m instead of com_kunena.
        // That is why the workaround to set the context manually
        $this->context = 'com_kunena.admin.ranks';

        $app = Factory::getApplication();

        // Adjust the context to support modal layouts.
        $layout        = $app->input->get('layout');
        $this->context = 'com_kunena.admin.ranks';

        if ($layout) {
            $this->context .= '.' . $layout;
        }

        $this->setState('filter.rankTitle', $this->getUserStateFromRequest($this->context . 'filter.rankTitle', 'filter_rankTitle', null, 'string'));
        $this->setState('filter.rankSpecial', $this->getUserStateFromRequest($this->context . 'filter.rankSpecial', 'filter_rankSpecial', null, 'string'));
        $this->setState('filter.rankMin', $this->getUserStateFromRequest($this->context . 'filter.rankMin', 'filter_rankMin', null, 'int'));
        $this->setState('filter.search', $this->getUserStateFromRequest($this->context . 'filter.search', 'filter_search', null, 'string'));

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
        $id .= ':' . $this->getState('filter.rankSpecial');
        $id .= ':' . $this->getState('filter.rankMin');

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
        $query = $db->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                'a.rankId, a.rankTitle, a.rankMin, a.rankSpecial, a.rankImage'
            )
        );

        $query->from($db->quoteName('#__kunena_ranks', 'a'));

        // Filter by access level.
        $filter = $this->getState('filter.search');

        if (!empty($filter)) {
            // Searching on title down't work as expected because title is stored as language string
            $title = $db->quote('%' . $db->escape($filter, true) . '%');
            $query->where('(a.rankTitle LIKE ' . $title . ')');
        }

        $filter = $this->getState('filter.rankSpecial');

        if (is_numeric($filter)) {
            $query->where('a.rankSpecial = ' . (int) $filter);
        }

        $filter = $this->getState('filter.rankMin');

        if (is_numeric($filter)) {
            $query->where('a.rankMin > ' . (int) $filter);
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'rankId');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }
}
