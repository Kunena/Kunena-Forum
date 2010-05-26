<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

class CKunenaUserlist {
	public $allow = false;

	function __construct() {
		$this->app = JFactory::getApplication ();
		$this->config = KunenaFactory::getConfig ();
		$this->db = JFactory::getDBO ();

		$this->search = JRequest::getVar ( 'search', '' );
		$this->limitstart = JRequest::getInt ( 'limitstart', 0 );
		$this->limit = JRequest::getInt ( 'limit', (int)$this->config->userlist_rows );

		jimport ( 'joomla.html.pagination' );

		$filter_order = $this->app->getUserStateFromRequest ( 'kunena.userlist.filter_order', 'filter_order', 'registerDate', 'cmd' );
		$filter_order_dir = $this->app->getUserStateFromRequest ( 'kunena.userlist.filter_order_dir', 'filter_order_Dir', 'asc', 'word' );
		$order = JRequest::getVar ( 'order', '' );
		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_dir;

		// Total
		$this->db->setQuery ( "SELECT COUNT(*) FROM #__users WHERE block=0" );
		$this->total = $this->db->loadResult ();
		KunenaError::checkDatabaseError();

		// Search total
		$query = "SELECT COUNT(*) FROM #__users AS u INNER JOIN #__kunena_users AS fu ON u.id=fu.userid";
		if ($this->search != "") {
			$query .= " WHERE (u.name LIKE '%{$this->search}%' OR u.username LIKE '%{$this->search}%')";
		}

		$this->db->setQuery ( $query );
		$total = $this->db->loadResult ();
		KunenaError::checkDatabaseError();
		if ($this->limit > $total) {
			$this->limitstart = 0;
		}

		// Select query
		$query = "SELECT u.id, u.name, u.username, u.usertype, u.email, u.registerDate, u.lastvisitDate, fu.userid, fu.showOnline, fu.group_id, fu.posts, fu.karma, fu.uhits " . " FROM #__users AS u INNER JOIN #__kunena_users AS fu ON fu.userid = u.id WHERE block=0";
		$this->searchuri = "";
		if ($this->search != "") {
			$query .= " AND (name LIKE '%{$this->search}%' OR username LIKE '%{$this->search}%') AND u.id NOT IN (62)";
			$this->searchuri .= "&search=" . $this->search;
		} else {
			$query .= " AND u.id NOT IN (62)";
		}
		$query .= $orderby;
		$query .= " LIMIT $this->limitstart, $this->limit";

		$this->db->setQuery ( $query );
		$this->users = $this->db->loadObjectList ();
		KunenaError::checkDatabaseError();

		// table ordering
		$this->order_dir = $filter_order_dir;
		$this->order = $filter_order;

		if ($this->search == "") {
			$this->search = JText::_('COM_KUNENA_USRL_SEARCH');
		}
		$this->pageNav = new JPagination ( $total, $this->limitstart, $this->limit );

		$this->allow = true;
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			require_once (KUNENA_PATH_LIB .DS. 'kunena.who.class.php');
			$online =& CKunenaWhoIsOnline::getInstance();
			$online->displayWhoIsOnline();
		}
	}

	function displayForumJump() {
		if ($this->config->enableforumjump) {
			CKunenaTools::loadTemplate('/forumjump.php');
		}
	}

	function display() {
		if (! $this->allow) {
			echo JText::_('COM_KUNENA_NO_ACCESS');
			return;
		}
		CKunenaTools::loadTemplate('/userlist/userlist.php');
	}
}