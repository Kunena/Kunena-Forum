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

class KunenaAvatarAlphaUserPoints extends KunenaAvatar
{
	public function __construct() {
		$aup = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
		if (!file_exists ( $aup )) return;
		require_once ($aup);
		$this->priority = 60;
	}

	public function getEditURL()
	{
		return JRoute::_('index.php?option=com_alphauserpoints&view=account');
	}

	public function getURL($user, $size='thumb')
	{
		trigger_error(__CLASS__.'::'.__FUNCTION__.'() not implemented');
	}

	public function getLink($user, $class='', $size='thumb')
	{
		$user = KunenaFactory::getUser($user);
		$avatar = AlphaUserPointsHelper::getAupAvatar ( $user->userid, 0 );
		return $avatar;
	}
}
