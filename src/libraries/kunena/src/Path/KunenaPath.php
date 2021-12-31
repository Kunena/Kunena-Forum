<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Path
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Path;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;

/**
 * Class KunenaPath
 *
 * @see     Path
 *
 * @since   Kunena 6.0
 */
class KunenaPath extends Path
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public static $tmpdir = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public static $apache = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public static $owner = null;

	/**
	 * Returns server writable temporary directory, preferring to Joomla tmp if possible.
	 *
	 * @return  string  Path to temporary directory.
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public static function tmpdir(): string
	{
		if (!self::$tmpdir)
		{
			// Find Apache writable temporary directory defaulting to Joomla.
			$temp = @tempnam(Factory::getApplication()->get('tmp_path'), 'jj');

			// If the previous call fails, let's try system default instead.
			if ($temp === false)
			{
				$temp = @tempnam(sys_get_temp_dir(), 'jj');
			}

			// Set the temporary directory and remove created file.
			if ($temp !== false)
			{
				self::$apache = fileowner($temp);
				self::$tmpdir = \dirname($temp);
				unlink($temp);
			}
		}

		return realpath(self::$tmpdir);
	}

	/**
	 * Checks if path is writable either by the server or by FTP.
	 *
	 * @param   string  $path  paths
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public static function isWritable(string $path): bool
	{
		return is_writable($path) || self::isOwner($path);
	}

	/**
	 * Method to determine if script owns the path.
	 *
	 * @param   string  $path  Path to check ownership.
	 *
	 * @return  boolean  True if the php script owns the path passed.
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public static function isOwner($path)
	{
		if (!self::$owner)
		{
			$dir = Factory::getApplication()->get('tmp_path');
			$tmp = 'jj' . md5(mt_rand());

			$test = $dir . '/' . $tmp;

			// Create the test file
			$content = 'test';
			$success = File::write($test, $content, false);

			if (!$success)
			{
				return false;
			}

			self::$owner = fileowner($test);

			// Delete the test file
			File::delete($test);
		}

		// Test ownership
		return self::$owner == fileowner($path);
	}
}
