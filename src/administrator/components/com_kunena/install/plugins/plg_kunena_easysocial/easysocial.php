<?php
/**
 * @package        EasySocial
 * @copyright      Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Unauthorized Access');

class plgKunenaEasySocial extends JPlugin
{
	/***
	 * Determines if EasySocial exists on the site.
	 *
	 * @since     1.0
	 * @access    public
	 * @return    bool
	 */
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

		jimport('joomla.filesystem.file');

		if (!JFile::exists($file))
		{
			return false;
		}

		include_once($file);

		return true;
	}

	/**
	 * plgKunenaEasySocial constructor.
	 *
	 * @param $subject
	 * @param $config
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('3.0') && KunenaForum::installed()))
		{
			return true;
		}

		// Check if easysocial exists
		if (!$this->exists())
		{
			return true;
		}

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_community.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_community.sys', KPATH_ADMIN);
	}

	/**
	 * Get Kunena login integration object.
	 *
	 * @return KunenaLogin
	 */
	public function onKunenaGetLogin()
	{
		if (!$this->exists())
		{
			return true;
		}

		if (!$this->params->get('avatar', 1))
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
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->exists())
		{
			return true;
		}

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
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->exists())
		{
			return true;
		}

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
	 */
	public function onKunenaGetPrivate()
	{
		if (!$this->exists())
		{
			return true;
		}

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
	 */
	public function onKunenaGetActivity()
	{
		if (!$this->exists())
		{
			return true;
		}

		if (!$this->params->get('activity', 1))
		{
			return null;
		}

		require_once __DIR__ . "/activity.php";

		return new KunenaActivityEasySocial($this->params);
	}
}
