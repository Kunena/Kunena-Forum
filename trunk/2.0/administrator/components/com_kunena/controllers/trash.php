<?php
/**
 * @version		$Id: trash.php 3965 2010-12-08 13:40:51Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
kimport('kunena.user.helper');
kimport('kunena.forum.category.helper');
kimport('kunena.forum.topic.helper');
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

		// FIXME: mode down
		if (empty ( $cids )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		// FIXME: if (!empty ( $cids )) { setUserstate() } elseif ($md5) { doPurge() } else { ERROR: no messages selected }
		if ( empty($cids) || $md5) {

			$ids = $app->getUserState('com_kunena.purge');
			$md5calculated = md5(serialize($ids));
			// FIXME : unset the userstate
			if ( $md5 == $md5calculated ) {
				// FIXME: we do have delete() for topics and messages and they are doing also cleanup (removing thanks, polls etc)
				// first load topics/messages by KunenaForumTopic/MessageHelper
				// then foreach (...) { $message->authorise(); $message->delete() }

				require_once (KUNENA_PATH_LIB .DS. 'kunena.poll.class.php');
				$poll = CKunenaPolls::getInstance();
				require_once (KUNENA_PATH_LIB  .'/kunena.moderation.class.php');
				$kunena_mod = CKunenaModeration::getInstance();
				$db = JFactory::getDBO();
				foreach ($ids as $id ) {
					$db->setQuery ( "SELECT a.parent, a.id, b.threadid FROM #__kunena_messages AS a INNER JOIN #__kunena_polls AS b ON b.threadid=a.id WHERE threadid='{$id}'" );
					$mes = $db->loadObjectList ();
					if (KunenaError::checkDatabaseError()) return;
					if( !empty($mes[0])) {
						// FIXME : maybe create a function in poll class to check if a poll exist
						if ($mes[0]->parent == '0' && !empty($mes[0]->threadid) ) {
							//remove of poll
							$poll->delete_poll($mes[0]->threadid);
						}
					}
				}
				$sucess = $kunena_mod->deleteMessagePerminantly($id, 1);
				if ( $sucess ) {
					$app->enqueueMessage (JText::_('COM_KUNENA_TRASH_DELETE_DONE'));
				} else {
					$app->enqueueMessage (  $kunena_mod->getErrorMessage() );
				}
			} else {
				// $app->enqueueMessage (JText::_('error'));
			}
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		} else {
			$app->setUserState('com_kunena.purge', $cids);
		}

		$app->redirect(KunenaRoute::_($this->baseurl."&view=trash&layout=purge", false));
	}

	function restore() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$kunena_db = JFactory::getDBO ();
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		if (empty ( $cid )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		// FIXME: use new classes to do this
		foreach ( $cid as $id ) {
			$kunena_db->setQuery ( "UPDATE #__kunena_messages SET hold=0 WHERE hold IN (2,3) AND id={$id} " );
			$kunena_db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}
		KunenaUserHelper::recount();
		KunenaForumTopicHelper::recount();
		KunenaForumCategoryHelper::recount ();

		$app->enqueueMessage ( JText::_('COM_KUNENA_TRASH_RESTORE_DONE') );
		$app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
