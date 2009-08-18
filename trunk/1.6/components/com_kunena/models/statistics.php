<?php
/**
 * @version		$Id:  $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
kimport('database.query');

/**
 * Statistics model for the Kunena package.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelStatistics extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	private $__state_set = false;

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
	protected $_context = 'com_kunena.announcement';

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

			$this->setState('list.state', 1);

			// Load model type
			// all = all announcements
			// published = published announcements
			$this->setState('type', JRequest::getCmd('type', 'all'));
			
			// Load the check parameters.
			$this->setState('check.state', true);

			// Load the parameters.
			$this->setState('params', $params);

			$this->__state_set = true;
		}

		return parent::getState($property);
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
	 * Method to get the general forum stats.
	 *
	 * @return	integer	The number of published items.
	 * @since	1.6
	 */
	public function getForumStats()
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

		// Load the total messages, threads, categories and sections.
		$query = $this->_getForumStatsQuery();
		$this->_db->setQuery($query->toString());
		$return = $this->_db->loadObject();

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
	 * Method to get the user count and last user.
	 *
	 * @return	integer	The number of published items.
	 * @since	1.6
	 */
	public function getUserStats()
	{
		// Get a unique key for the current list state.
		$key = $this->_getStoreId($this->_context);

		// Try to load the value from internal storage.
		if (!empty ($this->_users[$key])) {
			return $this->_users[$key];
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

		// Load the total messages, threads, categories and sections.
		$query = $this->_getLastUserQuery();
		$this->_db->setQuery($query->toString());
		$return = $this->_db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Load the total messages, threads, categories and sections.
		$query = $this->_getTotalUsersQuery();
		$this->_db->setQuery($query->toString());
		$return->users = $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Push the value into internal storage.
		$this->_users[$key] = $return;

		// Push the value into cache.
		//$cache->store($total, $store);

		return $this->_users[$key];
	}

	/**
	 * Method to get the recent activity stats.
	 *
	 * @return	integer	The number of published items.
	 * @since	1.6
	 */
	public function getRecentStats()
	{
		// Get a unique key for the current list state.
		$key = $this->_getStoreId($this->_context);

		// Try to load the value from internal storage.
		if (!empty ($this->_recent[$key])) {
			return $this->_recent[$key];
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

		// Load the total messages, threads, categories and sections.
		$query = $this->_getRecentStatsQuery();
		$this->_db->setQuery($query->toString());
		$return = $this->_db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// Push the value into internal storage.
		$this->_recent[$key] = $return;

		// Push the value into cache.
		//$cache->store($total, $store);

		return $this->_recent[$key];
	}
	
	/**
	 * Method to build an SQL query to get the total users
	 *
	 * @return	string	An SQL query.
	 * @since	1.6
	 */
	protected function _getTotalUsersQuery()
	{
		$query = new KQuery();

		$query->select('COUNT(*) as users');
		$query->from('#__users');

		// echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';

		return $query;
	}

	/**
	 * Method to build an SQL query to get the last user
	 *
	 * @return	string	An SQL query.
	 * @since	1.6
	 */
	protected function _getLastUserQuery()
	{
		$query = new KQuery();

		$query->select('id AS last_userid, username AS last_username');
		$query->from('#__users');
		$query->order('id DESC');

		// echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';

		return $query;
	}
	
	/**
	 * Method to build an SQL query to get the forum statistics
	 *
	 * @return	string	An SQL query.
	 * @since	1.6
	 */
	protected function _getForumStatsQuery()
	{
		$query = new KQuery();

		$query->select('SUM(IF (parent>0, 0, numTopics)) AS threads, SUM(IF (parent>0, 0, numPosts+numTopics)) AS messages, SUM(parent=0) AS sections, SUM(parent>0) AS categories');
		$query->from('#__kunena_categories');

		// echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';

		return $query;
	}
	
	/**
	 * Method to build an SQL query to get the recent activity statistices
	 *
	 * @return	string	An SQL query.
	 * @since	1.6
	 */
	protected function _getRecentStatsQuery()
	{
		$query = new KQuery();

		$todaystart = strtotime(date('Y-m-d'));
		$yesterdaystart = $todaystart - (1 * 24 * 60 * 60);
		
		$query->select("SUM(time >= {$todaystart} AND parent=0) AS todayopen");
		$query->select("SUM(time >= {$yesterdaystart} AND time < {$todaystart} AND parent=0) AS yesterdayopen");
		$query->select("SUM(time >= {$todaystart} AND parent>0) AS todayanswered");
		$query->select("SUM(time >= {$yesterdaystart} AND time < {$todaystart} AND parent>0) AS yesterdayanswered");
		$query->from('#__kunena_messages');
		$query->where("time >= {$yesterdaystart} AND hold=0");
		
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
		$id	.= ':'.$this->getState('list.state');
		$id	.= ':'.$this->getState('check.state');

		return md5($id);
	}
}
