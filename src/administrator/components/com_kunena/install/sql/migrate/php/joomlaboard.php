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

/**
 * Class KunenaMigratorJoomlaboard
 * @since Kunena
 */
class KunenaMigratorJoomlaboard
{
	/**
	 * @var array
	 * @since Kunena
	 */
	protected $versions = array(
		array('version' => '1.0', 'date' => '1000-01-01', 'table' => 'sb_messages', 'column' => 'id'),
	);

	/**
	 * @return KunenaMigratorJoomlaboard|null
	 * @since Kunena
	 */
	public static function getInstance()
	{
		static $instance = null;

		if (!$instance)
		{
			$instance = new KunenaMigratorJoomlaboard;
		}

		return $instance;
	}

	/**
	 * Detect JoomlaBoard version.
	 *
	 * @return  string  JoomlaBoard version or null.
	 * @since Kunena
	 */
	public function detect()
	{
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
