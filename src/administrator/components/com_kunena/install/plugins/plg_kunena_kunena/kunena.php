<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Kunena
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

class PlgKunenaKunena extends JPlugin
{
	/**
	 * @param   object &$subject
	 *
	 * @param   array  $config
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		parent::__construct($subject, $config);


		$method = method_exists(get_class(new KunenaControllerApplicationDisplay), 'poweredBy');
		if (!$method)
		{
			JFactory::getApplication()->enqueueMessage('Please Buy Official powered by remover plugin on: https://www.kunena.org/downloads', 'notice');
		}

		$this->loadLanguage('plg_kunena_kunena.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_kunena.sys', KPATH_ADMIN);
	}

	/**
	 * @return KunenaAvatarKunena|null
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return null;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarKunena($this->params);
	}

	/**
	 * @return KunenaProfileKunena|null
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return null;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileKunena($this->params);
	}
}
