<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyprofile
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Easyprofile;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use function defined;

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
	 * @param   object  $subject subject
	 * @param   object  $config  config
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('3.0') && KunenaForum::installed()))
		{
			return;
		}

		// Do not load if Easyprofile is not installed
		$path = JPATH_SITE . '/components/com_jsn/helpers/helper.php';

		if (!is_file($path))
		{
			if (PluginHelper::isEnabled('kunena', 'easyprofile'))
			{
				$db    = Factory::getDBO();
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__extensions'));
				$query->where($db->quoteName('element') . ' = ' . $db->quote('easyprofile'));
				$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
				$query->where($db->quoteName('folder') . ' = ' . $db->quote('kunena'));
				$query->set($db->quoteName('enabled') . ' = 0');
				$db->setQuery($query);
				$db->execute();
			}

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
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		require_once __DIR__ . "/avatar.php";

		return new AvatarEasyprofile($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return  KunenaProfileEasyprofile|void
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

		return new KunenaProfileEasyprofile($this->params);
	}
}
