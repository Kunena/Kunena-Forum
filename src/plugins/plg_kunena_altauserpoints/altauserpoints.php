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

use Joomla\CMS\Plugin\CMSPlugin;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;

/**
 * plgKunenaAltaUserPoints class to handle integration with AltaUserPoints
 *
 * @since  5.0
 */
class plgKunenaAltaUserPoints extends CMSPlugin
{
	/**
	 * Constructor of plgKunenaAltaUserPoints class
	 *
	 * @param   DispatcherInterface  &$subject  The object to observe
	 * @param   array                 $config   An optional associative array of configuration settings.
	 *                                          Recognized key values include 'name', 'group', 'params', 'language'
	 *                                          (this list is not meant to be comprehensive).
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.0') && KunenaForum::enabled()))
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
	 * @return  KunenaAvatar|void
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAvatar()
	{
		if (!isset($this->params))
		{
			return;
		}

		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		require_once __DIR__ . "/KunenaAvatarAltaUserPoints.php";

		return new KunenaAvatarAltaUserPoints;
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return  KunenaProfileAltaUserPoints|void
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetProfile()
	{
		if (!isset($this->params))
		{
			return;
		}

		if (!$this->params->get('profile', 1))
		{
			return;
		}

		require_once __DIR__ . "/KunenaProfileAltaUserPoints.php";

		return new KunenaProfileAltaUserPoints($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return  KunenaActivityAltaUserPoints|void
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetActivity()
	{
		if (!isset($this->params))
		{
			return;
		}

		if (!$this->params->get('activity', 1))
		{
			return;
		}

		require_once __DIR__ . "/KunenaActivityAltaUserPoints.php";

		return new KunenaActivityAltaUserPoints($this->params);
	}
}
