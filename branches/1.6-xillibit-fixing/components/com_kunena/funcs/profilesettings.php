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
$user = JFactory::getUser ();
$kunena_config =& CKunenaConfig::getInstance();
$kunena_db = &JFactory::getDBO();

if ( $this->user->id != '0' && $this->user->id == $user->id ) {

	//check that the userid is the same that the userid of $profile

	$kunena_app = & JFactory::getApplication ();
	$do = JRequest::getVar('do', '');

	if ( $do == 'saveprofile' ) {

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
		$kunena_db->setQuery ( "UPDATE #__fb_users SET personalText={$kunena_db->Quote($personnaltext)},birthdate={$kunena_db->Quote($birthdate)},
			location={$kunena_db->Quote($location)},gender={$kunena_db->Quote($gender)},ICQ={$kunena_db->Quote($icq)}, AIM={$kunena_db->Quote($aim)},
			YIM={$kunena_db->Quote($yim)},MSN={$kunena_db->Quote($msn)},SKYPE={$kunena_db->Quote($skype)},GTALK={$kunena_db->Quote($gtalk)},
			TWITTER={$kunena_db->Quote($twitter)},FACEBOOK={$kunena_db->Quote($facebook)},MYSPACE={$kunena_db->Quote($myspace)},
			LINKEDIN={$kunena_db->Quote($linkedin)},DELICIOUS={$kunena_db->Quote($delicious)},FRIENDFEED={$kunena_db->Quote($friendfeed)},
			DIGG={$kunena_db->Quote($digg)},BLOGSPOT={$kunena_db->Quote($blogspot)},FLICKR={$kunena_db->Quote($flickr)},BEBO={$kunena_db->Quote($bebo)},
			websitename={$kunena_db->Quote($websitename)},websiteurl={$kunena_db->Quote($websiteurl)},signature={$kunena_db->Quote($signature)}
			WHERE userid={$kunena_db->Quote($this->user->id)}" );
		$kunena_db->query ();
		check_dberror ( 'Unable to update kunena user profile.' );

		$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, '', true) );

	} elseif ( $do == 'saveuser' ) {
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

		$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, '', true) );
	} elseif ( $do == 'savesettings' ) {
		$messageordering = JRequest::getInt('messageordering', '', 'post', 'messageordering');
		$hidemail = JRequest::getInt('hidemail', '', 'post', 'hidemail');
		$showonline = JRequest::getInt('showonline', '', 'post', 'showonline');

		//Query on kunena user
		$kunena_db->setQuery ( "UPDATE #__fb_users SET ordering='$messageordering', hideEmail='$hidemail', showOnline='$showonline'
							WHERE userid='{$this->user->id}'" );
		$kunena_db->query ();
		check_dberror ( 'Unable to update kunena user profile.' );

		$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, '', true) );
	} elseif ( $do == 'saveavatar' ) {
		$action = JRequest::getString('action', '');

		if ( $action == 'keep' ) {
			$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, '', true),JText::_('COM_KUNENA_AVATAR_UNCHANGED') );
		} else if ( $action == 'delete' ) {
			//set default avatar
			$kunena_db->setQuery ( "UPDATE #__fb_users SET avatar='nophoto.jpg' WHERE userid='{$this->user->id}'" );
			$kunena_db->query ();
			check_dberror ( 'Unable to set default avatar.' );

			$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, '', true),JText::_('COM_KUNENA_SET_DEFAULT_AVATAR') );
		} else if ( $action == 'upload' ) {
			require_once (KUNENA_PATH_LIB .DS. 'kunena.upload.class.php');
			$upload = new CKunenaUpload();
			$upload->uploadFile(KUNENA_PATH_AVATAR_UPLOADED , 'kavatar', false);
			$fileinfo = $upload->getFileInfo();

			if ($fileinfo['ready'] === true) {
				if(JDEBUG == 1 && defined('JFIREPHP')){
					FB::log('Kunena save avatar: ' . $fileinfo['name']);
				}
				$kunena_db->setQuery ( "UPDATE #__fb_users SET avatar='thumb/{$kunena_db->quote($fileinfo['name'])}' WHERE userid='{$this->user->id}'" );

				if (! $kunena_db->query () || $kunena_db->getErrorNum()) {
					$upload->fail(JText::_('COM_KUNENA_UPLOAD_ERROR_AVATAR_DATABASE_STORE'));
					$fileinfo = $upload->getFileInfo();
				}
			}
			if (!$fileinfo['status']) $kunena_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo['name']).': '.$fileinfo['error'], 'error' );
			else $kunena_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_PROFILE_AVATAR_UPLOADED' ) );

			//$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, '', true), JText::_('COM_KUNENA_AVATAR_UPLOADED_WITH_SUCCESS'));

		} else if ( $action == 'gallery' ) {
			$AvatarGallery = JRequest::getString('newAvatar', '');

			$kunena_db->setQuery ( "UPDATE #__fb_users SET avatar='gallery/$AvatarGallery' WHERE userid='{$this->user->id}'" );
			$kunena_db->query ();
			check_dberror ( 'Unable to set default avatar.' );

			$kunena_app->redirect ( CKunenaLink::GetMyProfileURL($kunena_config, $this->user->id, '', true),JText::_('COM_KUNENA_AVATAR_SET_FROM_GALLERY_WITH_SUCCESS') );
		}
	}
}
?>