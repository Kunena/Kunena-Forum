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
require_once(JPATH_SITE.'/components/com_kunena/libraries/query.php');

/**
 * The Kunena thread model
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelThread extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	protected
	 * @var		boolean
	 */
	var $__state_set		= null;

	/**
	 * Internal array to hold thread data access objects by ID.
	 *
	 * @access	private
	 * @var		array
	 */
	var $_threads			= array();

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
			$app		= &JFactory::getApplication('site');
			$user		= &JFactory::getUser();
			$params		= $app->getParams('com_kunena');
			$context	= 'com_kunena.thread.';


			// Load the user parameters.
			$this->setState('user',	$user);
			$this->setState('user.id', (int)$user->id);
			$this->setState('user.aid', (int)$user->get('aid'));

			// Load the parameters.
			$this->setState('params', $params);

			$this->setState('thread.id', JRequest::getInt('thread_id'));
			$this->setState('category.id', JRequest::getInt('cat_id'));

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	function &getThread($t_id=null)
	{
		$thread = new JObject;
		return $thread;
	}

	function getPosts()
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

	function &getCategory()
	{
		$result = null;
		if ($id = (int) $this->getState('category.id'))
		{
			$model	= &JModel::getInstance('Category', 'KunenaModel');
			$model->setState('filter.category_id', $id);
			$result	= $model->getCategory();
		}
		return $result;
	}

	/**
	 * Subscribe to the post
	 */
	function subscribe($threadIds, $userId=null)
	{
		$userId = (empty($userId)) ? $this->getState('user.id') : $userId;

		// Verify a valid user id.
		if (empty($userId)) {
			$this->setError(JText::_('KUNENA_MISSING_USER_ID'));
			return false;
		}

		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Does the thread subscription setting already exist for this combination?
		$db->setQuery(
			'SELECT `thread_id`' .
			' FROM `#__kunena_subscriptions`' .
			' WHERE `user_id`='.(int)$userId .
			' AND (`thread_id`='.implode(' OR `thread_id`=', $threadIds).')'
		);
		$exists = $db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$query = 'INSERT INTO `#__kunena_subscriptions` (`thread_id`, `user_id`) VALUES';
		$execute = false;
		foreach ($threadIds as $threadId)
		{
			if (!in_array($threadId, $exists)) {
				$query .= '('.(int) $threadId.','.(int) $userId.'),';
				$execute = true;
			}
		}

		// Insert the new thread subscription setting.
		$db->setQuery(rtrim($query, ','));
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function unsubscribe($threadIds, $userId=null)
	{
		$userId = (empty($userId)) ? $this->getState('user.id') : $userId;

		// Verify a valid user id.
		if (empty($userId)) {
			$this->setError(JText::_('KUNENA_MISSING_USER_ID'));
			return false;
		}

		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Delete the relevant thread subscription settings.
		$db->setQuery(
			'DELETE FROM `#__kunena_subscriptions`' .
			' WHERE `user_id`='.(int)$userId .
			' AND (`thread_id`='.implode(' OR `thread_id`=', $threadIds).')'
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function favorite($threadIds, $userId=null)
	{
		$userId = (empty($userId)) ? $this->getState('user.id') : $userId;

		// Verify a valid user id.
		if (empty($userId)) {
			$this->setError(JText::_('KUNENA_MISSING_USER_ID'));
			return false;
		}

		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Does the favorite thread setting already exist for this combination?
		$db->setQuery(
			'SELECT `thread_id`' .
			' FROM `#__kunena_favorite_threads`' .
			' WHERE `user_id`='.(int)$userId .
			' AND (`thread_id`='.implode(' OR `thread_id`=', $threadIds).')'
		);
		$exists = $db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$query = 'INSERT INTO `#__kunena_favorite_threads` (`thread_id`, `user_id`) VALUES';
		$execute = false;
		foreach ($threadIds as $threadId)
		{
			if (!in_array($threadId, $exists)) {
				$query .= '('.(int) $threadId.','.(int) $userId.'),';
				$execute = true;
			}
		}

		// Insert the new favorite thread setting.
		$db->setQuery(rtrim($query, ','));
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function unfavorite($threadIds, $userId=null)
	{
		$userId = (empty($userId)) ? $this->getState('user.id') : $userId;

		// Verify a valid user id.
		if (empty($userId)) {
			$this->setError(JText::_('KUNENA_MISSING_USER_ID'));
			return false;
		}

		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Delete the relevant favorite thread settings.
		$db->setQuery(
			'DELETE FROM `#__kunena_favorite_threads`' .
			' WHERE `user_id`='.(int)$userId .
			' AND (`thread_id`='.implode(' OR `thread_id`=', $threadIds).')'
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function lock($threadIds)
	{
		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Lock the relevant threads.
		$db->setQuery(
			'UPDATE `#__kunena_threads`' .
			' SET `locked`=1' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function unlock($threadIds)
	{
		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Unlock the relevant threads.
		$db->setQuery(
			'UPDATE `#__kunena_threads`' .
			' SET `locked`=0' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function sticky($threadIds)
	{
		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Sticky the relevant threads.
		$db->setQuery(
			'UPDATE `#__kunena_threads`' .
			' SET `ordering`=1' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function unsticky($threadIds)
	{
		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Unsticky the relevant threads.
		$db->setQuery(
			'UPDATE `#__kunena_threads`' .
			' SET `ordering`=0' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function publish($threadIds)
	{
		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Get a list of the category ids for the relevant threads.
		$db->setQuery(
			'SELECT DISTINCT `category_id`' .
			' FROM `#__kunena_threads`' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$categoryIds = $db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Publish the relevant threads.
		$db->setQuery(
			'UPDATE `#__kunena_threads`' .
			' SET `published`=1' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Update category totals
		foreach ($categoryIds as $categoryId)
		{
			// TODO
		}

		return true;
	}

	function unpublish($threadIds)
	{
		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Get a list of the category ids for the relevant threads.
		$db->setQuery(
			'SELECT DISTINCT `category_id`' .
			' FROM `#__kunena_threads`' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$categoryIds = $db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Unpublish the relevant threads.
		$db->setQuery(
			'UPDATE `#__kunena_threads`' .
			' SET `published`=0' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Update category totals
		foreach ($categoryIds as $categoryId)
		{
			// TODO
		}

		return true;
	}

	function delete($threadIds)
	{
		// Verify a valid thread id.
		if (empty($threadIds)) {
			$this->setError(JText::_('KUNENA_MISSING_THREAD_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Get a list of the category ids for the relevant threads.
		$db->setQuery(
			'SELECT DISTINCT `category_id`' .
			' FROM `#__kunena_threads`' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$categoryIds = $db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Delete the relevant posts.
		$db->setQuery(
			'DELETE FROM `#__kunena_posts`' .
			' WHERE `thread_id`='.implode(' OR `thread_id`=', $threadIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Delete the relevant threads.
		$db->setQuery(
			'DELETE FROM `#__kunena_threads`' .
			' WHERE `id`='.implode(' OR `id`=', $threadIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Update category totals
		foreach ($categoryIds as $categoryId)
		{
			// TODO
		}

		return true;
	}

	function move($threadIds, $categoryId)
	{

	}

	function split()
	{

	}
}
