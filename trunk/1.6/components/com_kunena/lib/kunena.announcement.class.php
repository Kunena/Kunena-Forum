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

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

class CKunenaAnnouncement {
	public $id = null;
	public $title = null;
	public $description = null;
	public $sdescription = null;
	public $created = null;
	public $published = 1;
	public $showdate = 1;
	public $announcement = null;
	public $canEdit = false;

	function __construct() {
		$this->my = JFactory::getUser ();
		$this->db = JFactory::getDBO ();
		$this->config = KunenaFactory::getConfig ();
		$this->app = JFactory::getApplication ();

		$annmods = @explode ( ',', $this->config->annmodid );
		if (in_array ( $this->my->id, $annmods ) || CKunenaTools::isAdmin ()) {
			$this->canEdit = true;
		} else {
			$this->canEdit = false;
		}

		$this->announcement = new stdClass();
		$this->announcement->id = 0;
		$this->announcement->title = '';
		$this->announcement->description = '';
		$this->announcement->sdescription = '';
		$this->announcement->created = '';
		$this->announcement->published = 1;
		$this->announcement->showdate = 1;
	}

	public function &getInstance() {
		static $instance = NULL;
		if (! $instance) {
			$instance = new CKunenaAnnouncement ();
		}
		return $instance;
	}

	function edit($id) {
		if (! $this->canEdit) {
			$this->app->redirect ( CKunenaLink::GetKunenaURL ( false ), JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ) );
		}
		if ($this->tokenProtection ())
			return false;

		$now = new JDate();
		$title = JRequest::getVar ( "title", "" );
		$description = JRequest::getVar ( 'description', '', 'string', JREQUEST_ALLOWRAW );
		$sdescription = JRequest::getVar ( 'sdescription', '', 'string', JREQUEST_ALLOWRAW );
		$created = JRequest::getVar ( "created", $now->toMysql() );
		if (!$created) $created = $now->toMysql();
		$published = JRequest::getInt ( "published", 1 );
		$showdate = JRequest::getInt ( "showdate", 1 );

		if (!$id) {
			$query = "INSERT INTO #__kunena_announcement VALUES ('',
				{$this->db->Quote ( $title )},
				{$this->db->Quote ( $sdescription )},
				{$this->db->Quote ( $description )},
				{$this->db->Quote ( $created )},
				{$this->db->Quote ( $published )},
				0,
				{$this->db->Quote ( $showdate )})";
			$msg = JText::_ ( 'COM_KUNENA_ANN_SUCCESS_ADD' );
		} else {
			$query = "UPDATE #__kunena_announcement SET title={$this->db->Quote ( $title )},
				description={$this->db->Quote ( $description )},
				sdescription={$this->db->Quote ( $sdescription )},
				created={$this->db->Quote ( $created )},
				published={$this->db->Quote ( $published )},
				showdate={$this->db->Quote ( $showdate )}
				WHERE id=$id";
			$msg = JText::_ ( 'COM_KUNENA_ANN_SUCCESS_EDIT' );
		}
		$this->db->setQuery ( $query );
		if ($this->db->query ()) {
			$this->app->redirect ( CKunenaLink::GetAnnouncementURL ( 'show', null, false ), $msg );
		}
		if (KunenaError::checkDatabaseError()) return;
	}

	function delete($id) {
		if (! $this->canEdit) {
			$this->app->redirect ( CKunenaLink::GetKunenaURL ( false ), JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ) );
		}
		$query = "DELETE FROM #__kunena_announcement WHERE id={$this->db->Quote ($id)} ";
		$this->db->setQuery ( $query );
		$this->db->query ();
		if (KunenaError::checkDatabaseError()) return;

		$this->app->redirect ( CKunenaLink::GetAnnouncementURL ( 'show', null, false ), JText::_ ( 'COM_KUNENA_ANN_DELETED' ) );
	}

	function getAnnouncement($id = 0, $mode=0) {
		if ($mode) $published = "AND published='1'";
		else $published = "";
		if (! $id) {
			$query = "SELECT * FROM #__kunena_announcement WHERE published='1' ORDER BY created DESC";
		} else {
			$query = "SELECT * FROM #__kunena_announcement WHERE id={$this->db->Quote($id)} $published";
		}
		$this->db->setQuery ( $query, 0, 1 );
		$announcement = $this->db->loadObject ();
		if (KunenaError::checkDatabaseError()) return;
		if (! $announcement) {
			return;
		}

		// TODO: deprecated
		$this->id = $announcement->id;
		$this->title = KunenaParser::parseText ( $announcement->title );
		$this->sdescription = KunenaParser::parseBBCode ( $announcement->sdescription );
		$this->description = KunenaParser::parseBBCode ( $announcement->description );
		$this->created = $announcement->created;
		$this->published = $announcement->published;
		$this->showdate = $announcement->showdate;
		// end deprecated

		$this->announcement = $announcement;
	}

	function getAnnouncements($start, $limit) {
		$query = "SELECT * FROM #__kunena_announcement ORDER BY created DESC";
		$this->db->setQuery ( $query, $start, $limit );
		$this->announcements = $this->db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;
		if (empty ( $this->announcement )) {
			return;
		}
	}

	function displayBox() {
		if ($this->config->showannouncement && $this->id) {
			CKunenaTools::loadTemplate ( '/announcement/box.php' );
		}
	}

	function display() {
		if (! $this->config->showannouncement) {
			return;
		}
		$do = JRequest::getVar ( "do", "" );
		$id = intval ( JRequest::getVar ( "id", "" ) );

		switch ($do) {
			case 'read' :
				$this->getAnnouncement ( $id, 1 );
				CKunenaTools::loadTemplate ( '/announcement/read.php' );
				break;
			case 'show' :
				$this->getAnnouncements ( 0, 5 );
				CKunenaTools::loadTemplate ( '/announcement/show.php' );
				break;
			case 'edit' :
				if (!$this->canEdit) {
					$this->app->redirect ( CKunenaLink::GetKunenaURL( false ), JText::_( 'COM_KUNENA_POST_NOT_MODERATOR' ));
					return;
				}
				$this->getAnnouncement ( $id, 0 );
			// Continue
			case 'add' :
				if (!$this->canEdit) {
					$this->app->redirect(CKunenaLink::GetKunenaURL(false), JText::_('COM_KUNENA_POST_NOT_MODERATOR'));
					return;
				}
				CKunenaTools::loadTemplate ( '/announcement/edit.php' );
				break;
			case 'delete' :
				$this->delete ( $id );
				break;
			case 'doedit' :
				$this->edit ( $id );
				break;
			default :
				$this->getAnnouncements(0, 5);
				CKunenaTools::loadTemplate ( '/announcement/show.php' );
		}
	}

	function escape($var) {
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	function tokenProtection() {
		// get the token put in the message form to check that the form has been valided successfully
		if (JRequest::checkToken () == false) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return true;
		}
		return false;
	}
}