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
kimport ( 'kunena.forum.category.helper' );
kimport ( 'kunena.user.helper' );

require_once KPATH_SITE . '/lib/kunena.link.class.php';

/**
 * Kunena User Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerUser extends KunenaController {
	public function karmaup() {
		$this->karma(1);
	}

	public function karmadown() {
		$this->karma(-1);
	}

	public function save() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		// perform security checks
		$this->user = JFactory::getUser();
		if ($this->user->id == 0) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}

		$this->config = KunenaFactory::getConfig();

		$this->saveUser();

		$this->me = KunenaFactory::getUser();
		$this->saveProfile();
		$this->saveAvatar();
		$this->saveSettings();
		if (!$this->me->save()) {
			$app = JFactory::getApplication();
			$app->enqueueMessage($this->me->getError(), 'notice');
		}

		$msg = JText::_( 'COM_KUNENA_PROFILE_SAVED' );
		$this->setRedirect ( CKunenaLink::GetMyProfileURL($this->user->get('id'), '', false), $msg );
	}

	function ban() {
		$app = JFactory::getApplication();
		$user = KunenaFactory::getUser(JRequest::getInt ( 'userid', 0 ));
		if(!$user->exists() || !JRequest::checkToken()) {
			$app->redirect ( CKunenaLink::GetProfileURL($user->userid, false), COM_KUNENA_ERROR_TOKEN, 'error' );
			return;
		}

		$ip = JRequest::getVar ( 'ip', '' );
		$block = JRequest::getInt ( 'block', 0 );
		$expiration = JRequest::getString ( 'expiration', '' );
		$reason_private = JRequest::getString ( 'reason_private', '' );
		$reason_public = JRequest::getString ( 'reason_public', '' );
		$comment = JRequest::getString ( 'comment', '' );

		kimport ( 'kunena.user.ban' );
		$ban = KunenaUserBan::getInstanceByUserid ( $user->userid, true );
		if (! $ban->id) {
			$ban->ban ( $user->userid, $ip, $block, $expiration, $reason_private, $reason_public, $comment );
			$success = $ban->save ();
		} else {
			$delban = JRequest::getString ( 'delban', '' );

			if ( $delban ) {
				$ban->unBan($comment);
				$success = $ban->save ();
			} else {
				$ban->blocked = $block;
				$ban->setExpiration ( $expiration, $comment );
				$ban->setReason ( $reason_private, $reason_public );
				$success = $ban->save ();
			}
		}

		if ($block) {
			if ($ban->isEnabled ())
				$message = JText::_ ( 'COM_KUNENA_USER_BLOCKED_DONE' );
			else
				$message = JText::_ ( 'COM_KUNENA_USER_UNBLOCKED_DONE' );
		} else {
			if ($ban->isEnabled ())
				$message = JText::_ ( 'COM_KUNENA_USER_BANNED_DONE' );
			else
				$message = JText::_ ( 'COM_KUNENA_USER_UNBANNED_DONE' );
		}

		if (! $success) {
			$app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$app->enqueueMessage ( $message );
		}

		$banDelPosts = JRequest::getVar ( 'bandelposts', '' );
		$DelAvatar = JRequest::getVar ( 'delavatar', '' );
		$DelSignature = JRequest::getVar ( 'delsignature', '' );
		$DelProfileInfo = JRequest::getVar ( 'delprofileinfo', '' );

		$db = JFactory::getDBO();
		if (! empty ( $DelAvatar ) || ! empty ( $DelProfileInfo )) {
			jimport ( 'joomla.filesystem.file' );
			$avatar_deleted = '';
			// Delete avatar from file system
			if (JFile::exists ( KUNENA_PATH_AVATAR_UPLOADED . DS . $userprofile->avatar ) && !stristr($userprofile->avatar,'gallery/')) {
				JFile::delete ( KUNENA_PATH_AVATAR_UPLOADED . DS . $userprofile->avatar );
				$avatar_deleted = $app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_AVATAR_FILESYSTEM') );
			}
			$user->avatar = '';
			$user->save();
			$app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_AVATAR') . $avatar_deleted );
		}
		if (! empty ( $DelProfileInfo )) {
			$user->personalText = '';
			$user->birthdate = '0000-00-00';
			$user->location = '';
			$user->gender = 0;
			$user->ICQ = '';
			$user->AIM = '';
			$user->YIM = '';
			$user->MSN = '';
			$user->SKYPE = '';
			$user->GTALK = '';
			$user->TWITTER = '';
			$user->FACEBOOK = '';
			$user->MYSPACE = '';
			$user->LINKEDIN = '';
			$user->DELICIOUS = '';
			$user->FRIENDFEED = '';
			$user->DIGG = '';
			$user->BLOGSPOT = '';
			$user->FLICKR = '';
			$user->BEBO = '';
			$user->websitename = '';
			$user->websiteurl = '';
			$user->signature = '';
			$user->save();
			$app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_PROFILEINFO') );
		} elseif (! empty ( $DelSignature )) {
			$user->signature = '';
			$user->save();
			$app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_SIGNATURE') );
		}

		if (! empty ( $banDelPosts )) {
			// FIXME: delete user posts needs new logic (not here)
			//select only the messages which aren't already in the trash
/*			$db->setQuery ( "UPDATE #__kunena_messages SET hold=2 WHERE hold!=2 AND userid={$db->Quote($user->userid)}" );
			$idusermessages = $db->loadObjectList ();
			KunenaError::checkDatabaseError();
			$app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_MESSAGES') );*/
		}

		$app->redirect ( CKunenaLink::GetProfileURL($user->userid, false) );
	}

	function cancel()
	{
		$app = JFactory::getApplication();
		$app->redirect ( CKunenaLink::GetMyProfileURL(null, '', false) );
	}

	function login() {
		$app = JFactory::getApplication();
		if(!JRequest::checkToken()) {
			$app->redirect ( JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' ), COM_KUNENA_ERROR_TOKEN, 'error' );
		}

		$username = JRequest::getString ( 'username', '', 'POST' );
		$password = JRequest::getString ( 'passwd', '', 'POST' );
		$remember = JRequest::getInt ( 'remember', 0, 'POST');
		$return = JRequest::getString ( 'return', '', 'POST' );

		$login = KunenaFactory::getLogin();
		$result = $login->loginUser($username, $password, $remember, $return);
		if ($result) $app->enqueueMessage ( $result, 'notice' );
		$app->redirect ( JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' ) );
	}

	function logout() {
		$app = JFactory::getApplication();
		if(!JRequest::checkToken()) {
			$app->redirect ( JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' ), COM_KUNENA_ERROR_TOKEN, 'error' );
		}

		$return = JRequest::getString ( 'return', '', 'POST' );
		$login = KunenaFactory::getLogin();
		$result = $login->logoutUser($return);
		if ($result) $app->enqueueMessage ( $result, 'notice' );
		$app->redirect ( JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' ) );
	}

	// Internal functions:

	protected function karma($karmaDelta) {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ('get')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}
		$karma_delay = '14400'; // 14400 seconds = 6 hours
		$userid = JRequest::getInt ( 'userid', 0 );
		$catid = JRequest::getInt ( 'catid', 0 );

		$config = KunenaFactory::getConfig();
		$me = KunenaFactory::getUser();
		$target = KunenaFactory::getUser($userid);

		if (!$config->showkarma || !$me->exists() || !$target->exists() || $karmaDelta == 0) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_USER_ERROR_KARMA' ), 'error' );
			$this->redirectBack ();
		}

		$now = JFactory::getDate()->toUnix();
		if (!$me->isModerator($catid) && $now - $me->karma_time < $karma_delay) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_KARMA_WAIT' ), 'notice' );
			$this->redirectBack ();
		}

		if ($karmaDelta > 0) {
			if ($me->userid == $target->userid) {
				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_KARMA_SELF_INCREASE' ), 'notice' );
				$karmaDelta = -10;
			} else {
				$app->enqueueMessage ( JText::_('COM_KUNENA_KARMA_INCREASED' ) );
			}
		} else {
			if ($me->userid == $target->userid) {
				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_KARMA_SELF_DECREASE' ), 'notice' );
			} else {
				$app->enqueueMessage ( JText::_('COM_KUNENA_KARMA_DECREASED' ) );
			}
		}

		$me->karma_time = $now;
		if ($me->userid != $target->userid && !$me->save()) {
			$app->enqueueMessage($me->getError(), 'notice');
			$this->redirectBack ();
		}
		$target->karma += $karmaDelta;
		if (!$target->save()) {
			$app->enqueueMessage($target->getError(), 'notice');
			$this->redirectBack ();
		}
		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		$activity->onAfterKarma($target->userid, $me->userid, $karmaDelta);
		$this->redirectBack ();
	}

	// Mostly copied from Joomla 1.5
	protected function saveUser()
	{
		$app = JFactory::getApplication();
		$user = $this->user; //new JUser ( $this->user->get('id') );

		// we don't want users to edit certain fields so we will ignore them
		$ignore = array('id', 'gid', 'block', 'usertype', 'registerDate', 'activation');

		//clean request
		$post = JRequest::get( 'post' );
		$post['password']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['password2']	= JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW);
		if (empty($post['password']) || empty($post['password2'])) {
			unset($post['password'], $post['password2']);
		}
		if ($this->config->usernamechange) $post['username'] = JRequest::getVar('username', '', 'post', 'username');
		else $ignore[] = 'username';
		foreach ($ignore as $field) {
			if (isset($post[$field]))
				unset($post[$field]);
		}

		jimport ( 'joomla.version' );
		$jversion = new JVersion ();
		if ( $jversion->RELEASE == '1.6' ) {
			jimport('joomla.user.helper');
			$result = JUserHelper::getUserGroups($user->id);

			$groups = array();
			foreach ( $result as $key => $value ) {
				$groups[]= $key;
			}

			$post['groups'] = $groups;
		}

		// get the redirect
		$return = CKunenaLink::GetMyProfileURL($user->id, '', false);
		$err_return = CKunenaLink::GetMyProfileURL($user->id, 'edit', false);

		// do a password safety check
		if ( !empty($post['password']) && !empty($post['password2']) ) {
			if(strlen($post['password']) < 5 && strlen($post['password2']) < 5 ) {
				if($post['password'] != $post['password2']) {
					$msg = JText::_('COM_KUNENA_PROFILE_PASSWORD_MISMATCH');
					$app->redirect ( $err_return, $msg, 'error' );
				}
				$msg = JText::_('COM_KUNENA_PROFILE_PASSWORD_NOT_MINIMUM');
				$app->redirect ( $err_return, $msg, 'error' );
			}
		}

		$username = $this->user->username;

		// Bind the form fields to the user table
		if (!$user->bind($post)) {
			$app->enqueueMessage ( $user->getError(), 'error' );
			return false;
		}

		// Store user to the database
		if (!$user->save(true)) {
			$app->enqueueMessage ( $user->getError(), 'error' );
			return false;
		}

		$session = JFactory::getSession();
		$session->set('user', $user);

		// update session if username has been changed
		if ( $username && $username != $user->username )
		{
			$table = JTable::getInstance('session', 'JTable' );
			$table->load($session->getId());
			$table->username = $user->username;
			$table->store();
		}
	}

	protected function saveProfile() {
		$this->me->personalText = JRequest::getVar ( 'personaltext', '' );
		$this->me->birthdate = JRequest::getInt ( 'birthdate1', '' ).'-'.JRequest::getInt ( 'birthdate2', '' ).'-'.JRequest::getInt ( 'birthdate3', '' );
		$this->me->location = trim(JRequest::getVar ( 'location', '' ));
		$this->me->gender = JRequest::getInt ( 'gender', '' );
		$this->me->ICQ = trim(JRequest::getString ( 'icq', '' ));
		$this->me->AIM = trim(JRequest::getString ( 'aim', '' ));
		$this->me->YIM = trim(JRequest::getString ( 'yim', '' ));
		$this->me->MSN = trim(JRequest::getString ( 'msn', '' ));
		$this->me->SKYPE = trim(JRequest::getString ( 'skype', '' ));
		$this->me->GTALK = trim(JRequest::getString ( 'gtalk', '' ));
		$this->me->TWITTER = trim(JRequest::getString ( 'twitter', '' ));
		$this->me->FACEBOOK = trim(JRequest::getString ( 'facebook', '' ));
		$this->me->MYSPACE = trim(JRequest::getString ( 'myspace', '' ));
		$this->me->LINKEDIN = trim(JRequest::getString ( 'linkedin', '' ));
		$this->me->DELICIOUS = trim(JRequest::getString ( 'delicious', '' ));
		$this->me->FRIENDFEED = trim(JRequest::getString ( 'friendfeed', '' ));
		$this->me->DIGG = trim(JRequest::getString ( 'digg', '' ));
		$this->me->BLOGSPOT = trim(JRequest::getString ( 'blogspot', '' ));
		$this->me->FLICKR = trim(JRequest::getString ( 'flickr', '' ));
		$this->me->BEBO = trim(JRequest::getString ( 'bebo', '' ));
		$this->me->websitename = JRequest::getString ( 'websitename', '' );
		$this->me->websiteurl = JRequest::getString ( 'websiteurl', '' );
		$this->me->signature = JRequest::getVar ( 'signature', '', 'post', 'string', JREQUEST_ALLOWRAW );
	}

	protected function saveAvatar() {
		$app = JFactory::getApplication();
		$action = JRequest::getString('avatar', 'keep');

		require_once (KUNENA_PATH_LIB .DS. 'kunena.upload.class.php');
		$upload = new CKunenaUpload();
		$upload->setAllowedExtensions('gif, jpeg, jpg, png');

		$db = JFactory::getDBO();
		if ( $upload->uploaded('avatarfile') ) {
			$filename = 'avatar'.$this->profile->userid;

			if (preg_match('|^users/|' , $this->profile->avatar)) {
				// Delete old uploaded avatars:
				if ( JFolder::exists( KPATH_MEDIA.'/avatars/resized' ) ) {
					$deletelist = JFolder::folders(KPATH_MEDIA.'/avatars/resized', '.', false, true);
					foreach ($deletelist as $delete) {
						if (is_file($delete.'/'.$this->profile->avatar))
							JFile::delete($delete.'/'.$this->profile->avatar);
					}
				}
				if ( JFile::exists( KPATH_MEDIA.'/avatars/'.$this->profile->avatar ) ) {
					JFile::delete(KPATH_MEDIA.'/avatars/'.$this->profile->avatar);
				}
			}

			$upload->setImageResize(intval($this->config->avatarsize)*1024, 200, 200, $this->config->avatarquality);
			$upload->uploadFile(KPATH_MEDIA . '/avatars/users' , 'avatarfile', $filename, false);
			$fileinfo = $upload->getFileInfo();

			if ($fileinfo['ready'] === true) {
				if(JDEBUG == 1 && defined('JFIREPHP')){
					FB::log('Kunena save avatar: ' . $fileinfo['name']);
				}
				$this->me->avatar = 'users/'.$fileinfo['name'];
			}
			if (!$fileinfo['status']) $app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo['name']).': '.$fileinfo['error'], 'error' );
			else $app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_PROFILE_AVATAR_UPLOADED' ) );

		} else if ( $action == 'delete' ) {
			//set default avatar
			$this->me->avatar = '';
		} else if ( substr($action, 0, 8) == 'gallery/' && strpos($action, '..') === false) {
			$this->me->avatar = $action;
		}
	}

	protected function saveSettings() {
		$this->me->ordering = JRequest::getInt('messageordering', '', 'post', 'messageordering');
		$this->me->hideEmail = JRequest::getInt('hidemail', '', 'post', 'hidemail');
		$this->me->showOnline = JRequest::getInt('showonline', '', 'post', 'showonline');
	}
}