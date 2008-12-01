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
 * The Kunena category controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version	1.0
 */
class KunenaControllerCategory extends JController
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->registerTask('save2copy',	'save');
		$this->registerTask('save2new',	'save');
		$this->registerTask('apply',		'save');
		$this->registerTask('unpublish',	'publish');
		$this->registerTask('trash',		'publish');
		$this->registerTask('orderup',		'ordering');
		$this->registerTask('orderdown',	'ordering');

		$this->registerTask('reviewon',		'toggle');
		$this->registerTask('reviewoff',	'toggle');
		$this->registerTask('moderatedon',		'toggle');
		$this->registerTask('moderatedoff',	'toggle');
		$this->registerTask('lockedon',		'toggle');
		$this->registerTask('lockedoff',	'toggle');
	}

	/**
	 * Display the view
	 */
	function display()
	{
	}

	/**
	 * Proxy for getModel
	 */
	function &getModel()
	{
		return parent::getModel('Category', '', array('ignore_request' => true));
	}

	/**
	 * Method to edit a object
	 *
	 * Sets object ID in the session from the request, checks the item out, and then redirects to the edit page.
	 *
	 * @access	public
	 * @return	void
	 */
	function edit()
	{
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$id  = JRequest::getInt('id', @$cid[0]);

		$session = &JFactory::getSession();
		$session->set('kunena.category.id', $id);

		// Checkout item
		$model = $this->getModel();

		if ($id) {
			$model->checkout($id);
		}
		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=category&layout=edit', false));
	}

	/**
	 * Method to cancel an edit
	 *
	 * Checks the item in, sets item ID in the session to null, and then redirects to the list page.
	 *
	 * @access	public
	 * @return	void
	 */
	function cancel()
	{
		// Checkin item if checked out
		$session = &JFactory::getSession();
		if ($id = (int) $session->get('kunena.category.id')) {
			$model = $this->getModel();
			$model->checkin($id);
		}

		// Clear the session of the item
		$session->set('kunena.category.id', null);

		$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=categories', false));
	}

	/**
	 * Save the record
	 */
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken();

		// Get posted form variables.
		$values		= JRequest::getVar('jxform', array(), 'post', 'array');

		// Get the id of the item out of the session.
		$session	= &JFactory::getSession();
		$id			= (int) $session->get('kunena.category.id');
		$values['id'] = $id;

		// Get the extensions model and set the post request in its state.
		$model	= &$this->getModel();
		$post = JRequest::get('post');
		$model->setState('request', $post);

		// Get the form model object and validate it.
		$form	= &$model->getForm('model');
		$form->filter($values);
		$result	= $form->validate($values);

		if (JError::isError($result)) {
			JError::raiseError(500, $result->message);
		}

		if ($model->save($values)) {
			$msg = JText::_('FB Message Saved');
		}
		else {
			$msg = $model->getError();
		}

		if ($this->_task == 'apply') {
			$session->set('Kunena.category.id', $model->getState('id'));
			$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=category&layout=edit', false), $msg);
		}
		else if ($this->_task == 'save2new') {
			$session->set('Kunena.category.id', null);
			$model->checkin($id);

			$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=category&layout=edit', false), $msg);
		}
		else {
			$session->set('Kunena.category.id', null);
			$model->checkin($id);

			$this->setRedirect(JRoute::_('index.php?option=com_kunena&view=categories', false), $msg);
		}
	}

	/**
	 * Removes an item
	 */
	function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('Select an item to delete'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if (!$model->delete($cid)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_kunena&view=categories');
	}

	/**
	 * Method to publish a list of taxa
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('publish' => 1, 'unpublish' => 0, 'trash' => -2);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('FB Error No items selected'));
		}
		else {
			// Get the model.
			$model	= $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->publish($cid, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_kunena&view=categories');
	}

	/**
	 * Changes the order of an item
	 */
	function ordering()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid	= JRequest::getVar('cid', null, 'post', 'array');
		$inc	= $this->getTask() == 'orderup' ? -1 : +1;

		$model = & $this->getModel();
		$model->ordering($cid, $inc);

		$this->setRedirect('index.php?option=com_kunena&view=categories');
	}

	/**
	 * Change or increment the access
	 */
	function access()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid	= JRequest::getVar('cid',		null, 'post', 'array');
		$level	= JRequest::getVar('level',	null, 'post');

		$model	= & $this->getModel();
		$result	= $model->access($cid, $level);
		if (JError::isError($result)) {
			$this->setMessage($result->getMessage());
		}
		else {
			$this->setMessage(JText::sprintf('Items updated', $result));
		}

		$this->setRedirect('index.php?option=com_kunena&view=categories');
	}

	//
	// Ancilliary methods
	//

	/**
	 * "Quick" methods for adding content
	 */
	function quick()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$vars	= JRequest::getVar('quick', array(), 'post', 'array');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');
		$result	= true;

		$model = & $this->getModel();
		$model->setState('vars',	$vars);
		$model->setState('ids',	$cid);
		$result = $model->quick();
		if (JError::isError($result)) {
			JError::raiseNotice(500, $result->getMessage());
		}
		$this->setRedirect('index.php?option=com_kunena&view=categories');
	}

	/**
	 * Method to run batch opterations.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function batch()
	{
		// Get variables from the request.
		$vars	= JRequest::getVar('batch', array(), 'post', 'array');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');

		$model	= &$this->getModel();
		$model->batch($vars, $cid);

		$this->setRedirect('index.php?option=com_kunena&view=categories');
	}

	/**
	 * Method to publish a list of taxa
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function toggle()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		// Get the model.
		$model	= $this->getModel();
		// Publish the items.
		if (!$model->toggle($cid, $this->getTask())) {
			JError::raiseWarning(500, $model->getError());
		}

		$this->setRedirect('index.php?option=com_kunena&view=categories');
	}

}
