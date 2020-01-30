<?php
/**
 * Kunena Plugin
 *
 * @package        Kunena.Plugins
 * @subpackage     Kunena
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Gravatar;

defined('_JEXEC') or die();

use Joomla\CMS\Plugin\CMSPlugin;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use function defined;

/**
 * Class plgKunenaGravatar
 *
 * @since   Kunena 6.0
 */
class plgKunenaGravatar extends CMSPlugin
{
	/**
	 * plgKunenaGravatar constructor.
	 *
	 * @param   object  $subject subject
	 * @param   object  $config  config
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return KunenaAvatarGravatar|void
	 *
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		require_once __DIR__ . '/class.php';
		require_once __DIR__ . '/avatar.php';

		return new KunenaAvatarGravatar($this->params);
	}
}
