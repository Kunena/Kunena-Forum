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
 * Kunena Ranks Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerRanks extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=ranks';
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
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_RANKS_SELECTED' ), 'notice' );
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

		$rank_title = JRequest::getVar ( 'rank_title' );
		$rank_image = JRequest::getVar ( 'rank_image' );
		$rank_special = JRequest::getVar ( 'rank_special' );
		$rank_min = JRequest::getVar ( 'rank_min' );
		$rankid = JRequest::getInt( 'rankid', 0 );



		if ( !$rankid ) {
			$db->setQuery ( "INSERT INTO #__kunena_ranks SET rank_title = '$rank_title', rank_image = '$rank_image', rank_special = '$rank_special', rank_min = '$rank_min'" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		} else {
			$db->setQuery ( "UPDATE #__kunena_ranks SET rank_title = '$rank_title', rank_image = '$rank_image', rank_special = '$rank_special', rank_min = '$rank_min' WHERE rank_id = '$rankid'" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}

		$app->enqueueMessage ( JText::_('COM_KUNENA_RANK_SAVED') );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function rankupload() {
		kimport('kunena.upload.helper');
		$app = JFactory::getApplication ();

		if (!JRequest::checkToken()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
			return;
		}

		$file 			= JRequest::getVar( 'Filedata', '', 'files', 'array' );
		$uploadfolder	= JRequest::getVar( 'folderranks', 'ranks', '', 'path' );
		$format			= JRequest::getVar( 'format', 'html', '', 'cmd');
		$view			= JRequest::getVar( 'view', '');

		$upload = KunenaUploadHelper::upload($file, $uploadfolder, $format, $view);
		if ( $upload ) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_A_RANKS_UPLOAD_SUCCESS') );
		} else {
			$app->enqueueMessage ( JText::_('COM_KUNENA_A_RANKS_UPLOAD_ERROR_UNABLE') );
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
			$db->setQuery ( "DELETE FROM #__kunena_ranks WHERE rank_id IN ($cids)" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}

		$app->enqueueMessage (JText::_('COM_KUNENA_RANK_DELETED') );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}
}
