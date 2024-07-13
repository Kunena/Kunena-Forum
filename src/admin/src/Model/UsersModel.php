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
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Users Model for Kunena
 *
 * @since   Kunena 2.0
 */
class UsersModel extends ListModel
{
    /**
     * @var KunenaUser|null
     * @since version
     */
    private $me;

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
                'username',
                'name',
                'rank',
                'email',
                'ip',
                'signature',
                'enabled',
                'banned',
                'moderator',
            ];
        }

        $this->me = KunenaUserHelper::getMyself();

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
    public function getForm($data = [], $loadData = true): void
    {
        // TODO: Implement getForm() method.
    }

    /**
     * Method to get User objects of data items.
     *
     * @return  boolean|KunenaUser|array
     *
     * @throws  Exception
     * @since   Kunena 3.0
     */
    public function getItems()
    {
        // Get a storage key.
        $store = $this->getStoreId();

        // Try to load the data from internal storage.
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }

        $items = parent::getItems();
        $ids   = array_column($items, 'id');

        $instances = KunenaUserHelper::loadUsers($ids);

        // Add the items to the internal cache.
        $this->cache[$store] = $instances;

        return $this->cache[$store];
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
        $id .= ':' . $this->getState('filter.username');
        $id .= ':' . $this->getState('filter.email');
        $id .= ':' . $this->getState('filter.ip');
        $id .= ':' . $this->getState('filter.rank');
        $id .= ':' . $this->getState('filter.signature');
        $id .= ':' . $this->getState('filter.enabled');
        $id .= ':' . $this->getState('filter.banned');
        $id .= ':' . $this->getState('filter.moderator');

        return parent::getStoreId($id);
    }

    /**
     * Method to get html list of Kunena categories
     *
     * @return  string
     *
     * @throws  Exception
     * @since   Kunena 3.0
     */
    public function getModCatsList(): string
    {
        $options = [];
        $me      = KunenaUserHelper::getMyself();

        if ($me->isAdmin()) {
            $options[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_GLOBAL_MODERATOR'));
        }

        return HTMLHelper::_('kunenaforum.categorylist', 'catid[]', 0, $options, ['action' => 'admin'], 'class="input-block-level w-100" multiple="multiple" size="5"', 'value', 'text', 0, 'catidmodslist');
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
    protected function populateState($ordering = 'username', $direction = 'asc')
    {
        $app = Factory::getApplication();

        // Adjust the context to support modal layouts.
        $layout        = $app->input->get('layout');
        $this->context = 'com_kunena.admin.users';

        if ($layout) {
            $this->context .= '.' . $layout;
        }

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $rank = $this->getUserStateFromRequest($this->context . '.filter.rank', 'filter_rank', '');
        $this->setState('filter.rank', $rank);

        $signature = $this->getUserStateFromRequest($this->context . '.filter.signature', 'filter_signature', '');
        $this->setState('filter.signature', $signature);

        $enabled = $this->getUserStateFromRequest($this->context . '.filter.enabled', 'filter_enabled', '');
        $this->setState('filter.enabled', $enabled);

        $banned = $this->getUserStateFromRequest($this->context . '.filter.banned', 'filter_banned', '');
        $this->setState('filter.banned', $banned);

        $moderator = $this->getUserStateFromRequest($this->context . '.filter.moderator', 'filter_moderator', '');
        $this->setState('filter.moderator', $moderator);

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
        $query = $db->createQuery();

        // Select the required fields from the table.
        // We construct a composedBanned here to we can correctly sort of banned status
        $query->select(
        	$this->getState(
            	'list.select',
            	'a.id, CASE WHEN a.block = 1 THEN "9999-12-31 23:59:59" ELSE ku.banned END AS composedBanned'
            )
        );
        $query->from($db->quoteName('#__users', 'a'));

        // Join over the users for the linked user.
        $query->join('LEFT', $db->quoteName('#__kunena_users', 'ku') . ' ON a.id = ku.userid');

        // Filter by search.
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where('(a.username LIKE ' . $search . ' OR a.name LIKE ' . $search . ' OR a.email LIKE ' . $search . ' OR ku.ip LIKE ' . $search . ' OR a.id LIKE ' . $search . ')');
            }
        }

        // Filter by rank.
        $rank = $this->getState('filter.rank');

        if (!empty($rank)) {
            $rank = $db->quote('%' . $db->escape($rank, true) . '%');
            $query->where('ku.rank LIKE ' . $rank);
        }

        // Filter by signature.
        $filter = $this->getState('filter.signature');

        if ($filter !== '') {
            if ($filter) {
                $query->where("(ku.signature!={$db->quote('')} AND ku.signature IS NOT NULL)");
            } else {
                $query->where("(ku.signature={$db->quote('')} OR ku.signature IS NULL)");
            }
        }

        // Filter by enabled state.
        $filter = $this->getState('filter.enabled');

        if ($filter !== '') {
            $query->where('a.block=' . (int) $filter);
        }

        // Filter by banned state.
        $filter = $this->getState('filter.banned');

        if ($filter !== '') {
            $now      = new Date();
            $nullDate = $db->quote('1000-01-01 00:00:00');

            // We create a composedBanned here to be able to filter on this composed field
            $composedBannedCondition = 'CASE WHEN a.block = 1 THEN "9999-12-31 23:59:59" ELSE ku.banned END';

            if ($filter) {
                $query->where("$composedBannedCondition > {$nullDate}");
            } else {
                $query->where("( $composedBannedCondition IS NULL OR ( $composedBannedCondition >={$nullDate} AND $composedBannedCondition < {$db->quote($now->toSql())}))");
            }
        }

        $filter = $this->getState('filter.moderator');

        if ($filter !== '') {
            $query->where('ku.moderator =' . (int) $filter);
        }

        // Add the list ordering clause.
        $direction = strtoupper($this->state->get('list.direction'));

        switch ($this->state->get('list.ordering')) {
            case 'id':
                $query->order('a.id ' . $direction);
                break;
            case 'email':
                $query->order('a.email ' . $direction);
                break;
            case 'ip':
                $query->order('ku.ip ' . $direction);
                break;
            case 'rank':
                $query->order('ku.rank ' . $direction);
                break;
            case 'signature':
                $query->order('ku.signature ' . $direction);
                break;
            case 'enabled':
                $query->order('a.block ' . $direction);
                break;
            case 'banned':
                $query->order('composedBanned ' . $direction);
                break;
            case 'moderator':
                $query->order('ku.moderator ' . $direction);
                break;
            case 'name':
                $query->order('a.name ' . $direction);
                break;
            case 'username':
            default:
                $query->order('a.username ' . $direction);
        }

        return $query;
    }
}
