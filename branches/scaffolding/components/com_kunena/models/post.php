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
 * Post model for the Kunena package.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelPost extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	private
	 * @var		boolean
	 */
	var $__state_set	= false;

	/**
	 * Overridden method to get model state variables.
	 *
	 * @access	public
	 * @param	string	$property	Optional parameter name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.0
	 */
	function getState($property = null, $default = null)
	{
		// If the model state is uninitialized lets set some values we will need from the request.
		if (!$this->__state_set)
		{
			$app		= &JFactory::getApplication('site');
			$user		= &JFactory::getUser();
			$params		= $app->getParams('com_kunena');
			$context	= 'com_kunena.post.';

			// Attempt to auto-load the post id.
			if (!$postId = (int)$app->getUserState('com_kunena.edit.post.id')) {
				$postId = (int)JRequest::getInt('post_id');
			}

			// Only set the post id if there is a value.
			if ($postId) {
				$this->setState('post.id', $postId);
			}

			// Load the user parameters.
			$this->setState('user',	$user);
			$this->setState('user.id', (int)$user->id);
			$this->setState('user.aid', (int)$user->get('aid'));

			// Load the parameters.
			$this->setState('params', $params);

			$this->__state_set = true;
		}

		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
	}

	/**
	 * Method to get a Kunena post item.
	 *
	 * @access	public
	 * @param	integer	The id of the post to get.
	 * @return	mixed	Post data object on success, false on failure.
	 * @since	1.0
	 */
	function &getItem($postId = null)
	{
		// Initialize variables.
		$postId = (!empty($postId)) ? $postId : (int)$this->getState('post.id');
		$false	= false;

		// Get a subscription row instance.
		$table = &$this->getTable('Post', 'KunenaTable');

		// Attempt to load the row.
		$return = $table->load($postId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->serError($table->getError());
			return $false;
		}

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		$value = JArrayHelper::toObject($table->getProperties(1), 'JObject');
		return $value;
	}

	/**
	 * Method to get the post form.
	 *
	 * @access	public
	 * @return	mixed	JXForm object on success, false on failure.
	 * @since	1.0
	 */
	function &getForm()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$false	= false;

		// Get the form.
		jximport('jxtended.form.form');
		JXForm::addFormPath(JPATH_COMPONENT.'/models/forms');
		JXForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
		JXForm::addFieldPath(JPATH_ADMINISTRATOR.'/components/com_members/models/fields');
		$form = &JXForm::getInstance('post');

		// Set the rule path.
		jximport('jxtended.form.validator');
		JXFormValidator::addRulePath(JPATH_COMPONENT.'/models/rules');

		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return $false;
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_kunena.edit.post.data', array());

		// Bind the form data if present.
		if (!empty($data)) {
			$form->bind($data);
		}

		return $form;
	}

	function preview()
	{

	}

	/**
	 * Method to validate the form data.
	 *
	 * @access	public
	 * @param	array	The form data.
	 * @return	mixed	Array of filtered data if valid, false otherwise.
	 * @since	1.0
	 */
	function validate($data)
	{
		// Get the form.
		$form = &$this->getForm();

		// Check for an error.
		if ($form === false) {
			return false;
		}

		// Filter and validate the form data.
		$data	= $form->filter($data);
		$return	= $form->validate($data);

		// Check for an error.
		if (JError::isError($return)) {
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}

			return false;
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @access	public
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function save($data)
	{
		$postId = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('post.id');
		$isNew	= true;

		// Get a post row instance.
		$table = &$this->getTable('Post', 'KunenaTable');

		// Load the row if saving an existing item.
		if ($postId > 0) {
			if ($table->load($postId)) {
				$isNew = false;
			}
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('KUNENA_POST_BIND_FAILED', $table->getError()));
			return false;
		}

		// Check for valid user data.
		if (empty($table->name) && empty($table->email) && empty($table->user_id)) {
			$user = &JFactory::getUser();
			$table->user_id = $user->get('id');
			$table->name	= $user->get('username');
			$table->email	= $user->get('email');
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $table->id;
	}

	function delete($postIds, $removeAttachments = true)
	{
		// Verify a valid post id.
		if (empty($postIds)) {
			$this->setError(JText::_('KUNENA_MISSING_POST_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Get a list of the category ids for the relevant posts.
		$db->setQuery(
			'SELECT DISTINCT `category_id`' .
			' FROM `#__kunena_posts`' .
			' WHERE `id`='.implode(' OR `id`=', $postIds)
		);
		$categoryIds = $db->loadResultArray();

		// Get a list of the thread ids for the relevant posts.
		$db->setQuery(
			'SELECT DISTINCT `thread_id`' .
			' FROM `#__kunena_posts`' .
			' WHERE `id`='.implode(' OR `id`=', $postIds)
		);
		$threadIds = $db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Delete the relevant posts.
		$db->setQuery(
			'DELETE FROM `#__kunena_posts`' .
			' WHERE `id`='.implode(' OR `id`=', $postIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Update threads totals
		foreach ($threadIds as $threadId)
		{
			// TODO
		}

		// Delete the threads where no posts exist.
		$db->setQuery(
			'DELETE FROM `#__kunena_threads`' .
			' WHERE `total_posts` < 1'
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

		// Update user stats.
		// TODO

		// Delete ghost posts pointing to the posts.
		// TODO

		// Delete attachments if desired.
		if ($removeAttachments)
		{
			// Select the file paths for attached files to relevant posts.
			$db->setQuery(
				'SELECT `file_path`' .
				' FROM `#__kunena_attachments`' .
				' WHERE `post_id`='.implode(' OR `post_id`=', $postIds)
			);
			$files = $db->loadResultArray();

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			if (!empty($files)) {
				// Import library dependencies.
				jimport('joomla.filesystem.file');

				if (!JFile::delete($files)) {
					$this->setError(JText::_('KUNENA_UNABLE_TO_DELETE_FILES'));
					return false;
				}

				// Delete the attachment entries from the database.
				$db->setQuery(
					'DELETE FROM `#__kunena_attachments`' .
					' WHERE `post_id`='.implode(' OR `post_id`=', $postIds)
				);
				$db->query();

				// Check for a database error.
				if ($this->_db->getErrorNum()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		return true;
	}

	function move($postId, $threadId, $leaveGhost = false)
	{
		// Get the database object.
		$db = &$this->_db;

		// Verify a valid post id.
		if (empty($postId)) {
			$this->setError(JText::sprintf('KUNENA_POST_MOVE_FAILURE', JText::_('KUNENA_MISSING_POST_ID')));
			return false;
		}

		// Verify a valid thread id.
		if (empty($threadId)) {
			$this->setError(JText::sprintf('KUNENA_POST_MOVE_FAILURE', JText::_('KUNENA_MISSING_THREAD_ID')));
			return false;
		}

		// Get the selected post data.
		$db->setQuery(
			'SELECT *' .
			' FROM `#__kunena_posts`' .
			' WHERE `id`='.(int)$postId
		);
		$post = $db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Get the new thread data.
		$db->setQuery(
			'SELECT *' .
			' FROM `#__kunena_threads`' .
			' WHERE `id`='.(int)$threadId
		);
		$newThread = $db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Get the old thread data.
		$db->setQuery(
			'SELECT *' .
			' FROM `#__kunena_threads`' .
			' WHERE `id`='.(int)$post->thread_id
		);
		$oldThread = $db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Get the new category data.
		$db->setQuery(
			'SELECT *' .
			' FROM `#__kunena_categories`' .
			' WHERE `id`='.(int)$newThread->category_id
		);
		$newCategory = $db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Get the old category data.
		$db->setQuery(
			'SELECT *' .
			' FROM `#__kunena_categories`' .
			' WHERE `id`='.(int)$oldThread->category_id
		);
		$oldCategory = $db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Perform the actual move of the post.
		$db->setQuery(
			'UPDATE `#__kunena_posts`' .
			' SET `category_id` = '.(int)$newThread->category_id .
			' SET `thread_id` = '.(int)$newThread->id .
			' WHERE `id` = '.(int)$postId
		);

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		/*
		 * If we have the "Leave Ghost Message" setting enabled for this move, then
		 * leave a ghost message.
		 */
		if ($leaveGhost)
		{
			$newSubject = JText::sprintf('KUNENA_POST_MOVED_SUBJECT', $post->subject);
			$newMessage = JText::sprintf('KUNENA_POST_MOVED_MESSAGE', 'catid='.$newThread->category_id.'&id='.$newThread->id);
			$db->setQuery(
				'INSERT INTO `#__kunena_posts` (`subject`, `message`, `created_time`, `category_id`, `thread_id`, `moved`)' .
				' VALUES (0, '.$db->Quote($newSubject).', '.$db->Quote($newMessage).', '.(int)$post->created_time.', '.(int)$post->category_id.', '.(int)$post->thread_id.', 1)'
			);

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		// Determine if the category last post needs to be adjusted.
		// TODO

		// Determine if the thread last post needs to be adjusted.
		// TODO

		// Recount posts for both threads.
		// TODO

		// Recount threads for both categories.
		// TODO

		return true;
	}

	function publish($postIds)
	{
		// Verify a valid post id.
		if (empty($postIds)) {
			$this->setError(JText::_('KUNENA_MISSING_POST_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Get a list of the category ids for the relevant posts.
		$db->setQuery(
			'SELECT DISTINCT `category_id`' .
			' FROM `#__kunena_posts`' .
			' WHERE `id`='.implode(' OR `id`=', $postIds)
		);
		$categoryIds = $db->loadResultArray();

		// Get a list of the thread ids for the relevant posts.
		$db->setQuery(
			'SELECT DISTINCT `thread_id`' .
			' FROM `#__kunena_posts`' .
			' WHERE `id`='.implode(' OR `id`=', $postIds)
		);
		$threadIds = $db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Publish the relevant posts.
		$db->setQuery(
			'UPDATE `#__kunena_posts`' .
			' SET `published`=1' .
			' WHERE `id`='.implode(' OR `id`=', $postIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Update threads totals
		foreach ($threadIds as $threadId)
		{
			// TODO
		}

		// Publish the threads where posts are published.
		$db->setQuery(
			'UPDATE `#__kunena_threads`' .
			' SET `published`=1' .
			' WHERE `total_posts` > 0'
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

		// Update user stats.
		// TODO

		// Publish ghost posts pointing to the posts.
		// TODO

		return true;
	}

	function unpublish($postIds)
	{
		// Verify a valid post id.
		if (empty($postIds)) {
			$this->setError(JText::_('KUNENA_MISSING_POST_ID'));
			return false;
		}

		// Get the database object.
		$db = &$this->_db;

		// Get a list of the category ids for the relevant posts.
		$db->setQuery(
			'SELECT DISTINCT `category_id`' .
			' FROM `#__kunena_posts`' .
			' WHERE `id`='.implode(' OR `id`=', $postIds)
		);
		$categoryIds = $db->loadResultArray();

		// Get a list of the thread ids for the relevant posts.
		$db->setQuery(
			'SELECT DISTINCT `thread_id`' .
			' FROM `#__kunena_posts`' .
			' WHERE `id`='.implode(' OR `id`=', $postIds)
		);
		$threadIds = $db->loadResultArray();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Unpublish the relevant posts.
		$db->setQuery(
			'UPDATE `#__kunena_posts`' .
			' SET `published`=0' .
			' WHERE `id`='.implode(' OR `id`=', $postIds)
		);
		$db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Update threads totals
		foreach ($threadIds as $threadId)
		{
			// TODO
		}

		// Unpublish the threads where posts are published.
		$db->setQuery(
			'UPDATE `#__kunena_threads`' .
			' SET `published`=0' .
			' WHERE `total_posts` < 1'
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

		// Update user stats.
		// TODO

		// Unpublish ghost posts pointing to the posts.
		// TODO

		return true;
	}

	function canAdd($catId, $threadId = null, $userId = null)
	{
		if ($catId < 2) {
			$this->setError(JText::_('KUNENA INVALID CATEGORY'));
			return false;
		}

		// Get a category row instance.
		$category = & $this->getTable('Category', 'KunenaTable');

		// Load the row if saving an existing item.
		if (!$category->load($catId)) {
			$this->setError(JText::_('KUNENA INVALID CATEGORY'));
			return false;
		}

		// Import the ACL helper library.
		jximport('jxtended.acl.acl');

		// Get the allowed access levels for the user.
		$levels = JXAcl::getAllowedAssetGroups('core', 'global.view', $this->getState('user.id'), false);

		if (!is_array($levels)) {
			$levels = explode(',', $levels);
		}

		// Ensure the user is assigned to an access level that can post in the category.
		if (!in_array($category->post_access, $levels)) {
			$this->setError(JText::_('KUNENA NOT AUTHORIZED'));
			return false;
		}

		// Check to make sure the thread is not locked.
		if ($threadId)
		{
			// Get a thread row instance.
			$thread = & $this->getTable('Thread', 'KunenaTable');

			// Load the row to verify locked state.
			if (!$thread->load($threadId)) {
				$this->setError(JText::_('KUNENA INVALID THREAD'));
				return false;
			}

			// Ensure the thread is not locked.
			if ($thread->locked) {
				$this->setError(JText::_('KUNENA THREAD LOCKED'));
				return false;
			}
		}

		return true;
	}

	function canEdit($catId, $threadId = null, $userId = null)
	{
		if ($catId < 2) {
			$this->setError(JText::_('KUNENA INVALID CATEGORY'));
			return false;
		}

		// Get a category row instance.
		$category = & $this->getTable('Category', 'KunenaTable');

		// Load the row if saving an existing item.
		if (!$category->load($catId)) {
			$this->setError(JText::_('KUNENA INVALID CATEGORY'));
			return false;
		}

		// Import the ACL helper library.
		jximport('jxtended.acl.acl');

		// Get the allowed access levels for the user.
		$levels = JXAcl::getAllowedAssetGroups('com_kunena', 'view.category', $this->getState('user.id'), true);

		if (!is_array($levels)) {
			$levels = explode(',', $levels);
		}

		// Ensure the user is assigned to an access level that can post in the category.
		if (!in_array($category->post_access, $levels)) {
			$this->setError(JText::_('KUNENA NOT AUTHORIZED'));
			return false;
		}

		// Check to make sure the thread is not locked.
		if ($threadId)
		{
			// Get a thread row instance.
			$thread = & $this->getTable('Thread', 'KunenaTable');

			// Load the row to verify locked state.
			if (!$thread->load($threadId)) {
				$this->setError(JText::_('KUNENA INVALID THREAD'));
				return false;
			}

			// Ensure the thread is not locked.
			if ($thread->locked) {
				$this->setError(JText::_('KUNENA THREAD LOCKED'));
				return false;
			}
		}

		return true;
	}
}
