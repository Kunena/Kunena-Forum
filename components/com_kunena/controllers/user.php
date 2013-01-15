<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once KPATH_SITE . '/lib/kunena.link.class.php';

/**
 * Kunena User Controller
 *
 * @since		2.0
 */
class KunenaControllerUser extends KunenaController {
	public function display($cachable = false, $urlparams = false) {
		// Redirect profile to integrated component if profile integration is turned on
		$redirect = 1;
		$active = $this->app->getMenu ()->getActive ();

		if (!empty($active)) {
			if (version_compare(JVERSION, '1.6', '>')) {
				// Joomla 1.6+
				$params = $active->params;
			} else {
				// Joomla 1.5
				$params = new JParameter($active->params);
			}
			$redirect = $params->get('integration', 1);
		}
		if ($redirect && JRequest::getCmd('format', 'html') == 'html') {
			$profileIntegration = KunenaFactory::getProfile();
			$layout = JRequest::getCmd('layout', 'default');
			if ($profileIntegration instanceof KunenaProfileKunena) {
				// Continue
			} elseif ($layout == 'default') {
				$url = $this->me->getUrl(false);
			} elseif ($layout == 'list') {
				$url = $profileIntegration->getUserListURL('', false);
			}
			if (!empty($url)) {
				$this->setRedirect($url);
				return;
			}
		}
		parent::display();
	}

	public function change() {
		if (! JRequest::checkToken ('get')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$layout = JRequest::getString ( 'topic_layout', 'default' );
		$this->me->setTopicLayout ( $layout );
		$this->redirectBack ();
	}

	public function karmaup() {
		$this->karma(1);
	}

	public function karmadown() {
		$this->karma(-1);
	}

	public function save() {
		// TODO: allow moderators to save another users profile (without account info)
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		// perform security checks
		if (!$this->me->exists()) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}

		$this->user = JFactory::getUser();
		$success = $this->saveUser();
		if (!$success) {
			$this->app->enqueueMessage($this->user->getError(), 'notice');
		} else {
			$this->saveProfile();
			$this->saveAvatar();
			$this->saveSettings();
			if (!$this->me->save()) {
				$this->app->enqueueMessage($this->me->getError(), 'notice');
			}
		}

		$msg = JText::_( 'COM_KUNENA_PROFILE_SAVED' );
		$this->setRedirect ( $this->me->getUrl(false), $msg );
	}

	function ban() {
		$user = KunenaFactory::getUser(JRequest::getInt ( 'userid', 0 ));
		if(!$user->exists() || !JRequest::checkToken()) {
			$this->app->redirect ( $user->getUrl(false), JText::_('COM_KUNENA_ERROR_TOKEN'), 'error' );
			return;
		}

		$ip = JRequest::getVar ( 'ip', '' );
		$block = JRequest::getInt ( 'block', 0 );
		$expiration = JRequest::getString ( 'expiration', '' );
		$reason_private = JRequest::getString ( 'reason_private', '' );
		$reason_public = JRequest::getString ( 'reason_public', '' );
		$comment = JRequest::getString ( 'comment', '' );

		$ban = KunenaUserBan::getInstanceByUserid ( $user->userid, true );
		if (! $ban->id) {
			$ban->ban ( $user->userid, $ip, $block, $expiration, $reason_private, $reason_public, $comment );
			$success = $ban->save ();
			$this->report($user->userid);
		} else {
			$delban = JRequest::getString ( 'delban', '' );

			if ( $delban ) {
				$ban->unBan($comment);
				$success = $ban->save ();
			} else {
				$ban->blocked = $block;
				$ban->setExpiration ( $expiration, $comment );
				$ban->setReason ( $reason_public, $reason_private );
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
			$this->app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$this->app->enqueueMessage ( $message );
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
			if (JFile::exists ( JPATH_ROOT . '/media/kunena/avatars/' . $userprofile->avatar ) && !stristr($userprofile->avatar,'gallery/')) {
				JFile::delete ( JPATH_ROOT . '/media/kunena/avatars/' . $userprofile->avatar );
				$avatar_deleted = $this->app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_AVATAR_FILESYSTEM') );
			}
			$user->avatar = '';
			$user->save();
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_AVATAR') . $avatar_deleted );
		}
		if (! empty ( $DelProfileInfo )) {
			$user->personalText = '';
			$user->birthdate = '0000-00-00';
			$user->location = '';
			$user->gender = 0;
			$user->icq = '';
			$user->aim = '';
			$user->yim = '';
			$user->msn = '';
			$user->skype = '';
			$user->gtalk = '';
			$user->twitter = '';
			$user->facebook = '';
			$user->myspace = '';
			$user->linkedin = '';
			$user->delicious = '';
			$user->friendfeed = '';
			$user->digg = '';
			$user->blogspot = '';
			$user->flickr = '';
			$user->bebo = '';
			$user->websitename = '';
			$user->websiteurl = '';
			$user->signature = '';
			$user->save();
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_PROFILEINFO') );
		} elseif (! empty ( $DelSignature )) {
			$user->signature = '';
			$user->save();
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_SIGNATURE') );
		}

		if (! empty ( $banDelPosts )) {
			list($total, $messages) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, array('starttime'=> '-1','user' => $user->userid));
			foreach($messages as $mes) {
				$mes->publish(KunenaForum::DELETED);
			}
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_MESSAGES') );
		}

		$this->app->redirect ( $user->getUrl(false) );
	}

