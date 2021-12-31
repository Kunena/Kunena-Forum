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
 * Statistics Model for Kunena
 *
 * @since 5.0
 */
class KunenaAdminModelStatistics extends \Joomla\CMS\MVC\Model\ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      Kunena
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'user',
				'time_start',
				'time_stop',
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
	 * @since   3.1
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
	 * @param   string $id A prefix for the store id.
	 *
	 * @return    string        A store id.
	 * @since Kunena
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.user');
		$id .= ':' . $this->getState('filter.time_start');
		$id .= ':' . $this->getState('filter.time_stop');

		return parent::getStoreId($id);
	}

	/**
	 * Build a finder query to load the list data.
	 *
	 * @param   string $field field
	 *
	 * @return KunenaLogFinder
	 * @throws Exception
	 * @since Kunena
	 */
	protected function getFinder($field = 'user_id')
	{
		// Get a storage key.
		$store = $this->getStoreId('getFinder_' . $field);

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Create a new query object.
		$db     = $this->getDbo();
		$finder = new KunenaLogFinder;

		// Filter by username or name.
		$filter = $this->getState('filter.user');

		if (!empty($filter))
		{
			$filter = $db->quote('%' . $db->escape($filter, true) . '%');
			$finder->innerJoin('#__users AS u ON u.id=a.' . $field);
			$finder->where('u.username', 'LIKE', $filter, false);
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

		$access = KunenaAccess::getInstance();
		$finder->where($field, 'IN', array_keys($access->getAdmins() + $access->getModerators()));
		$finder->where('type', '!=', 3);

		$finder->order($field, 'asc');

		$finder->select('MAX(a.id) AS id, MAX(a.time) AS time, COUNT(*) AS count');

		$finder->group($field);
		$finder->group('type');
		$finder->group('operation');

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
	 * @since   3.1
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

		$access  = KunenaAccess::getInstance();
		$userIds = array_keys($access->getAdmins() + $access->getModerators());

		$data = array();

		foreach ($userIds as $id)
		{
			$class          = new stdClass;
			$class->user_id = $id;
			$class->posts   = 0;
			$class->moves   = 0;
			$class->edits   = 0;
			$class->deletes = 0;
			$class->thanks  = 0;
			$data[$id]      = $class;
		}

		// Load the list items.
		$items = $this->getFinder()
			->start((int) $this->getStart())
			->limit((int) $this->getState('list.limit'))
			->find();

		foreach ($items as $item)
		{
			$class = $data[$item->user_id];

			switch ($item->operation)
			{
				case KunenaLog::LOG_TOPIC_CREATE:
				case KunenaLog::LOG_POST_CREATE:
					$class->posts += $item->count;
					break;

				case KunenaLog::LOG_TOPIC_MODERATE:
				case KunenaLog::LOG_POST_MODERATE:
					$class->moves += $item->count;
					break;

				case KunenaLog::LOG_TOPIC_EDIT:
				case KunenaLog::LOG_POST_EDIT:
					// Case KunenaLog::LOG_PRIVATE_POST_EDIT:
					if ($item->type == KunenaLog::TYPE_MODERATION)
					{
						$class->edits += $item->count;
					}
					break;

				case KunenaLog::LOG_POST_DELETE:
					// Case KunenaLog::LOG_PRIVATE_POST_DELETE:
					if ($item->type == KunenaLog::TYPE_MODERATION)
					{
						$class->deletes += $item->count;
					}
					break;
			}
		}

		unset($items);

		$items = $this->getFinder('target_user')
			->start((int) $this->getStart())
			->limit((int) $this->getState('list.limit'))
			->find();

		foreach ($items as $item)
		{
			if (!isset($data[$item->user_id]))
			{
				$class                = new stdClass;
				$class->user_id       = $item->user_id;
				$class->posts         = 0;
				$class->moves         = 0;
				$class->edits         = 0;
				$class->deletes       = 0;
				$class->thanks        = 0;
				$data[$item->user_id] = $class;
			}

			$class = $data[$item->user_id];

			switch ($item->operation)
			{
				case KunenaLog::LOG_POST_THANKYOU:
					$class->thanks += $item->count;
					break;
			}
		}

		unset($items);

		KunenaUserHelper::loadUsers($userIds);

		// Add the items to the internal cache.
		$this->cache[$store] = $data;

		return $this->cache[$store];
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   null $ordering  ordering
	 * @param   null $direction direction
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

		$now   = new \Joomla\CMS\Date\Date;
		$month = new \Joomla\CMS\Date\Date('-1 month');

		$filter_active = '';

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.user', 'filter_user', '', 'string');
		$this->setState('filter.user', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.time_start', 'filter_time_start', $month->format('Y-m-d'), 'string');
		$this->setState('filter.time_start', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.time_stop', 'filter_time_stop', '', 'string');
		$this->setState('filter.time_stop', $value);

		$this->setState('filter.active', !empty($filter_active));

		// List state information.
		parent::populateState('user_id', 'asc');
	}
}
