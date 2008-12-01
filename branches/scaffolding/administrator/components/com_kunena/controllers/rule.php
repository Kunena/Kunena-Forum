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
 * The Kunena Access Control Rule Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaControllerRule extends JController
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
		$this->registerTask('apply', 'save');
		$this->registerTask('deny', 'allow');
		$this->registerTask('disable', 'enable');
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
		$this->setRedirect('index.php?option=com_kunena');
	}

	/**
	 * Method to add a new rule to the access control system.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function add()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Clear the rule id from the session.
		$app->setUserState('acl.edit.rule.id', null);

		// Syncronize the ACL assets.
		require_once(JPATH_ADMINISTRATOR.'/components/com_kunena/helpers/access.php');
		$sync = KunenaHelperAccess::synchronize();

		// Redirect to the rule edit screen.
		$this->setRedirect('index.php?option=com_kunena&view=rule&layout=edit&hidemainmenu=1');
	}

	/**
	 * Method to checkout a group for editing.  If a different group
	 * was previously checked-out, the previous group will be checked
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

		// Get the previous rule id (if any) and the current rule id.
		$previous_id	= (int) $app->getUserState('acl.edit.rule.id');
		$rule_id		= (int) (count($cid) ? $cid[0] : JRequest::getInt('rule_id'));

		// Set the rule id for the rule to edit in the session.
		$app->setUserState('acl.edit.rule.id', $rule_id);

		// Syncronize the ACL assets.
		require_once(JPATH_ADMINISTRATOR.'/components/com_kunena/helpers/access.php');
		$sync = GalleryHelperAccess::synchronize();

		// Redirect to the rule edit screen.
		$this->setRedirect('index.php?option=com_kunena&view=rule&layout=edit&hidemainmenu=1');
	}

	/**
	 * Method to cancel an add or edit operation.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function cancel()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$this->setRedirect('index.php?option=com_kunena&view=rules');
	}

	/**
	 * Method to save a rule.
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

		// Get the model and set the posted request variables.
		$model = &$this->getModel('Rule', 'KunenaModel');
		$model->setState('request', JRequest::get('post'));

		// Get the posted values from the request.
		$values = JRequest::getVar('jxform', array(), 'post', 'array');

		// Populate the known values.
		$values['id'] = (int) $app->getUserState('acl.edit.rule.id');
		$values['section_value'] = 'com_kunena';
		$values['acl_type'] = 2;

		// Attempt to save the rule.
		if (!$model->save($values)) {
			$this->setMessage($model->getError());
		}
		else {
			$this->setMessage(JText::_('Saved'));
		}

		// Redirect the user based on the posted task.
		if ($this->_task == 'apply')
		{
			// Redirect back to the rule edit screen.
			$this->setRedirect('index.php?option=com_kunena&view=rule&layout=edit&hidemainmenu=1');
		} else
		{
			// Clear the rule id from the session.
			$app->setUserState('acl.edit.rule.id', null);

			// Redirect to the list view.
			$this->setRedirect('index.php?option=com_kunena&view=rules');
		}
	}

	/**
	 * Method to set the allow/deny state of the item(s).
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function allow()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		// Get and sanitize the items to delete.
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		// Get the model.
		$model = &$this->getModel('Rule', 'KunenaModel');

		// Attempt to set the allow/deny state of the item(s).
		if ($this->_task == 'deny') {
			$result = $model->deny($cid);
		} else {
			$result = $model->allow($cid);
		}

		// If there is a problem, set the error message.
		if (!$result) {
			$this->setMessage($model->getError());
		}

		// Redirect to the list view.
		$this->setRedirect('index.php?option=com_kunena&view=rules');
	}

	/**
	 * Method to enable/disable item(s).
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function enable()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		// Get and sanitize the items to delete.
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		// Get the model.
		$model = &$this->getModel('Rule', 'KunenaModel');

		// Attempt to enable or disable the item(s).
		if ($this->_task == 'disable') {
			$result = $model->disable($cid);
		} else {
			$result = $model->enable($cid);
		}

		// If there is a problem, set the error message.
		if (!$result) {
			$this->setMessage($model->getError());
		}

		// Redirect to the list view.
		$this->setRedirect('index.php?option=com_kunena&view=rules');
	}

	/**
	 * Method to delete item(s).
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
		$model = &$this->getModel('Rule', 'KunenaModel');

		// Attempt to delete the item(s).
		if (!$model->delete($cid)) {
			$this->setMessage(JText::sprintf('KUNENA_RULES_DELETE_FAILED', $model->getError()), 'notice');
		}
		else {
			$this->setMessage(JText::sprintf('KUNENA_RULES_DELETE_SUCCESS', count($cid)));
		}

		// Redirect to the list view.
		$this->setRedirect('index.php?option=com_kunena&view=rules');
	}
}