<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      AltaUserPoints
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

/**
 * Class KunenaAvatarAltaUserPoints
 * @since Kunena
 */
class KunenaAvatarAltaUserPoints extends KunenaAvatar
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaAvatarAltaUserPoints constructor.
	 *
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @return mixed
	 * @since Kunena
	 */
	public function getEditURL()
	{
		return Route::_('index.php?option=com_altauserpoints&view=account');
	}

	/**
	 * @param $user
	 * @param $sizex
	 * @param $sizey
	 *
	 * @return string|void
	 * @since Kunena
	 */
	public function _getURL($user, $sizex, $sizey)
	{
		trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() not implemented');
	}

	/**
	 * @param          $user
	 * @param   string $class class
	 * @param   int    $sizex sizex
	 * @param   int    $sizey sizey
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getLink($user, $class = '', $sizex = 90, $sizey = 90)
	{
		$user = KunenaFactory::getUser($user);
		$size = $this->getSize($sizex, $sizey);

		if ($size->y > 100)
		{
			$profile = AltaUserPointsHelper::getUserInfo('', $user->userid);

			$avatar = ($profile->avatar != '') ? _AUP_AVATAR_LIVE_PATH . $profile->avatar : JPATH_ROOT . '/components/com_altauserpoints/assets/images/avatars/generic_gravatar_grey.png';
			$width  = 100 * (float) $size->x / (float) $size->y;
			$avatar = '<img src="' . $avatar . '" border="0" alt="" width="' . $width . '" height="100" />';
		}
		else
		{
			$profile = AltaUserPointsHelper::getUserInfo('', $user->userid);

			$avatar = ($profile->avatar != '') ? Uri::root() . '/components/com_altauserpoints/assets/images/avatars/' . $profile->avatar : Uri::root() . '/components/com_altauserpoints/assets/images/avatars/' . 'generic_gravatar_grey.png';

			$avatar = '<img src="' . $avatar . '" border="0" alt="" width="' . $size->x . '" height="' . $size->y . '" />';
		}

		if (!$avatar)
		{
			$avatar = '<img border="0" width="100" height="100" alt="" src="' . Uri::root() . 'components/com_altauserpoints/assets/images/avatars/generic_gravatar_grey.png">';
		}

		return $avatar;
	}
}
