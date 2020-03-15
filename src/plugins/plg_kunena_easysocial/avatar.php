<?php
/**
 * @package        EasySocial
 * @copyright      Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

namespace Kunena\Forum\Plugin\Kunena\Easysocial;

defined('_JEXEC') or die('Unauthorized Access');

use Exception;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\Avatar;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use function defined;

/**
 * Class \Kunena\Forum\Libraries\Integration\AvatarEasySocial
 *
 * @since   Kunena 6.0
 */
class AvatarEasySocial extends Avatar
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * \Kunena\Forum\Libraries\Integration\AvatarEasySocial constructor.
	 *
	 * @param   object  $params  params
	 *
	 * @since  Kunena 6.0
	 */
	public function __construct($params)
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
	public function load($userlist)
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (class_exists('CFactory') && method_exists('CFactory', 'loadUsers'))
		{
			CFactory::loadUsers($userlist);
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getEditURL()
	{
		return FRoute::profile(['layout' => 'edit']);
	}

	/**
	 * @param   int  $user   user
	 * @param   int  $sizex  sizex
	 * @param   int  $sizey  sizey
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function _getURL($user, $sizex, $sizey)
	{
		$user = KunenaFactory::getUser($user);

		$user = FD::user($user->userid);

		$avatar = $user->getAvatar(SOCIAL_AVATAR_LARGE);

		return $avatar;
	}
}
