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
kimport ( 'kunena.forum.category.helper' );
kimport ( 'kunena.forum.topic.helper' );

/**
 * Kunena Topics Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerTopics extends KunenaController {

	function none() {
		$app = JFactory::getApplication ();
		$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CONTROLLER_NO_TASK' ) );
		$this->redirectBack ();
	}

	function permdelete() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$message = '';
		$topics = KunenaForumTopicHelper::getTopics(array_keys(JRequest::getVar('topics', array ( 0 ), 'post', 'array')));
		if (!$topics) {
			$message = JText::_ ( 'COM_KUNENA_NO_TOPICS_SELECTED' );
		} else {
			foreach ( $topics as $topic ) {
				if ($topic->authorise('permdelete') && $topic->delete()) {
					$message = JText::_ ( 'COM_KUNENA_BULKMSG_DELETED' );
				} else {
					$app->enqueueMessage ( $topic->getError (), 'notice' );
				}
			}
		}
		if ($message) $app->enqueueMessage ( $message );
		$this->redirectBack ();
	}

	function delete() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$message = '';
		$topics = KunenaForumTopicHelper::getTopics(array_keys(JRequest::getVar('topics', array ( 0 ), 'post', 'array')));
		if (!$topics) {
			$message = JText::_ ( 'COM_KUNENA_NO_TOPICS_SELECTED' );
		} else {
			foreach ( $topics as $topic ) {
				if ($topic->authorise('delete') && $topic->publish(KunenaForum::TOPIC_DELETED)) {
					$message = JText::_ ( 'COM_KUNENA_BULKMSG_DELETED' );
				} else {
					$app->enqueueMessage ( $topic->getError (), 'notice' );
				}
			}
		}
		if ($message) $app->enqueueMessage ( $message );
		$this->redirectBack ();
	}

	function restore() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$message = '';
		$topics = KunenaForumTopicHelper::getTopics(array_keys(JRequest::getVar('topics', array ( 0 ), 'post', 'array')));
		if (!$topics) {
			$message = JText::_ ( 'COM_KUNENA_NO_TOPICS_SELECTED' );
		} else {
			foreach ( $topics as $topic ) {
				if ($topic->authorise('undelete') && $topic->publish(KunenaForum::PUBLISHED)) {
					$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_UNDELETE' );
				} else {
					$app->enqueueMessage ( $topic->getError (), 'notice' );
				}
			}
		}
		if ($message) $app->enqueueMessage ( $message );
		$this->redirectBack ();
	}

	function move() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topics = KunenaForumTopicHelper::getTopics(array_keys(JRequest::getVar('topics', array ( 0 ), 'post', 'array')));
		if (!$topics) {
			$message = JText::_ ( 'COM_KUNENA_NO_TOPICS_SELECTED' );
		} else {
			$target = KunenaForumCategoryHelper::get(JRequest::getInt('target', 0));
			if (!$target->authorise('read')) {
				$app->enqueueMessage ( $target->getError(), 'error' );
			} else {
				foreach ( $topics as $topic ) {
					if ($topic->authorise('move') && $topic->move($target)) {
						$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_MOVE' );
					} else {
						$app->enqueueMessage ( $topic->getError (), 'notice' );
					}
				}
			}
		}
		if (!empty($message)) $app->enqueueMessage ( $message );
		$this->redirectBack ();
	}

	function unfavorite() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topics = KunenaForumTopicHelper::getTopics(array_keys(JRequest::getVar('topics', array ( 0 ), 'post', 'array')));
		if ( KunenaForumTopicHelper::favorite(array_keys($topics), 0) ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_USER_UNFAVORITE_YES') );
		} else {
			$app->enqueueMessage ( JText::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC') );
		}
		$this->redirectBack ();
	}

	function unsubscribe() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$topics = KunenaForumTopicHelper::getTopics(array_keys(JRequest::getVar('topics', array ( 0 ), 'post', 'array')));
		if ( KunenaForumTopicHelper::subscribe(array_keys($topics), 0) ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_USER_UNSUBSCRIBE_YES') );
		} else {
			$app->enqueueMessage ( JText::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC') );
		}
		$this->redirectBack ();
	}
}