<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Lib
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

defined( '_JEXEC' ) or die();


jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class CKunenaPath extends JPath
{
	static public $tmpdir = null;
	static public $apache = null;
	static public $owner = null;

	public static function tmpdir()
	{
		if (!self::$tmpdir) {
			// Find Apache writable temporary directory defaulting to Joomla.
			$temp = @tempnam(JFactory::getConfig()->tmp_path, 'jj');

			// If the previous call fails, let's try system default instead.
			if ($temp === false) {
				$temp = @tempnam(sys_get_temp_dir(), 'jj');
			}

			// Set the temporary directory and remove created file.
			if ($temp !== false) {
				self::$apache = fileowner($temp);
				self::$tmpdir = dirname($temp);
				unlink($temp);
			}
		}
		return realpath(self::$tmpdir);
	}

	public static function isOwner($path)
	{
		if (!self::$owner) {
			$dir = JFactory::getConfig()->tmp_path;
			$tmp = 'jj'.md5(mt_rand());

			$test = $dir . '/' . $tmp;

			// Create the test file
			$content = 'test';
			$success = CKunenaFile::write($test, $content, false);

			if (!$success) return false;

			self::$owner = fileowner($test);

			// Delete the test file
			CKunenaFile::delete($test);
		}

		// Test ownership
		return (self::$owner == fileowner($path));
	}

	public static function isWritable($path)
	{
		if (is_writable($path) || self::isOwner($path)) return true;
		return false;
	}
}

class CKunenaFolder extends JFolder
{
	static function createIndex($folder) {
		// Make sure we have an index.html file in the current folder
		if (!CKunenaFile::exists($folder.'/index.html')) {
			$contents = '<html><body></body></html>';
			CKunenaFile::write($folder.'/index.html', $contents);
		}
	}
}

class CKunenaFile extends JFile {}
