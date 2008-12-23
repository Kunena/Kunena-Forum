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

jimport( 'joomla.application.component.controller' );

/**
 * The Kunena post controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version	1.0
 */
class KunenaControllerPost extends KunenaController
{
	/**
	 * Overridden constructor to register alternate tasks
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function __construct()
	{
		parent::__construct();

		// Register alternate tasks.
		$this->registerTask('unpublish', 'publish');
	}

	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function display()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_kunena', false));
	}

	/**
	 * Method to add a new post.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function add()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Clear the post id from the session.
		$app->setUserState('com_kunena.edit.post.id', null);

		// Get the category, thread and parent ids to file the post under.
		$catId		= JRequest::getInt('cat_id', null);
		$threadId	= JRequest::getInt('thread_id', null);
		$parentId	= JRequest::getInt('parent_id', null);

		// Redirect back to the forum root.
		if (empty($catId)) {
			$this->setMessage(JText::_('KUNENA_POST_INVALID_CATEGORY'));
			$this->setRedirect(JRoute::_('index.php?option=com_kunena', false));
		}

		$model = & $this->getModel('Post');
		$authorized = $model->canAdd($catId, $threadId);

		if (!$authorized) {
			JError::raiseError(403, $model->getError());
		}
die;

		// Set the category, thread and parent ids for the post to edit in the session.
		$app->setUserState('com_kunena.edit.post.catId', $catId);
		$app->setUserState('com_kunena.edit.post.threadId', $threadId);
		$app->setUserState('com_kunena.edit.post.parentId', $parentId);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=post&layout=edit', false));
	}

	/**
	 * Method to check out a post for editing and redirect to the edit form.
	 *
	 * If a different post was previously checked-out, the previous post will be checked
	 * in first.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function edit()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		// Get the previous post id (if any) and the current post id.
		$previousId = (int) $app->getUserState('com_kunena.edit.post.id');
		$postId		= (int) (count($cid) ? $cid[0] : JRequest::getInt('post_id'));

		// Set the post id for the post to edit in the session.
		$app->setUserState('com_kunena.edit.post.id', $postId);

		// Get the model.
		$model = &$this->getModel('Post');

		// Check out the post.
		if ($postId) {
			$model->checkout($postId);
		}

		// Check in the previous post.
		if ($previousId) {
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=post&layout=edit', false));
	}

	/**
	 * Method to cancel an edit
	 *
	 * Checks the item in, sets item id in the session to null, and then redirects to the list screen.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function cancel()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Get the previous post id.
		$previousId = (int) $app->getUserState('com_kunena.edit.post.id');

		// Get the model.
		$model = &$this->getModel('Post');

		// Check in the previous post.
		if ($previousId) {
			$model->checkin($previousId);
		}

		// Clear the post id from the session.
		$app->setUserState('com_kunena.edit.post.id', null);

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=threads', false));
	}

	/**
	 * Method to save a post.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JX_INVALID_TOKEN'));

		// Initialize variables.
		$app = &JFactory::getApplication();

		// Get the posted values from the request.
		$data = JRequest::getVar('jxform', array(), 'post', 'array');

		// Ensure there is a valid user or valid user information.
		if (empty($data['name']) || empty($data['email'])) {
			$user = &JFactory::getUser();
			if ($user->get('guest')) {
				$app->redirect(JRoute::_('index.php?option=com_kunena&view=post&layout=edit', false), JText::_('KUNENA_POST_INVALID_USER'), 'notice');
			}
		}

		// Populate the row ids from the session.
		$data['id'] = (int) $app->getUserState('com_kunena.edit.post.id');
		$data['category_id'] = (int) $app->getUserState('com_kunena.edit.post.catId');
		$data['thread_id'] = (int) $app->getUserState('com_kunena.edit.post.threadId');
		$data['parent_id'] = (int) $app->getUserState('com_kunena.edit.post.parentId');

		// Get the model and attempt to validate the posted data.
		$model = &$this->getModel('Post');
		$return	= $model->validate($data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				} else {
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_kunena.edit.post.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=post&layout=edit', false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_kunena.edit.post.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('KUNENA_POST_SAVE_FAILED', $model->getError()), 'notice');
			$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=post&layout=edit', false));
			return false;
		}

		$threadId = $data['thread_id'];
		$catId = $data['category_id'];

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task)
		{
			case 'apply':
				// Redirect back to the edit screen.
				$this->setMessage(JText::_('KUNENA_POST_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=post&layout=edit', false));
				break;

			default:
				// Check in the post.
				$postId = (int) $app->getUserState('com_kunena.edit.post.id');
				if ($postId) {
					$model->checkin($postId);
				}

				// Clear the post id from the session.
				$app->setUserState('com_kunena.edit.post.id', null);

				// Redirect to the list screen.
				$this->setMessage(JText::_('KUNENA_POST_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=thread&cat_id='.$data['category_id'].'&thread_id='.$data['thread_id'], false));
				break;
		}

		// Flush the data from the session.
		$app->setUserState('com_kunena.edit.post.data', null);
	}

	function publish()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Post', 'KunenaModel');

		if ($this->_task == 'unpublish') {
			$model->unpublish($cid);
		} else {
			$model->publish($cid);
		}
	}

	function delete()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Post', 'KunenaModel');

		$model->delete($cid);
	}

	function move()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$leaveGhost	= JRequest::getInt('leave_ghost', null, 'post');
		$threadId = JRequest::getInt('thread_id', null, 'post');
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Post', 'KunenaModel');

		$model->move($cid, $threadId, $leaveGhost);
	}
}
