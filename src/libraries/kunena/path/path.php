<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Path
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

jimport('joomla.filesystem.path');

/**
 * Class KunenaPath
 *
 * @see   JPath
 * @since Kunena
 */
class KunenaPath extends JPath
{
	/**
	 * @var null
	 * @since Kunena
	 */
	public static $tmpdir = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public static $apache = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public static $owner = null;

	/**
	 * Returns server writable temporary directory, preferring to Joomla tmp if possible.
	 *
	 * @return  string  Path to temporary directory.
	 * @since Kunena
	 */
	public static function tmpdir()
	{
		if (!self::$tmpdir)
		{
			// Find Apache writable temporary directory defaulting to Joomla.
			$temp = @tempnam(Factory::getConfig()->get('tmp_path'), 'jj');

			// If the previous call fails, let's try system default instead.
			if ($temp === false)
			{
				$temp = @tempnam(sys_get_temp_dir(), 'jj');
			}

			// Set the temporary directory and remove created file.
			if ($temp !== false)
			{
				self::$apache = fileowner($temp);
				self::$tmpdir = dirname($temp);
				unlink($temp);
			}
		}

		return realpath(self::$tmpdir);
	}

	/**
	 * Checks if path is writeable either by the server or by FTP.
	 *
	 * @param   string $path paths
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public static function isWritable($path)
	{
		if (is_writable($path) || self::isOwner($path))
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to determine if script owns the path.
	 *
	 * @param   string $path Path to check ownership.
	 *
	 * @return  boolean  True if the php script owns the path passed.
	 * @since Kunena
	 */
	public static function isOwner($path)
	{
		if (!self::$owner)
		{
			$dir = Factory::getConfig()->get('tmp_path');
			$tmp = 'jj' . md5(mt_rand());

			$test = $dir . '/' . $tmp;

			// Create the test file
			$content = 'test';
			$success = KunenaFile::write($test, $content, false);

			if (!$success)
			{
				return false;
			}

			self::$owner = fileowner($test);

			// Delete the test file
			KunenaFile::delete($test);
		}

		// Test ownership
		return self::$owner == fileowner($path);
	}
}
