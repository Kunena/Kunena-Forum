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

	function __construct($userid) {
		require_once (KUNENA_PATH_LIB . DS . "kunena.user.class.php");

		$this->_db = JFactory::getDBO ();
		$this->_app = JFactory::getApplication ();
		$this->_config = CKunenaConfig::getInstance ();
		$this->my = JFactory::getUser ();

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
		$this->profile->store();
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
				$this->gender = JText::_('COM_KUNENA_MYPROFILE_MALE');
				break;
			case 2:
				$this->genderclass = 'female';
				$this->gender = JText::_('COM_KUNENA_MYPROFILE_FEMALE');
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

	function displaySummary() {
		$user = JFactory::getUser();
		if ($user->id != $this->user->id)
		{
			$this->profile->uhits++;
			$this->profile->store();
		}

		CKunenaTools::loadTemplate('/profile/summary.php');
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

	function display() {
		if (!$this->user->id) return;
		$this->displaySummary();
	}
}