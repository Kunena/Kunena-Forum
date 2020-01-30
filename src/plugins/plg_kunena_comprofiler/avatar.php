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

namespace Kunena\Forum\Plugin\Kunena\Comprofiler;

defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Integration\Avatar;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use function defined;

/**
 * Class \Kunena\Forum\Libraries\Integration\AvatarComprofiler
 *
 * @since   Kunena 6.0
 */
class AvatarComprofiler extends Avatar
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * \Kunena\Forum\Libraries\Integration\AvatarComprofiler constructor.
	 *
	 * @param   object  $params params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param   object  $userlist userlist
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function load($userlist)
	{
		CBuser::advanceNoticeOfUsersNeeded($userlist);
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getEditURL()
	{
		global $_CB_framework;

		return $_CB_framework->userProfileEditUrl();
	}

	/**
	 * @param   int  $user  user
	 * @param   int  $sizex sizex
	 * @param   int  $sizey sizey
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
