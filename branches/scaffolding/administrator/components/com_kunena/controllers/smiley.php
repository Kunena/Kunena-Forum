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

jimport('joomla.application.component.controller');

/**
 * The Kunena smiley controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version	1.0
 */
class KunenaControllerSmiley extends JController
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		// Map the save tasks.
		$this->registerTask('save2new',		'save');
		$this->registerTask('apply',		'save');

		// Map the publishing state tasks.
		$this->registerTask('unpublish',	'publish');

		// Map the ordering tasks.
		$this->registerTask('orderup',		'ordering');
		$this->registerTask('orderdown',	'ordering');
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
	 * Method to add a new smiley.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function add()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Clear the smiley id from the session.
		$app->setUserState('com_kunena.edit.smiley.id', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smiley&layout=edit&hidemainmenu=1', false));
	}

	/**
	 * Method to check out a smiley for editing and redirect to the edit form.
	 *
	 * If a different smiley was previously checked-out, the previous smiley will be checked
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

		// Get the previous smiley id (if any) and the current smiley id.
		$previousId = (int) $app->getUserState('com_kunena.edit.smiley.id');
		$catId		= (int) (count($cid) ? $cid[0] : JRequest::getInt('smiley_id'));

		// Set the smiley id for the smiley to edit in the session.
		$app->setUserState('com_kunena.edit.smiley.id', $catId);

		// Get the model.
		$model = &$this->getModel('Smiley', 'KunenaModel');

		// Check out the smiley.
		if ($catId) {
			$model->checkout($catId);
		}

		// Check in the previous smiley.
		if ($previousId) {
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smiley&layout=edit&hidemainmenu=1', false));
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

		// Get the previous smiley id.
		$previousId = (int) $app->getUserState('com_kunena.edit.smiley.id');

		// Get the model.
		$model = &$this->getModel('Smiley', 'KunenaModel');

		// Check in the previous smiley.
		if ($previousId) {
			$model->checkin($previousId);
		}

		// Clear the smiley id from the session.
		$app->setUserState('com_kunena.edit.smiley.id', null);

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smilies', false));
	}

	/**
	 * Method to save a smiley.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		// Initialize variables.
		$app = &JFactory::getApplication();

		// Get the posted values from the request.
		$data = JRequest::getVar('jxform', array(), 'post', 'array');

		// Populate the row id from the session.
		$data['id'] = (int) $app->getUserState('com_kunena.edit.smiley.id');

		// Get the model and attempt to validate the posted data.
		$model = &$this->getModel('Smiley', 'KunenaModel');
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
			$app->setUserState('com_kunena.edit.smiley.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smiley&layout=edit&hidemainmenu=1', false));
//			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_kunena.edit.smiley.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('KUNENA_SMILEY_SAVE_FAILED', $model->getError()), 'notice');
			$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smiley&layout=edit&hidemainmenu=1', false));
			return false;
		}

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task)
		{
			case 'apply':
				// Redirect back to the edit screen.
				$this->setMessage(JText::_('KUNENA_SMILEY_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smiley&layout=edit&hidemainmenu=1', false));
				break;

			case 'save2new':
				// Check in the smiley.
				$catId = (int) $app->getUserState('com_kunena.edit.smiley.id');
				if ($catId) {
					$model->checkin($catId);
				}

				// Clear the smiley id from the session.
				$app->setUserState('com_kunena.edit.smiley.id', null);

				// Redirect back to the edit screen.
				$this->setMessage(JText::_('KUNENA_SMILEY_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smiley&layout=edit&hidemainmenu=1', false));
				break;

			default:
				// Check in the smiley.
				$catId = (int) $app->getUserState('com_kunena.edit.smiley.id');
				if ($catId) {
					$model->checkin($catId);
				}

				// Clear the smiley id from the session.
				$app->setUserState('com_kunena.edit.smiley.id', null);

				// Redirect to the list screen.
				$this->setMessage(JText::_('KUNENA_SMILEY_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smilies', false));
				break;
		}

		// Flush the data from the session.
		$app->setUserState('com_kunena.edit.smiley.data', null);
	}

	/**
	 * Method to delete smilies.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function delete()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		// Get and sanitize the items to delete.
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		// Get the model.
		$model = &$this->getModel('Smiley', 'KunenaModel');

		// Attempt to delete the item(s).
		if (!$model->delete($cid)) {
			$this->setMessage(JText::sprintf('KUNENA_SMILIES_DELETE_FAILED', $model->getError()), 'notice');
		}
		else {
			$this->setMessage(JText::sprintf('KUNENA_SMILIES_DELETE_SUCCESS', count($cid)));
		}

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smilies', false));
	}

	/**
	 * Method to set the published state of smilies.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function publish()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		// Get and sanitize the items to delete.
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		// Get the model.
		$model = &$this->getModel('Smiley', 'KunenaModel');

		// Attempt to set the publishing state for the smilies.
		switch ($this->_task)
		{
			case 'publish':
				$result = $model->setProperty($cid, 'palette', 1);
				break;

			case 'unpublish':
				$result = $model->setProperty($cid, 'palette', 0);
				break;
		}

		// If there is a problem, set the error message.
		if (!$result) {
			$this->setMessage($model->getError());
		}

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smilies', false));
	}

	/**
	 * Method to adjust the ordering of a smiley in the tree.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function ordering()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		// Get the smiley id from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$catId	= (int) (count($cid) ? $cid[0] : JRequest::getInt('smiley_id'));

		// Get the model.
		$model = &$this->getModel('Smiley', 'KunenaModel');

		// Attempt to adjust the ordering for the categort.
		switch ($this->_task)
		{
			case 'orderup':
				// Adjust the ordering up one.
				$result = $model->ordering($catId, -1);
				break;

			case 'orderdown':
				// Adjust the ordering down one.
				$result = $model->ordering($catId, 1);
				break;

			default:
				// Get the ordering adjustment from the request.
				$adjustment = JRequest::getInt('adjustment', 0);

				// Adjust the ordering based on the adjustment value.
				$result = $model->ordering($catId, $adjustment);
				break;
		}

		// If there is a problem, set the error message.
		if (!$result) {
			$this->setMessage($model->getError());
		}

		// Redirect to the list view.
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=smilies', false));
	}
}
