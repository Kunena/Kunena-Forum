<?php
/**
 * Kunena Plugin
 *
 * @package        Kunena.Plugins
 * @subpackage     Kunena
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Plugin\CMSPlugin;
use Kunena\Forum\Libraries\Forum\KunenaForum;

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
	 * @param   object  $subject                The object to observe
	 * @param   array   $config                 An optional associative array of configuration settings.
	 *                                          Recognized key values include 'name', 'group', 'params', 'language'
	 *                                          (this list is not meant to be comprehensive).
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function __construct(object &$subject, $config = [])
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.0') && KunenaForum::enabled()))
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

		require_once KPATH_FRAMEWORK . '/External/Emberlabs/Gravatar.php';
		require_once __DIR__ . '/KunenaAvatarGravatar.php';

		return new KunenaAvatarGravatar($this->params);
	}
}
