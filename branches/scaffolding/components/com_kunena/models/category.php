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
 * Category model for the Kunena package.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelCategory extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	protected
	 * @var		boolean
	 */
	var $__state_set = null;

	/**
	 * Internal array to hold thread data.
	 *
	 * @access	private
	 * @var		array
	 */
	var $_threads = array();

	/**
	 * Internal array to hold child category data.
	 *
	 * @access	private
	 * @var		array
	 */
	var $_children = array();

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
		// If the model state is uninitialized lets set some values we will need from the request.
		if (!$this->__state_set)
		{
			$app		= &JFactory::getApplication();
			$user		= &JFactory::getUser();
			$config		= &JFactory::getConfig();
			$params		= $app->getParams('com_kunena');
			$context	= 'com_kunena.category.';

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
			switch ($params->get('category-order'))
			{
				default:
					$this->setState('list.ordering', 'a.ordering DESC');
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

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * @return	array	List of items
	 */
	function &getCategory()
	{
		$false		= false;

		$query = new KQuery();

		// Select all fields from the articles table.
		$query->select('a.*');
		$query->from('`#__kunena_categories` AS a');

		// Filter the categories over the parent if set.
		$category_id = $this->getState('filter.category_id');
		if ($category_id !== null) {
			$query->where('a.id = '.(int)$category_id);
		}

		// Filter the categories over the parent if set.
		$category_path = $this->getState('filter.category_path');
		if (!empty($category_path)) {
			$query->where('a.path = '.$this->_db->Quote($category_path));
		}

		// Get the row from the database.
		//echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';
		$this->_db->setQuery($query->toString());
		$category = $this->_db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		$category->children = $this->_getChildrenByCategory($category->id);

		return $category;
	}

	function getLastReplies()
	{
		if (empty($this->_models['posts'])) {
			$this->_models['posts'] = &JModel::getInstance('Posts', 'KunenaModel');
		}

		if (empty($this->_models['posts'])) {
			$null = null;
			return $null;
		}


		$posts = $this->_models['posts']->getItems();
		return $posts;
	}

	function &getThreads()
	{
		if (empty($this->_models['threads'])) {
			$this->_models['threads'] = &JModel::getInstance('Threads', 'KunenaModel');
		}

		if (empty($this->_models['threads'])) {
			$null = null;
			return $null;
		}

		$threads = $this->_models['threads']->getItems();
		return $threads;
	}

	function getThreadsTotal()
	{
		if (empty($this->_models['threads'])) {
			$this->_models['threads'] = &JModel::getInstance('Threads', 'KunenaModel');
		}

		if (empty($this->_models['threads'])) {
			$null = null;
			return $null;
		}


		$total = $this->_models['threads']->getTotal();
		return $total;
	}

	function getPendingMessagesTotal()
	{
		$this->getThreads();
		return $this->_messagesPendingTotal;
	}

	function _getChildrenByCategory($c_id)
	{
		if (empty($this->_models['categories'])) {
			$this->_models['categories'] = &JModel::getInstance('Categories', 'KunenaModel');
		}

		if (empty($this->_models['categories'])) {
			$null = null;
			return $null;
		}

		$children = $this->_models['categories']->getItems();
		return $children;
	}
}
