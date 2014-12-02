<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

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
			$params = $active->params;
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
		if (! JSession::checkToken ('get')) {
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
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		// perform security checks
		if (!$this->me->exists()) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}

		$this->user = JFactory::getUser();
		if (!$this->saveUser()) {
			// Error was already enqueued.
		} elseif (!$this->saveAvatar()) {
			$this->app->enqueueMessage( JText::_( 'COM_KUNENA_PROFILE_AVATAR_NOT_SAVED' ), 'notice' );
		} else {
			$this->saveProfile();
			$this->saveSettings();
			if (!$this->me->save()) {
				$this->app->enqueueMessage($this->me->getError(), 'notice');
			} else {
				$this->app->enqueueMessage( JText::_( 'COM_KUNENA_PROFILE_SAVED' ) );
			}
		}

		$this->setRedirect($this->me->getUrl(false));
	}

	function ban() {
		$user = KunenaFactory::getUser(JRequest::getInt ( 'userid', 0 ));
		if(!$user->exists() || !JSession::checkToken('post')) {
			$this->app->redirect ( $user->getUrl(false), JText::_('COM_KUNENA_ERROR_TOKEN'), 'error' );
			return;
		}
		$ban = KunenaUserBan::getInstanceByUserid($user->userid, true);
		if (!$ban->canBan()) {
			$this->setRedirect($user->getUrl(false), $ban->getError(), 'error');
			return;
		}

		$ip = JRequest::getString ( 'ip', '' );
		$block = JRequest::getInt ( 'block', 0 );
		$expiration = JRequest::getString ( 'expiration', '' );
		$reason_private = JRequest::getString ( 'reason_private', '' );
		$reason_public = JRequest::getString ( 'reason_public', '' );
		$comment = JRequest::getString ( 'comment', '' );

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

		$banDelPosts = JRequest::getString('bandelposts', '');
		$DelAvatar = JRequest::getString('delavatar', '');
		$DelSignature = JRequest::getString('delsignature', '');
		$DelProfileInfo = JRequest::getString('delprofileinfo', '');

		if (! empty ( $DelAvatar ) || ! empty ( $DelProfileInfo )) {
			jimport ( 'joomla.filesystem.file' );
			$avatar_deleted = '';
			// Delete avatar from file system
			if (JFile::exists ( JPATH_ROOT . '/media/kunena/avatars/' . $user->avatar ) && !stristr($user->avatar,'gallery/')) {
				JFile::delete ( JPATH_ROOT . '/media/kunena/avatars/' . $user->avatar );
				$avatar_deleted = JText::_('COM_KUNENA_MODERATE_DELETED_BAD_AVATAR_FILESYSTEM');
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
			$params = array('starttime' => '-1','user' => $user->userid,'mode' => 'unapproved');

			list($total, $messages) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, $params);

			$parmas_recent = array('starttime' => '-1','user' => $user->userid);

			list($total, $messages_recent) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, $parmas_recent);

			$messages = array_merge($messages_recent, $messages);

			foreach($messages as $mes) {
				$mes->publish(KunenaForum::DELETED);
			}
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_MODERATE_DELETED_BAD_MESSAGES') );
		}

		$this->app->redirect ( $user->getUrl(false) );
	}

	function cancel() {
		$user = KunenaFactory::getUser();
		$this->app->redirect ( $user->getUrl(false) );
	}

	function login() {
		if(!JFactory::getUser()->guest || !JSession::checkToken('post')) {
			$this->app->redirect ( JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' ), JText::_('COM_KUNENA_ERROR_TOKEN'), 'error' );
		}

		$username = JRequest::getString ( 'username', '', 'POST' );
		$password = JRequest::getString ( 'password', '', 'POST', JREQUEST_ALLOWRAW );
		$remember = JRequest::getBool ( 'remember', false, 'POST');

		$login = KunenaLogin::getInstance();
		$error = $login->loginUser($username, $password, $remember);

		// Get the return url from the request and validate that it is internal.
		$return = base64_decode(JRequest::getVar('return', '', 'method', 'base64')); // Internal URI
		if (!$error && $return && JURI::isInternal($return))
		{
			// Redirect the user.
			$this->app->redirect(JRoute::_($return, false));
		}

		$this->redirectBack ();
	}

	function logout() {
		if(!JSession::checkToken('request')) {
			$this->app->redirect ( JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' ), JText::_('COM_KUNENA_ERROR_TOKEN'), 'error' );
		}

		$login = KunenaLogin::getInstance();
		if (!JFactory::getUser()->guest) $login->logoutUser();

		// Get the return url from the request and validate that it is internal.
		$return = base64_decode(JRequest::getVar('return', '', 'method', 'base64')); // Internal URI
		if ($return && JURI::isInternal($return))
		{
			// Redirect the user.
			$this->app->redirect(JRoute::_($return, false));
		}

		$this->redirectBack ();
	}

	// Internal functions:

	protected function karma($karmaDelta) {
		if (! JSession::checkToken ('get')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}
		$karma_delay = '14400'; // 14400 seconds = 6 hours
		$userid = JRequest::getInt ( 'userid', 0 );

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
		$user = KunenaUserHelper::get($this->user->id);

		// we only allow users to edit few fields
		$allow = array('name', 'email', 'password', 'password2', 'params');
		if ($this->config->usernamechange) {
			if (version_compare(JVERSION, '2.5.5','<') || JComponentHelper::getParams('com_users')->get('change_login_name', 1)) $allow[] = 'username';
		}

		//clean request
		$post = JRequest::get( 'post' );
		$post['password']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW); // RAW input
		$post['password2']	= JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW); // RAW input
		if (empty($post['password']) || empty($post['password2'])) {
			unset($post['password'], $post['password2']);
		}
		$post = array_intersect_key($post, array_flip($allow));

		// get the redirect
		$return = $user->getUrl(false);
		$err_return = $user->getUrl(false, 'edit');

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

		$user = new JUser($this->user->id);
		// Bind the form fields to the user table
		if (!$user->bind($post)) {
			return false;
		}

		// Store user to the database
		if (!$user->save(true)) {
			$this->app->enqueueMessage($user->getError(), 'notice');
			return false;
		}

		// Reload the user.
		$this->user->load($this->user->id);
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
		$this->me->personalText = JRequest::getString ( 'personaltext', '' );
		$this->me->birthdate = JRequest::getInt ( 'birthdate1', '0000' ).'-'.JRequest::getInt ( 'birthdate2', '00' ).'-'.JRequest::getInt ( 'birthdate3', '00' );
		$this->me->location = trim(JRequest::getString ( 'location', '' ));
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
		$this->me->signature = JRequest::getVar('signature', '', 'post', 'string', JREQUEST_ALLOWRAW); // RAW input
	}

	protected function saveAvatar() {
		$action = JRequest::getString('avatar', 'keep');
		$current_avatar = $this->me->avatar;

		require_once (KPATH_SITE.'/lib/kunena.upload.class.php');
		$upload = new CKunenaUpload();
		$upload->setAllowedExtensions('gif, jpeg, jpg, png');

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
			if (!$fileinfo['status']) {
				$this->me->avatar = $current_avatar;
				if (!$fileinfo['not_valid_img_ext'])
				{
					$this->app->enqueueMessage(
						JText::sprintf('COM_KUNENA_UPLOAD_FAILED', htmlspecialchars($fileinfo['name'], ENT_COMPAT, 'UTF-8'))
						. ': ' . JText::sprintf('COM_KUNENA_AVATAR_UPLOAD_NOT_VALID_EXTENSIONS', 'gif, jpeg, jpg, png'),
						'error'
					);
				}
				else
				{
					$this->app->enqueueMessage(
						JText::sprintf('COM_KUNENA_UPLOAD_FAILED', htmlspecialchars($fileinfo['name'], ENT_COMPAT, 'UTF-8'))
						. ': ' . $fileinfo['error'], 'error'
					);
				}
				return false;
			} else {
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_PROFILE_AVATAR_UPLOADED' ) );
			}
		} else if ( $action == 'delete' ) {
			//set default avatar
			$this->me->avatar = '';
		} else if ( substr($action, 0, 8) == 'gallery/' && strpos($action, '..') === false) {
			$this->me->avatar = $action;
		}
		return true;
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

		// TODO: replace this code by using JHttpTransport class
		$data = "username=".$spammer->username."&ip_addr=".$ip."&email=".$spammer->email."&api_key=".$this->config->stopforumspam_key;
		$fp = fsockopen("www.stopforumspam.com",80);
		fputs($fp, "POST /add.php HTTP/1.1\n" );
		fputs($fp, "Host: www.stopforumspam.com\n" );
		fputs($fp, "Content-type: application/x-www-form-urlencoded\n" );
		fputs($fp, "Content-length: ".strlen($data)."\n" );
		fputs($fp, "Connection: close\n\n" );
		fputs($fp, $data);
		// Create a buffer which holds the response
		$response = '';
		// Read the response
		while (!feof($fp))
		{
			$response .= fread($fp, 1024);
		}
		// The file pointer is no longer needed. Close it
		fclose($fp);

		if (strpos($response, 'HTTP/1.1 200 OK') === 0)
		{
			// Report accepted. There is no need to display the reason
			$this->app->enqueueMessage(JText::_('COM_KUNENA_STOPFORUMSPAM_REPORT_SUCCESS'));
			return true;
		}
		else
		{
			// Report failed or refused
			$reasons = array();
			preg_match('/<p>.*<\/p>/', $response, $reasons);
			// stopforumspam returns only one reason, which is reasons[0], but we need to strip out the html tags before using it
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_STOPFORUMSPAM_REPORT_FAILED', strip_tags($reasons[0])),'error');
			return false;
		}
	}

	public function delfile() {
		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		if (!empty($cid)) {
			$number = 0;

			foreach($cid as $id) {
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
