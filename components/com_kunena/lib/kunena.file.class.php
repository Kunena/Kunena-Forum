<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Lib
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

defined( '_JEXEC' ) or die();


jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class CKunenaPath extends JPath
{
	public static function tmpdir()
	{
		static $tmpdir=false;
		if ($tmpdir) return realpath($tmpdir);

		jimport('joomla.filesystem.file');
		jimport('joomla.user.helper');

		$tmp = md5(JUserHelper::genRandomPassword(16));
		$ssp = ini_get('session.save_path');
		$jtp = JPATH_SITE.'/tmp';

		// Try to find a writable directory
		$tmpdir = @is_writable('/tmp') ? '/tmp' : false;
//		$tmpdir = (!$tmpdir && is_writable($ssp)) ? $ssp : false;
		$tmpdir = (!$tmpdir && is_writable($jtp)) ? $jtp : false;

		if (!$tmpdir) {
			$temp=tempnam(JPATH_ROOT . '/tmp','');
			if (file_exists($temp)) {
				unlink($temp);
				$tmpdir = dirname($temp);
			}
		}
		return realpath($tmpdir);
	}

	function isWritable($path)
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

class CKunenaFile extends JFile
{

	static function copy($src, $dest, $path = null, $use_streams = false)
	{
		// Initialize variables
		jimport('joomla.client.helper');
		$FTPOptions = JClientHelper::getCredentials('ftp');

		if ($path) {
			$src = CKunenaPath::clean($path.'/'.$src);
			$dest = CKunenaPath::clean($path.'/'.$dest);
		}

		if ($FTPOptions['enabled'] == 1) {
			// Make sure that we can copy file in FTP mode
			if (self::exists($dest) && !CKunenaPath::isOwner($dest)) @chmod($dest, 0777);
		}
		$ret = parent::copy($src, $dest);
		if ($ret === false && $FTPOptions['enabled'] == 1) @chmod($dest, 0644);

		return $ret;
	}

	static function move($src, $dest, $path = null, $use_streams = false)
	{
		// Initialize variables
		jimport('joomla.client.helper');
		$FTPOptions = JClientHelper::getCredentials('ftp');

		if ($path) {
			$src = CKunenaPath::clean($path.'/'.$src);
			$dest = CKunenaPath::clean($path.'/'.$dest);
		}

		if ($FTPOptions['enabled'] == 1) {
			// Make sure that we can move file in FTP mode
			if (self::exists($dest) && !CKunenaPath::isOwner($dest)) @chmod($dest, 0777);
			// If owner is not right, copy the file
			if (self::exists($src) && !CKunenaPath::isOwner($src)) {
				if (($ret = self::copy($src, $dest)) === true) {
					self::delete($src);
				}
			} else {
				$ret = parent::move($src, $dest);
			}
		} else {
			$ret = parent::move($src, $dest);
		}
		if ($ret === false && $FTPOptions['enabled'] == 1) @chmod($dest, 0644);
		return $ret;
	}

	static function write($file, &$buffer, $use_streams = false)
	{
		// Initialize variables
		jimport('joomla.client.helper');
		$FTPOptions = JClientHelper::getCredentials('ftp');

		if ($FTPOptions['enabled'] == 1) {
			// Make sure that we can copy file in FTP mode
			if (self::exists($file) && !CKunenaPath::isOwner($file)) @chmod($file, 0777);
		}
		$ret = parent::write($file, $buffer);
		if ($ret === false && $FTPOptions['enabled'] == 1) @chmod($file, 0644);

		return $ret;
	}

	static function upload($src, $dest, $use_streams = false)
	{
		// Initialize variables
		jimport('joomla.client.helper');
		$FTPOptions = JClientHelper::getCredentials('ftp');
		$ret = false;
		if (is_uploaded_file($src)) {
			if ($FTPOptions['enabled'] == 1 && self::exists($dest) && !CKunenaPath::isOwner($dest)) @chmod($dest, 0777);
			$ret = parent::upload($src, $dest);
			if ($FTPOptions['enabled'] == 1) {
				if ($ret === true) {
					jimport('joomla.client.ftp');
					// FIXME: renamed class JClientFtp from Joomla! 3.0/Platfrom 12.1
					$ftp = JClientFtp::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);
					@unlink($src);
					$ret = true;
				} else {
					@chmod($src, 0644);
				}
			}
		}	else {
			JError::raiseWarning(21, JText::_('WARNFS_ERR02'));
		}
	}
}
?>
