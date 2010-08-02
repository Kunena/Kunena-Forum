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
 **/
defined( '_JEXEC' ) or die();


class CKunenaProfile {
	public $user = null;
	public $profile = null;
	public $online = null;
	public $allow = false;

	function __construct($userid, $do='') {
		kimport('html.parser');
		require_once(KPATH_SITE.'/lib/kunena.timeformat.class.php');
		$this->_db = JFactory::getDBO ();
		$this->_app = JFactory::getApplication ();
		$this->config = KunenaFactory::getConfig ();
		$this->my = JFactory::getUser ();
		$this->do = $do;

		if (!$userid) {
			$this->user = $this->my;
		}
		else {
			$this->user = JFactory::getUser( $userid );
		}
		if ($this->user->id == 0) return;
		$this->allow = true;

		$integration = KunenaFactory::getProfile();
		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

		$this->profile = KunenaFactory::getUser ( $this->user->id );
		if ($this->profile->posts === null) {
			$this->profile->save();
		}
		if ($this->profile->userid == $this->my->id) {
			if ($this->do != 'edit') $this->editlink = CKunenaLink::GetMyProfileLink ( $this->profile->userid, JText::_('COM_KUNENA_EDIT'), 'nofollow', 'edit' );
			else $this->editlink = CKunenaLink::GetMyProfileLink ( $this->profile->userid, JText::_('COM_KUNENA_BACK'), 'nofollow' );
		}
		$this->name = $this->user->username;
		if ($this->config->userlist_name) $this->name = $this->user->name . ' (' . $this->name . ')';
		if ($this->config->showuserstats) {
			if ($this->config->userlist_usertype) $this->usertype = $this->user->usertype;
			$this->rank_image = $this->profile->getRank (0, 'image');
			$this->rank_title = $this->profile->getRank (0, 'title');
			$this->posts = $this->profile->posts;
			$this->userpoints = $integration->getUserPoints($this->profile->userid);
			$this->usermedals = $integration->getUserMedals($this->profile->userid);
		}
		if ($this->config->userlist_joindate || CKunenaTools::isModerator($this->my->id)) $this->registerdate = $this->user->registerDate;
		if ($this->config->userlist_lastvisitdate || CKunenaTools::isModerator($this->my->id)) $this->lastvisitdate = $this->user->lastvisitDate;
		$this->avatarlink = $this->profile->getAvatarLink('kavatar','profile');
		$this->personalText = $this->profile->personalText;
		$this->signature = $this->profile->signature;
		$this->timezone = $this->user->getParam('timezone', 0);
		$this->moderator = CKunenaTools::isModerator($this->profile->userid);
		$this->admin = CKunenaTools::isAdmin($this->profile->userid);
		switch ($this->profile->gender) {
			case 1:
				$this->genderclass = 'male';
				$this->gender = JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE');
				break;
			case 2:
				$this->genderclass = 'female';
				$this->gender = JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE');
				break;
			default:
				$this->genderclass = 'unknown';
				$this->gender = JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN');
		}
		if ($this->profile->location)
			$this->locationlink = '<a href="http://maps.google.com?q='.kunena_htmlspecialchars($this->profile->location).'" target="_blank">'.kunena_htmlspecialchars($this->profile->location).'</a>';
		else
			$this->locationlink = JText::_('COM_KUNENA_LOCATION_UNKNOWN');

		$this->online = $this->profile->isOnline();
		$this->showUnusedSocial = true;

		$avatar = KunenaFactory::getAvatarIntegration();
		$this->editavatar = is_a($avatar, 'KunenaAvatarKunena') ? true : false;

		kimport('userban');
		$this->banInfo = KunenaUserBan::getInstanceByUserid($userid, true);
		$this->canBan = $this->banInfo->canBan();
		if ( $this->config->showbannedreason ) $this->banReason = $this->banInfo->reason_public;
	}

