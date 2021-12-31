<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Kunena
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Class PlgKunenaKunena
 *
 * @since   Kunena 6.0
 */
class PlgKunenaKunena extends CMSPlugin
{
	/**
	 * @return  false|KunenaAvatarKunena
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return false;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarKunena($this->params);
	}

	/**
	 * @return  false|KunenaProfileKunena
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return false;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileKunena($this->params);
	}
}
