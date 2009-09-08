<?php
/**
 * @version		$Id: recent.php 1004 2009-08-16 23:03:00Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
kimport('database.query');
kimport('user.user');

/**
 * Categories model for the Kunena package.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelCategories extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $__state_set = false;

	/**
	 * An array of totals for the lists.
	 *
	 * @var		array
	 * @since	1.6
	 */
	protected $_totals = array();

	/**
	 * Array of lists containing items.
	 *
	 * @var		array
	 * @since	1.6
	 */
	protected $_lists = array();

	/**
	 * The model context for caching.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $_context = 'com_kunena.categories';

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	Optional default value.
	 * @return	mixed	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null)
	{
		if (!$this->__state_set)
		{
			// Get the application object and component options.
			$app	= JFactory::getApplication();
			$params	= $app->getParams('com_kunena');

			// If the limit is set to -1, use the global config list_limit value.
			$limit	= JRequest::getInt('limit', $params->get('list_limit', 20));
			$limit	= ($limit === -1) ? $app->getCfg('list_limit', 20) : $limit;

			// Load the list state.
			$this->setState('list.start', JRequest::getInt('limitstart'));
			$this->setState('list.limit', $limit);

			// Load model type
			// nested = default categories view 3 levels deep
			// flat = flat list of categories ordered by name
			$this->setState('type', JRequest::getCmd('type', 'nested'));

			$this->setState('order', JRequest::getCmd('order', 'asc'));

			// Root category for the model - allows for menu links to sub segments
			// of the category hierarchy
			$this->setState('category.id', JRequest::getInt('category', 0));

			// Load the user parameters.
			$user = JFactory::getUser();
			$this->setState('user',	$user);
			$this->setState('user.id', (int)$user->id);
			$this->setState('user.aid', (int)$user->get('aid'));

			// Load the parameters.
			$this->setState('params', $params);

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to get a list of items.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	1.6
	 */
	public function getItems()
	{
		// Get a unique key for the current list state.
		$key = $this->_getStoreId($this->_context);

		// Try to load the value from internal storage.
		if (!empty($this->_lists[$key])) {
			return $this->_lists[$key];
		}

		// Try to load the value from cache.
		//$cache = &JFactory::getCache('com_kunena', 'output');
		//$store = $this->_getStoreId('categories_list');
		//$data  = $cache->get($store);

		// Check the cache data.
		//if ($data !== false) {
		//	$this->_lists[$key] = unserialize($data);
		//	return $data;
		//}

		// Load the list.
		$query	= $this->_getListQuery();
		$rows	= $this->_getList($query->toString(), $this->getState('list.start'), $this->getState('list.limit'));

		// Push the value into cache.
		//$cache->store(serialize($rows), $store);

		switch ($this->getState('type'))
		{
		    case 'nested':
		    	$nested = array();
		    	foreach ($rows as $row) {
		    		if ($row->parent)
		    			$nested['category_'.$row->parent][] = $row;
		    		else
		    			$nested['sections'][] = $row;
		    	}
		    	$this->_lists[$key] = $nested;
				break;
				
		    case 'flat':
				// Add the rows to the internal storage.
				$this->_lists[$key] = $rows;
		    	break;
		    	
		    default:
		        // Invalid view type specified
		}

		return $this->_lists[$key];
	}

	/**
	 * Method to get a list pagination object.
	 *
	 * @return	object	A JPagination object.
	 * @since	1.6
	 */
	public function getPagination()
	{
		jimport('joomla.html.pagination');

		// Create the pagination object.
		$instance = new JPagination($this->getTotal(), (int)$this->getState('list.start'), (int)$this->getState('list.limit'));

		return $instance;
	}

	/**
	 * Method to get the total number of published items.
	 *
	 * @return	integer	The number of published items.
	 * @since	1.6
	 */
	public function getTotal()
	{
		// Get a unique key for the current list state.
		$key = $this->_getStoreId($this->_context);

		// Try to load the value from internal storage.
		if (!empty ($this->_totals[$key])) {
			return $this->_totals[$key];
		}

		// Try to load the value from cache.
		//$cache = &JFactory::getCache('com_kunena', 'output');
		//$store = $this->_getStoreId('categories_total');
		//$total = $cache->get($store);

		// Check the cache data.
		//if ($total !== false) {
		//	$this->_totals[$key] = (int)$total;
		//	return $total;
		//}

		// Load the total.
		$query = $this->_getTotalQuery();
		$this->_db->setQuery($query->toString());
		$return = (int) $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Push the value into internal storage.
		$this->_totals[$key] = $return;

		// Push the value into cache.
		//$cache->store($total, $store);

		return $this->_totals[$key];
	}

	/**
	 * Method to get announcement
	 *
	 * @return	array	Announcements (currently limited to one).
	 * @since	1.6
	 */
	public function getAnnouncement()
	{
		if (empty($this->_models['announcement'])) {
			$this->_models['announcement'] = &JModel::getInstance('Announcement', 'KunenaModel');
		}

		if (empty($this->_models['announcement'])) {
			$null = null;
			return $null;
		}
		$this->_models['announcement']->getState();
		$this->_models['announcement']->setState('list.start', 0);
		$this->_models['announcement']->setState('list.limit', 1);
		$this->_models['announcement']->setState('type', 'published');
		return $this->_models['announcement']->getItems();
	}

	/**
	 * Method to get forum statistics
	 *
	 * @return	array	Statistics object.
	 * @since	1.6
	 */
	public function getStatistics()
	{
		if (empty($this->_models['statistics'])) {
			$this->_models['statistics'] = &JModel::getInstance('Statistics', 'KunenaModel');
		}

		if (empty($this->_models['statistics'])) {
			$null = null;
			return $null;
		}
		$this->_models['statistics']->getState();
		$this->_models['statistics']->setState('type', 'all');
		$stats['users'] = $this->_models['statistics']->getUserStats();
		$stats['forum'] = $this->_models['statistics']->getForumStats();
		$stats['recent'] = $this->_models['statistics']->getRecentStats();
		return $stats;
	}

	/**
	 * Method to build an SQL query to get the total count of item
	 *
	 * @return	string	An SQL query.
	 * @since	1.6
	 */
	protected function _getTotalQuery()
	{
		$query = new KQuery();

		// Build base query
		$user = KUser::getInstance(true);

		$query->select('count(*)');

		switch ($this->getState('type'))
		{
		    case 'nested':
		    case 'flat':
		        $query->from('#__kunena_categories AS c');
		        $query->where('c.id IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
		        $query->where('c.published=1');

		        break;
		    default:
		        // Invalid view type specified
		}

		// echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';

		return $query;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query.
	 * @since	1.6
	 */
	protected function _getListQuery()
	{
	    $query = new KQuery();
		$user = KUser::getInstance(true);

		// Build base query

		$query->select('c.*, m.subject, m.name AS username, m.userid');
	    $query->from('#__kunena_categories AS c');
	    $query->leftJoin('#__kunena_messages AS m ON c.id_last_msg = m.id');
	    $query->where('c.id IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
	    $query->where('c.published=1');
		
		switch ($this->getState('type'))
		{
			case 'nested':
		   		if (strtolower($this->getState('order'))=='desc')
				{
					$query->order('c.parent DESC, c.ordering DESC');
				}
				else
				{
					$query->order('c.parent, c.ordering');
				}
				break;
		    case 'flat':
		   		if (strtolower($this->getState('order'))=='desc')
				{
					$query->order('c.name DESC');
				}
				else
				{
					$query->order('c.name');
				}

		        break;
		    default:
		        // Invalid view type specified
		}

		// echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	A prefix for the store id.
	 * @return	string	A store id.
	 * @since	1.6
	 */
	protected function _getStoreId($id = '')
	{
		// Compile the store id.
		// TODO: Add additional states
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.state');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('check.state');
		$id	.= ':'.$this->getState('user.aid');
		$id	.= ':'.$this->getState('filter.parent_id');
		$id	.= ':'.$this->getState('type');

		return md5($id);
	}
}
