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

kimport ( 'kunena.model' );

/**
 * Announcement Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelAnnouncement extends KunenaModel {
	public $announcement = null;
	public $announcements = null;
	public $canEdit = false;

	public function __construct() {
		parent::__construct();
		$this->db = JFactory::getDBO ();
		$this->app = JFactory::getApplication ();
	}

	protected function populateState() {
		$id = $this->getInt ( 'id', 0 );
		$this->setState ( 'item.id', $id );

		$value = $this->getInt ( 'limit', 0 );
		if ($value < 1) $value = 5;
		$this->setState ( 'list.limit', $value );

		$value = $this->getInt ( 'limitstart', 0 );
		if ($value < 0) $value = 0;
		$this->setState ( 'list.start', $value );
	}

	function getNewAnnouncement() {
		$this->announcement = new stdClass();
		$this->announcement->id = 0;
		$this->announcement->title = '';
		$this->announcement->description = '';
		$this->announcement->sdescription = '';
		$this->announcement->created = '';
		$this->announcement->published = 1;
		$this->announcement->showdate = 1;
		return $this->announcement;
	}

	function getCanEdit() {
		$me = KunenaFactory::getUser ();
		$config = KunenaFactory::getConfig();
		$annmods = explode ( ',', $config->annmodid );
		if ($me->exists() && (in_array ( $me->userid, $annmods ) || $me->isAdmin ())) {
			$this->canEdit = true;
		} else {
			$this->canEdit = false;
		}
		return $this->canEdit;
	}

	function edit() {
		$now = new JDate();
		$title = JRequest::getString ( "title", "" );
		$description = JRequest::getVar ( 'description', '', 'string', JREQUEST_ALLOWRAW );
		$sdescription = JRequest::getVar ( 'sdescription', '', 'string', JREQUEST_ALLOWRAW );
		$created = JRequest::getString ( "created", $now->toMysql() );
		if (!$created) $created = $now->toMysql();
		$published = JRequest::getInt ( "published", 1 );
		$showdate = JRequest::getInt ( "showdate", 1 );

		$id = $this->getState ( 'item.id' );
		if (!$id) {
			$query = "INSERT INTO #__kunena_announcement VALUES ('',
				{$this->db->Quote ( $title )},
				{$this->db->Quote ( $sdescription )},
				{$this->db->Quote ( $description )},
				{$this->db->Quote ( $created )},
				{$this->db->Quote ( $published )},
				0,
				{$this->db->Quote ( $showdate )})";
		} else {
			$query = "UPDATE #__kunena_announcement SET title={$this->db->Quote ( $title )},
				description={$this->db->Quote ( $description )},
				sdescription={$this->db->Quote ( $sdescription )},
				created={$this->db->Quote ( $created )},
				published={$this->db->Quote ( $published )},
				showdate={$this->db->Quote ( $showdate )}
				WHERE id=$id";
		}
		$this->db->setQuery ( $query );
		$this->db->query ();
		KunenaError::checkDatabaseError();
	}

	function delete() {
		$id = $this->getState ( 'item.id' );
		$query = "DELETE FROM #__kunena_announcement WHERE id={$this->db->Quote ($id)} ";
		$this->db->setQuery ( $query );
		$this->db->query ();
		KunenaError::checkDatabaseError();
	}

	function getAnnouncement() {
		$id = $this->getState ( 'item.id' );
		if (! $id) {
			$query = "SELECT * FROM #__kunena_announcement WHERE published='1' ORDER BY created DESC";
		} else {
			$query = "SELECT * FROM #__kunena_announcement WHERE id={$this->db->Quote($id)}" . ($this->getCanEdit() ? '': " AND published='1'");
		}
		$this->db->setQuery ( $query, 0, 1 );
		$this->announcement = $this->db->loadObject ();
		if (!$this->announcement) $this->getNewAnnouncement();
		KunenaError::checkDatabaseError();

		return $this->announcement;
	}

	function getAnnouncements() {
		$query = "SELECT * FROM #__kunena_announcement ORDER BY created DESC";
		$this->db->setQuery ( $query, $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		$this->announcements = $this->db->loadObjectList ();
		KunenaError::checkDatabaseError();

		return $this->announcements;
	}
}