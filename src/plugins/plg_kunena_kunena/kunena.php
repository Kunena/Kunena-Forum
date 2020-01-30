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
use Kunena\Forum\Libraries\Integration\Avatar;
use Kunena\Forum\Libraries\Integration\Profile;
use function defined;

/**
 * Class PlgKunenaKunena
 *
 * @since   Kunena 6.0
 */
class PlgKunenaKunena extends CMSPlugin
{
	/**
	 * @param   object $subject  subject
	 * @param   array  $config   config
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(&$subject, $config)
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
	 * @return  Avatar|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		require_once __DIR__ . "/avatar.php";

		return new Avatar($this->params);
	}

	/**
	 * @return  Profile|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return;
		}

		require_once __DIR__ . "/profile.php";

		return new Profile($this->params);
	}
}
