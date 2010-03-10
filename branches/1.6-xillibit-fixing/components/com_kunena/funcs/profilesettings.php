<?php
/**
 * @version $Id:  $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();
$user = JFactory::getUser ();

if ( $this->user->id != '0' && $this->user->id == $user->id ) {

	//check that the userid is the same that the userid of $profile

	$kunena_app = & JFactory::getApplication ();
	$do = JRequest::getVar("do", "");

	if ( $do == 'editprofile' ) {
		CKunenaTools::loadTemplate('/profile/editprofile.php');
	} elseif ( $do == 'editavatar' ) {
		CKunenaTools::loadTemplate('/profile/editavatar.php');
	} elseif ( $do == 'editsettings' ) {
		CKunenaTools::loadTemplate('/profile/editsettings.php');
	} elseif ( $do == 'editjoomlaprofile' ) {
		CKunenaTools::loadTemplate('/profile/editjoomlaprofile.php');
	} elseif ( $do == 'saveprofile' ) {

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
		$kunena_db->setQuery ( "UPDATE #__fb_users SET personalText={$kunena_db->Quote($personnaltext)},birthdate='$birthdate',location='$location',gender='$gender',ICQ='$icq', AIM='$aim', YIM='$yim',MSN='$msn',SKYPE='$skype',GTALK='$gtalk',TWITTER='$twitter',FACEBOOK='$facebook',MYSPACE='$myspace',LINKEDIN='$linkedin',DELICIOUS='$delicious',FRIENDFEED='$friendfeed',DIGG='$digg',BLOGSPOT='$blogspot',FLICKR='$flickr',BEBO='$bebo',websitename='$websitename',websiteurl='$websiteurl',signature='$signature'
							WHERE userid='{$this->user->id}'" );
		$kunena_db->query ();
		check_dberror ( 'Unable to update kunena user profile.' );

		$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, $name='', $rel='nofollow', $redirect=false,$do='') );

	} elseif ( $do == 'savejoomladetails' ) {
		$kunena_app =& JFactory::getApplication();
		$session = &JFactory::getSession();

		$user = new JUser($this->user->id);

		$post['username']	= JRequest::getVar('username', '', 'post', 'username');
		$post['name']	= JRequest::getVar('name', '', 'post', 'name');
		$post['email']	= JRequest::getVar('email', '', 'post', 'email');
		$pass	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$pass2	= JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW);
		if ( !empty($pass) && !empty($pass2) ) {
			if ($pass == $pass2) {
				$post['password'] = $pass;
				$post['password2'] = $pass2;
			}
		}

		if ( !empty($post) ) {
			if (!$user->bind($post))
			{
				$kunena_app->enqueueMessage(JText::_('COM_KUNENA_MYPROFILE_CANNOT_SAVE_USERINFO'), 'message');
				$kunena_app->enqueueMessage($user->getError(), 'error');
			}

			if (!$user->save())
			{
				$kunena_app->enqueueMessage(JText::_('COM_KUNENA_MYPROFILE_CANNOT_SAVE_USERINFO'), 'message');
				$kunena_app->enqueueMessage($user->getError(), 'error');
			}
		}

		$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, $name='', $rel='nofollow', $redirect=false,$do='') );
	} elseif ( $do == 'savesettings' ) {
		(int)$messageordering = JRequest::getInt('messageordering', '', 'post', 'messageordering');
		(int)$hidemail = JRequest::getInt('hidemail', '', 'post', 'hidemail');
		(int)$showonline = JRequest::getInt('showonline', '', 'post', 'showonline');

		//Query on kunena user
		$kunena_db->setQuery ( "UPDATE #__fb_users SET ordering='$messageordering', hideEmail='$hidemail', showOnline='$showonline'
							WHERE userid='{$this->user->id}'" );
		$kunena_db->query ();
		check_dberror ( 'Unable to update kunena user profile.' );

		$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, $name='', $rel='nofollow', $redirect=false,$do='') );
	} elseif ( $do == 'saveavatar' ) {
		require_once (KUNENA_PATH_LIB .DS. 'kunena.upload.class.php');
		$upload = new CKunenaUpload();
		$upload->uploadFile(KUNENA_PATH_AVATAR_UPLOADED .DS. $this->user->id , 'kavatar', 'false');
		$fileinfo = $upload->getFileInfo();
		print_r($fileinfo);

		if ($fileinfo['ready'] === true) {
			if(JDEBUG == 1 && defined('JFIREPHP')){
				FB::log('Kunena save avatar: ' . $fileinfo['name']);
			}
			$this->_db->setQuery ( "UPDATE #__fb_users SET avatar='{$this->_db->quote($this->user->id)}' WHERE userid='{$this->user->id}'" );

				if (! $this->_db->query () || $this->_db->getErrorNum()) {
				$upload->fail(JText::_('COM_KUNENA_UPLOAD_ERROR_AVATAR_DATABASE_STORE'));
				$fileinfo = $upload->fileInfo();
			}
		}

		//$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, $name='', $rel='nofollow', $redirect=false,$do='') );
	}
}
?>