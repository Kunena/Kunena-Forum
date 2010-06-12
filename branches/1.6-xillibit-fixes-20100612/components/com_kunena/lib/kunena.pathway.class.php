<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

class CkunenaPathway {
	protected $_db;
	public $config;
	public $document;

	function __construct() {
		$this->_db = JFactory::getDBO ();
		$this->config = CKunenaConfig::getInstance ();
		$this->document = & JFactory::getDocument ();
	}

	private function _getOnlineUsers($sfunc = '') {
		$queryName = $this->config->username ? "username" : "name";
		$query = "SELECT w.userid, w.func, u.$queryName AS username, k.showOnline FROM #__kunena_whoisonline AS w
				LEFT JOIN #__users AS u ON u.id=w.userid
				LEFT JOIN #__kunena_users AS k ON k.userid=w.userid
				WHERE w.link LIKE '%" . $this->_db->getEscaped ( JURI::current () ) . "%' AND w.func LIKE '%$sfunc%'
				GROUP BY w.userid ORDER BY u.{$queryName} ASC";
		$this->_db->setQuery ( $query );
		$users = $this->_db->loadObjectList ();
		KunenaError::checkDatabaseError();

		return $users;
	}

	public function getMessagesTitles($id) {
		$sql = "SELECT subject, id FROM #__kunena_messages WHERE id='{$id}'";
		$this->_db->setQuery ( $sql );
		$kunena_topic_title = KunenaParser::parseText ($this->_db->loadResult () );
		KunenaError::checkDatabaseError();

		return $kunena_topic_title;
	}

	public function getCatsDetails($catids) {
		$query = "SELECT * FROM #__kunena_categories WHERE id='{$catids}' AND published='1'";
		$this->_db->setQuery ( $query );
		$results = $this->_db->loadObject ();
		KunenaError::checkDatabaseError();

		return $results;
	}

	public function getTotalViewing($sfunc) {
		$users = $this->_getOnlineUsers($sfunc);

		return $total_viewing = count ( $users );
	}

	public function getUsersOnlineList($sfunc) {
		$users = $this->_getOnlineUsers($sfunc);

		$onlineUsersList = '';
		$totalguest = 0;
		$divider = ', ';
		$lastone = end ( $users );
		foreach ( $users as $user ) {
			if ($user->userid != 0) {
				if ($user == $lastone && ! $totalguest) {
					$divider = '';
				}
				if ($user->showOnline > 0) {
					$onlineUsersList = CKunenaLink::GetProfileLink ( $user->userid, $user->username ) . $divider;
				}
			} else {
				$totalguest = $totalguest + 1;
			}
		}
		if ($totalguest > 0) {
			if ($totalguest == 1) {
				$onlineUsersList .= '(' . $totalguest . ') ' . JText::_('COM_KUNENA_WHO_ONLINE_GUEST');
			} else {
				$onlineUsersList .= '(' . $totalguest . ') ' . JText::_('COM_KUNENA_WHO_ONLINE_GUESTS');
			}
		}

		return $onlineUsersList;
	}
}