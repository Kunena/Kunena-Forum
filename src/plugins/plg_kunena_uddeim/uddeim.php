<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      UddeIM
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Plugin\CMSPlugin;
use Kunena\Forum\Plugin\Kunena\Uddeim\KunenaPrivateUddeim;
use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * Class PlgKunenaUddeIM
 * @since Kunena
 */
class PlgKunenaUddeIM extends CMSPlugin
{
	/**
	 * @param   object $subject subject
	 * @param   array  $config  config
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
	    if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.2') && KunenaForum::enabled())) {
			return;
		}

		$path = JPATH_SITE . "/components/com_uddeim/uddeim.api.php";

		if (!is_file($path)) {
			return;
		}

		include_once $path;

		$uddeim = new uddeIMAPI;

		if ($uddeim->version() < 1)	{
			return;
		}

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_uddeim.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_uddeim.sys', JPATH_ADMINISTRATOR . '/components/com_kunena');
	}

	/**
	 * @return KunenaPrivateUddeIM|null
	 * @since Kunena
	 */
	public function onKunenaGetPrivate()
	{
	    if (!isset($this->params)) {
	        return;
	    }

	    if (!$this->params->get('private', 1)) {
			return;
		}

		return new KunenaPrivateUddeim($this->params);
	}
}
