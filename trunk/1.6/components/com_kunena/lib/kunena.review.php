<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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
	public $document;

	function __construct($catid = 0) {
		jimport ( 'joomla.environment.uri' );
		$this->_db = JFactory::getDBO ();
		$this->my = JFactory::getUser ();
		$this->config = KunenaFactory::getConfig ();
		$this->app = JFactory::getApplication ();
		$this->uri = JURI::getInstance ();
		$this->document = JFactory::getDocument ();
		$this->catid = intval($catid);
		$this->tabclass = array ("row1", "row2" );
		$this->MessagesToApprove = $this->GetApprovedMessageList ();
		$this->do = JRequest::getCmd ( 'do', '' );
		$this->header = JText::_ ( 'COM_KUNENA_MESSAGE_ADMINISTRATION' );
		$this->func = JString::strtolower ( JRequest::getCmd ( 'func', JRequest::getCmd ( 'view' ) ) );
		if( $this->func == 'review' ) $this->document->setTitle ( $this->header );
	}

	public function ApproveMessage() {
		if ($this->_checkToken ())
			return false;

		$array = JRequest::getVar('cb', array ( 0 ), 'post', 'array');

		$backUrl = JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' );

		$success = 0;
		require_once (KUNENA_PATH_LIB . '/kunena.posting.class.php');
		foreach ( $array as $id => $value ) {
			if (!$value) continue;

			$message = new CKunenaPosting ( );
			$message->action($id);
			if (!$message->canApprove()) {
				$errors = $message->getErrors ();
				foreach ( $errors as $field => $error ) {
					$this->_app->enqueueMessage ( $field . ': ' . $error, 'error' );
				}
				continue;
			}

			$this->_db->setQuery ( "UPDATE `#__kunena_messages` SET hold='0' WHERE id={$this->_db->Quote($id)}" );
			$this->_db->query ();
			if (KunenaError::checkDatabaseError ())
				return;

			CKunenaTools::modifyCategoryStats($message->get('id'), $message->get('parent'), $message->get('time'), $message->get('catid'));
			$message->emailToSubscribers(null, $this->config->allowsubscriptions, false, false);
			$success++;
		} //end foreach

		if ($success) $this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_MODERATE_APPROVE_SUCCESS' ), 'notice' );
		$this->app->redirect ( $backUrl );
	}

	public function DeleteMessage() {
		if ($this->_checkToken ())
			return false;

		$backUrl = JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' );
		require_once (JPATH_SITE . '/components/com_kunena/class.kunena.php');
		$items = KGetArrayInts ( "cb" );

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$message = '';
		foreach ( $items as $id => $value ) {
			// Permission checks inside:
			$delete = $kunena_mod->deleteMessage( $id, false );
			if (! $delete) {
				$this->app->enqueueMessage ( $kunena_mod->getErrorMessage (), 'notice' );
			} else {
				$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' ) );
			}
		} //end foreach


		$this->app->redirect ( $backUrl );
	}

	public function GetApprovedMessageList() {
		if ($this->_moderatorProtection ())
			return false;

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