<?php
/**
 * @version $Id: kunena.defines.php 510 2009-03-08 08:36:53Z fxstein $
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2009 Kunena All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

defined( '_JEXEC' ) or die('Restricted access');

jimport('joomla.filesystem.path');
jimport('joomla.filesystem.file');

class CKunenaPath extends JPath
{
	function tmpdir()
	{
		static $tmpdir=false;
		if (!empty($tmpdir)) return $tmpdir;

		if (function_exists('sys_get_temp_dir')) {
			$tmpdir  = sys_get_temp_dir();
		}
		if (empty($tmpdir)) {
			$file = tempnam(false,false);
			if ($file === false) return false;
			@unlink($file);
			$tmpdir = realpath(dirname($file));
		}
		return $tmpdir;
	}

	function _owner($getgroup = false)
	{
		static $owner=false;
		static $group=false;

		if ($getgroup === false && !empty($owner)) return $owner;
		if ($getgroup === true  && !empty($group)) return $group;

		jimport('joomla.user.helper');

		$tmp = md5(JUserHelper::genRandomPassword(16));
		$dir = self::tmpdir();

		if ($dir)
		{
			$test = $dir.DS.$tmp;
			// Create the test file
			JFile::write($test, '');
			// Test ownership
			$owner = fileowner($test);
			$group = filegroup($test);
			// Delete the test file
			JFile::delete($test);
		}
		return ($getgroup ? $group : $owner);
	}

	function owner()
	{
		return self::_owner();
	}

	function group()
	{
		return self::_owner(true);
	}

	function isOwner($path)
	{
		$owner = self::owner();
		return ($owner == fileowner($path));
	}

	function isWritable($path)
	{
		if (is_writable($path)) return true;
		$owner = self::owner();
		$group = self::group();
		$perms = self::getPermissions($path);
		if ($owner == fileowner($path)) {
			if ($perms[1] == 'w') return true;
		}
		if ($group == filegroup($path)) {
			if ($perms[4] == 'w') return true;
		}
		return false;
	}
}

class CKunenaFile extends JFile
{

	function copy($src, $dest, $path = null)
	{
		// Initialize variables
		jimport('joomla.client.helper');
		$FTPOptions = JClientHelper::getCredentials('ftp');

		if ($path) {
			$src = CKunenaPath::clean($path.DS.$src);
			$dest = CKunenaPath::clean($path.DS.$dest);
		}

		if ($FTPOptions['enabled'] == 1) {
			// Make sure that we can copy file in FTP mode
			if (self::exists($dest) && !CKunenaPath::isOwner($dest)) @chmod($dest, 0777);
		}
		$ret = parent::copy($src, $dest);
		if ($ret === false && $FTPOptions['enabled'] == 1) @chmod($dest, 0644);

		return $ret;
	}

	function move($src, $dest, $path = null)
	{
		// Initialize variables
		jimport('joomla.client.helper');
		$FTPOptions = JClientHelper::getCredentials('ftp');

		if ($path) {
			$src = CKunenaPath::clean($path.DS.$src);
			$dest = CKunenaPath::clean($path.DS.$dest);
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

	function write($file, $buffer)
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

	function upload($src, $dest)
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
					$ftp = & JFTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);
					$ftp->chmod($dest, 0644);
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
