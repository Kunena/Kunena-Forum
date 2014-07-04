<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
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
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$type = JRequest::getCmd ( 'type', 'topics', 'post' );
		$md5 = JRequest::getString ( 'md5', null );

		if ( !empty($cid) ) {
			$this->app->setUserState('com_kunena.purge', $cid);
			$this->app->setUserState('com_kunena.type', $type);

		} elseif ( $md5 ) {
			$ids = (array) $this->app->getUserState('com_kunena.purge');
			$type = (string) $this->app->getUserState('com_kunena.type');
			if ( $md5 == md5(serialize($ids)) ) {
				if ( $type=='topics' ) {
					$topics = KunenaForumTopicHelper::getTopics($ids, 'none');
					foreach ( $topics as $topic ) {
						$success = $topic->delete();
						if (!$success) $this->app->enqueueMessage ($topic->getError());
					}
					$this->app->enqueueMessage (JText::_('COM_KUNENA_TRASH_DELETE_TOPICS_DONE'));
				} elseif ( $type=='messages' ) {
					$messages = KunenaForumMessageHelper::getMessages($ids, 'none');
					foreach ( $messages as $message ) {
						$success = $message->delete();
						if (!$success) $this->app->enqueueMessage ($message->getError());
					}
					$this->app->enqueueMessage (JText::_('COM_KUNENA_TRASH_DELETE_MESSAGES_DONE'));
				}
			} else {
				// Error...
			}
			$this->app->setUserState('com_kunena.purge', null);
			$this->app->setUserState('com_kunena.type', null);
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );

		} else {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$this->app->redirect(KunenaRoute::_($this->baseurl."&layout=purge", false));
	}

	function restore() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$type = JRequest::getCmd ( 'type', 'topics', 'post' );

		if (empty ( $cid )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$nb_items = 0;
		if ( $type=='messages' ) {
			$messages = KunenaForumMessageHelper::getMessages($cid, 'none');
			foreach ( $messages as $target ) {
				if ( $target->authorise('undelete') && $target->publish(KunenaForum::PUBLISHED) ) {
					$nb_items++;
				} else {
					$this->app->enqueueMessage ( $target->getError(), 'notice' );
				}
			}
		} elseif ( $type=='topics' ) {
			$topics = KunenaForumTopicHelper::getTopics($cid, 'none');
			foreach ( $topics as $target ) {
				if ( $target->authorise('undelete') && $target->publish(KunenaForum::PUBLISHED) ) {
					$nb_items++;
				} else {
					$this->app->enqueueMessage ( $target->getError(), 'notice' );
				}
			}
		} else {
			// Error...
		}

		if ( $nb_items > 0 ) $this->app->enqueueMessage(JText::sprintf('COM_KUNENA_TRASH_ITEMS_RESTORE_DONE', $nb_items));

		KunenaUserHelper::recount();
		KunenaForumTopicHelper::recount();
		KunenaForumCategoryHelper::recount ();

		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
