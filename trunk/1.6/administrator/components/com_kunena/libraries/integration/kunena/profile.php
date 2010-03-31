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
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaProfileKunena extends KunenaProfile
{
	public function __construct() {
		$this->priority = 25;
	}

	public function getUserListURL()
	{
		return KunenaRoute::_('index.php?option=com_kunena&func=userlist');
	}

	public function getProfileURL($user, $task='')
	{
		if ($user == 0) return false;
		$user = KunenaFactory::getUser($user);
		$my = JFactory::getUser();
		if ($user === false) return false;
		$userid = $my->id != $user->userid ? "&userid={$user->userid}" : '';
		$do = $task ? '&do='.$task : '';
		return KunenaRoute::_("index.php?option=com_kunena&func=profile{$do}{$userid}");
	}

	public function showProfile($userid, &$msg_params) {}
}
