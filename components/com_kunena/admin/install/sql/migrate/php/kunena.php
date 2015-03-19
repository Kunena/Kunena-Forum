<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Installer
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaMigratorKunena
{
	public static function getInstance()
	{
		static $instance = null;
		if (!$instance)
		{
			$instance = new KunenaMigratorKunena();
		}

		return $instance;
	}

	/**
	 * Detect Kunena 1.x version.
	 *
	 * @return  string  Kunena version or null.
	 */
	public function detect()
	{
		// Check if Kunena 1.x can be found from the Joomla installation.
		if (KunenaInstaller::detectTable('fb_version'))
		{
			// Get installed version.
			$db = JFactory::getDBO();
			$db->setQuery("SELECT version, versiondate AS date FROM #__fb_version ORDER BY id DESC", 0, 1);
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
