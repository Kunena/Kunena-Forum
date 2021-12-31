<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easysocial
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @copyright      Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\Filesystem\File;

$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/plugins.php';

if (!File::exists($file))
{
	return;
}

require_once($file);
require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');

/**
 * @package     Kunena
 *
 * @since       Kunena 5.0
 */
class plgKunenaEasySocial extends EasySocialPlugins
{
	/**
	 * plgKunenaEasySocial constructor.
	 *
	 * @param $subject
	 * @param $config
	 *
	 * @since       Kunena 5.0
	 *
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('5.0') && KunenaForum::installed()))
		{
			return true;
		}

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_easysocial.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_easysocial.sys', KPATH_ADMIN);
	}

	/**
	 * Get Kunena login integration object.
	 *
	 * @return KunenaLogin
	 * @since       Kunena 5.0
	 */
	public function onKunenaGetLogin()
	{
		if (!$this->params->get('login', 1))
		{
			return null;
		}

		require_once __DIR__ . "/login.php";

		return new KunenaLoginEasySocial($this->params);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return KunenaAvatar
	 * @since       Kunena 5.0
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return null;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarEasySocial($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return KunenaProfile
	 * @since       Kunena 5.0
	 *
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return null;
		}

		require_once(__DIR__ . "/profile.php");

		return new KunenaProfileEasySocial($this->params);
	}

	/**
	 * Get Kunena private message integration object.
	 *
	 * @return KunenaPrivate
	 * @since       Kunena 5.0
	 */
	public function onKunenaGetPrivate()
	{
		if (!$this->params->get('private', 1))
		{
			return null;
		}

		require_once __DIR__ . "/private.php";

		return new KunenaPrivateEasySocial($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return KunenaActivity
	 * @since       Kunena 5.0
	 */
	public function onKunenaGetActivity()
	{
		if (!$this->params->get('activity', 1))
		{
			return null;
		}

		require_once __DIR__ . "/activity.php";

		return new KunenaActivityEasySocial($this->params);
	}
}