	function cancel()
	{
		$this->app->redirect ( CKunenaLink::GetMyProfileURL(null, '', false) );
	}

	function login() {
		if(!JFactory::getUser()->guest || !JRequest::checkToken()) {
			$this->app->redirect ( JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' ), JText::_('COM_KUNENA_ERROR_TOKEN'), 'error' );
		}

		$username = JRequest::getString ( 'username', '', 'POST' );
		$password = JRequest::getString ( 'password', '', 'POST', JREQUEST_ALLOWRAW );
		$remember = JRequest::getBool ( 'remember', false, 'POST');

		$login = KunenaLogin::getInstance();
		$login->loginUser($username, $password, $remember);
		$this->redirectBack ();
	}

	function logout() {
		if(!JRequest::checkToken('request')) {
			$this->app->redirect ( JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' ), JText::_('COM_KUNENA_ERROR_TOKEN'), 'error' );
		}

		$login = KunenaLogin::getInstance();
		if (!JFactory::getUser()->guest) $login->logoutUser();
		$this->redirectBack ();
	}

	// Internal functions:

	protected function karma($karmaDelta) {
		if (! JRequest::checkToken ('get')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}
		$karma_delay = '14400'; // 14400 seconds = 6 hours
		$userid = JRequest::getInt ( 'userid', 0 );
		$catid = JRequest::getInt ( 'catid', 0 );

		$target = KunenaFactory::getUser($userid);

		if (!$this->config->showkarma || !$this->me->exists() || !$target->exists() || $karmaDelta == 0) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_USER_ERROR_KARMA' ), 'error' );
			$this->redirectBack ();
		}

		$now = JFactory::getDate()->toUnix();
		if (!$this->me->isModerator() && $now - $this->me->karma_time < $karma_delay) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_KARMA_WAIT' ), 'notice' );
			$this->redirectBack ();
		}

		if ($karmaDelta > 0) {
			if ($this->me->userid == $target->userid) {
				$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_KARMA_SELF_INCREASE' ), 'notice' );
				$karmaDelta = -10;
			} else {
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_KARMA_INCREASED' ) );
			}
		} else {
			if ($this->me->userid == $target->userid) {
				$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_KARMA_SELF_DECREASE' ), 'notice' );
			} else {
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_KARMA_DECREASED' ) );
			}
		}

		$this->me->karma_time = $now;
		if ($this->me->userid != $target->userid && !$this->me->save()) {
			$this->app->enqueueMessage($this->me->getError(), 'notice');
			$this->redirectBack ();
		}
		$target->karma += $karmaDelta;
		if (!$target->save()) {
			$this->app->enqueueMessage($target->getError(), 'notice');
			$this->redirectBack ();
		}
		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		$activity->onAfterKarma($target->userid, $this->me->userid, $karmaDelta);
		$this->redirectBack ();
	}

