<?php
/**
 * @version $Id$
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
		kimport('kunena.upload.helper');
		$app = JFactory::getApplication ();

		if (!JRequest::checkToken()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
			return;
		}

		$file 			= JRequest::getVar( 'Filedata', '', 'files', 'array' );
		$uploadfolder	= JRequest::getVar( 'foldersmiley', 'emoticons', '', 'path' );
		$format			= JRequest::getVar( 'format', 'html', '', 'cmd');
		$view 			= JRequest::getVar( 'view', '' );

		$upload = KunenaUploadHelper::upload($file, $uploadfolder, $format, $view);
		if ( $upload ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD_SUCCESS') );
		} else {
			$app->enqueueMessage ( JText::_('COM_KUNENA_A_EMOTICONS_UPLOAD_ERROR_UNABLE') );
		}
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
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
