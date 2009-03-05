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

/**
 * The Kunena thread controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaControllerThread extends KunenaController
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
		$this->registerTask('unpublish',	'publish');
		$this->registerTask('unlock',		'lock');
		$this->registerTask('unsticky',		'sticky');
		$this->registerTask('unfavorite',	'favorite');
		$this->registerTask('unsubscribe',	'subscribe');
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

	function move()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$leaveGhost	= JRequest::getInt('leave_ghost', null, 'post');
		$categoryId = JRequest::getInt('category_id', null, 'post');
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Thread', 'KunenaModel');

		$model->move($cid, $categoryId, $leaveGhost);
	}

	function split()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Thread', 'KunenaModel');

		$model->split($cid);
	}

	function delete()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Thread', 'KunenaModel');

		$model->delete($cid);
	}

	function favorite()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Thread', 'KunenaModel');

		if ($this->_task == 'unfavorite') {
			$model->favorite($cid);
		} else {
			$model->favorite($cid);
		}
	}

	function subscribe()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Thread', 'KunenaModel');

		if ($this->_task == 'unsubscribe') {
			$model->unsubscribe($cid);
		} else {
			$model->subscribe($cid);
		}
	}

	function lock()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Thread', 'KunenaModel');

		if ($this->_task == 'unlock') {
			$model->unlock($cid);
		} else {
			$model->lock($cid);
		}
	}

	function sticky()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		// Get the thread model.
		$model = &$this->getModel('Thread', 'KunenaModel');

		if ($this->_task == 'unsticky') {
			$model->unsticky($cid);
		} else {
			$model->sticky($cid);
		}
	}

	function publish()
	{
		JRequest::checkToken() or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		$cid = JRequest::getVar('cid', null, 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = &$this->getModel('Thread', 'KunenaModel');

		if ($this->_task == 'unpublish') {
			$model->unpublish($cid);
		} else {
			$model->publish($cid);
		}
	}
}
