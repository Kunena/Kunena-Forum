<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyblog
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;

/**
 * Class KunenaAvatarEasyblog
 * @since Kunena
 */
class KunenaAvatarEasyblog extends KunenaAvatar
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaAvatarEasyblog constructor.
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
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
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
	 * @throws Exception
	 * @since Kunena
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
			$avatar = Uri::root(true) . '/components/com_easyblog/assets/images/default_blogger.png';
		}

		return $avatar;
	}
}
