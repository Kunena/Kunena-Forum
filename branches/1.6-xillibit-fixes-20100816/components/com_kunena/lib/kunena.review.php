<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
//Dont allow direct linking
defined ( '_JEXEC' ) or die ();

class CKunenaReview {
	protected $_db;
	public $my;
	public $config;
	public $app;
	public $embedded = null;

	function __construct($catid = 0) {
		$this->_db = JFactory::getDBO ();
		$this->my = JFactory::getUser ();
		$this->config = KunenaFactory::getConfig ();
		$this->app = JFactory::getApplication ();
		$this->uri = JURI::getInstance ();
		$this->catid = intval($catid);
		$this->tabclass = array ("row1", "row2" );
		$this->MessagesToApprove = $this->GetApprovedMessageList ();
		$this->do = JRequest::getCmd ( 'do', '' );
		$this->header = JText::_ ( 'COM_KUNENA_MESSAGE_ADMINISTRATION' );

		$view = JRequest::getVar ( 'view', '' );
		if ( $view == 'profile' ) $this->embedded = 1;
	}

	public function ApproveMessage() {
		if ($this->_checkToken ())
			return false;

		$array = JRequest::getVar('cb', array ( 0 ), 'post', 'array');

		$backUrl = $this->app->getUserState ( "com_kunena.ReviewURL" );

		foreach ( $array as $id => $value ) {
			// FIXME: add check that moderator really has permission to approve current message (not all mods are global)
			$this->_db->setQuery ( "UPDATE `#__kunena_messages` SET hold='0' WHERE id={$this->_db->Quote($id)}" );
			$this->_db->query ();
			if (KunenaError::checkDatabaseError ())
				return;

			$this->_db->setQuery ( "SELECT catid,id,parent,time FROM `#__kunena_messages` WHERE id={$this->_db->Quote($id)}" );
			$mesincat = $this->_db->loadObject ();
			KunenaError::checkDatabaseError ();

			CKunenaTools::modifyCategoryStats($id, $mesincat->parent, $mesincat->time, $mesincat->catid);
		} //end foreach

		$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_MODERATE_APPROVE_SUCCESS' ), 'notice' );
		$this->app->redirect ( $backUrl );
	}

	public function DeleteMessage() {
		if ($this->_checkToken ())
			return false;

		$backUrl = $this->app->getUserState ( "com_kunena.ReviewURL" );
		require_once (JPATH_SITE . '/components/com_kunena/class.kunena.php');
		$items = KGetArrayInts ( "cb" );

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$message = '';
		foreach ( $items as $id => $value ) {
			// TODO: make sure that that this action is allowed only if moderator really has rights to do this
			$delete = $kunena_mod->deleteThread ( $id, $DeleteAttachments = false );
			if (! $delete) {
				$this->app->enqueueMessage ( $kunena_mod->getErrorMessage (), 'notice' );
			} else {
				$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' ) );
			}
		} //end foreach


		$this->app->redirect ( $backUrl );
	}

	public function GetApprovedMessageList() {
		$queryCatid = '';
		if ( $this->catid > 0 ) $queryCatid = " AND catid='{$this->catid}'";
		// FIXME: only show unapproved messages from categories where user has moderator rights
		$this->_db->setQuery ( "SELECT m.*, t.message,cat.name AS catname FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid LEFT JOIN #__kunena_categories AS cat ON cat.id=m.catid WHERE hold='1' " . $queryCatid . " ORDER BY id ASC" );
		$MesNeedReview = $this->_db->loadObjectList ();
		if (KunenaError::checkDatabaseError ())
			return;

		return $MesNeedReview;
	}

	protected function _moderatorProtection() {
		// FIXME: only allow action in categories where user has moderator rights
		if (! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ), 'notice' );
			return true;
		}
		return false;
	}

	protected function _checkToken() {
		if (JRequest::checkToken () == false) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->_app->redirect ( CKunenaLink::GetReviewURL ( false ) );

			return true;
		}
		return false;
	}

	public function escape($var) {
		return htmlspecialchars ( $var, ENT_COMPAT, 'UTF-8' );
	}

	public function display() {
		if ($this->_moderatorProtection ())
			return false;

		switch ($this->do) {
			case 'modapprove' :
				$this->ApproveMessage ();
				break;
			case 'moddelete' :
				$this->DeleteMessage ();
				break;
			default :
				CKunenaTools::loadTemplate ( '/moderate/moderate_messages.php' );
		}
	}
}

?>