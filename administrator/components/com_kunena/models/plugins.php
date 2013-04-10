<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport ( 'joomla.application.component.modellist' );

/**
 * Methods supporting a list of plugin records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 * @since       1.6
 */
class KunenaAdminModelPlugins extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'enabled',
				'name',
				'element',
				'access',
				'checked_out',
				'checked_out_time',
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
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');
		if ($layout) {
			$this->context .= '.'.$layout;
		}

		$filter_active = '';

		// Load the filter state.
		$filter_active .= $value = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $value !== '' ? $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.enabled', 'filter_enabled', '', 'string');
		$this->setState('filter.enabled', $value !== '' ? (int) $value : null );

		$filter_active .= $value = $this->getUserStateFromRequest ( $this->context .'.filter.title', 'filter_title', '', 'string' );
		$this->setState ( 'filter.title', $value !== '' ? $value : null );

		$filter_active .= $value = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_element', '', 'string');
		$this->setState('filter.element', $value !== '' ? $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', '', 'string');
		$this->setState('filter.access', $value !== '' ? (int) $value : null );

		$this->setState ( 'filter.active',!empty($filter_active));

		// List state information.
		parent::populateState('name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string	A prefix for the store id.
	 *
	 * @return  string	A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.title');
		$id	.= ':'.$this->getState('filter.element');
		$id	.= ':'.$this->getState('filter.access');

		return parent::getStoreId($id);
	}


	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				' a.enabled, a.name, a.element, a.access, a.extension_id, a.checked_out, a.checked_out_time'
			)
		);
		$query->from($db->quoteName('#__extensions').' AS a');

		$query->where($db->quoteName('type').'= '.$db->quote('plugin').' AND '.$db->quoteName('folder').'= '.$db->quote('kunena'));

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Filter by published state
		$filter = $this->getState('filter.enabled');
		if (is_numeric($filter))
		{
			$query->where('a.enabled = '.(int) $filter);
		} elseif ($filter === '')
		{
			$query->where('(a.enabled IN (0, 1))');
		}

		// Filter by access level.
		$filter = $this->getState ( 'filter.title');
		if (!empty($filter)) {
			$title = $db->Quote('%'.$db->escape($filter, true).'%');
			$query->where('(a.name LIKE '.$title.')');
		}

		// Filter by access level.
		$filter = $this->getState ( 'filter.element');
		if (!empty($filter)) {
			$title = $db->Quote('%'.$db->escape($filter, true).'%');
			$query->where('(a.element LIKE '.$title.')');
		}

		// Filter by access level.
		$filter = $this->getState('filter.access');
		if ($filter)
		{
			$query->where('a.access = '.(int) $filter);
		}

		// Add the list ordering clause.
		$direction	= strtoupper($this->state->get('list.direction'));
		switch ($this->state->get('list.ordering')) {
			case 'enabled':
				$query->order('a.enabled ' . $direction);
				break;
			case 'name':
				$query->order('a.name ' . $direction);
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

		return $query;
	}
}
