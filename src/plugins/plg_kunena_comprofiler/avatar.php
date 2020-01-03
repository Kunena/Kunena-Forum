<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaAvatarComprofiler
 *
 * @since   Kunena 6.0
 */
class KunenaAvatarComprofiler extends KunenaAvatar
{
	/**
	 * @var null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaAvatarComprofiler constructor.
	 *
	 * @param $params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param $userlist
	 *
	 * @since   Kunena 6.0
	 */
	public function load($userlist)
	{
		CBuser::advanceNoticeOfUsersNeeded($userlist);
	}

	/**
	 * @return string
	 * @since   Kunena 6.0
	 */
	public function getEditURL()
	{
		global $_CB_framework;

		return $_CB_framework->userProfileEditUrl();
	}

	/**
	 * @param $user
	 * @param $sizex
	 * @param $sizey
	 *
	 * @return string
	 * @since Kunena
	 * @throws Exception
	 */
	protected function _getURL($user, $sizex, $sizey)
	{
		$user = KunenaFactory::getUser($user);

		// Get CUser object
		$cbUser = null;

		if ($user->userid)
		{
			$cbUser = CBuser::getInstance($user->userid);
		}

		if ($cbUser === null)
		{
			if ($sizex <= 144)
			{
				return selectTemplate() . 'images/avatar/tnnophoto_n.png';
			}

			return selectTemplate() . 'images/avatar/nophoto_n.png';
		}

		if ($sizex <= 144)
		{
			return $cbUser->getField('avatar', null, 'csv');
		}

		return $cbUser->getField('avatar', null, 'csv', 'none', 'list');
	}
}