	/**
	* Escapes a value for output in a view script.
	*
	* If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	* {@link $_encoding} setting.
	*
	* @param  mixed $var The output to escape.
	* @return mixed The escaped value.
	*/
	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	function getAvatarGallery($path) {
		jimport('joomla.filesystem.folder');
		$files = JFolder::files($path,'(\.gif|\.png|\.jpg|\.jpeg)$');
		return $files;
	}

	// This function was modified from the one posted to PHP.net by rockinmusicgv
	// It is available under the readdir() entry in the PHP online manual
	function getAvatarGalleries($path, $select_name) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.utilities.string');
		$folders = JFolder::folders($path,'.',true, true);
		foreach ($folders as $key => $folder) {
			$folder = substr($folder, strlen($path)+1);
			$folders[$key] = $folder;
		}

		$selected = JString::trim($this->gallery);
		$str =  "<select name=\" {$this->escape($select_name)}\" id=\"avatar_category_select\" onchange=\"switch_avatar_category(this.options[this.selectedIndex].value)\">\n";
		$str .=  "<option value=\"default\"";

		if ($selected == "") {
			$str .=  " selected=\"selected\"";
		}

		$str .=  ">" . JText::_ ( 'COM_KUNENA_DEFAULT_GALLERY' ) . "</option>\n";

		asort ( $folders );

		foreach ( $folders as $key => $val ) {
			$str .=  '<option value="' . urlencode($val) . '"';

			if ($selected == $val) {
				$str .=  " selected=\"selected\"";
			}

			$str .=  ">{$this->escape(JString::ucwords(JString::str_ireplace('/', ' / ', $val)))}</option>\n";
		}

