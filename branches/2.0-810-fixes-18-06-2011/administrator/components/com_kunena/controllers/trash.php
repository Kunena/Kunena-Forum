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
kimport('kunena.forum.topic.helper');
kimport('kunena.forum.message.helper');
kimport ( 'kunena.error' );

/**
 * Kunena Trash Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerTrash extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=trash';
	}

	function purge() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cids = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$md5 = JRequest::getString ( 'md5', null );
		$topic = JRequest::getInt ( 'topics', 0, 'post' );
		$message = JRequest::getInt ( 'messages', 0, 'post' );

		if ( !empty($cids) ) {
			$app->setUserState('com_kunena.purge', $cids);
			$app->setUserState('com_kunena.topic', $topic);
			$app->setUserState('com_kunena.message', $message);
		} elseif ( $md5 ) {
			$ids = $app->getUserState('com_kunena.purge');
			$md5calculated = md5(serialize($ids));
			// FIXME : unset the userstate
			if ( $md5 == $md5calculated ) {
				$topic = $app->getUserState('com_kunena.topic');
				$message = $app->getUserState('com_kunena.message');
				if ( $topic ) {
					$topics = KunenaForumTopicHelper::getTopics($ids);
					foreach ( $topics as $topic ) {
						$topic->authorise('delete');
						$topic->delete();
					}
					$app->enqueueMessage (JText::_('COM_KUNENA_TRASH_DELETE_DONE'));
					$app->redirect ( KunenaRoute::_($this->baseurl."&layout=topics", false) );
				} elseif ( $message ) {
					$messages = KunenaForumMessageHelper::getMessages($ids);
					foreach ( $messages as $message ) {
						$message->authorise('delete');
						$message->delete();
					}
					$app->enqueueMessage (JText::_('COM_KUNENA_TRASH_DELETE_DONE'));
					$app->redirect ( KunenaRoute::_($this->baseurl."&layout=messages", false) );
				} else {
					// error
				}
			}
		} else {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$app->redirect(KunenaRoute::_($this->baseurl."&layout=purge", false));
	}

	function restore() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$kunena_db = JFactory::getDBO ();
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$topics = JRequest::getInt ( 'topics', 0, 'post' );
		$messages = JRequest::getInt ( 'messages', 0, 'post' );

		if (empty ( $cid )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$msg = JText::_('COM_KUNENA_TRASH_RESTORE_DONE');

		if ( $messages ) {
			$messages = KunenaForumMessageHelper::getMessages($cid);
			foreach ( $messages as $target ) {
				if ( $target->authorise('undelete') && $target->publish(KunenaForum::PUBLISHED) ) {
					$app->enqueueMessage ( $msg );
				} else {
					$app->enqueueMessage ( $target->getError(), 'notice' );
				}
			}
		} elseif ( $topics ) {
			$topics = KunenaForumTopicHelper::getTopics($cid);
			foreach ( $topics as $target ) {
				if ( $target->authorise('undelete') && $target->publish(KunenaForum::PUBLISHED) ) {
					$app->enqueueMessage ( $msg );
				} else {
					$app->enqueueMessage ( $target->getError(), 'notice' );
				}
			}
		} else {
			// error
		}

		KunenaUserHelper::recount();
		KunenaForumTopicHelper::recount();
		KunenaForumCategoryHelper::recount ();

		$app->redirect(KunenaRoute::_($this->baseurl, false));
	}

	function messages() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$app->redirect(KunenaRoute::_($this->baseurl."&layout=messages", false));
	}

	function topics() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$app->redirect(KunenaRoute::_($this->baseurl."&layout=topics", false));
	}
}
