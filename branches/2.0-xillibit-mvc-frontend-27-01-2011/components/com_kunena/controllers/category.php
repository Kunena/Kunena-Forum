<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
kimport ( 'kunena.error' );
kimport ( 'kunena.forum.category.helper' );

/**
 * Kunena Category Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerCategory extends KunenaController {
	function markread() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$category = KunenaForumCategoryHelper::get(JRequest::getInt('catid', 0));
		if (!$category->authorise('read')) {
			$app->enqueueMessage ( $category->getError(), 'error' );
			$this->redirectBack ();
		}

		$db = JFactory::getDBO();
		$session = KunenaFactory::getSession();
		if ($session->userid) {
			// Mark all unread topics in the category to read
			$readTopics = $session->readtopics;
			$db->setQuery ( "SELECT id FROM #__kunena_topics WHERE category_id={$db->quote($category->id)} AND id NOT IN ({$readTopics}) AND last_post_time>={$db->quote($session->lasttime)}" );
			$readForum = $db->loadResultArray ();
			if (KunenaError::checkDatabaseError()) $this->redirectBack ();
			$readTopics = implode(',', array_merge(explode(',', $readTopics), $readForum));

			$session->readtopics = $readTopics;
			if (!$session->save ()) {
				$app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error' );
			} else {
				$app->enqueueMessage ( JText::_('COM_KUNENA_GEN_FORUM_MARKED') );
			}
		}
		$this->redirectBack ();
	}

	function subscribe() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$category = KunenaForumCategoryHelper::get(JRequest::getInt('catid', 0));
		if (!$category->authorise('read')) {
			$app->enqueueMessage ( $category->getError(), 'error' );
			$this->redirectBack ();
		}

		$db = JFactory::getDBO();
		$me = KunenaFactory::getUser();
		if ($me->exists()) {
			$query = "INSERT INTO #__kunena_user_categories (user_id,category_id,subscribed)
				VALUES ({$db->quote($me->userid)},{$db->quote($category->id)},1)
				ON DUPLICATE KEY UPDATE subscribed=1";
			$db->setQuery ( $query );
			$db->query ();
			KunenaError::checkDatabaseError();
			if ($db->getAffectedRows ()) {
				$app->enqueueMessage ( JText::_('COM_KUNENA_GEN_CATEGORY_SUBCRIBED') );
			}
		}

		$this->redirectBack ();
	}

	function unsubscribe() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$category = KunenaForumCategoryHelper::get(JRequest::getInt('catid', 0));
		if (!$category->authorise('read')) {
			$app->enqueueMessage ( $category->getError(), 'error' );
			$this->redirectBack ();
		}

		$db = JFactory::getDBO();
		$me = KunenaFactory::getUser();
		if ($me->exists()) {
			$query = "UPDATE #__kunena_user_categories SET subscribed=0
				WHERE user_id={$db->quote($me->userid)} AND category_id={$db->quote($category->id)}";
			$db->setQuery ( $query );
			$db->query ();
			KunenaError::checkDatabaseError();
			if ($db->getAffectedRows ()) {
				$app->enqueueMessage ( JText::_('COM_KUNENA_GEN_CATEGORY_UNSUBCRIBED') );
			}
		}

		$this->redirectBack ();
	}
}