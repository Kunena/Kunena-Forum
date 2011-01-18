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

/**
 * Kunena Trash Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaControllerTrash extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=trash';
	}

	function purge() {
		$app = JFactory::getApplication ();

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$cids = implode ( ',', $cid );

		if (empty ( $cid )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}
		// check if user has permission
		if ($cids) {
			$kunena_db->setQuery ( "SELECT * FROM #__kunena_messages WHERE hold=2 AND id IN ($cids)");
			$items = $kunena_db->loadObjectList ();
			if (KunenaError::checkDatabaseError()) return;
		}

		$app->setRedirect(KunenaRoute::_($this->baseurl."&view=trash&layout=purge", false));
	}

	function restore() {
		$app = JFactory::getApplication ();

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		if (empty ( $cid )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		foreach ( $cid as $id ) {
			$kunena_db->setQuery ( "SELECT * FROM #__kunena_messages WHERE id=$id AND hold=2" );
			$mes = $kunena_db->loadObject ();
			if (KunenaError::checkDatabaseError()) return;

			$kunena_db->setQuery ( "UPDATE #__kunena_messages SET hold=0 WHERE hold IN (2,3) AND thread=$mes->thread " );
			$kunena_db->query ();
			if (KunenaError::checkDatabaseError()) return;

			kimport('kunena.user.helper');
			kimport('kunena.forum.category.helper');
			KunenaUserHelper::recount();
			KunenaForumCategoryHelper::recount ();
		}

		$app->enqueueMessage ( JText::_('COM_KUNENA_TRASH_RESTORE_DONE') );
		$app->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function deleteitemsnow() {
		$app = JFactory::getApplication ();
		$kunena_db = &JFactory::getDBO ();
		$path = KUNENA_PATH_LIB  .'/kunena.moderation.class.php';
		require_once ($path);

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		if (empty ( $cid )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_MESSAGES_SELECTED' ), 'notice' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$kunena_mod = CKunenaModeration::getInstance();
		require_once (KUNENA_PATH_LIB .DS. 'kunena.poll.class.php');
		$poll = CKunenaPolls::getInstance();

		foreach ($cid as $id ) {
			// Delete message perminantly with the attachments
			$kunena_mod->deleteMessagePerminantly($id, true);

			$kunena_db->setQuery ( "SELECT a.parent, a.id, b.threadid FROM #__kunena_messages AS a INNER JOIN #__kunena_polls AS b ON b.threadid=a.id WHERE threadid='{$id}'" );
			$mes = $kunena_db->loadObjectList ();
			if (KunenaError::checkDatabaseError()) return;

			// Remove poll attached to the messages
			if( !empty($mes[0])) {
				// FIXME: make something better to check if the poll exist
				if ($mes[0]->parent == '0' && !empty($mes[0]->threadid) ) {
					$poll->delete_poll($mes[0]->threadid);
				}
			}
		}

		$app->enqueueMessage( JText::_('COM_KUNENA_TRASH_DELETE_DONE') );
		$app->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

}