	// Mostly copied from Joomla 1.5
	protected function saveUser(){
		// we only allow users to edit few fields
		$allow = array('name', 'email', 'password', 'password2', 'params');
		if ($this->config->usernamechange) {
			if (version_compare(JVERSION, '2.5.5','<') || JComponentHelper::getParams('com_users')->get('change_login_name', 1)) $allow[] = 'username';
		}

		//clean request
		$post = JRequest::get( 'post' );
		$post['password']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['password2']	= JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW);
		if (empty($post['password']) || empty($post['password2'])) {
			unset($post['password'], $post['password2']);
		}
		$post = array_intersect_key($post, array_flip($allow));

		// get the redirect
		$return = CKunenaLink::GetMyProfileURL($this->user->id, '', false);
		$err_return = CKunenaLink::GetMyProfileURL($this->user->id, 'edit', false);

		// do a password safety check
		if ( !empty($post['password']) && !empty($post['password2']) ) {
			if(strlen($post['password']) < 5 && strlen($post['password2']) < 5 ) {
				if($post['password'] != $post['password2']) {
					$msg = JText::_('COM_KUNENA_PROFILE_PASSWORD_MISMATCH');
					$this->app->redirect ( $err_return, $msg, 'error' );
				}
				$msg = JText::_('COM_KUNENA_PROFILE_PASSWORD_NOT_MINIMUM');
				$this->app->redirect ( $err_return, $msg, 'error' );
			}
		}

		$username = $this->user->get('username');

		// Bind the form fields to the user table
		if (!$this->user->bind($post)) {
			return false;
		}

		// Store user to the database
		if (!$this->user->save(true)) {
			return false;
		}

		$session = JFactory::getSession();
		$session->set('user', $this->user);

