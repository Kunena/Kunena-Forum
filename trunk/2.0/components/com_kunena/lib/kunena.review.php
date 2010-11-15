<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 */
//Dont allow direct linking
defined ( '_JEXEC' ) or die ();
kimport('kunena.forum.message.helper');

class CKunenaReview {
	public $my;
	public $config;
	public $app;
	public $embedded = null;
	public $document;

	function __construct($catid = 0) {
		jimport ( 'joomla.environment.uri' );
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
		kimport('kunena.forum.message.helper');

		if (!JRequest::checkToken()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$success = 0;
		$messages = KunenaForumMessageHelper::getMessages(JRequest::getVar('cb', array ( 0 ), 'post', 'array'));
		foreach ( $messages as $message ) {
			if ($message->authorise('approve') && $message->publish(KunenaForum::PUBLISHED)) {
				$message->sendNotification();
				$success++;
			} else {
				$this->app->enqueueMessage ( $message->getError (), 'notice' );
			}
		}
		if ($success) $this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_MODERATE_APPROVE_SUCCESS' ) );
	}

	public function DeleteMessage() {
		kimport('kunena.forum.message.helper');

		if (!JRequest::checkToken()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$success = 0;
		$messages = KunenaForumMessageHelper::getMessages(JRequest::getVar('cb', array ( 0 ), 'post', 'array'));
		foreach ( $messages as $message ) {
			if ($message->authorise('delete') && $message->publish(KunenaForum::DELETED)) {
				$success++;
			} else {
				$this->app->enqueueMessage ( $message->getError (), 'notice' );
			}
		}
		if ($success) $this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' ) );
	}

	protected function GetApprovedMessageList() {
		$me = KunenaFactory::getUser();
		$allowed = $me->getAllowedCategories('moderate');

		$queryCatid = '';
		$db = JFactory::getDBO ();
		if ( $this->catid > 0 ) $queryCatid = " AND catid='{$this->catid}'";
		else $queryCatid = " AND catid IN ({$allowed})";
		$db->setQuery ( "SELECT m.*, t.message
			FROM #__kunena_messages AS m
			INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid
			WHERE hold='1' " . $queryCatid . " ORDER BY id ASC" );
		$MesNeedReview = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError ())
			return;

		return $MesNeedReview;
	}

	public function escape($var) {
		return htmlspecialchars ( $var, ENT_COMPAT, 'UTF-8' );
	}

	public function display() {
		if (! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ), 'notice' );
			return false;
		}

		$backUrl = JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' );
		switch ($this->do) {
			case 'modapprove' :
				$this->ApproveMessage ();
				$this->app->redirect ( $backUrl );
				break;
			case 'moddelete' :
				$this->DeleteMessage ();
				$this->app->redirect ( $backUrl );
				break;
			default :
				CKunenaTools::loadTemplate ( '/moderate/moderate_messages.php' );
		}
	}
}