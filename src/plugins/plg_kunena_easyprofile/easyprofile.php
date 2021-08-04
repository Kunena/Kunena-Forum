<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyprofile
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * Class plgKunenaEasyprofile
 *
 * @since   Kunena 6.0
 */
class plgKunenaEasyprofile extends CMSPlugin
{
	/**
	 * plgKunenaEasyprofile constructor.
	 *
	 * @param   DispatcherInterface  &$subject  The object to observe
	 * @param   array                $config    An optional associative array of configuration settings.
	 *                                          Recognized key values include 'name', 'group', 'params', 'language'
	 *                                         (this list is not meant to be comprehensive).
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(object &$subject, object $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.0') && KunenaForum::enabled()))
		{
			return;
		}

		// Do not load if Easyprofile is not installed
		$path = JPATH_SITE . '/components/com_jsn/helpers/helper.php';

		if (!is_file($path))
		{
			return;
		}

		include_once $path;

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_easyprofile.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_easyprofile.sys', KPATH_ADMIN);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return  AvatarEasyprofile|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAvatar(): AvatarEasyprofile
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		return new AvatarEasyprofile($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return  KunenaProfileEasyprofile|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetProfile(): KunenaProfileEasyprofile
	{
		if (!$this->params->get('profile', 1))
		{
			return;
		}

		return new KunenaProfileEasyprofile($this->params);
	}
}
