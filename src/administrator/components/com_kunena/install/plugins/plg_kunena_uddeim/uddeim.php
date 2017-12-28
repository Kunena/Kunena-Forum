<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  UddeIM
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

class PlgKunenaUddeIM extends JPlugin
{
	/**
	 * @param   object $subject
	 * @param   array  $config
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		KunenaFactory::loadLanguage('plg_kunena_uddeim.sys', 'admin');
		$path = JPATH_SITE . "/components/com_uddeim/uddeim.api.php";

		if (!is_file($path))
		{
			return;
		}

		include_once($path);

		$uddeim = new uddeIMAPI();

		if ($uddeim->version() < 1)
		{
			return;
		}

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_uddeim.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_uddeim.sys', KPATH_ADMIN);
	}

	/**
	 * @return KunenaPrivateUddeIM|null
	 */
	public function onKunenaGetPrivate()
	{
		if (!$this->params->get('private', 1))
		{
			return null;
		}

		require_once __DIR__ . "/private.php";

		return new KunenaPrivateUddeIM($this->params);
	}
}
