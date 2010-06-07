<?php
/**
 * @version $Id: kunena.session.class.php 2071 2010-03-17 11:27:58Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaProfileAlphaUserPoints extends KunenaProfile
{
	public function __construct() {
		$aup = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
		if (!file_exists ( $aup )) return;
		require_once ($aup);
		$this->priority = 60;
	}

	public function getUserListURL($action='')
	{
		return AlphaUserPointsHelper::getAupUsersURL();
	}

	public function getProfileURL($user, $task='')
	{
		if ($user == 0) return false;
		$user = KunenaFactory::getUser($user);
		$my = JFactory::getUser();
		if ($user === false) return false;
		$userid = $my->id != $user->userid ? '&userid='.AlphaUserPointsHelper::getAnyUserReferreID($user->userid) : '';
		return JRoute::_('index.php?option=com_alphauserpoints&view=account'.$userid);
	}

	public function getUserMedals($userid) {
		if ($userid == 0) return false;

		if(!defined("_AUP_MEDALS_LIVE_PATH")) {
			define('_AUP_MEDALS_LIVE_PATH', JURI::base(true) .
			'/components/com_alphauserpoints/assets/images/awards/icons/');
		}

		$aupmedals = '';
		$aupmedals = AlphaUserPointsHelper::getUserMedals ( '', $userid ) ;

		return $aupmedals;
	}

	public function showProfile($userid, &$msg_params) {}
}
