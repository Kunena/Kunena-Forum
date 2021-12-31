<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Community
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class \Kunena\Forum\Libraries\Integration\AvatarCommunity
 *
 * @since   Kunena 6.0
 */
class KunenaAvatarCommunity extends KunenaAvatar
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * \Kunena\Forum\Libraries\Integration\AvatarCommunity constructor.
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
	 * @param   array  $userlist
	 *
	 * @return  void
	 * @since   Kunena 6.0
	 */
	public function load(array $userlist): void
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (class_exists('CFactory') && method_exists('CFactory', 'loadUsers'))
		{
			CFactory::loadUsers($userlist);
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getEditURL(): string
	{
		return CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar');
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
	 * @throws  Exception
	 */
	protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
	{
		$kuser = KunenaFactory::getUser($user);

		// Get CUser object
		$user = CFactory::getUser($kuser->userid);

		if ($kuser->userid == 0)
		{
			$avatar = str_replace(Uri::root(true), '', COMMUNITY_PATH_ASSETS) . "user-Male.png";
		}
		elseif ($sizex <= 90)
		{
			$avatar = $user->getThumbAvatar();
		}
		else
		{
			$avatar = $user->getAvatar();
		}

		return $avatar;
	}
}
