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

use Joomla\CMS\Factory;

/**
 * plgKunenaAltaUserPoints class to handle integration with AltaUserPoints
 *
 * @since  5.0
 */
class plgKunenaAltaUserPoints extends \Joomla\CMS\Plugin\CMSPlugin
{
	/**
	 * Constructor of plgKunenaAltaUserPoints class
	 *
	 * @param   object &$subject The object to observe
	 * @param   array  $config   An optional associative array of configuration settings.
	 *
	 * @since Kunena
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		$aup = JPATH_SITE . '/components/com_altauserpoints/helper.php';

		if (!file_exists($aup))
		{
			return;
		}

		require_once $aup;

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_altauserpoints.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_altauserpoints.sys', KPATH_ADMIN);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return KunenaAvatar
	 * @since Kunena
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarAltaUserPoints($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return KunenaProfile
	 * @since Kunena
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileAltaUserPoints($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return KunenaActivity
	 * @since Kunena
	 */
	public function onKunenaGetActivity()
	{
		if (!$this->params->get('activity', 1))
		{
			return;
		}

		require_once __DIR__ . "/activity.php";

		return new KunenaActivityAltaUserPoints($this->params);
	}
}
