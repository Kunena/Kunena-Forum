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

jimport('joomla.application.component.model');
require_once(JPATH_SITE.'/components/com_kunena/helpers/access.php');
require_once(JPATH_SITE.'/components/com_kunena/libraries/query.php');

/**
 * Threads model for the Kunena package.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelThreads extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	private
	 * @var		boolean
	 */
	var $__state_set	= false;

	/**
	 * An array of totals for the lists.
	 *
	 * @access	protected
	 * @var		array
	 */
	var $_totals		= array();

	/**
	 * Array of lists containing items.
	 *
	 * @access	protected
	 * @var		array
	 */
	var $_lists			= array();

	/**
	 * Overridden method to get model state variables.
	 *
	 * @access	public
	 * @param	string	$property	Optional parameter name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.0
	 */
	function getState($property = null)
	{
		if (!$this->__state_set)
		{
			$app		= &JFactory::getApplication();
			$user		= &JFactory::getUser();
			$config		= &JFactory::getConfig();
			$params		= $app->getParams('com_kunena');
			$context	= 'com_kunena.threads.';

			// Get the list filters.
			$this->setState('filter.category_id', JRequest::getInt('cat_id', $params->get('cat_id', null)));
			$this->setState('filter.category_path', JRequest::getString('cat_path', $params->get('cat_path', null)));

			// If the limit is set to -1, use the global config list_limit value.
			$limit	= JRequest::getInt('limit', $params->get('list_limit', 0));
			$limit	= ($limit === -1) ? $app->getCfg('list_limit', 20) : $limit;

			// Load the list state.
			$this->setState('list.start', JRequest::getInt('limitstart'));
			$this->setState('list.limit', $limit);
			$this->setState('list.state', 1);

			// Load the list ordering.
			switch ($params->get('threads-order'))
			{
				default:
					$this->setState('list.ordering', 'a.ordering DESC, last_post DESC');
					break;
			}

			// Load the user parameters.
			$this->setState('user',	$user);
			$this->setState('user.id', (int)$user->id);
			$this->setState('user.aid', (int)$user->get('aid'));

			// Load the check parameters.
			$this->setState('check.access',	true);
			$this->setState('check.state', true);

			// Load the parameters.
			$this->setState('params', $params);

			// Set the access type state.
			$this->setState('access', true);

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to get a list of items.
	 *
	 * @access	public
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	1.0
	 */
	function &getItems()
	{
		// Get a unique key for the current list state.
		$key = $this->_getStoreId('kunena.threads');

		// Try to load the value from internal storage.
		if (!empty ($this->_lists[$key])) {
			return $this->_lists[$key];
		}

		// Try to load the value from cache.
		//$cache = &JFactory::getCache('com_kunena', 'output');
		//$store = $this->_getStoreId('threads_list');
		//$data  = $cache->get($store);

		// Check the cache data.
		//if ($data !== false) {
		//	$this->_lists[$key] = unserialize($data);
		//	return $data;
		//}

		// Load the list.
		$query	= $this->_getListQuery();
		$rows	= $this->_getList($query->toString(), $this->getState('list.start'), $this->getState('list.limit'));

		// Setup some variables to check if the user has appropriate access rights.
		$access = $this->getState('access');
		if ($access === true) {
			$aids = KunenaHelperAccess::getAccessLevelsArray($this->getState('user.id'));
			if (JError::isError($aids)) {
				// TODO: we should throw an error
				$aids = array(0);
			}
		} else {
			$aid = (int)$this->getState('user.aid', 0);
		}

		// Handle parameters based fields.
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			if ($access === true) {
				$rows[$i]->authorized = (in_array($rows[$i]->access, $aids)) ? true : false;
			} else {
				$rows[$i]->authorized = ($rows[$i]->access <= $aid) ? true : false;
			}
		}

		// Push the value into cache.
		//$cache->store(serialize($rows), $store);

		// Add the rows to the internal storage.
		$this->_lists[$key] = $rows;

		return $this->_lists[$key];
	}

	/**
	 * Method to get a list pagination object.
	 *
	 * @access	public
	 * @return	object	A JPagination object.
	 * @since	1.0
	 */
	function &getPagination()
	{
		jimport('joomla.html.pagination');

		// Create the pagination object.
		$instance = new JPagination($this->getTotal(), (int)$this->getState('list.start'), (int)$this->getState('list.limit'));

		return $instance;
	}

	/**
	 * Method to get the total number of published items.
	 *
	 * @access	public
	 * @return	int		The number of published items.
	 * @since	1.0
	 */
	function getTotal()
	{
		// Get a unique key for the current list state.
		$key = $this->_getStoreId('kunena.threads');

		// Try to load the value from internal storage.
		if (!empty ($this->_totals[$key])) {
			return $this->_totals[$key];
		}

		// Try to load the value from cache.
		//$cache = &JFactory::getCache('com_kunena', 'output');
		//$store = $this->_getStoreId('threads_total');
		//$total = $cache->get($store);

		// Check the cache data.
		//if ($total !== false) {
		//	$this->_totals[$key] = (int)$total;
		//	return $total;
		//}

		// Load the total.
		$query = $this->_getListQuery();
		$return = (int)$this->_getListCount($query->toString());

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
	 * Gets the parent issue
	 * @return	object
	 */
	function &getCategory()
	{
		$result = null;
		if ($id = (int) $this->getState('filter.category_id'))
		{
			$model	= &JModel::getInstance('Category', 'KunenaModel');
			$model->setState('category_id', $id);
			$result	= $model->getItem();
		}
		return $result;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @access	protected
	 * @return	string		An SQL query
	 * @since	1.0
	 */
	function _getListQuery()
	{
		$query = new KQuery();

		// Select all fields from the articles table.
		$query->select('a.*');
		$query->from('`#__kunena_threads` AS a');

		// Resolve foriegn keys with the messages_text table.
		$query->select('MAX(b.created_time) AS last_post, COUNT(b.id) AS num_posts');
		$query->join('LEFT', '#__kunena_posts AS b ON b.thread_id = a.id');

		// Resolve foriegn keys with the categories table.
		$query->select('c.access, c.post_access');
		$query->join('LEFT', '#__kunena_categories AS c ON c.id = a.category_id');
		$query->group('a.id');

		// If the model is set to check item access, add to the query.
		if ($this->getState('check.access', true))
		{
			// Get the ACL system configuration.
			if ($this->getState('access') === true)
			{
				// Check access using extended ACL.
				jximport('jxtended.acl.acl');
				$levels = JxAcl::getAllowedAssetGroups('core', 'global.view', $this->getState('user.id'));

				if (JError::isError($levels)) {
					// TODO: we should throw an error
					$levels = false;
				}
				if ($levels) {
					$query->where('c.access IN ('.$levels.')');
				}
				else {
					$query->where('c.access = 0');
				}
			}
			else
			{
				// Check access using base ACL.
				$query->where('c.access <= ' . (int)$this->getState('user.aid', 0));
			}
		}

		// If the model is set to check publication state, add to the query.
		if ($this->getState('check.state', true)) {
			$query->where('a.published = 1');
		}

		// Filter the threads over the category if set.
		$category_id = $this->getState('filter.category_id');
		if ($category_id !== null) {
			$query->where('a.category_id = '.(int)$category_id);
		}

		// Add the list ordering clause.
		$query->order($this->_db->getEscaped($this->getState('list.ordering')));

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
	 * @param	string		$id		A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.0
	 */
	function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.state');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('check.access');
		$id	.= ':'.$this->getState('check.state');
		$id	.= ':'.$this->getState('user.aid');
		$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.category_path');

		return md5($id);
	}
}
