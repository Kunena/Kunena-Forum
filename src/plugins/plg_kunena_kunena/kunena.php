<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Kunena
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Kunena;

defined('_JEXEC') or die();

use Joomla\CMS\Plugin\CMSPlugin;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Image\KunenaImageHelper;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use function defined;

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
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0')))
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
	public function onKunenaGetAvatar(): KunenaAvatar
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		return new KunenaAvatar;
	}

	/**
	 * @return  KunenaProfile|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetProfile(): KunenaProfile
	{
		if (!$this->params->get('profile', 1))
		{
			return;
		}

		return new KunenaProfile;
	}
}
