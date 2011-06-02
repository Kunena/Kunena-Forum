<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
kimport('kunena.user.helper');
kimport('kunena.forum.category.helper');

/**
 * Kunena Recount Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerRecount extends KunenaController {
	function display() {
		$this->recount();
	}

	function recount() {
		$app = JFactory::getApplication ();
		$state = $app->getUserState ( 'com_kunena.admin.recount', null );

		if ($state === null) {
			// First run
			$query = "SELECT MAX(id) FROM #__kunena_messages";
			$db = JFactory::getDBO();
			$db->setQuery ( $query );
			$state = new StdClass();
			$state->step = 0;
			$state->maxId = (int) $db->loadResult ();
			$state->start = 0;
		}

		$this->checkTimeout();
		while (1) {
			$count = mt_rand(95000, 105000);
			switch ($state->step) {
				case 0:
					// Update topic statistics
					kimport('kunena.forum.topic.helper');
					KunenaForumTopicHelper::recount(false, $state->start, $state->start+$count);
					$state->start += $count;
					//$app->enqueueMessage ( JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_TOPICS', min($state->start, $state->maxId), $state->maxId) );
					break;
				case 1:
					// Update usertopic statistics
					kimport('kunena.forum.topic.user.helper');
					KunenaForumTopicUserHelper::recount(false, $state->start, $state->start+$count);
					$state->start += $count;
					//$app->enqueueMessage ( JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_USERTOPICS', min($state->start, $state->maxId), $state->maxId) );
					break;
				case 2:
					// Update user statistics
					kimport('kunena.user.helper');
					KunenaUserHelper::recount();
					//$app->enqueueMessage ( JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_USER') );
					break;
				case 3:
					// Update category statistics
					kimport('kunena.forum.category.helper');
					KunenaForumCategoryHelper::recount();
					//$app->enqueueMessage ( JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_CATEGORY') );
					break;
				default:
					$app->setUserState ( 'com_kunena.admin.recount', null );
					$app->enqueueMessage (JText::_('COM_KUNENA_RECOUNTFORUMS_DONE'));
					$this->setRedirect(KunenaRoute::_('index.php?option=com_kunena', false));
					return;
			}
			if (!$state->start || $state->start > $state->maxId) {
				$state->step++;
				$state->start = 0;
			}
			if ($this->checkTimeout()) break;
		}
		$app->setUserState ( 'com_kunena.admin.recount', $state );
		$this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=recount&task=recount', false));
	}

	protected function checkTimeout($stop = false) {
		static $start = null;
		if ($stop) $start = 0;
		$time = microtime (true);
		if ($start === null) {
			$start = $time;
			return false;
		}
		if ($time - $start < 1)
			return false;

		return true;
	}
}