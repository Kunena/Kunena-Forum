<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Joomla
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Plugin\CMSPlugin;
use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * Class plgKunenaJoomla
 *
 * @since   Kunena 6.0
 */
class plgKunenaJoomla extends CMSPlugin
{
	/**
	 * @return  false|KunenaAccessJoomla
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAccessControl()
	{
		if (!$this->params->get('access', 1))
		{
			return false;
		}

		require_once __DIR__ . "/access.php";

		return new KunenaAccessJoomla($this->params);
	}

	/**
	 * @return  KunenaLoginJoomla
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetLogin()
	{
		require_once __DIR__ . "/login.php";

		return new KunenaLoginJoomla($this->params);
	}
}
