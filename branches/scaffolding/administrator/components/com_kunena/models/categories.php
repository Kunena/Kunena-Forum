<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

jximport('jxtended.application.component.model.list');
jximport('jxtended.database.query');

/**
 * Categories model for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelCategories extends JXModelList
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	 var $_context = 'kunena.categories';

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @access	protected
	 * @return	string		An SQL query
	 * @since	1.0
	 */
	function _getListQuery()
	{
		// Create a new query object.
		$query = new JXQuery;

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__kunena_categories` AS a');

		// Self-join to get level information and pre-order tree traversal results.
		$query->select('COUNT(DISTINCT c2.id) AS level');
		$query->join('LEFT OUTER', '`#__kunena_categories` AS c2 ON a.left_id > c2.left_id AND a.right_id < c2.right_id');
		$query->group('a.id');

		// Exclude the root category.
		$query->where('a.id > 1');

		// Filter the items over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where('a.title LIKE '.$this->_db->Quote('%'.$this->_db->getEscaped($search, true).'%'));
		}

		// Filter the items over the published state if set.
		if ($this->getState('check.state')) {
			$state_id = $this->getState('filter.state');
			if ($state_id !== '*' and $state_id !== null) {
				$query->where('a.published = '.(int)$state_id);
			}
		}

		// Add the list ordering clause.
		$query->order($this->_db->getEscaped($this->getState('list.ordering', 'a.left_id')).' '.$this->_db->getEscaped($this->getState('list.direction', 'ASC')));

		/*
		 * Resolve Foriegn Keys.
		 */

		// Checked out editor.
		$query->select('co.name AS editor');
		$query->join('LEFT', '`#__users` AS co ON co.id = a.checked_out');

		// Access level.
		$query->select('g.title AS access_name');
		$query->join('LEFT', '`#__access_assetgroups` AS g ON g.id=a.access');

		// Access level.
		$query->select('pg.title AS post_access_name');
		$query->join('LEFT', '`#__access_assetgroups` AS pg ON pg.id=a.post_access');

		//echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';
		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @access	protected
	 * @param	string		$context	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.0
	 */
	function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.select');
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.state');

		return md5($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @access	protected
	 * @return	void
	 * @since	1.0
	 */
	function _populateState()
	{
		// Initialize variables.
		$app		= &JFactory::getApplication('administrator');
		$user		= &JFactory::getUser();
		$params		= JComponentHelper::getParams('com_kunena');
		$context	= 'com_kunena.categories.';

		// Load the filter state.
		$this->setState('filter.search', $app->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));
		$this->setState('filter.state', $app->getUserStateFromRequest($context.'filter.state', 'filter_state', '*', 'string'));

		// Load the list state.
		$this->setState('list.start', $app->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($context.'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
		$this->setState('list.ordering', $app->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'a.left_id', 'cmd'));
		$this->setState('list.direction', $app->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

		// Load the user parameters.
		$this->setState('user',	$user);
		$this->setState('user.id', (int)$user->id);
		$this->setState('user.aid', (int)$user->get('aid'));

		// Load the check parameters.
		if ($this->_state->get('filter.state') === '*') {
			$this->setState('check.state', false);
		} else {
			$this->setState('check.state', true);
		}

		// Load the parameters.
		$this->setState('params', $params);
	}
}