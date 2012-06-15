<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Ranks Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerRanks extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=ranks';
	}

	function add() {
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=add", false));
	}

	function edit() {
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$id = array_shift($cid);
		if (!$id) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_RANKS_SELECTED' ), 'notice' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
		} else {
			$this->setRedirect(KunenaRoute::_($this->baseurl."&layout=add&id={$id}", false));
		}
	}

	function save() {
		$db = JFactory::getDBO ();

		if (!JRequest::checkToken()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
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

		$this->app->enqueueMessage ( JText::_('COM_KUNENA_RANK_SAVED') );
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function rankupload() {
		if (!JRequest::checkToken()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
			return;
		}

		$file 			= JRequest::getVar( 'Filedata', '', 'files', 'array' );
		$format			= JRequest::getVar( 'format', 'html', '', 'cmd');
		$view			= JRequest::getVar( 'view', '');

		$upload = KunenaUploadHelper::upload($file, 'ranks', $format, $view);
		if ( $upload ) {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_RANKS_UPLOAD_SUCCESS') );
		} else {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_A_RANKS_UPLOAD_ERROR_UNABLE') );
		}
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function delete() {
		$db = JFactory::getDBO ();

		if (!JRequest::checkToken()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
			return;
		}

		$cids = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$cids = implode ( ',', $cids );
		if ($cids) {
			$db->setQuery ( "DELETE FROM #__kunena_ranks WHERE rank_id IN ($cids)" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}

		$this->app->enqueueMessage (JText::_('COM_KUNENA_RANK_DELETED') );
		$this->app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}
}
