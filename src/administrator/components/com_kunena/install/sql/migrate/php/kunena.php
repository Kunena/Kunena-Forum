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
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Class KunenaMigratorKunena
 * @since Kunena
 */
class KunenaMigratorKunena
{
	/**
	 * @return KunenaMigratorKunena|null
	 * @since Kunena
	 */
	public static function getInstance()
	{
		static $instance = null;

		if (!$instance)
		{
			$instance = new KunenaMigratorKunena;
		}

		return $instance;
	}

	/**
	 * Detect Kunena 1.x version.
	 *
	 * @return  string  Kunena version or null.
	 * @since Kunena
	 */
	public function detect()
	{
		// Check if Kunena 1.x can be found from the Joomla installation.
		if (KunenaInstaller::detectTable('fb_version'))
		{
			// Get installed version.
			$db = Factory::getDBO();
			$db->setQuery("SELECT version, versiondate AS date FROM `#__fb_version` ORDER BY id DESC", 0, 1);
			$version = $db->loadRow();

			// Do not detect FireBoard 1.0.5 RC1 / RC2.
			if ($version && version_compare($version->version, '1.0.5', '<='))
			{
				return null;
			}

			// Return FireBoard version.
			if ($version->version)
			{
				return $version->version;
			}
		}

		return null;
	}
}
