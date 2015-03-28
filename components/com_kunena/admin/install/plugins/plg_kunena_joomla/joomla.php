<?php
/**
 * Kunena Plugin
 *
 * @package    Kunena.Plugins
 * @subpackage Joomla
 *
 * @Copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.kunena.org
 **/
defined('_JEXEC') or die ();

class plgKunenaJoomla extends JPlugin
{
	/**
	 * @param object $subject
	 * @param array  $config
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_joomla.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_joomla.sys', KPATH_ADMIN);
	}

	/**
	 * @return KunenaAccessJoomla|null
	 */
	public function onKunenaGetAccessControl()
	{
		if (!$this->params->get('access', 1))
		{
			return null;
		}

		require_once __DIR__ . "/access.php";

		return new KunenaAccessJoomla($this->params);
	}

	/**
	 * @return KunenaLoginJoomla|null
	 */
	public function onKunenaGetLogin()
	{
		if (!$this->params->get('login', 1))
		{
			return null;
		}

		require_once __DIR__ . "/login.php";

		return new KunenaLoginJoomla($this->params);
	}
}
