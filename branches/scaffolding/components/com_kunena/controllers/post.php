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
		$this->setRedirect('index.php?option=com_kunena');
	}

	function add()
	{

	}

	function edit()
	{

	}

	function save()
	{

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
