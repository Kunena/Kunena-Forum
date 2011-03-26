<?php
/**
 * @version $Id: topicicons.php 4488 2011-02-24 09:41:43Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );

/**
 * Kunena Topicicons Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerTopicicons extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=topicicons';
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
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_TOPICICON_SELECTED' ), 'notice' );
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

		$iconname = JRequest::getString ( 'topiciconname' );
		$filename = JRequest::getString ( 'topiciconslist' );
		$published = JRequest::getInt ( 'published' );
		$ordering = JRequest::getInt ( 'ordering', 0 );
		$topiciconid = JRequest::getInt( 'topiciconid', 0 );

		if ( !$topiciconid ) {
			$db->setQuery ( "INSERT INTO #__kunena_topics_icons SET name = '$iconname', filename = '$filename', published = '$published', ordering ='$ordering'" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		} else {
			$db->setQuery ( "UPDATE #__kunena_topics_icons SET name = '$iconname', filename = '$filename', published = '$published', ordering ='$ordering' WHERE id = '$topiciconid'" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}

		$app->enqueueMessage ( JText::_('COM_KUNENA_TOPICICON_SAVED') );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function orderup() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->orderUpDown ( array_shift($cid), -1 );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function orderdown() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->orderUpDown ( array_shift($cid), 1 );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	protected function orderUpDown($id, $direction) {
		$lang = JFactory::getLanguage();
		$lang->load('com_kunena', JPATH_ADMINISTRATOR);

		if (!$id) return;

		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$db = JFactory::getDBO ();
		$row = new TableKunenaTopicsIcons ( $db );
		$row->load ( $id );

		// Ensure that we have the right ordering
		$row->reorder ( );
		$row->move ( $direction );
	}

	function publish() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'published', 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
	function unpublish() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'published', 0);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	protected function setVariable($cid, $variable, $value) {
		$lang = JFactory::getLanguage();
		$lang->load('com_kunena', JPATH_ADMINISTRATOR);

		$app = JFactory::getApplication ();
		$db = JFactory::getDBO ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$id = array_shift($cid);
		if (empty ( $id )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_TOPICICON_SELECTED' ), 'notice' );
			return;
		}

		$db->setQuery ( "UPDATE #__kunena_topics_icons SET published = '$value' WHERE id='$id'" );
		$db->query ();
		if (KunenaError::checkDatabaseError()) return;

		if ( $value ) $status = JText::_ ( 'COM_KUNENA_A_TOPICICON_PUBLISHED' );
		else $status = JText::_ ( 'COM_KUNENA_A_TOPICICON_UNPUBLISHED' );

		$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_TOPICICON', ' '.$status ) );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function bydefault() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setDefault($cid, 1);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function notbydefault() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setDefault($cid, 0);
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	protected function setDefault($cid, $value) {
		$app = JFactory::getApplication ();
		$db = JFactory::getDBO ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$id = array_shift($cid);
		if (empty ( $id )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_TOPICICON_SELECTED' ), 'notice' );
			return;
		}

		$defaultexist = 0;
		if ($value == 1) {
			$query = "SELECT isdefault FROM #__kunena_topics_icons WHERE isdefault='1'";
			$db->setQuery ( $query );
			$defaultexist = $db->loadResult();
			if (KunenaError::checkDatabaseError()) return;
		}

		if ( $defaultexist == 1) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_TOPICICON_ALREADY_DEFAULT' ) );
		} else {
			$db->setQuery ( "UPDATE #__kunena_topics_icons SET isdefault = '$value' WHERE id='$id'" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;

			if ( $value ) $status = JText::_ ( 'COM_KUNENA_A_TOPICICON_DEFAULT' );
			else $status = JText::_ ( 'COM_KUNENA_A_TOPICICON_NOTDEFAULT' );

			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_TOPICICON', ' '.$status ) );
		}

		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function topiciconupload() {
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
		$foldertopicicons	= JRequest::getVar( 'foldertopicicons', 'emoticons', '', 'path' );
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
			$filepathtopicicon = JPath::clean(COM_KUNENA_MEDIA_BASE.'/'.$foldertopicicons.'/'.strtolower($file['name']));

			if (!MediaHelper::canUpload( $file, $err )) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Invalid: '.$filepathtopicicon.': '.$err));
					header('HTTP/1.0 415 Unsupported Media Type');
					jexit('Error. Unsupported Media Type!');
				} else {
					JError::raiseNotice(100, JText::_($err));
					$app->redirect ( KunenaRoute::_($this->baseurl, false) );

					return;
				}
			}

			if (JFile::exists($filepathtopicicon)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'File already exists: '.$filepathtopicicon));
					header('HTTP/1.0 409 Conflict');
					jexit('Error. File already exists');
				} else {
					JError::raiseNotice(100, JText::_('COM_KUNENA_A_TOPICON_UPLOAD_ERROR_EXIST'));
					$app->redirect ( KunenaRoute::_($this->baseurl, false) );

					return;
				}
			}

			if (!JFile::upload($file['tmp_name'], $filepathtopicicon)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Cannot upload: '.$filepathtopicicon));
					header('HTTP/1.0 400 Bad Request');
					jexit('Error. Unable to upload file');
				} else {
					JError::raiseWarning(100, JText::_('COM_KUNENA_A_TOPICON_UPLOAD_ERROR_UNABLE'));
					$app->redirect ( KunenaRoute::_($this->baseurl, false) );

					return;
				}
			} else {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = &JLog::getInstance();
					$log->addEntry(array('comment' => $foldertopicicons));
					jexit('Upload complete');
				} else {
					$app->enqueueMessage(JText::_('COM_KUNENA_A_TOPICON_UPLOAD_SUCCESS'));
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
			$db->setQuery ( "DELETE FROM #__kunena_topics_icons WHERE id IN ($cids)" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}

		$app->enqueueMessage (JText::_('COM_KUNENA_TOPICICONS_DELETED') );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function saveorder() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$order = JRequest::getVar ( 'order', array (), 'post', 'array' );

		if (empty ( $cid )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_TOPICICONS_SELECTED' ), 'notice' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$success = false;

		// load topicicons
		foreach ( $topicicons as $icon ) {
			if (! isset ( $order [$icon->id] ) || $icon->ordering == $order [$icon->id])
				continue;

				$db = JFactory::getDBO ();
				$db->setQuery ( "UPDATE #__kunena_topics_icons SET ordering='{$order [$icon->id]}' WHERE id='{$icon->id}'" );
				$sections = $db->Query ();
				KunenaError::checkDatabaseError ();
				$success = true;
		}

		if ($success) {
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_NEW_ORDERING_SAVED' ) );
		} else {
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_ORDERING_SAVE_FAILED' ) );
		}
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}
}