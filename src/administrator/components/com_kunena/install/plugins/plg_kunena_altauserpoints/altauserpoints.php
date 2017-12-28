<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  AltaUserPoints
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * plgKunenaAltaUserPoints class to handle integration with AltaUserPoints
 *
 * @since  5.0
 */
class plgKunenaAltaUserPoints extends JPlugin
{
	/**
	 * Constructor of plgKunenaAltaUserPoints class
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
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
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return null;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarAltaUserPoints($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return KunenaProfile
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return null;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileAltaUserPoints($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return KunenaActivity
	 */
	public function onKunenaGetActivity()
	{
		if (!$this->params->get('activity', 1))
		{
			return null;
		}

		require_once __DIR__ . "/activity.php";

		return new KunenaActivityAltaUserPoints($this->params);
	}
}
