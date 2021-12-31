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

/**
 * Class plgKunenaGravatar
 * @since Kunena
 */
class plgKunenaGravatar extends \Joomla\CMS\Plugin\CMSPlugin
{
	/**
	 * plgKunenaGravatar constructor.
	 *
	 * @param $subject
	 * @param $config
	 *
	 * @since Kunena
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
	 * @return KunenaAvatar
	 * @since Kunena
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
