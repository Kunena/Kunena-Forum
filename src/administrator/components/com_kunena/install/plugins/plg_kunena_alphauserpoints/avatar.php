<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  AlphaUserPoints
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * KunenaActivityAlphaUserPoints class to handle integration with AlphaUserPoints
 *
 * @deprecated  5.0
 */
class KunenaAvatarAlphaUserPoints extends KunenaAvatar
{
	protected $params = null;

	/**
	 * KunenaAvatarAlphaUserPoints constructor.
	 *
	 * @param $params
	 *
	 * @deprecated  5.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @return mixed
	 *
	 * @deprecated  5.0
	 */
	public function getEditURL()
	{
		return JRoute::_('index.php?option=com_alphauserpoints&view=account');
	}

	/**
	 * @param $user
	 * @param $sizex
	 * @param $sizey
	 *
	 * @deprecated  5.0
	 * @return string|void
	 */
	public function _getURL($user, $sizex, $sizey)
	{
		trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() not implemented');
	}

	/**
	 * @param        $user
	 * @param string $class
	 * @param int    $sizex
	 * @param int    $sizey
	 *
	 * @return string
	 *
	 * @deprecated  5.0
	 */
	public function getLink($user, $class = '', $sizex = 90, $sizey = 90)
	{
		$user = KunenaFactory::getUser($user);
		$size = $this->getSize($sizex, $sizey);

		if ($size->y > 100)
		{
			$avatar = AlphaUserPointsHelper::getAupAvatar($user->userid, 0, 100 * (float) $size->x / (float) $size->y, '100');
		}
		else
		{
			$avatar = AlphaUserPointsHelper::getAupAvatar($user->userid, 0, $size->x, $size->y);
		}

		if (!$avatar)
		{
			$avatar = '<img border="0" width="100" height="100" alt="" src="' . JUri::root() . 'components/com_alphauserpoints/assets/images/avatars/generic_gravatar_grey.png">';
		}

		return $avatar;
	}
}