		// update session if username has been changed
		if ( $username && $username != $this->user->username ){
			$table = JTable::getInstance('session', 'JTable' );
			$table->load($session->getId());
			$table->username = $this->user->username;
			$table->store();
		}
		return true;
	}

	protected function saveProfile() {
		$this->me->personalText = JRequest::getVar ( 'personaltext', '' );
		$this->me->birthdate = JRequest::getInt ( 'birthdate1', '0000' ).'-'.JRequest::getInt ( 'birthdate2', '00' ).'-'.JRequest::getInt ( 'birthdate3', '00' );
		$this->me->location = trim(JRequest::getVar ( 'location', '' ));
		$this->me->gender = JRequest::getInt ( 'gender', '' );
		$this->me->icq = trim(JRequest::getString ( 'icq', '' ));
		$this->me->aim = trim(JRequest::getString ( 'aim', '' ));
		$this->me->yim = trim(JRequest::getString ( 'yim', '' ));
		$this->me->msn = trim(JRequest::getString ( 'msn', '' ));
		$this->me->skype = trim(JRequest::getString ( 'skype', '' ));
		$this->me->gtalk = trim(JRequest::getString ( 'gtalk', '' ));
		$this->me->twitter = trim(JRequest::getString ( 'twitter', '' ));
		$this->me->facebook = trim(JRequest::getString ( 'facebook', '' ));
		$this->me->myspace = trim(JRequest::getString ( 'myspace', '' ));
		$this->me->linkedin = trim(JRequest::getString ( 'linkedin', '' ));
		$this->me->delicious = trim(JRequest::getString ( 'delicious', '' ));
		$this->me->friendfeed = trim(JRequest::getString ( 'friendfeed', '' ));
		$this->me->digg = trim(JRequest::getString ( 'digg', '' ));
		$this->me->blogspot = trim(JRequest::getString ( 'blogspot', '' ));
		$this->me->flickr = trim(JRequest::getString ( 'flickr', '' ));
		$this->me->bebo = trim(JRequest::getString ( 'bebo', '' ));
		$this->me->websitename = JRequest::getString ( 'websitename', '' );
		$this->me->websiteurl = JRequest::getString ( 'websiteurl', '' );
		$this->me->signature = JRequest::getVar ( 'signature', '', 'post', 'string', JREQUEST_ALLOWRAW );
	}

	protected function saveAvatar() {
		$action = JRequest::getString('avatar', 'keep');

		require_once (KPATH_SITE.'/lib/kunena.upload.class.php');
		$upload = new CKunenaUpload();
		$upload->setAllowedExtensions('gif, jpeg, jpg, png');

		$db = JFactory::getDBO();
		if ( $upload->uploaded('avatarfile') ) {
			$filename = 'avatar'.$this->me->userid;

			if (preg_match('|^users/|' , $this->me->avatar)) {
				// Delete old uploaded avatars:
				if ( JFolder::exists( KPATH_MEDIA.'/avatars/resized' ) ) {
					$deletelist = JFolder::folders(KPATH_MEDIA.'/avatars/resized', '.', false, true);
					foreach ($deletelist as $delete) {
						if (is_file($delete.'/'.$this->me->avatar))
							JFile::delete($delete.'/'.$this->me->avatar);
					}
				}
				if ( JFile::exists( KPATH_MEDIA.'/avatars/'.$this->me->avatar ) ) {
					JFile::delete(KPATH_MEDIA.'/avatars/'.$this->me->avatar);
				}
			}

			$upload->setImageResize(intval($this->config->avatarsize)*1024, 200, 200, $this->config->avatarquality);
			$upload->uploadFile(KPATH_MEDIA . '/avatars/users' , 'avatarfile', $filename, false);
			$fileinfo = $upload->getFileInfo();

			if ($fileinfo['ready'] === true) {
				$this->me->avatar = 'users/'.$fileinfo['name'];
			}
			if (!$fileinfo['status']) $this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo['name']).': '.$fileinfo['error'], 'error' );
			else $this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_PROFILE_AVATAR_UPLOADED' ) );

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

	// Reports a user to stopforumspam.com
	protected function report($userid) {
		if(!$this->config->stopforumspam_key || ! $userid)
		{
			return false;
		}
		$spammer = JFactory::getUser($userid);

		$db = JFactory::getDBO();
		$db->setQuery ( "SELECT ip FROM #__kunena_messages WHERE userid=".$userid." GROUP BY ip ORDER BY `time` DESC", 0, 1 );
		$ip = $db->loadResult();

		$data = "username=".$spammer->username."&ip_addr=".$ip."&email=".$spammer->email."&api_key=".$this->config->stopforumspam_key;
		$fp = fsockopen("www.stopforumspam.com",80);
		fputs($fp, "POST /add.php HTTP/1.1\n" );
		fputs($fp, "Host: www.stopforumspam.com\n" );
		fputs($fp, "Content-type: application/x-www-form-urlencoded\n" );
		fputs($fp, "Content-length: ".strlen($data)."\n" );
		fputs($fp, "Connection: close\n\n" );
		fputs($fp, $data);
		fclose($fp);
		return true;
	}

	public function delfile() {
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}
		$cids = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		if ( !empty($cids) ) {
			$number = 0;

			foreach( $cids as $id ) {
				$attachment = KunenaForumMessageAttachmentHelper::get($id);
				if ($attachment->authorise('delete') && $attachment->delete()) $number++;
			}

			if ( $number > 0 ) {
				$this->app->enqueueMessage ( JText::sprintf( 'COM_KUNENA_ATTACHMENTS_DELETE_SUCCESSFULLY', $number) );
				$this->redirectBack ();
			} else {
				$this->app->enqueueMessage ( JText::_( 'COM_KUNENA_ATTACHMENTS_DELETE_FAILED') );
				$this->redirectBack ();
			}
		} else {
			$this->app->enqueueMessage ( JText::_( 'COM_KUNENA_ATTACHMENTS_NO_ATTACHMENTS_SELECTED') );
			$this->redirectBack ();
		}
	}
}
