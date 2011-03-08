<?php
/**
 * @version $Id: users.php 4381 2011-02-05 20:55:31Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );

/**
 * Kunena Users Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerUsers extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=users';
	}

	function edit() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$userid = (int)array_shift($cid);

		if ($userid < 0 ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$app->setUserState ( 'kunena.user.userid', $userid );

		$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=edit&userid={$userid}", false));
	}

	function save() {
		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$newview = JRequest::getVar ( 'newview' );
		$newrank = JRequest::getVar ( 'newrank' );
		$signature = JRequest::getVar ( 'message' );
		$deleteSig = JRequest::getVar ( 'deleteSig' );
		$moderator = JRequest::getInt ( 'moderator' );
		$uid = JRequest::getInt ( 'uid' );
		$avatar = JRequest::getVar ( 'avatar' );
		$deleteAvatar = JRequest::getVar ( 'deleteAvatar' );
		$neworder = JRequest::getInt ( 'neworder' );
		$modCatids = JRequest::getVar ( 'catid', array () );

		if ($deleteSig == 1) {
			$signature = "";
		}
		$avatar = '';
		if ($deleteAvatar == 1) {
			$avatar = ",avatar=''";
		}

		$db->setQuery ( "UPDATE #__kunena_users SET signature={$db->quote($signature)}, view='$newview',moderator='$moderator', ordering='$neworder', rank='$newrank' $avatar where userid='$uid'" );
		$db->query ();
		if (KunenaError::checkDatabaseError()) return;

		//delete all moderator traces before anyway
		$db->setQuery ( "DELETE FROM #__kunena_moderation WHERE userid='$uid'" );
		$db->query ();
		if (KunenaError::checkDatabaseError()) return;

		//if there are moderatored forums, add them all
		if ($moderator == 1) {
			if (!empty ( $modCatids ) && !in_array(0, $modCatids)) {
				foreach ( $modCatids as $c ) {
					$db->setQuery ( "INSERT INTO #__kunena_moderation SET catid='$c', userid='$uid'" );
					$db->query ();
					if (KunenaError::checkDatabaseError()) return;
				}
			}
		}

		$db->setQuery ( "UPDATE #__kunena_sessions SET allowed='na' WHERE userid='$uid'" );
		$db->query ();
		KunenaError::checkDatabaseError();

		$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_USER_PROFILE_SAVED_SUCCESSFULLY' ) );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function trashusermessages() {
		$app = JFactory::getApplication ();
		$db = JFactory::getDBO ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$path = KUNENA_PATH_LIB.'/kunena.moderation.class.php';
		require_once ($path);
		$kunena_mod = CKunenaModeration::getInstance();

		$uids = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		if ($uids) {
			//select only the messages which aren't already in the trash
			$db->setQuery ( "SELECT id FROM #__kunena_messages WHERE hold!=2 AND userid IN ('$uids')" );
			$idusermessages = $db->loadObjectList ();
			if (KunenaError::checkDatabaseError()) return;
			foreach ($idusermessages as $messageID) {
				$kunena_mod->deleteMessage($messageID->id, $DeleteAttachments = false);
			}
		} else {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$app->enqueueMessage ( JText::_('COM_KUNENA_A_USERMES_TRASHED_DONE') );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function move() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$userids = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		if ($userids < 0 ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$app->setUserState ( 'kunena.usermove.userid', $userids );

		$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=move", false));
	}

	function movemessages () {
		$app = JFactory::getApplication ();
		$db = JFactory::getDBO ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$path = KUNENA_PATH_LIB.'/kunena.moderation.class.php';
		require_once ($path);
		$kunena_mod = CKunenaModeration::getInstance();

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		$path = KUNENA_PATH_LIB  .'/kunena.moderation.class.php';
		require_once ($path);
		$kunena_mod = CKunenaModeration::getInstance();

		$uid = JRequest::getVar( 'uid', '', 'post' );
		if ($uid) {
		$db->setQuery ( "SELECT id,thread FROM #__kunena_messages WHERE hold=0 AND userid IN ('$uid')" );
		$idusermessages = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;
			if ( !empty($idusermessages) ) {
				foreach ($idusermessages as $id) {
					$kunena_mod->moveMessage($id->id, $cid[0], $TargetSubject = '', $TargetMessageID = 0);
				}
			}
		}  else {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$app->enqueueMessage ( JText::_('COM_A_KUNENA_USERMES_MOVED_DONE') );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function logout() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$id = (int)array_shift($cid);

		if ($id < 0 ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$options = array();
		$options['clientid'][] = 0; // site
		$app->logout( (int) $id, $options);

		$app->enqueueMessage ( JText::_('COM_A_KUNENA_USER_LOGOUT_DONE'));
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function delete() {
		$app = JFactory::getApplication ();
		kimport('kunena.user.helper');
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cids = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		if ( empty($cids) ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		foreach ( $cids as $userid ) {
			$user = KunenaUserHelper::get($userid);
			$user->delete();
		}

		$app->enqueueMessage (JText::_('COM_A_KUNENA_USER_DELETE_DONE'));
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function ban() {
		$app = JFactory::getApplication ();
		kimport('kunena.user.ban');
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$userid = (int)array_shift($cid);

		if ($userid < 0 ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$ban = KunenaUserBan::getInstanceByUserid ( $userid, true );
		if (! $ban->id) {
			$ban->ban ( $userid, null, 0 );
			$success = $ban->save ();
		} else {
			jimport ('joomla.utilities.date');
			$now = new JDate();
			$ban->setExpiration ( $now );
			$success = $ban->save ();
		}

		$message = JText::_ ( 'COM_KUNENA_USER_BANNED_DONE' );

		if (! $success) {
			$app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$app->enqueueMessage ( $message );
		}

		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function unban() {
		$app = JFactory::getApplication ();
		kimport('kunena.user.ban');
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$userid = (int)array_shift($cid);

		if ($userid < 0 ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$ban = KunenaUserBan::getInstanceByUserid ( $userid, true );
		if (! $ban->id) {
			$ban->ban ( $userid, null, 0 );
			$success = $ban->save ();
		} else {
			jimport ('joomla.utilities.date');
			$now = new JDate();
			$ban->setExpiration ( $now );
			$success = $ban->save ();
		}

		$message = JText::_ ( 'COM_KUNENA_USER_UNBAN_DONE' );

		if (! $success) {
			$app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$app->enqueueMessage ( $message );
		}

		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function block() {
		$app = JFactory::getApplication ();
		kimport('kunena.user.ban');
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$userid = (int)array_shift($cid);

		if ($userid < 0 ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$ban = KunenaUserBan::getInstanceByUserid ( $userid, true );
		if (! $ban->id) {
			$ban->ban ( $userid, null, 1 );
			$success = $ban->save ();
		} else {
			jimport ('joomla.utilities.date');
			$now = new JDate();
			$ban->setExpiration ( $now );
			$success = $ban->save ();
		}

		$message = JText::_ ( 'COM_KUNENA_USER_BLOCKED_DONE' );


		if (! $success) {
			$app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$app->enqueueMessage ( $message );
		}

		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function unblock() {
		$app = JFactory::getApplication ();
		kimport('kunena.user.ban');
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$userid = (int)array_shift($cid);

		if ($userid < 0 ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$ban = KunenaUserBan::getInstanceByUserid ( $userid, true );
		if (! $ban->id) {
			$ban->ban ( $userid, null, 1 );
			$success = $ban->save ();
		} else {
			jimport ('joomla.utilities.date');
			$now = new JDate();
			$ban->setExpiration ( $now );
			$success = $ban->save ();
		}

		$message = JText::_ ( 'COM_KUNENA_USER_UNBLOCK_DONE' );

		if (! $success) {
			$app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$app->enqueueMessage ( $message );
		}

		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}
}
