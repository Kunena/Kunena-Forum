<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Kunena
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Plugin\CMSPlugin;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Integration\KunenaProfile;

/**
 * Class PlgKunenaKunena
 *
 * @since   Kunena 6.0
 */
class PlgKunenaKunena extends CMSPlugin
{
	/**
	 * @param   object  $subject  subject
	 * @param   array   $config   config
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(object $subject, array $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.0')))
		{
			return;
		}

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_kunena.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_kunena.sys', KPATH_ADMIN);
	}

	/**
	 * @return  KunenaAvatar|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAvatar()
	{
		/*if (!$this->params->get('avatar', 1))
		{
			return false;
		}

		return new KunenaAvatar;*/
	}

	/**
	 * @return  KunenaProfile|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetProfile()
	{
		/*if (!$this->params->get('profile', 1))
		{
			return false;
		}

		return new KunenaProfile;*/
	}
}
