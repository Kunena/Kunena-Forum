<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaAvatarAlphaUserPoints extends KunenaAvatar
{
	public function __construct() {
		$aup = JPATH_SITE . '/components/com_alphauserpoints/helper.php';
		if (!file_exists ( $aup )) return;
		require_once ($aup);
		$this->priority = 60;
	}

	public function getEditURL()
	{
		return JRoute::_('index.php?option=com_alphauserpoints&view=account');
	}

	public function _getURL($user, $sizex, $sizey)
	{
		trigger_error(__CLASS__.'::'.__FUNCTION__.'() not implemented');
	}

	public function getLink($user, $class='', $sizex=90, $sizey=90)
	{
		$user = KunenaFactory::getUser($user);
		$size = $this->getSize($sizex, $sizey);
		if ($size->y > 100) {
			$avatar = AlphaUserPointsHelper::getAupAvatar ( $user->userid, 0, 100*(float)$size->x/(float)$size->y, '100'  );
		} else {
			$avatar = AlphaUserPointsHelper::getAupAvatar ( $user->userid, 0, $size->x, $size->y  );
		}
		if (!$avatar) {
			// FIXME: need a better way to do this
			$avatar = '<img border="0" width="100" height="100" alt="" src="http://kunena16cb/components/com_alphauserpoints/assets/images/avatars/generic_gravatar_grey.gif">';
		}
		return $avatar;
	}
}
