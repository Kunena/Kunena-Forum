<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Trash Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerTrash extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=trash';
	}

	function purge() {
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cids = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$md5 = JRequest::getString ( 'md5', null );
		$topic = JRequest::getInt ( 'topics', 0, 'post' );
		$message = JRequest::getInt ( 'messages', 0, 'post' );

		if ( !empty($cids) ) {
			$this->app->setUserState('com_kunena.purge', $cids);
			$this->app->setUserState('com_kunena.topic', $topic);
			$this->app->setUserState('com_kunena.message', $message);
		} elseif ( $md5 ) {
			$ids = $this->app->getUserState('com_kunena.purge');
			$md5calculated = md5(serialize($ids));
			// FIXME : unset the userstate
			if ( $md5 == $md5calculated ) {
				$topic = $this->app->getUserState('com_kunena.topic');
				$message = $this->app->getUserState('com_kunena.message');
				if ( $topic ) {
					$topics = KunenaForumTopicHelper::getTopics($ids);
					foreach ( $topics as $topic ) {
						$topic->authorise('delete');
						$topic->delete();
					}
					$this->app->enqueueMessage (JText::_('COM_KUNENA_TRASH_DELETE_TOPICS_DONE'));
					$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
				} elseif ( $message ) {
					$messages = KunenaForumMessageHelper::getMessages($ids);
					foreach ( $messages as $message ) {
						$message->authorise('delete');
						$message->delete();
					}
					$this->app->enqueueMessage (JText::_('COM_KUNENA_TRASH_DELETE_MESSAGES_DONE'));
					$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
				} else {
					// error
				}
			}
		} else {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$this->app->redirect(KunenaRoute::_($this->baseurl."&layout=purge", false));
	}

	function restore() {
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$kunena_db = JFactory::getDBO ();
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$topics = JRequest::getInt ( 'topics', 0, 'post' );
		$messages = JRequest::getInt ( 'messages', 0, 'post' );

		if (empty ( $cid )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$msg = JText::_('COM_KUNENA_TRASH_RESTORE_DONE');

		if ( $messages ) {
			$messages = KunenaForumMessageHelper::getMessages($cid);
			foreach ( $messages as $target ) {
				if ( $target->authorise('undelete') && $target->publish(KunenaForum::PUBLISHED) ) {
					$this->app->enqueueMessage ( $msg );
				} else {
					$this->app->enqueueMessage ( $target->getError(), 'notice' );
				}
			}
		} elseif ( $topics ) {
			$topics = KunenaForumTopicHelper::getTopics($cid);
			foreach ( $topics as $target ) {
				if ( $target->authorise('undelete') && $target->publish(KunenaForum::PUBLISHED) ) {
					$this->app->enqueueMessage ( $msg );
				} else {
					$this->app->enqueueMessage ( $target->getError(), 'notice' );
				}
			}
		} else {
			// error
		}

		KunenaUserHelper::recount();
		KunenaForumTopicHelper::recount();
		KunenaForumCategoryHelper::recount ();

		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
