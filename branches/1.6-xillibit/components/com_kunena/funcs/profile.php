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

	function __construct($userid, $do='') {
		kimport('html.parser');
		$this->_db = JFactory::getDBO ();
		$this->_app = JFactory::getApplication ();
		$this->config = CKunenaConfig::getInstance ();
		$this->my = JFactory::getUser ();
		$this->do = $do;

		if (!$userid) {
			$this->user = $this->my;
		}
		else {
			$this->user = JFactory::getUser( $userid );
		}
		$this->profile = KunenaFactory::getUser ( $this->user->id );
		if ($this->profile->userid == 0) return;
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
			$rank = $this->profile->getRank();
			if ($rank->rank_title) $this->rank_title = $rank->rank_title;
			if ($rank->rank_image) $this->rank_image = KUNENA_URLRANKSPATH . $rank->rank_image;
			$this->posts = $this->profile->posts;
		}
		if ($this->config->userlist_joindate || CKunenaTools::isModerator($this->my->id)) $this->registerdate = $this->user->registerDate;
		if ($this->config->userlist_lastvisitdate || CKunenaTools::isModerator($this->my->id)) $this->lastvisitdate = $this->user->lastvisitDate;
		$this->avatarlink = $this->profile->getAvatarLink('','profile');
		$this->personalText = KunenaParser::parseText(stripslashes($this->profile->personalText));
		$this->signature = KunenaParser::parseBBCode(stripslashes($this->profile->signature));
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
			$this->location = '<a href="http://maps.google.com?q='.kunena_htmlspecialchars(stripslashes($this->profile->location)).'" target="_blank">'.kunena_htmlspecialchars(stripslashes($this->profile->location)).'</a>';
		else
			$this->location = JText::_('COM_KUNENA_LOCATION_UNKNOWN');

		$this->online = $this->profile->isOnline();

		$avatar = KunenaFactory::getAvatarIntegration();
		$this->editavatar = is_a($avatar, 'KunenaAvatarKunena') ? true : false;
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

		$selected = JString::ltrim(JString::rtrim(preg_replace('`/`',' ',$this->gallery)));
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
		} else {
			$this->gallery = $this->gallery . '/';
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
		if ($this->config->showkarma && $this->profile->userid) {
			$userkarma = '<strong>'. JText::_('COM_KUNENA_KARMA') . "</strong>: " . $this->profile->karma;

			if ($this->my->id && $this->my->id != $this->profile->userid) {
				$userkarma .= ' '.CKunenaLink::GetKarmaLink ( 'decrease', '', '', $this->profile->userid, '<span class="karmaminus" alt="Karma-" border="0" title="' . JText::_('COM_KUNENA_KARMA_SMITE') . '"> </span>' );
				$userkarma .= ' '.CKunenaLink::GetKarmaLink ( 'increase', '', '', $this->profile->userid, '<span class="karmaplus" alt="Karma+" border="0" title="' . JText::_('COM_KUNENA_KARMA_APPLAUD') . '"> </span>' );
			}
		}

		return $userkarma;
	}

	function display() {
		if (!$this->profile->userid) return;

		switch ($this->do) {
			case 'save':
				$this->save();
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
		$personnaltext = JRequest::getVar ( 'personnaltext', '' );
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
		$signature = JRequest::getVar ( 'signature', '' );

		//Query on kunena user
		$this->_db->setQuery ( "UPDATE #__fb_users SET personalText={$this->_db->Quote($personnaltext)},birthdate={$this->_db->Quote($birthdate)},
			location={$this->_db->Quote($location)},gender={$this->_db->Quote($gender)},ICQ={$this->_db->Quote($icq)}, AIM={$this->_db->Quote($aim)},
			YIM={$this->_db->Quote($yim)},MSN={$this->_db->Quote($msn)},SKYPE={$this->_db->Quote($skype)},GTALK={$this->_db->Quote($gtalk)},
			TWITTER={$this->_db->Quote($twitter)},FACEBOOK={$this->_db->Quote($facebook)},MYSPACE={$this->_db->Quote($myspace)},
			LINKEDIN={$this->_db->Quote($linkedin)},DELICIOUS={$this->_db->Quote($delicious)},FRIENDFEED={$this->_db->Quote($friendfeed)},
			DIGG={$this->_db->Quote($digg)},BLOGSPOT={$this->_db->Quote($blogspot)},FLICKR={$this->_db->Quote($flickr)},BEBO={$this->_db->Quote($bebo)},
			websitename={$this->_db->Quote($websitename)},websiteurl={$this->_db->Quote($websiteurl)},signature={$this->_db->Quote($signature)}
			WHERE userid={$this->_db->Quote($this->profile->userid)}" );
		$this->_db->query ();
		check_dberror ( 'Unable to update kunena user profile.' );
	}

	protected function saveAvatar() {
		$action = JRequest::getString('avatar', 'keep');

		require_once (KUNENA_PATH_LIB .DS. 'kunena.upload.class.php');
		$upload = new CKunenaUpload();
		$upload->setAllowedExtensions('gif, jpeg, jpg, png');

		if ( $upload->uploaded('avatarfile') ) {
			$uploadpath = 'users';
			$path = KUNENA_PATH_AVATAR_UPLOADED .DS. $uploadpath;

			// Delete old uploaded avatars:
			$deletelist = JFolder::files($path, 'user'.$this->profile->userid, false, true);
			foreach ($deletelist as $delete) {
				JFile::delete($delete);
			}
			$upload->setImageResize(intval($this->config->avatarsize)*1024, 200, 200, $this->config->avatarquality);

			$upload->uploadFile($path , 'avatarfile', 'user'.$this->profile->userid, false);
			$fileinfo = $upload->getFileInfo();

			if ($fileinfo['ready'] === true) {
				if(JDEBUG == 1 && defined('JFIREPHP')){
					FB::log('Kunena save avatar: ' . $fileinfo['name']);
				}
				$this->_db->setQuery ( "UPDATE #__fb_users SET avatar={$this->_db->quote($fileinfo['name'])} WHERE userid='{$this->profile->userid}'" );

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
			$this->_db->setQuery ( "UPDATE #__fb_users SET avatar='' WHERE userid='{$this->profile->userid}'" );
			$this->_db->query ();
			check_dberror ( 'Unable to set default avatar.' );
		} else if ( substr($action, 0, 8) == 'gallery/' && strpos($action, '..') === false) {
			$this->_db->setQuery ( "UPDATE #__fb_users SET avatar={$this->_db->quote($action)} WHERE userid='{$this->profile->userid}'" );
			$this->_db->query ();
			check_dberror ( 'Unable to set avatar from gallery.' );
		}
	}

	protected function saveSettings() {
		$messageordering = JRequest::getInt('messageordering', '', 'post', 'messageordering');
		$hidemail = JRequest::getInt('hidemail', '', 'post', 'hidemail');
		$showonline = JRequest::getInt('showonline', '', 'post', 'showonline');

		//Query on kunena user
		$this->_db->setQuery ( "UPDATE #__fb_users SET ordering='$messageordering', hideEmail='$hidemail', showOnline='$showonline'
							WHERE userid='{$this->profile->userid}'" );
		$this->_db->query ();
		check_dberror ( 'Unable to update kunena user profile.' );
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

	function cancel()
	{
		$this->_app->redirect ( CKunenaLink::GetMyProfileURL($this->profile->userid, '', false) );
	}
}