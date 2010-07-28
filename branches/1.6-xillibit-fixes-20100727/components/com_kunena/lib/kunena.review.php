<?php
/**
* @version $Id: kunena_review.php 3076 2010-07-19 01:39:17Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
*/
//Dont allow direct linking
defined( '_JEXEC' ) or die();

class CKunenaReview {
	protected $_db;
	public $my;
	public $config;
	public $app;

	function __construct($catid='') {
		$this->_db = JFactory::getDBO ();
		$this->my = JFactory::getUser ();
		$this->config = KunenaFactory::getConfig ();
		$this->app = JFactory::getApplication ();
		$this->uri = JURI::getInstance ();
		$this->catid = $catid;
		$this->tabclass = array ("row1", "row2" );
		$this->MessagesToApprove = $this->GetApprovedMessageList();
		$this->do = JRequest::getCmd ( 'do', '' );
		$this->header = JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION');
	}

	public function ApproveMessage() {
		if ($this->_checkToken())
			return false;

		$backUrl = $this->app->getUserState ( "com_kunena.ReviewURL" );
		require_once(JPATH_SITE.'/components/com_kunena/class.kunena.php');
		$items = KGetArrayInts ( "cb" );

		$message = '';
		foreach ( $items as $id => $value ) {
			$this->_db->setQuery("UPDATE `#__kunena_messages` SET hold='0' WHERE id='{$id}'");
        	$this->_db->query();
			if (KunenaError::checkDatabaseError()) return;
		} //end foreach

		$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_MODERATE_APPROVE_SUCCESS' ), 'notice' );
		$this->app->redirect ( $backUrl );

		// Is this is really needed ?
		//CKunenaTools::modifyCategoryStats($id, $msg->parent, $msg->time, $msg->catid);
	}

	public function DeleteMessage() {
		if ($this->_checkToken())
			return false;

		$backUrl = $this->app->getUserState ( "com_kunena.ReviewURL" );
		require_once(JPATH_SITE.'/components/com_kunena/class.kunena.php');
		$items = KGetArrayInts ( "cb" );

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$message = '';
		foreach ( $items as $id => $value ) {
			$delete = $kunena_mod->deleteThread ( $id, $DeleteAttachments = false );
			if (! $delete) {
				$this->app->enqueueMessage ( $kunena_mod->getErrorMessage (), 'notice' );
			} else {
				$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' ));
			}
		} //end foreach

		$this->app->redirect ( $backUrl );
	}

	public function GetApprovedMessageList() {
		$queryCatid = '';
		if ( !empty($catid) ) $queryCatid = " AND catid='{$this->catid}'";
		$this->_db->setQuery("SELECT m.*, t.message,cat.name AS catname FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid LEFT JOIN #__kunena_categories AS cat ON cat.id=m.catid WHERE hold='1' ".$queryCatid." ORDER BY id ASC");
        $MesNeedReview = $this->_db->loadObjectList();
        if (KunenaError::checkDatabaseError()) return;

        return $MesNeedReview;
	}

	protected function _moderatorProtection() {
		if (! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ), 'notice' );
			return true;
		}
		return false;
	}

	protected function _checkToken() {
		if (JRequest::checkToken ( ) == false) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
            $kunena_app->redirect ( CKunenaLink::GetReviewURL(false) );

			return true;
		}
		return false;
	}

	public function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	public function display() {
		if ($this->_moderatorProtection ())
			return false;

		switch ($this->do) {
			case 'modapprove' :
				$this->ApproveMessage ( );
				break;
			case 'moddelete' :
				$this->DeleteMessage ( );
				break;
			default:
				CKunenaTools::loadTemplate('/moderate/moderate_messages.php');
		}
	}
}

?>