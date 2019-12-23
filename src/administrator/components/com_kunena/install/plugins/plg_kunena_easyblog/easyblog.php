<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyblog
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Class plgKunenaEasyblog
 * @since Kunena
 */
class plgKunenaEasyblog extends \Joomla\CMS\Plugin\CMSPlugin
{
	/**
	 * plgKunenaEasyblog constructor.
	 *
	 * @param $subject
	 * @param $config
	 *
	 * @since Kunena
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('3.0') && KunenaForum::installed()))
		{
			return;
		}

		// Do not load if Easyblog is not installed
		$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (!is_file($path))
		{
			if (\Joomla\CMS\Plugin\PluginHelper::isEnabled('kunena', 'easyblog'))
			{
				$db = Factory::getDBO();
				$query = $db->getQuery(true);
				$query->update('`#__extensions`');
				$query->where($db->quoteName('element') . ' = ' . $db->quote('easyblog'));
				$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
				$query->where($db->quoteName('folder') . '= ' . $db->quote('kunena'));
				$query->set($db->quoteName('enabled') . '=0');
				$db->setQuery($query);
				$db->execute();
			}

			return;
		}

		include_once $path;

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_easyblog.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_easyblog.sys', KPATH_ADMIN);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return \KunenaAvatarEasyblog|null
	 * @since Kunena
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarEasyblog($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return \KunenaProfileEasyblog|null
	 * @since Kunena
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileEasyblog($this->params);
	}
}
