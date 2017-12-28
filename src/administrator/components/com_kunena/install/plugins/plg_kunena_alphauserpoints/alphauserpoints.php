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
 * @since       2.0
 *
 * @deprecated  5.0
 */
class plgKunenaAlphaUserPoints extends JPlugin
{

	/**
	 * plgKunenaAlphaUserPoints constructor.
	 *
	 * @param $subject
	 * @param $config
	 *
	 * @deprecated  5.0
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		$aup = JPATH_SITE . '/components/com_alphauserpoints/helper.php';

		if (!file_exists($aup))
		{
			return;
		}

		require_once $aup;

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_alphauserpoints.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_alphauserpoints.sys', KPATH_ADMIN);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return KunenaAvatar
	 *
	 * @deprecated  5.0
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return null;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarAlphaUserPoints($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return KunenaProfile
	 *
	 * @deprecated  5.0
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return null;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileAlphaUserPoints($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return KunenaActivity
	 *
	 * @deprecated  5.0
	 */
	public function onKunenaGetActivity()
	{
		if (!$this->params->get('activity', 1))
		{
			return null;
		}

		require_once __DIR__ . "/activity.php";

		return new KunenaActivityAlphaUserPoints($this->params);
	}
}
