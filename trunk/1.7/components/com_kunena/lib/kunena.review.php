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
kimport('kunena.forum.message.helper');

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
		require_once (KUNENA_PATH_LIB . DS . 'kunena.posting.class.php');
		foreach ( $array as $id => $value ) {
			if (!$value) continue;

			$message = KunenaForumMessageHelper::get($id);
			if (!$message->authorise('approve')) {
				$this->_app->enqueueMessage ( $message->getError(), 'error' );
				continue;
			}

			$this->_db->setQuery ( "UPDATE `#__kunena_messages` SET hold='0' WHERE id={$this->_db->Quote($id)}" );
			$this->_db->query ();
			if (KunenaError::checkDatabaseError ())
				return;

			// Update category stats
			$category = KunenaForumCategoryHelper::get($message->get ( 'catid' ));
			if (!$message->get ( 'parent' )) $category->numTopics++;
			$category->numPosts++;
			$category->last_topic_id = $message->get ( 'thread' );
			$category->last_topic_subject = $message->get ( 'subject' );
			$category->last_post_id = $message->get ( 'id' );
			$category->last_post_time = $message->get ( 'time' );
			$category->last_post_userid = $message->get ( 'userid' );
			$category->last_post_message = $message->get ( 'message' );
			$category->last_post_guest_name = $message->get ( 'name' );
			$category->save();

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

		$me = KunenaFactory::getUser();
		$allowed = $me->getAllowedCategories('moderate');

		$queryCatid = '';
		if ( $this->catid > 0 ) $queryCatid = " AND catid='{$this->catid}'";
		else $queryCatid = " AND catid IN ({$allowed})";
		$this->_db->setQuery ( "SELECT m.*, t.message FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE hold='1' " . $queryCatid . " ORDER BY id ASC" );
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