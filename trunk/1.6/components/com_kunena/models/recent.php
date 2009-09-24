<?php
/**
 * @version		$Id$
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
class KunenaModelRecent extends JModel
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
	protected $_context = 'com_kunena.recent';

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
			if ($params->get('filter_limit_override', 1))
				$limit = JRequest::getInt('limit', $params->get('limit', 20));
			else
				$limit = $params->get('limit', 20);
			$limit = ($limit < 1) ? $app->getCfg('list_limit', 20) : $limit;

			// Load the list state.
			if ($params->get('filter_limitstart_allow', 1))
				$this->setState('list.start', JRequest::getInt('limitstart'));
			else
				$this->setState('list.start', 0);
			$this->setState('list.limit', $limit);

			// Load model type
			// all = recent topics accross all allowd categories
			// my = my recent topics
			// category = recent topics with a select category
			$this->setState('type', $type = JRequest::getCmd('type', 'all'));
			$this->setState('order', $params->get('order', 'desc'));

			// If recent request is for a category, we also get a category id
			$this->setState('category.id', JRequest::getInt('category', 0));

			// Load the user parameters.
			$user = JFactory::getUser();
			$this->setState('user',	$user);
			$this->setState('user.id', (int)$user->id);
			$this->setState('user.aid', (int)$user->get('aid'));

			// Load filter
			if ($params->get('filter_time_override', 1))
				$this->setState('filter.time', $app->getUserStateFromRequest('com_kunena.recent.'.$type.'.filter.time', 'filter_time', $params->get('filter_time', 720)));
			else
				$this->setState('filter.time', $params->get('filter_time', 720));
				
			$this->setState('filter.categories', $params->get('filter_categories', ''));
			$this->setState('filter.categories.which', $params->get('filter_categories_which', 0));
			
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

		// Add the rows to the internal storage.
		$this->_lists[$key] = $rows;

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
		$user = KUser::getInstance(true);

		// Build base query
		$filter_time = $this->getState('filter.time');
		switch ($filter_time) {
			//case 'new':
			case 'session':
				$time = JFactory::getDate($user->last_visit_time);
				break;	
			case 'all':
				break;
			default:
				$time = JFactory::getDate('-'.(int)$filter_time.' hours');
		}

		$query->select('count(*)');

		switch ($this->getState('type'))
		{
		    case 'all':
		        $query->from('#__kunena_threads AS t');
		        $query->where('t.hold=0 AND t.moved_id=0 AND t.catid IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
		        if (isset($time)) $query->where('last_post_time >'.$time->toUnix());

		        break;
		    case 'my':
				$query1 = new KQuery();
				$query2 = new KQuery();

				$query1->select('m.thread As thread');
		        $query1->from('#__kunena_messages AS m');
				$query1->from('#__kunena_threads AS t1');
				$query1->where('t1.id = m.thread');
		        $query1->where('t1.hold=0 AND t1.moved_id=0 AND t1.catid IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
		        if (isset($time)) $query1->where('t1.last_post_time >'.$time->toUnix());
		        $query1->where('m.userid = '.intval($this->getState('user.id')));

				$query2->select('f.thread As thread');
		        $query2->from('#__kunena_favorites AS f');
				$query2->from('#__kunena_threads AS t2');
				$query2->where('t2.id = f.thread');
		        $query2->where('t2.hold=0 AND t2.moved_id=0 AND t2.catid IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
		        if (isset($time)) $query2->where('t2.last_post_time >'.$time->toUnix());
		        $query2->where('f.userid = '.intval($this->getState('user.id')));

				$query->from('('.$query1->toString().' UNION '.$query2->toString().' ) AS t');

				break;
		    case 'category':
		        $query->from('#__kunena_threads AS t');
		        $query->where('t.hold=0 AND t.moved_id=0 AND t.catid IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
		        $query->where('t.catid = '.intval($this->getState('category.id')));

		        break;
		    default:
		        // Invalid view type specified
		}
		
		$catlist = trim($this->getState('filter.categories'));
		if ($catlist != '')
		{
			$not = $this->getState('filter.categories.which') ? '' : 'NOT ';
			// FIXME: instead of escape, clean the list
			$query->where('t.catid '.$not.'IN ('.$this->_db->getEscaped($catlist).')');	
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
		$filter_time = $this->getState('filter.time');
		switch ($filter_time) {
			//case 'new':
			case 'session':
				$time = JFactory::getDate($user->last_visit_time);
				break;	
			case 'all':
				break;
			default:
				$time = JFactory::getDate('-'.(int)$filter_time.' hours');
		}
		if (!$user->userid) $new = '0';
		else $new = '(t.last_post_time > '.(int)$user->last_visit_time.')';

		$query->select('t.*');
		$query->select('(f.thread > 0) AS myfavorite');
		$query->select($new.' AS new');
		$query->select('c.name AS catname');


		switch ($this->getState('type'))
		{
		    case 'all':
		        $query->from('#__kunena_threads AS t');
		        $query->where('t.hold=0 AND t.moved_id=0 AND t.catid IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
		        if (isset($time)) $query->where('t.last_post_time >'.$time->toUnix());

		        break;
		    case 'my':
		        $query->from('#__kunena_threads AS t');

				$query1 = new KQuery();
				$query2 = new KQuery();

				$query1->select('m.thread As thread');
		        $query1->from('#__kunena_messages AS m');
				$query1->from('#__kunena_threads AS t1');
				$query1->where('t1.id = m.thread');
		        $query1->where('t1.hold=0 AND t1.moved_id=0 AND t1.catid IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
		        if (isset($time)) $query1->where('t1.last_post_time >'.$time->toUnix());
		        $query1->where('m.userid = '.intval($user->userid));

				$query2->select('f.thread As thread');
		        $query2->from('#__kunena_favorites AS f');
				$query2->from('#__kunena_threads AS t2');
				$query2->where('t2.id = f.thread');
		        $query2->where('t2.hold=0 AND t2.moved_id=0 AND t2.catid IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
		        if (isset($time)) $query2->where('t2.last_post_time >'.$time->toUnix());
		        $query2->where('f.userid = '.intval($user->userid));

				$query->join('', '('.$query1->toString().' UNION '.$query2->toString().' ) AS tmp ON t.id = tmp.thread');

				break;
		    case 'category':
		        $query->from('#__kunena_threads AS t');
		        $query->where('t.hold=0 AND t.moved_id=0 AND t.catid IN ('.$this->_db->getEscaped($user->getAllowedCategories()).')');
		        $query->where('t.catid = '.intval($this->getState('category.id')));
		        $query->order('t.ordering');

		        break;
		    default:
		        // Invalid view type specified
		}

		$query->join('LEFT', '#__kunena_favorites AS f ON f.thread = t.id AND f.userid = '.intval($user->userid));
		$query->join('LEFT', '#__kunena_categories AS c ON c.id = t.catid');

		// Redundant: $query->where('t.moved_id = 0 and t.hold = 0');

		$catlist = trim($this->getState('filter.categories'));
		if ($catlist != '')
		{
			$not = $this->getState('filter.categories.which') ? '' : 'NOT ';
			// FIXME: instead of escape, clean the list
			$query->where('t.catid '.$not.'IN ('.$this->_db->getEscaped($catlist).')');	
		}
		
		$query->group('t.id');

		$order = strtoupper($this->getState('order'));
		if ($this->getState('type')=='my') {
			$query->order('f.thread '.$order);
		}

		$query->order('t.last_post_time '.$order);

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
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.state');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('check.state');
		$id	.= ':'.$this->getState('user.aid');
		$id	.= ':'.$this->getState('filter.parent_id');

		return md5($id);
	}
}
