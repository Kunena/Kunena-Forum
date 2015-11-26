<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class KunenaAdminModelPlugins extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		$this->option = 'com_kunena';

		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'enabled',
				'name',
				'element',
				'access',
				'extension_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->context = 'com_kunena.admin.plugins';

		$filter_active = '';

		// Load the filter state.
		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $value !== '' ? $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.enabled', 'filter_enabled', '', 'string');
		$this->setState('filter.enabled', $value !== '' ? $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.name', 'filter_name', null, 'cmd');
		$this->setState('filter.name', $value !== '' ? $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.element', 'filter_element', '');
		$this->setState('filter.element', $value !== '' ? $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $value !== '' ? $value : null);

		$this->setState('filter.active', !empty($filter_active));

		// Load the parameters.
		$params = JComponentHelper::getParams('com_plugins');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('folder', 'asc');
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
	 * @return  string    A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.enabled');
		$id .= ':' . $this->getState('filter.name');
		$id .= ':' . $this->getState('filter.element');
		$id .= ':' . $this->getState('filter.access');

		return parent::getStoreId($id);
	}

	/**
	 * Returns an object list
	 *
	 * @param   JDatabaseQuery  $query      The query
	 * @param   int            $limitstart  Offset
	 * @param   int            $limit      The number of records
	 *
	 * @return  array
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$search   = $this->getState('filter.name') ? $this->getState('filter.name') : $this->getState('filter.search');
		$ordering = $this->getState('list.ordering', 'ordering');

		if ($ordering == 'name' || (!empty($search) && stripos($search, 'id:') !== 0))
		{
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
			$this->translate($result);

			if (!empty($search))
			{
				foreach ($result as $i => $item)
				{
					if (!preg_match("/$search/i", $item->name))
					{
						unset($result[$i]);
					}
				}
			}

			$lang      = JFactory::getLanguage();
			$direction = ($this->getState('list.direction') == 'desc') ? -1 : 1;
			Joomla\Utilities\ArrayHelper::sortObjects($result, $ordering, $direction, true, $lang->getLocale());

			$total                                      = count($result);
			$this->cache[$this->getStoreId('getTotal')] = $total;

			if ($total < $limitstart)
			{
				$limitstart = 0;
				$this->setState('list.start', 0);
			}

			return array_slice($result, $limitstart, $limit ? $limit : null);
		}
		else
		{
			// Add the list ordering clause.
			$direction = strtoupper($this->state->get('list.direction'));

			switch ($this->state->get('list.ordering'))
			{
				case 'ordering':
					$query->order('a.ordering ' . $direction);
					break;
				case 'enabled':
					$query->order('a.enabled ' . $direction);
					break;
				case 'element':
					$query->order('a.element ' . $direction);
					break;
				case 'access':
					$query->order('a.access ' . $direction);
					break;
				default:
					$query->order('a.extension_id ' . $direction);
			}

			$result = parent::_getList($query, $limitstart, $limit);
			$this->translate($result);

			return $result;
		}
	}

	/**
	 * Translate a list of objects
	 *
	 * @param   array  $items  The array of objects
	 *
	 * @return  array The array of translated objects
	 */
	protected function translate(&$items)
	{
		$lang = JFactory::getLanguage();

		foreach ($items as &$item)
		{
			$source    = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
			$extension = 'plg_' . $item->folder . '_' . $item->element;
			$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false)
			|| $lang->load($extension . '.sys', $source, null, false, false)
			|| $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			|| $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false);
			$item->name = JText::_($item->name);
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.extension_id , a.name, a.element, a.folder, a.checked_out, a.checked_out_time,' .
				' a.enabled, a.access, a.ordering'
			)
		);
		$query->from($db->quoteName('#__extensions') . ' AS a');

		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$query->where($db->quoteName('folder') . '= ' . $db->quote('kunena'));

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Filter by access level.
		$access = $this->getState('filter.access');

		if ($access)
		{
			$query->where('a.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.enabled');

		if (is_numeric($published))
		{
			$query->where('a.enabled = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.enabled IN (0, 1))');
		}

		// Filter by state
		$query->where('a.state >= 0');

		// Filter by element.
		$search = $this->getState('filter.element');

		if ($search)
		{
			$query->where('a.element LIKE ' . $db->quote("%$search%"));
		}

		// Filter by search in id
		if (!empty($search) && stripos($search, 'id:') === 0)
		{
			$query->where('a.extension_id = ' . (int) substr($search, 3));
		}

		return $query;
	}
}
