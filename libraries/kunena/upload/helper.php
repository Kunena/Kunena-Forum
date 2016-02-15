<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Upload
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Upload Backend Helper Class
 */
class KunenaUploadHelper
{
	protected static $_instances = array ();

	private function __construct() {}

	public static function upload($file, $uploadfolder, $format)
	{
		jimport( 'joomla.filesystem.folder' );
		require_once( JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php' );

		$err = null;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);

		if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || !empty($file['error']))
		{
			return false;
		}

		if (!JFolder::exists($uploadfolder))
		{
			return false;
		}

		if (isset($file['name']))
		{
			$filepath = JPath::clean($uploadfolder.'/'.strtolower($file['name']));

			if (!MediaHelper::canUpload( $file, $err ))
			{
				if ($format == 'json')
				{
					//jimport('joomla.error.log');
					//$log = JLog::getInstance('upload.error.php');
					//$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$err));
					header('HTTP/1.0 415 Unsupported Media Type');
					jexit('Error. Unsupported Media Type!');
				}
				else
				{
					return false;
				}
			}

			if (JFile::exists($filepath))
			{
				if ($format == 'json')
				{
					//jimport('joomla.error.log');
					//$log = JLog::getInstance('upload.error.php');
					//$log->addEntry(array('comment' => 'File already exists: '.$filepath));
					header('HTTP/1.0 409 Conflict');
					jexit('Error. File already exists');
				}
				else
				{
					$ext = JFile::getExt($file['name']);
					$name = JFile::stripExt($file['name']);
					$newFileName = '';

					for ($i = 2; file_exists("{$uploadfolder}/{$newFileName}"); $i++)
					{
						$newFileName = $name . "-$i." . $ext;
					}

					$filepath = $uploadfolder . '/' . $newFileName;
				}
			}

			if (!JFile::upload($file['tmp_name'], $filepath))
			{
				if ($format == 'json')
				{
					//jimport('joomla.error.log');
					//$log = JLog::getInstance('upload.error.php');
					//$log->addEntry(array('comment' => 'Cannot upload: '.$filepath));
					header('HTTP/1.0 400 Bad Request');
					jexit('Error. Unable to upload file');
				}
				else
				{
					return false;
				}
			}
			else
			{
				if ($format == 'json')
				{
					//jimport('joomla.error.log');
					//$log = JLog::getInstance();
					//$log->addEntry(array('comment' => $uploadfolder));
					jexit('Upload complete');
				}
				else
				{
					return true;
				}
			}
		}
		else
		{
			return false;
		}
	}
}
