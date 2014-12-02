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
 * Kunena Users Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerUsers extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=users';
	}

	function edit() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid < 0 ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$this->app->setUserState ( 'kunena.user.userid', $userid );

		$this->setRedirect(JRoute::_("index.php?option=com_kunena&view=user&layout=edit&userid={$userid}", false));
	}

	function save() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$newview = JRequest::getString ( 'newview' );
		$newrank = JRequest::getString ( 'newrank' );
		$signature = JRequest::getString ( 'signature', '', 'POST', JREQUEST_ALLOWRAW );
		$deleteSig = JRequest::getInt ( 'deleteSig' );
		$moderator = JRequest::getInt ( 'moderator' );
		$uid = JRequest::getInt ( 'uid' );
		$deleteAvatar = JRequest::getInt ( 'deleteAvatar' );
		$neworder = JRequest::getInt ( 'neworder' );
		$modCatids = $moderator ? JRequest::getVar('catid', array(), 'post', 'array') : array(); // Array of integers
		JArrayHelper::toInteger($modCatids);

		if ( $uid ) {
			$user = KunenaFactory::getUser($uid);

			// Prepare variables
			if ($deleteSig == 1) $user->signature = '';
			else $user->signature = $signature;
			$user->view = $newview;
			$user->ordering = $neworder;
			$user->rank = $newrank;
			if ($deleteAvatar == 1) $user->avatar = '';
			if ( !$user->save() ) {
				$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_USER_PROFILE_SAVED_FAILED' ), 'error');
			} else {
				$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_USER_PROFILE_SAVED_SUCCESSFULLY' ) );
			}

			// Update moderator rights
			$categories = KunenaForumCategoryHelper::getCategories(false, false, 'admin');

			foreach ($categories as $category) {
				$category->setModerator($user, in_array($category->id, $modCatids));
			}
			// Global moderator is a special case
			if ($this->me->isAdmin()) {
				KunenaAccess::getInstance()->setModerator(0, $user, in_array(0, $modCatids));
			}
		}
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function trashusermessages() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		if ($cid) {
			foreach($cid as $id) {
				list($total, $messages) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, array('starttime'=> '-1','user' => $id));
				foreach($messages as $mes) {
					$mes->publish(KunenaForum::DELETED);
				}
			}
		} else {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_USERMES_TRASHED_DONE') );
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function move() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		if ( empty($cid) ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$this->app->setUserState ( 'kunena.usermove.userids', $cid );

		$this->setRedirect(JRoute::_("index.php?option=com_kunena&view=user&layout=move", false));
	}

	function movemessages () {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$catid = JRequest::getInt('catid');
		$uids = (array) $this->app->getUserState ( 'kunena.usermove.userids' );

		$error = null;
		if ($uids) {
			foreach($uids as $id) {
				list($total, $messages) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, array('starttime'=> '-1','user' => $id));

				foreach($messages as $object) {
					$topic = $object->getTopic();

					if (!$object->authorise ( 'move' )) {
						$error = $object->getError();
					} else {
						$target = KunenaForumCategoryHelper::get( $catid );
						if (!$topic->move ( $target, false, false, '', false )) {
							$error = $topic->getError();
						}
					}
				}
			}

		} else {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		if ($error) {
			$this->app->enqueueMessage ( $error, 'notice' );
		} else {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_USERMES_MOVED_DONE') );
		}
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function logout() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);
		$id = array_shift($cid);

		if ($id < 0 ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$options = array('clientid'=>0); // Just logout from site
		$this->app->logout( (int) $id, $options);

		$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_USER_LOGOUT_DONE') );
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function remove() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		if ( empty($cid) ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$users = KunenaUserHelper::loadUsers($cid);

		$my = JFactory::getUser();
		$usernames = array();
		foreach ( $users as $user ) {
			$groups = JUserHelper::getUserGroups($user->userid);

			if ( $my->id == $user->userid ) {
				$this->app->enqueueMessage (JText::_('COM_KUNENA_USER_ERROR_CANNOT_DELETE_YOURSELF'), 'notice');
				continue;
			}

			$instance = JUser::getInstance($user->userid);

			if ( $instance->authorise('core.admin') )  {
				$this->app->enqueueMessage (JText::_('COM_KUNENA_USER_ERROR_CANNOT_DELETE_ADMINS'), 'notice');
				continue;
			}

			$result = $user->delete();
			if ( !$result ) {
				$this->app->enqueueMessage( JText::sprintf('COM_KUNENA_USER_DELETE_KUNENA_USER_TABLE_FAILED', $user->userid), 'notice' );
				continue;
			}

			// Delete the user too from Joomla!
			$jresult = $instance->delete();
			if ( !$jresult ) {
				$this->app->enqueueMessage( JText::sprintf('COM_KUNENA_USER_DELETE_JOOMLA_USER_TABLE_FAILED', $user->userid), 'notice' );
				continue;
			}

			$usernames[] = $user->username;
		}

		if ( !empty($usernames) )  $this->app->enqueueMessage (JText::sprintf('COM_KUNENA_USER_DELETE_DONE_SUCCESSFULLY', implode(', ',$usernames)) );

		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function ban() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid < 0 ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
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
			$this->app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$this->app->enqueueMessage ( $message );
		}

		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function unban() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid < 0 ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
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
			$this->app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$this->app->enqueueMessage ( $message );
		}

		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function block() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid < 0 ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
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
			$this->app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$this->app->enqueueMessage ( $message );
		}

		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function unblock() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid < 0 ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
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
			$this->app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$this->app->enqueueMessage ( $message );
		}

		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	public function batch_moderators() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);
		$catids = JRequest::getVar('catid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($catids);

		if ( empty($cid) ) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_USERS_BATCH_NO_USERS_SELECTED' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		if ( empty($catids) ) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_USERS_BATCH_NO_CATEGORIES_SELECTED' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		// Update moderator rights
		$categories = KunenaForumCategoryHelper::getCategories(false, false, 'admin');
		$users = KunenaUserHelper::loadUsers($cid);
		foreach ($users as $user) {
			foreach ($categories as $category) {
				if (in_array($category->id, $catids)) $category->setModerator($user, true);
			}
			// Global moderator is a special case
			if ($this->me->isAdmin() && in_array(0, $catids)) {
				KunenaAccess::getInstance()->setModerator(0, $user, true);
			}
		}

		$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_USERS_SET_MODERATORS_DONE' ) );
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return void
	 *
	 * @since 3.1
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
