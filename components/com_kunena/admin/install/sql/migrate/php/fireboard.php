<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Installer
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaMigratorFireboard
{
	protected $versions = array(
		array('version' => '1.0.4', 'date' => '2007-12-23', 'table' => 'fb_sessions', 'column' => 'currvisit'),
		array('version' => '1.0.3', 'date' => '2007-09-04', 'table' => 'fb_categories', 'column' => 'headerdesc'),
		array('version' => '1.0.2', 'date' => '2007-08-03', 'table' => 'fb_users', 'column' => 'rank'),
		array('version' => '1.0.1', 'date' => '2007-05-20', 'table' => 'fb_users', 'column' => 'uhits'),
		array('version' => '1.0.0', 'date' => '2007-04-15', 'table' => 'fb_messages', 'column' => 'id'),
	);

	public static function getInstance()
	{
		static $instance = null;
		if (!$instance)
		{
			$instance = new KunenaMigratorFireboard();
		}

		return $instance;
	}

	/**
	 * Detect FireBoard version.
	 *
	 * @return  string  FireBoard version or null.
	 */
	public function detect()
	{
		// Check if FireBoard can be found from the Joomla installation.
		if (KunenaInstaller::detectTable('fb_version'))
		{
			// Get installed version.
			$db = JFactory::getDBO();
			$db->setQuery("SELECT version, versiondate AS date FROM #__fb_version ORDER BY id DESC", 0, 1);
			$version = $db->loadRow();

			// Do not detect Kunena 1.x.
			if ($version && version_compare($version->version, '1.0.5', '>'))
			{
				return null;
			}
			// Return FireBoard version.
			if ($version->version)
			{
				return $version->version;
			}
		}
		foreach ($this->versions as $version)
		{
			if (KunenaInstaller::getTableColumn($version['table'], $version['column']))
			{
				return $version->version;
			}
		}

		return null;
	}
}