		$str .=  "</select>\n";
		return $str;
	}

	function displayEditUser() {
		jimport ( 'joomla.version' );
		$jversion = new JVersion ();
		// FIXME: not implemented in J1.6
		if ($jversion->RELEASE == 1.6) {
			echo 'Not implemented in J1.6!';
			return;
		}

		$this->user = JFactory::getUser();

		// check to see if Frontend User Params have been enabled
		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$check = $usersConfig->get('frontend_userparams');

		if ($check == 1 || $check == NULL)
		{
			if($this->user->authorize( 'com_user', 'edit' )) {
				$params = $this->user->getParameters(true);
				$this->userparams = $params->renderToArray();
			}
		}
		CKunenaTools::loadTemplate('/profile/edituser.php');
	}

	function displayEditProfile() {
		$bd = @explode("-" , $this->profile->birthdate);

		$this->birthdate["year"] = $bd[0];
		$this->birthdate["month"] = $bd[1];
		$this->birthdate["day"] = $bd[2];

		$this->genders[] = JHTML::_('select.option', '0', JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
		$this->genders[] = JHTML::_('select.option', '1', JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
		$this->genders[] = JHTML::_('select.option', '2', JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));

		CKunenaTools::loadTemplate('/profile/editprofile.php');
	}

	function displayEditAvatar() {
		if (!$this->editavatar) return;
		$this->gallery = JRequest::getVar('gallery', 'default');
		if ($this->gallery == 'default') {
			$this->gallery = '';
		}
		$path = KUNENA_PATH_AVATAR_UPLOADED .'/gallery';
		if (is_dir($path)) {
			$this->galleryurl = KUNENA_LIVEUPLOADEDPATH . '/avatars/gallery';
		} else {
			$path = KUNENA_PATH_UPLOADED_LEGACY . '/avatars/gallery';
			$this->galleryurl = KUNENA_LIVEUPLOADEDPATH_LEGACY . '/avatars/gallery';
		}
		$this->galleries = $this->getAvatarGalleries($path, 'gallery');
		$this->galleryimg = $this->getAvatarGallery($path . '/' . $this->gallery);
		CKunenaTools::loadTemplate('/profile/editavatar.php');
	}

	function displayEditSettings() {
		CKunenaTools::loadTemplate('/profile/editsettings.php');
	}

	function displayUserPosts()
	{
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('userposts', 0);
		$obj->user = $this->user;
		$obj->getUserPosts();
		$obj->displayPosts();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displayReviewPosts()
	{
		require_once (KUNENA_PATH_LIB . DS . 'kunena.review.php');
		$review = new CKunenaReview('');
		$review->display();
	}

	function displayGotThankYou()
	{
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('userposts',0);
		$obj->user = $this->user;
		$obj->getGotThankYouPosts();
		$obj->displayPosts();
	}

	function displaySaidThankYou()
	{
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('userposts',0);
		$obj->user = $this->user;
		$obj->getSaidThankYouPosts();
		$obj->displayPosts();
	}
	function displayOwnTopics()
	{
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('owntopics', 0);
		$obj->user = $this->user;
		$obj->getOwnTopics();
		$obj->displayFlat();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displayUserTopics()
	{
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('usertopics', 0);
		$obj->user = $this->user;
		$obj->getUserTopics();
		$obj->displayFlat();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displayFavorites()
	{
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('favorites', 0);
		$obj->user = $this->user;
		$obj->getFavorites();
		$obj->displayFlat();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displaySubscriptions()
	{
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('subscriptions', 0);
		$obj->user = $this->user;
		$obj->getSubscriptions();
		$obj->displayFlat();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displayCategoriesSubscriptions()
	{
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('catsSubscriptions', 0);
		$obj->user = $this->user;
		$obj->getCategoriesSubscriptions();
		$obj->displayFlatCats();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displayBanUser()
	{
		$this->baninfo = KunenaUserBan::getInstanceByUserid($this->profile->userid, true);
		CKunenaTools::loadTemplate('/profile/banuser.php');
	}

	function displayBanHistory()
	{
		$this->banhistory = $this->getBanHistory();
		CKunenaTools::loadTemplate('/profile/banhistory.php');
	}

	function displayBanManager()
	{
		$this->bannedusers = $this->getBannedUsers();
		CKunenaTools::loadTemplate('/profile/banmanager.php');
	}

	function getBannedUsers() {
		kimport('userban');
		$banned = KunenaUserBan::getBannedUsers();
		return $banned;
	}

	function getBanHistory() {
		kimport('userban');
		$user_history = KunenaUserBan::getUserHistory($this->profile->userid);
		return $user_history;
	}

	function displayTab() {
		switch ($this->do) {
			case 'edit':
				$user = JFactory::getUser();
				if ($user->id == $this->profile->userid) CKunenaTools::loadTemplate('/profile/edittab.php');
				break;
			default:
				CKunenaTools::loadTemplate('/profile/usertab.php');
		}
	}

	function displaySummary() {
		$user = JFactory::getUser();
		if ($user->id != $this->profile->userid)
		{
			$this->profile->uhits++;
			$this->profile->save();
		}

		CKunenaTools::loadTemplate('/profile/summary.php');
	}

	function displayEdit() {
		$user = JFactory::getUser();
		if ($user->id != $this->profile->userid) return;

		CKunenaTools::loadTemplate('/profile/edit.php');
	}

	function displayKarma() {
		$userkarma = '';
		if ($this->config->showkarma && $this->profile->userid) {
			$userkarma = '<strong>'. JText::_('COM_KUNENA_KARMA') . "</strong>: " . $this->profile->karma;

			if ($this->my->id && $this->my->id != $this->profile->userid) {
				$userkarma .= ' '.CKunenaLink::GetKarmaLink ( 'decrease', '', '', $this->profile->userid, '<span class="kkarma-minus" title="' . JText::_('COM_KUNENA_KARMA_SMITE') . '"> </span>' );
				$userkarma .= ' '.CKunenaLink::GetKarmaLink ( 'increase', '', '', $this->profile->userid, '<span class="kkarma-plus" title="' . JText::_('COM_KUNENA_KARMA_APPLAUD') . '"> </span>' );
			}
		}

		return $userkarma;
	}

	function display() {
		if (!$this->allow) return;

		switch ($this->do) {
			case 'save':
				$this->save();
				break;
			case 'ban':
				$this->ban();
				break;
			case 'cancel':
				$this->cancel();
				break;
			default:
				$this->displaySummary();
		}
	}

	// Mostly copied from Joomla 1.5
	protected function saveUser()
	{
		$user = $this->user; //new JUser ( $this->user->get('id') );

		// we don't want users to edit certain fields so we will ignore them
		$ignore = array('id', 'gid', 'block', 'usertype', 'registerDate', 'activation');

		//clean request
		$post = JRequest::get( 'post' );
		$post['password']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['password2']	= JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW);
		if ($this->config->usernamechange) $post['username']	= JRequest::getVar('username', '', 'post', 'username');
		else $ignore[] = 'username';

		// get the redirect
		$return = CKunenaLink::GetMyProfileURL($this->user->get('id'), '', false);
		$err_return = CKunenaLink::GetMyProfileURL($this->user->get('id'), 'edit', false);

		// do a password safety check
		if(strlen($post['password']) || strlen($post['password2'])) { // so that "0" can be used as password e.g.
			if($post['password'] != $post['password2']) {
				$msg	= JText::_('COM_KUNENA_PROFILE_PASSWORD_MISMATCH');
				$this->_app->redirect ( $err_return, $msg, 'error' );
			}
		}

		$username = $this->user->get('username');

		// Bind the form fields to the user table
		if (!$user->bind($post, $ignore)) {
			$this->_app->redirect ( $err_return, $this->_db->getErrorMsg(), 'error' );
		}

		// Store the web link table to the database
		if (!$user->save(true)) {
			$this->_app->redirect ( $err_return, $this->user->getError(), 'error' );
		}

		$session = JFactory::getSession();
		$session->set('user', $user);

		// update session if username has been changed
		if ( $username && $username != $user->get('username') )
		{
			$table = JTable::getInstance('session', 'JTable' );
			$table->load($session->getId());
			$table->username = $user->get('username');
			$table->store();
		}
	}

	protected function saveProfile() {
		$personnaltext = JRequest::getVar ( 'personaltext', '' );
		$birthdate1 = JRequest::getInt ( 'birthdate1', '' );
		$birthdate2 = JRequest::getInt ( 'birthdate2', '' );
		$birthdate3 = JRequest::getInt ( 'birthdate3', '' );
		$birthdate = $birthdate1.'-'.$birthdate2.'-'.$birthdate3;
		$location = trim(JRequest::getVar ( 'location', '' ));
		$gender = JRequest::getInt ( 'gender', '' );
		$icq = trim(JRequest::getVar ( 'icq', '' ));
		$aim = trim(JRequest::getVar ( 'aim', '' ));
		$yim = trim(JRequest::getVar ( 'yim', '' ));
		$msn = trim(JRequest::getVar ( 'msn', '' ));
		$skype = trim(JRequest::getVar ( 'skype', '' ));
		$gtalk = trim(JRequest::getVar ( 'gtalk', '' ));
		$twitter = trim(JRequest::getVar ( 'twitter', '' ));
		$facebook = trim(JRequest::getVar ( 'facebook', '' ));
		$myspace = trim(JRequest::getVar ( 'myspace', '' ));
		$linkedin = trim(JRequest::getVar ( 'linkedin', '' ));
		$delicious = trim(JRequest::getVar ( 'delicious', '' ));
		$friendfeed = trim(JRequest::getVar ( 'friendfeed', '' ));
		$digg = trim(JRequest::getVar ( 'digg', '' ));
		$blogspot = trim(JRequest::getVar ( 'blogspot', '' ));
		$flickr = trim(JRequest::getVar ( 'flickr', '' ));
		$bebo = trim(JRequest::getVar ( 'bebo', '' ));
		$websitename = JRequest::getVar ( 'websitename', '' );
		$websiteurl = JRequest::getVar ( 'websiteurl', '' );
		$signature = JRequest::getVar ( 'signature', '', 'post', 'string', JREQUEST_ALLOWRAW );

		//Query on kunena user
		$this->_db->setQuery ( "UPDATE #__kunena_users SET personalText={$this->_db->Quote($personnaltext)},birthdate={$this->_db->Quote($birthdate)},
			location={$this->_db->Quote($location)},gender={$this->_db->Quote($gender)},ICQ={$this->_db->Quote($icq)}, AIM={$this->_db->Quote($aim)},
			YIM={$this->_db->Quote($yim)},MSN={$this->_db->Quote($msn)},SKYPE={$this->_db->Quote($skype)},GTALK={$this->_db->Quote($gtalk)},
			TWITTER={$this->_db->Quote($twitter)},FACEBOOK={$this->_db->Quote($facebook)},MYSPACE={$this->_db->Quote($myspace)},
			LINKEDIN={$this->_db->Quote($linkedin)},DELICIOUS={$this->_db->Quote($delicious)},FRIENDFEED={$this->_db->Quote($friendfeed)},
			DIGG={$this->_db->Quote($digg)},BLOGSPOT={$this->_db->Quote($blogspot)},FLICKR={$this->_db->Quote($flickr)},BEBO={$this->_db->Quote($bebo)},
			websitename={$this->_db->Quote($websitename)},websiteurl={$this->_db->Quote($websiteurl)},signature={$this->_db->Quote($signature)}
			WHERE userid={$this->_db->Quote($this->profile->userid)}" );
		$this->_db->query ();
		KunenaError::checkDatabaseError();
	}

	protected function saveAvatar() {
		$action = JRequest::getString('avatar', 'keep');

		require_once (KUNENA_PATH_LIB .DS. 'kunena.upload.class.php');
		$upload = new CKunenaUpload();
		$upload->setAllowedExtensions('gif, jpeg, jpg, png');

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
				$this->_db->setQuery ( "UPDATE #__kunena_users SET avatar={$this->_db->quote('users/'.$fileinfo['name'])} WHERE userid='{$this->profile->userid}'" );

				if (! $this->_db->query () || $this->_db->getErrorNum()) {
					$upload->fail(JText::_('COM_KUNENA_UPLOAD_ERROR_AVATAR_DATABASE_STORE'));
					$fileinfo = $upload->getFileInfo();
				}
			}
			if (!$fileinfo['status']) $this->_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo['name']).': '.$fileinfo['error'], 'error' );
			else $this->_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_PROFILE_AVATAR_UPLOADED' ) );

			//$this->_app->redirect ( CKunenaLink::GetMyProfileURL($this->profile->userid, '', false), JText::_('COM_KUNENA_AVATAR_UPLOADED_WITH_SUCCESS'));

		} else if ( $action == 'delete' ) {
			//set default avatar
			$this->_db->setQuery ( "UPDATE #__kunena_users SET avatar='' WHERE userid={$this->_db->Quote($this->profile->userid)}" );
			$this->_db->query ();
			if (KunenaError::checkDatabaseError()) return;
		} else if ( substr($action, 0, 8) == 'gallery/' && strpos($action, '..') === false) {
			$this->_db->setQuery ( "UPDATE #__kunena_users SET avatar={$this->_db->quote($action)} WHERE userid={$this->_db->Quote($this->profile->userid)}" );
			$this->_db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}
	}

	protected function saveSettings() {
		$messageordering = JRequest::getInt('messageordering', '', 'post', 'messageordering');
		$hidemail = JRequest::getInt('hidemail', '', 'post', 'hidemail');
		$showonline = JRequest::getInt('showonline', '', 'post', 'showonline');

		//Query on kunena user
		$this->_db->setQuery ( "UPDATE #__kunena_users SET ordering={$this->_db->Quote($messageordering)}, hideEmail={$this->_db->Quote($hidemail)}, showOnline={$this->_db->Quote($showonline)}
							WHERE userid={$this->_db->Quote($this->profile->userid)}" );
		$this->_db->query ();
		KunenaError::checkDatabaseError();
	}

	function save()
	{
		// get the redirect
		$return = CKunenaLink::GetMyProfileURL($this->user->get('id'), '', false);
		$err_return = CKunenaLink::GetMyProfileURL($this->user->get('id'), 'edit', false);

		// Check for request forgeries
		JRequest::checkToken() or $this->_app->redirect ( $err_return, COM_KUNENA_ERROR_TOKEN, 'error' );

		// perform security checks
		if ($this->user->get('id') <= 0 || $this->user->get('id') != $this->my->get('id')) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}

		$this->saveUser();
		$this->saveProfile();
		$this->saveAvatar();
		$this->saveSettings();

		$msg = JText::_( 'COM_KUNENA_PROFILE_SAVED' );
		$this->_app->redirect ( $return, $msg );
	}

	function ban() {
		$userid = JRequest::getInt ( 'userid', 0 );
		$ip = JRequest::getVar ( 'ip', '' );
		$block = JRequest::getInt ( 'block', 0 );
		$expiration = JRequest::getString ( 'expiration', '' );
		$reason_private = JRequest::getString ( 'reason_private', '' );
		$reason_public = JRequest::getString ( 'reason_public', '' );
		$comment = JRequest::getString ( 'comment', '' );

		if(!JRequest::checkToken()) {
			$this->_app->redirect ( CKunenaLink::GetProfileURL($this->profile->userid, false), COM_KUNENA_ERROR_TOKEN, 'error' );
			return false;
		}

		kimport ( 'userban' );
		$ban = KunenaUserBan::getInstanceByUserid ( $userid, true );
		if (! $ban->id) {
			$ban->ban ( $userid, $ip, $block, $expiration, $reason_private, $reason_public, $comment );
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
			$this->_app->enqueueMessage ( $ban->getError (), 'error' );
		} else {
			$this->_app->enqueueMessage ( $message );
		}

		$banDelPosts = JRequest::getVar ( 'bandelposts', '' );
		$DelAvatar = JRequest::getVar ( 'delavatar', '' );
		$DelSignature = JRequest::getVar ( 'delsignature', '' );
		$DelProfileInfo = JRequest::getVar ( 'delprofileinfo', '' );

		if (! empty ( $DelAvatar )) {
			jimport ( 'joomla.filesystem.file' );
			$userprofile = KunenaFactory::getUser ( $userid );

			$this->_db->setQuery ( "UPDATE #__kunena_users SET avatar=null WHERE userid={$this->_db->Quote($userid)}" );
			$this->_db->Query ();
			KunenaError::checkDatabaseError();

			// Delete avatar from file system
			if (JFile::exists ( KUNENA_PATH_AVATAR_UPLOADED . DS . $userprofile->avatar )) {
				JFile::delete ( KUNENA_PATH_AVATAR_UPLOADED . DS . $userprofile->avatar );
			}
		}

		if (! empty ( $DelSignature )) {
			$this->_db->setQuery ( "UPDATE #__kunena_users SET signature=null WHERE userid={$this->_db->Quote($userid)}" );
			$this->_db->Query ();
			KunenaError::checkDatabaseError();
		}

		if (! empty ( $DelProfileInfo )) {
			$this->_db->setQuery ( "UPDATE #__kunena_users SET signature=null,avatar=null,karma=null,personalText=null,gender=0,birthdate=0000-00-00,location=null,ICQ=null,AIM=null,YIM=null,MSN=null,SKYPE=null,GTALK=null,websitename=null,websiteurl=null,rank=0,TWITTER=null,FACEBOOK=null,MYSPACE=null,LINKEDIN=null,DELICIOUS=null,FRIENDFEED=null,DIGG=null,BLOGSPOT=null,FLICKR=null,BEBO=null WHERE userid={$this->_db->Quote($userid)}" );
			$this->_db->Query ();
			KunenaError::checkDatabaseError();
		}

		if (! empty ( $banDelPosts )) {
			//select only the messages which aren't already in the trash
			$this->_db->setQuery ( "UPDATE #__kunena_messages SET hold=2 WHERE hold!=2 AND userid={$this->_db->Quote($userid)}" );
			$idusermessages = $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();
		}

		$this->_app->redirect ( CKunenaLink::GetProfileURL($this->profile->userid, false) );
	}

	function cancel()
	{
		$this->_app->redirect ( CKunenaLink::GetMyProfileURL($this->profile->userid, '', false) );
	}
}