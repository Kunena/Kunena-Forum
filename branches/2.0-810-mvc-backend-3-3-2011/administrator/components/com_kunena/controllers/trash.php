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
				$messages = KunenaForumMessageHelper::getMessages($ids);
				foreach ($messages as $message) {
					if ($message->authorise('permdelete') && $message->delete()) {
						$app->enqueueMessage (JText::_('COM_KUNENA_TRASH_DELETE_DONE'));
					} else {
						$app->enqueueMessage (  $message->getError(), 'notice' );
					}
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
