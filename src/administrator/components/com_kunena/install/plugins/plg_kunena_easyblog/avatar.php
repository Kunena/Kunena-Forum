<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Easyblog
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaAvatarEasyblog extends KunenaAvatar
{
	protected $params = null;

	/**
	 * KunenaAvatarEasyblog constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @return bool
	 */
	public function getEditURL()
	{
		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit');
	}

	/**
	 * @param $user
	 * @param $sizex
	 * @param $sizey
	 *
	 * @return string
	 */
	public function _getURL($user, $sizex, $sizey)
	{
		if (!$user->userid == 0)
		{
			$user   = KunenaFactory::getUser($user->userid);
			$user   = EB::user($user->userid);
			$avatar = $user->getAvatar();
		}
		else
		{
			$avatar = JUri::root(true) . '/components/com_easyblog/assets/images/default_blogger.png';
		}

		return $avatar;
	}
}
