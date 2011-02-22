<?php
/**
 * @version $Id: kunenacategories.php 4220 2011-01-18 09:13:04Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
kimport ( 'kunena.error' );

/**
 * Kunena Smileys Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerSmilies extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=smilies';
	}

	function add() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=add", false));
	}

	function edit() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$id = array_shift($cid);
		if (!$id) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_SMILEYS_SELECTED' ), 'notice' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		} else {
			$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=add&id={$id}", false));
		}
	}

	function save() {
		$app = JFactory::getApplication ();
		$db = JFactory::getDBO ();
		if (!JRequest::checkToken()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
			return;
		}

		$smiley_code = JRequest::getString ( 'smiley_code' );
		$smiley_location = JRequest::getVar ( 'smiley_url' );
		$smiley_emoticonbar = JRequest::getInt ( 'smiley_emoticonbar', 0 );
		$smileyid = JRequest::getInt( 'smileyid', 0 );

		if ( !$smileyid ) {
			$db->setQuery ( "INSERT INTO #__kunena_smileys SET code = '$smiley_code', location = '$smiley_location', emoticonbar = '$smiley_emoticonbar'" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		} else {
			$db->setQuery ( "UPDATE #__kunena_smileys SET code = '$smiley_code', location = '$smiley_location', emoticonbar = '$smiley_emoticonbar' WHERE id = '$smileyid'" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}

		$app->enqueueMessage ( JText::_('COM_KUNENA_SMILEY_SAVED') );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function smileyupload() {
		$config = KunenaFactory::getConfig ();
		$app = JFactory::getApplication ();
		// load language fo component media
		JPlugin::loadLanguage( 'com_media' );
		$params = JComponentHelper::getParams('com_media');
		require_once( JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php' );
		define('COM_KUNENA_MEDIA_BASE', JPATH_ROOT.'/components/com_kunena/template/'.$config->template.'/images');
		// Check for request forgeries
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );

		$file 			= JRequest::getVar( 'Filedata', '', 'files', 'array' );
		$foldersmiley	= JRequest::getVar( 'foldersmiley', 'emoticons', '', 'path' );
		$format			= JRequest::getVar( 'format', 'html', '', 'cmd');
		$view 			= JRequest::getVar( 'view', '' );
		$err			= null;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name']	= JFile::makeSafe($file['name']);

		if (isset($file['name'])) {
			$filepathsmiley = JPath::clean(COM_KUNENA_MEDIA_BASE.'/'.$foldersmiley.'/'.strtolower($file['name']));

			if (!MediaHelper::canUpload( $file, $err )) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Invalid: '.$filepathsmiley.': '.$err));
					header('HTTP/1.0 415 Unsupported Media Type');
					jexit('Error. Unsupported Media Type!');
				} else {
					JError::raiseNotice(100, JText::_($err));
					$app->redirect ( KunenaRoute::_($this->baseurl, false) );

					return;
				}
			}

			if (JFile::exists($filepathsmiley)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'File already exists: '.$filepathsmiley));
					header('HTTP/1.0 409 Conflict');
					jexit('Error. File already exists');
				} else {
					JError::raiseNotice(100, JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD_ERROR_EXIST'));
					$app->redirect ( KunenaRoute::_($this->baseurl, false) );

					return;
				}
			}

			if (!JFile::upload($file['tmp_name'], $filepathsmiley)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Cannot upload: '.$filepathsmiley));
					header('HTTP/1.0 400 Bad Request');
					jexit('Error. Unable to upload file');
				} else {
					JError::raiseWarning(100, JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD_ERROR_UNABLE'));
					$app->redirect ( KunenaRoute::_($this->baseurl, false) );

					return;
				}
			} else {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance();
					$log->addEntry(array('comment' => $foldersmiley));
					jexit('Upload complete');
				} else {
					$app->enqueueMessage(JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD_SUCCESS'));
					$app->redirect ( KunenaRoute::_($this->baseurl, false) );

					return;
				}
			}
		} else {
			JError::raiseError ( 500, JText::sprintf ( 'COM_KUNENA_INVALID_REQUEST', ucfirst ( $view ) ) );
		}
	}

	function delete() {
		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();

		if (!JRequest::checkToken()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
			return;
		}

		$cids = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$cids = implode ( ',', $cids );
		if ($cids) {
			$db->setQuery ( "DELETE FROM #__kunena_smileys WHERE id IN ($cids)" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}

		$app->enqueueMessage (JText::_('COM_KUNENA_SMILEY_DELETED') );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}
}
