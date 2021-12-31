<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      UddeIM
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Class PlgKunenaUddeIM
 * @since Kunena
 */
class PlgKunenaUddeIM extends \Joomla\CMS\Plugin\CMSPlugin
{
	/**
	 * @param   object $subject subject
	 * @param   array  $config  config
	 *
	 * @throws Exception
	 * @deprecated  6.0
	 * @since Kunena
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

		include_once $path;

		$uddeim = new uddeIMAPI;

		if ($uddeim->version() < 1)
		{
			return;
		}

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_uddeim.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_uddeim.sys', KPATH_ADMIN);
	}

	/**
	 * @return KunenaPrivateUddeIM|null
	 * @since Kunena
	 */
	public function onKunenaGetPrivate()
	{
		if (!$this->params->get('private', 1))
		{
			return;
		}

		require_once __DIR__ . "/private.php";

		return new KunenaPrivateUddeIM($this->params);
	}
}
