<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Community
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Community;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\Avatar;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use function defined;

/**
 * Class \Kunena\Forum\Libraries\Integration\AvatarCommunity
 *
 * @since   Kunena 6.0
 */
class AvatarCommunity extends Avatar
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
	public function getEditURL()
	{
		return CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar');
	}

	/**
	 * @param $user
	 * @param $sizex
	 * @param $sizey
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function _getURL($user, $sizex, $sizey)
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
