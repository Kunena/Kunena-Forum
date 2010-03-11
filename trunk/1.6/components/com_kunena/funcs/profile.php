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
		require_once (KUNENA_PATH_LIB . DS . "kunena.user.class.php");

		$this->_db = JFactory::getDBO ();
		$this->_app = JFactory::getApplication ();
		$this->_config = CKunenaConfig::getInstance ();
		$this->my = JFactory::getUser ();
		$this->do = $do;

		if (!$userid) {
			$this->user = JFactory::getUser();
		}
		else {
			$this->user = JUser::getInstance ( $userid );
		}
		if (!$this->user->id) return;
		$this->profile = CKunenaUserprofile::getInstance ( $this->user->id );
		if ($this->profile->posts === null) {
			$this->_db->setQuery ( "INSERT INTO #__fb_users (userid) VALUES ('{$this->user->id}')" );
			$this->_db->query ();
			check_dberror ( 'Unable to create user profile.' );
			$this->profile = CKunenaUserprofile::getInstance($this->user->id, true);
		}
		$this->avatarurl = KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $this->profile->avatar;
		$this->personalText = CKunenaTools::parseText($this->profile->personalText);
		$this->signature = CKunenaTools::parseBBCode($this->profile->signature);
		$this->timezone = $this->user->getParam('timezone', 0);
		$this->moderator = CKunenaTools::isModerator($this->user->id);
		$this->admin = CKunenaTools::isAdmin($this->user->id);
		$rank = CKunenaTools::getRank($this->profile);
		$this->rank_title = $rank->rank_title;
		$this->rank_image = KUNENA_URLRANKSPATH . $rank->rank_image;
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

		$query = 'SELECT MAX(s.time) FROM #__session AS s WHERE s.userid = ' . $this->user->id . ' AND s.client_id = 0 GROUP BY s.userid';
		$this->_db->setQuery ( $query );
		$lastseen = $this->_db->loadResult ();
		check_dberror ( "Unable get user online information." );
		$timeout = $this->_app->getCfg ( 'lifetime', 15 ) * 60;
		$this->online = ($lastseen + $timeout) > time ();
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

	function displayEditUser() {
		$this->user = JFactory::getUser();

		// check to see if Frontend User Params have been enabled
		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$check = $usersConfig->get('frontend_userparams');

		if ($check == 1 || $check == NULL)
		{
			if($this->user->authorize( 'com_user', 'edit' )) {
				$this->params		= $this->user->getParameters(true);
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
		if (!$this->_config->allowavatar) return;
		$this->gallery='';
		$path = KUNENA_PATH_UPLOADED .DS. 'avatars/gallery/' . $this->gallery;
		//$this->galleryimg = $this->getAvatarGallery($path);
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
				if ($user->id == $this->user->id) CKunenaTools::loadTemplate('/profile/edittab.php');
				break;
			default:
				CKunenaTools::loadTemplate('/profile/usertab.php');
		}
	}

	function displaySummary() {
		$user = JFactory::getUser();
		if ($user->id != $this->user->id)
		{
			$this->profile->uhits++;
			$this->profile->store();
		}

		CKunenaTools::loadTemplate('/profile/summary.php');
	}

	function displayEdit() {
		$user = JFactory::getUser();
		if ($user->id != $this->user->id) return;

		CKunenaTools::loadTemplate('/profile/edit.php');
	}

	function display() {
		if (!$this->user->id) return;
		else $this->displaySummary();
	}
}