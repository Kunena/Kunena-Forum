<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Community
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @copyright   (C)  2013 - 2014 iJoomla, Inc. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

class plgKunenaCommunity extends JPlugin
{
	/**
	 * plgKunenaCommunity constructor.
	 *
	 * @param $subject
	 * @param $config
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		// Do not load if JomSocial is not installed
		$path = JPATH_ROOT . '/components/com_community/libraries/core.php';

		if (!is_file($path))
		{
			return;
		}

		include_once($path);

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_community.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_community.sys', KPATH_ADMIN);
	}

	/**
	 * Get Kunena access control object.
	 *
	 * @return KunenaAccess
	 *
	 * @todo Should we remove category ACL integration?
	 */
	public function onKunenaGetAccessControl()
	{
		if (!$this->params->get('access', 1))
		{
			return null;
		}

		require_once __DIR__ . "/access.php";

		return new KunenaAccessCommunity($this->params);
	}

	/**
	 * Get Kunena login integration object.
	 *
	 * @return \KunenaLoginCommunity|null
	 */
	public function onKunenaGetLogin()
	{
		if (!$this->params->get('login', 1))
		{
			return null;
		}

		require_once __DIR__ . "/login.php";

		return new KunenaLoginCommunity($this->params);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return \KunenaAvatarCommunity|null
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return null;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarCommunity($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return \KunenaProfileCommunity|null
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return null;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileCommunity($this->params);
	}

	/**
	 * Get Kunena private message integration object.
	 *
	 * @return \KunenaPrivateCommunity|null
	 */
	public function onKunenaGetPrivate()
	{
		if (!$this->params->get('private', 1))
		{
			return null;
		}

		require_once __DIR__ . "/private.php";

		return new KunenaPrivateCommunity($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return \KunenaActivityCommunity|null
	 */
	public function onKunenaGetActivity()
	{
		if (!$this->params->get('activity', 1))
		{
			return null;
		}

		require_once __DIR__ . "/activity.php";

		return new KunenaActivityCommunity($this->params);
	}
}
