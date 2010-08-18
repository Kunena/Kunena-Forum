<?php
/**
 * @version $Id: kunena.file.class.php 2562 2010-05-27 20:08:40Z mahagr $
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2010 Kunena All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

defined( '_JEXEC' ) or die();


jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class CKunenaPath extends JPath
{
	function tmpdir()
	{
		static $tmpdir=false;
		if ($tmpdir) return realpath($tmpdir);

		jimport('joomla.filesystem.file');
		jimport('joomla.user.helper');

		$tmp = md5(JUserHelper::genRandomPassword(16));
		$ssp = ini_get('session.save_path');
		$jtp = JPATH_SITE.DS.'tmp';

		// Try to find a writable directory
		$tmpdir = is_writable('/tmp') ? '/tmp' : false;
//		$tmpdir = (!$tmpdir && is_writable($ssp)) ? $ssp : false;
		$tmpdir = (!$tmpdir && is_writable($jtp)) ? $jtp : false;

		if (!$tmpdir) {
			$temp=tempnam(JPATH_ROOT . DS . 'tmp','');
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
}

class CKunenaFile extends JFile
{

	static function copy($src, $dest, $path = null)
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

	static function move($src, $dest, $path = null)
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

	static function write($file, $buffer)
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

	static function upload($src, $dest)
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
