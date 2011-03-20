<?php
/**
 * @version $Id: helper.php 4381 2011-02-05 20:55:31Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport('kunena.error');

/**
 * Kunena Upload Backend Helper Class
 */
class KunenaUploadHelper {
	protected static $_instances = array ();

	private function __construct() {}

	public static function upload($file, $uploadfolder, $format, $view) {
		jimport( 'joomla.filesystem.folder' );
		$config = KunenaFactory::getConfig ();
		// load language fo component media
		JPlugin::loadLanguage( 'com_media' );
		$params = JComponentHelper::getParams('com_media');
		require_once( JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php' );
		define('COM_KUNENA_MEDIA_BASE', JPATH_ROOT.'/components/com_kunena/template/'.$config->template.'/images');

		$err			= null;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name']	= JFile::makeSafe($file['name']);

		if ( !JFolder::exists(COM_KUNENA_MEDIA_BASE.'/'.$uploadfolder) ) return false;

		if (isset($file['name'])) {
			$filepath = JPath::clean(COM_KUNENA_MEDIA_BASE.'/'.$uploadfolder.'/'.strtolower($file['name']));

			if (!MediaHelper::canUpload( $file, $err )) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$err));
					header('HTTP/1.0 415 Unsupported Media Type');
					jexit('Error. Unsupported Media Type!');
				} else {
					return false;
				}
			}

			if (JFile::exists($filepath)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'File already exists: '.$filepath));
					header('HTTP/1.0 409 Conflict');
					jexit('Error. File already exists');
				} else {
					return false;
				}
			}

			if (!JFile::upload($file['tmp_name'], $filepath)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Cannot upload: '.$filepath));
					header('HTTP/1.0 400 Bad Request');
					jexit('Error. Unable to upload file');
				} else {
					return false;
				}
			} else {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance();
					$log->addEntry(array('comment' => $uploadfolder));
					jexit('Upload complete');
				} else {
					return true;
				}
			}
		} else {
			return false;
		}
	}
}
