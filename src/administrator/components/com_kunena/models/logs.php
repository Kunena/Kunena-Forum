<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator
 * @subpackage    Models
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

jimport('joomla.application.component.model');

/**
 * Logs Model for Kunena
 *
 * @since 5.0
 */
class KunenaAdminModelLogs extends \Joomla\CMS\MVC\Model\ListModel
{
	/**
	 * Constructor.
	 *
	 * @param    array $config An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      Kunena
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
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
			);
		}

		$this->me = KunenaUserHelper::getMyself();

		parent::__construct($config);
	}

	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 *
	 * @throws Exception
	 * @since   5.0
	 */
	public function getTotal()
	{
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the total.
		$finder = $this->getFinder();

		$total = (int) $finder->count();

		// Add the total to the internal cache.
		$this->cache[$store] = $total;

		return $this->cache[$store];
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param    string $id A prefix for the store id.
	 *
	 * @return    string        A store id.
	 * @since Kunena
	 */
	protected function getStoreId($id = '')
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
	 * Build a finder query to load the list data.
	 *
	 * @return    KunenaLogFinder
	 * @throws Exception
	 * @since Kunena
	 */
	protected function getFinder()
	{
		// Get a storage key.
		$store = $this->getStoreId('getFinder');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Create a new query object.
		$db     = $this->getDbo();
		$finder = new KunenaLogFinder;

		// Filter by type.
		$filter = $this->getState('filter.type');

		if (!empty($filter))
		{
			$finder->where('a.type', '=', $filter);
		}

		// Filter by username or name.
		$filter = $this->getState('filter.user');

		if (!empty($filter))
		{
			$filter = $db->quote('%' . $db->escape($filter, true) . '%');
			$finder->innerJoin('#__users AS u ON u.id=a.user_id');
			$finder->where('u.username', 'LIKE', $filter, false);
		}

		// Filter by category.
		$filter = $this->getState('filter.category');

		if (!empty($filter))
		{
			$filter = $db->quote('%' . $db->escape($filter, true) . '%');
			$finder->innerJoin('#__kunena_categories AS c ON c.id=a.category_id');
			$finder->where('c.name', 'LIKE', $filter, false);
		}

		// Filter by topic.
		$filter = $this->getState('filter.topic');

		if (!empty($filter))
		{
			$filter = $db->quote('%' . $db->escape($filter, true) . '%');
			$finder->innerJoin('#__kunena_topics AS t ON t.id=a.topic_id');
			$finder->where('t.subject', 'LIKE', $filter, false);
		}

		// Filter by target username or name.
		$filter = $this->getState('filter.target_user');

		if (!empty($filter))
		{
			$filter = $db->quote('%' . $db->escape($filter, true) . '%');
			$finder->innerJoin('#__users AS tu ON tu.id=a.target_user');
			$finder->where('tu.username', 'LIKE', $filter, false);
		}

		// Filter by IP address.
		$filter = $this->getState('filter.ip');

		if (!empty($filter))
		{
			$filter = $db->quote('%' . $db->escape($filter, true) . '%');
			$finder->where('a.ip', 'LIKE', $filter, false);
		}

		// Filter by time.
		$start = $this->getState('filter.time_start');
		$stop  = $this->getState('filter.time_stop');

		if ($start || $stop)
		{
			$start = $start ? new \Joomla\CMS\Date\Date($start) : null;
			$stop  = $stop ? new \Joomla\CMS\Date\Date($stop . ' +1 day') : null;
			$finder->filterByTime($start, $stop);
		}

		// Filter by operation.
		$filter = $this->getState('filter.operation');

		if (!empty($filter))
		{
			$finder->where('a.operation', '=', $filter);
		}

		// Add the list ordering clause.
		$direction = $this->state->get('list.direction') == 'asc' ? 1 : -1;

		switch ($this->state->get('list.ordering'))
		{
			case 'type':
				$finder->order('type', $direction);
				break;
			case 'user':
				$finder->order('user_id', $direction);
				break;
			case 'category':
				$finder->order('category_id', $direction);
				break;
			case 'topic':
				$finder->order('topic_id', $direction);
				break;
			case 'target_user':
				$finder->order('target_user', $direction);
				break;
			case 'ip':
				$finder->order('ip', $direction);
				break;
			case 'time':
				$finder->order('time', $direction);
				break;
			case 'operation':
				$finder->order('operation', $direction);
				break;
			case 'id':
			default:
				$finder->order('id', $direction);
		}

		$usertypes = $this->state->get('filter.usertypes');

		// Filter by user type.

		if (is_numeric($usertypes))
		{
			$access = KunenaAccess::getInstance();

			switch ($usertypes)
			{
				case 0:
					$finder->where('user_id', '=', 0);
					break;
				case 1:
					$finder->where('user_id', '>', 0);
					break;
				case 2:
					$finder->where('user_id', '>', 0);
					$finder->where('user_id', 'NOT IN', array_keys($access->getAdmins() + $access->getModerators()));
					break;
				case 3:
					$finder->where('user_id', 'IN', array_keys($access->getModerators()));
					break;
				case 4:
					$finder->where('user_id', 'IN', array_keys($access->getAdmins()));
					break;
				case 5:
					$finder->where('user_id', 'IN', array_keys($access->getAdmins() + $access->getModerators()));
					break;
			}
		}

		$group = $this->getState('group');

		if ($group)
		{
			$finder->select('MAX(a.id) AS id, MAX(a.time) AS time, COUNT(*) AS count');

			foreach ($group as $field)
			{
				$finder->group($field);
			}
		}

		// Add the finder to the internal cache.
		$this->cache[$store] = $finder;

		return $this->cache[$store];
	}

	/**
	 * Method to get User objects of data items.
	 *
	 * @return  KunenaUser  List of KunenaUser objects found.
	 *
	 * @throws Exception
	 * @throws null
	 * @since   5.0
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$items = $this->getFinder()
			->start((int) $this->getStart())
			->limit((int) $this->getState('list.limit'))
			->find();

		$userIds1 = $items->map(function ($item, $key) {
			return $item->user_id;

		});
		$userIds2 = $items->map(function ($item, $key) {

			return $item->target_user;

		});
		$userIds  = array_unique(array_merge($userIds1->all(), $userIds2->all()));

		KunenaUserHelper::loadUsers($userIds);

		KunenaForumTopicHelper::getTopics($items->map(function ($item, $key) {
			return $item->topic_id;

		})->all());

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   null $ordering
	 * @param   null $direction
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$filter_active = '';

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.id', 'filter_id', '', 'string');
		$this->setState('filter.id', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '', 'string');
		$this->setState('filter.type', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.user', 'filter_user', '', 'string');
		$this->setState('filter.user', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '', 'string');
		$this->setState('filter.category', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.topic', 'filter_topic', '', 'string');
		$this->setState('filter.topic', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.target_user', 'filter_target_user', '', 'string');
		$this->setState('filter.target_user', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.ip', 'filter_ip', '', 'string');
		$this->setState('filter.ip', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.time_start', 'filter_time_start', '', 'string');
		$this->setState('filter.time_start', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.time_stop', 'filter_time_stop', '', 'string');
		$this->setState('filter.time_stop', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.operation', 'filter_operation', '', 'string');
		$this->setState('filter.operation', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.usertypes', 'filter_usertypes', '', 'string');
		$this->setState('filter.usertypes', $value);

		$this->setState('filter.active', !empty($filter_active));

		$group = array();

		if ($this->getUserStateFromRequest($this->context . '.group.type', 'group_type', false, 'bool'))
		{
			$group['type'] = 'a.type';
		}

		if ($this->getUserStateFromRequest($this->context . '.group.user', 'group_user', false, 'bool'))
		{
			$group['user'] = 'a.user_id';
		}

		if ($this->getUserStateFromRequest($this->context . '.group.category', 'group_category', false, 'bool'))
		{
			$group['category'] = 'a.category_id';
		}

		if ($this->getUserStateFromRequest($this->context . '.group.topic', 'group_topic', false, 'bool'))
		{
			$group['topic'] = 'a.topic_id';
		}

		if ($this->getUserStateFromRequest($this->context . '.group.target_user', 'group_target_user', false, 'bool'))
		{
			$group['target_user'] = 'a.target_user';
		}

		if ($this->getUserStateFromRequest($this->context . '.group.ip', 'group_ip', false, 'bool'))
		{
			$group['ip'] = 'a.ip';
		}

		if ($this->getUserStateFromRequest($this->context . '.group.operation', 'group_operation', false, 'bool'))
		{
			$group['operation'] = 'a.operation';
		}

		$this->setState('group', $group);

		// List state information.
		parent::populateState('id', 'desc');
	}
}
