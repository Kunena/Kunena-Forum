<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Version;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use stdClass;

/**
 * Class KunenaVersion
 *
 * @since   Kunena 6.0
 */
class KunenaVersion
{
	/**
	 * Retrieve installed Kunena version, copyright and license as string.
	 *
	 * @return  string "Kunena X.Y.Z | YYYY-MM-DD | © 2008 - 2022 Copyright: Kunena Team. All rights reserved. |
	 *                License: GNU General Public License"
	 *
	 * @since   Kunena 6.0
	 */
	public static function getLongVersionHTML(): string
	{
		return self::getVersionHTML() . ' | ' . self::getCopyrightHTML();
	}

	/**
	 * Retrieve installed Kunena version as string.
	 *
	 * @return  string "Kunena X.Y.Z | YYYY-MM-DD [versionname]"
	 *
	 * @since   Kunena 6.0
	 */
	public static function getVersionHTML(): string
	{
		return 'Kunena ' . strtoupper(KunenaForum::version()) . ' | ' . KunenaForum::versionDate() . ' [ ' . KunenaForum::versionName() . ' ]';
	}

	/**
	 * Retrieve copyright information as string.
	 *
	 * @return  string "© 2008 - 2022 Copyright: Kunena Team. All rights reserved. | License: GNU General Public License"
	 *
	 * @since   Kunena 6.0
	 */
	public static function getCopyrightHTML(): string
	{
		return ': &copy; 2008 - 2022 ' . Text::_('COM_KUNENA_VERSION_COPYRIGHT') . ': <a href = "https://www.kunena.org/team" target = "_blank">'
			. Text::_('COM_KUNENA_VERSION_TEAM') . '</a>  | ' . Text::_('COM_KUNENA_VERSION_LICENSE')
			. ': <a href = "https://www.gnu.org/copyleft/gpl.html" target = "_blank">'
			. Text::_('COM_KUNENA_VERSION_GPL') . '</a>';
	}

	/**
	 * Get warning for unstable releases
	 *
	 * @param   string  $msg  Message to be shown containing two %s parameters for version (2.0.0RC) and version type
	 *                        (GIT, RC, BETA etc)
	 *
	 * @return  string    Warning message
	 *
	 * @since   Kunena 1.6
	 */
	public function getVersionWarning($msg = 'COM_KUNENA_VERSION_WARNING'): string
	{
		if (strpos(KunenaForum::version(), 'GIT') !== false)
		{
			$kn_version_type    = Text::_('COM_KUNENA_VERSION_GIT');
			$kn_version_warning = Text::_('COM_KUNENA_VERSION_GIT_WARNING');
		}
		else
		{
			if (strpos(KunenaForum::version(), 'DEV') !== false)
			{
				$kn_version_type    = Text::_('COM_KUNENA_VERSION_DEV');
				$kn_version_warning = Text::_('COM_KUNENA_VERSION_DEV_WARNING');
			}
			else
			{
				if (strpos(KunenaForum::version(), 'RC') !== false)
				{
					$kn_version_type    = Text::_('COM_KUNENA_VERSION_RC');
					$kn_version_warning = Text::_('COM_KUNENA_VERSION_RC_WARNING');
				}
				else
				{
					if (strpos(KunenaForum::version(), 'BETA') !== false)
					{
						$kn_version_type    = Text::_('COM_KUNENA_VERSION_BETA');
						$kn_version_warning = Text::_('COM_KUNENA_VERSION_BETA_WARNING');
					}
					else
					{
						if (strpos(KunenaForum::version(), 'ALPHA') !== false)
						{
							$kn_version_type    = Text::_('COM_KUNENA_VERSION_ALPHA');
							$kn_version_warning = Text::_('COM_KUNENA_VERSION_ALPHA_WARNING');
						}
					}
				}
			}
		}

		if (!empty($kn_version_warning) && !empty($kn_version_type))
		{
			return Text::sprintf($msg, '<strong>' . strtoupper(KunenaForum::version()), $kn_version_type . '</strong>') . ' ' . $kn_version_warning;
		}

		return '';
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function checkVersion(): bool
	{
		$version = $this->getDBVersion();

		if (!isset($version->version))
		{
			return false;
		}

		if ($version->state)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get version information from database
	 *
	 * @param   string  $prefix  Kunena table prefix
	 *
	 * @return  object  Version table
	 *
	 * @since   Kunena 1.6
	 */
	public function getDBVersion($prefix = 'kunena_')
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = "SHOW TABLES LIKE {$db->quote($db->getPrefix() . $prefix . 'version')}";
		$db->setQuery($query);

		if ($db->loadResult())
		{
			$query = $db->getQuery(true);
			$query
				->select('*')
				->from($db->quoteName($db->getPrefix() . $prefix . 'version'))
				->order('id DESC');
			$db->setQuery($query, 0, 1);
			$version = $db->loadObject();
		}

		if (!isset($version) || !\is_object($version) || !isset($version->state))
		{
			$version        = new stdClass;
			$version->state = '';
		}
		elseif (!empty($version->state))
		{
			if ($version->version != KunenaForum::version())
			{
				$version->state = '';
			}
		}

		return $version;
	}
}

/**
 * Class KunenaVersionException
 *
 * @since   Kunena 6.0
 */
class KunenaVersionException extends Exception
{
}
