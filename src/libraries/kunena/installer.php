<?php
/**
 * Kunena Component
 * @package    Kunena.Framework
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaInstaller
 */
class KunenaInstaller
{
	// Minimum supported versions during downgrade.
	protected static $downgrade = array('3.1' => '3.0.95');

	protected static $tables = null;

	/**
	 * Check if we are allowed to downgrade from the new to the old version.
	 *
	 * @param   string  $version
	 * @return  boolean  True if version can be safely downgraded.
	 */
	public static function canDowngrade($version)
	{
		if ($version == '@' . 'kunenaversion' . '@')
		{
			return true;
		}

		foreach (self::$downgrade as $major => $minor)
		{
			if (version_compare($version, $major, "<"))
			{
				continue;
			}

			if (version_compare($version, $minor, ">="))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get Kunena database schema version.
	 *
	 * @return  string  Version number or null.
	 */
	public static function getSchemaVersion()
	{
		// Check if Kunena can be found from the database.
		if (!self::detectTable('kunena_version'))
		{
			return null;
		}

		// Get installed version.
		$db = JFactory::getDBO();
		$db->setQuery("SELECT version FROM {$db->quoteName('#__kunena_version')} WHERE state='' ORDER BY id DESC", 0, 1);
		$version = $db->loadResult();

		return $version;
	}

	/**
	 * Detect if table exists in the database.
	 *
	 * @param   string  $table   Table name to be found.
	 * @param   string  $prefix  Database prefix.
	 * @param   bool    $reload  Reload all tables.
	 *
	 * @return boolean  True if the table exists in the database.
	 */
	public static function detectTable($table, $prefix = '#__', $reload = false)
	{
		$db = JFactory::getDBO();

		if (self::$tables === null || $reload)
		{
			$list = $db->getTableList();

			self::$tables = array();

			foreach ($list as $item)
			{
				self::$tables[$item] = false;
			}
		}

		if ($prefix == '#__')
		{
			$prefix = $db->getPrefix();
		}

		$table = $prefix . $table;

		return isset(self::$tables[$table]);
	}

	/**
	 * Get column type in the table.
	 *
	 * @param   string  $table   Table name to be found.
	 * @param   string  $column  Table column to be searched.
	 * @param   string  $prefix  Database prefix.
	 * @param   boolean $reload  Reload all tables.
	 *
	 * @return string|null  Column type or NULL if either table or column does not exist.
	 */
	public static function getTableColumn($table, $column, $prefix = '#__', $reload = false)
	{
		if (!self::detectTable($table, $prefix, $reload))
		{
			return false;
		}

		$db = JFactory::getDBO();

		if ($prefix == '#__')
		{
			$prefix = $db->getPrefix();
		}

		$table = $prefix . $table;

		if (!isset(self::$tables[$table]['columns']))
		{
			self::$tables[$table]['columns'] = $db->getTableColumns($table);
		}

		return isset(self::$tables[$table]['columns'][$column]) ? self::$tables[$table]['columns'][$column] : null;
	}
}
