<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class \Kunena\Forum\Libraries\Integration\AvatarComprofiler
 *
 * @since   Kunena 6.0
 */
class KunenaAvatarComprofiler extends KunenaAvatar
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * \Kunena\Forum\Libraries\Integration\AvatarComprofiler constructor.
	 *
	 * @param   object  $params  params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
	}

	/**
	 * @param   array  $userlist  userlist
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function load(array $userlist): void
	{
		CBuser::advanceNoticeOfUsersNeeded($userlist);
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getEditURL(): string
	{
		global $_CB_framework;

		return $_CB_framework->userProfileEditUrl();
	}

	/**
	 * @param   KunenaUser  $user   user
	 * @param   int         $sizex  sizex
	 * @param   int         $sizey  sizey
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
	{
		$user = KunenaFactory::getUser($user);

		// Get CUser object
		$cbUser = null;
		$url = null;

		if ($user->userid)
		{
			$cbUser = CBuser::getInstance($user->userid);
		}

		if ($cbUser !== null)
		{
			if ($sizex <= 144)
			{
				$url = $cbUser->getField('avatar', null, 'csv');
			}
			else
			{
				$url = $cbUser->getField('avatar', null, 'csv', 'none', 'list');
			}
		}

		if ($url === null)
		{
			if ($sizex <= 144)
			{
				return selectTemplate() . 'images/avatar/tnnophoto_n.png';
			}

			return selectTemplate() . 'images/avatar/nophoto_n.png';
		}

		return $url;
	}
}
