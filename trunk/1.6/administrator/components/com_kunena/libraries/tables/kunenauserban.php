<?php
/**
* @version $Id$
* Kunena Component - CKunenaUser class
* @package Kunena
*
* @Copyright (C) 2008-2011 www.kunena.org All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once(dirname(__FILE__).'/kunena.php');

/**
* Kunena User Table
* Provides access to the #__kunena_users_banlist table
*/
class TableKunenaUserBan extends JTable
{
	var $id = null;
	var $userid = null;
	var $ip = null;
	var $blocked = null;
	var $expiration = null;
	var $created_by = null;
	var $created_time = null;
	var $reason_private = null;
	var $reason_public = null;
	var $modified_by = null;
	var $modified_time = null;
	var $comments = null;
	var $params = null;

	const ANY = 0;
	const ACTIVE = 1;

	function __construct($db) {
		parent::__construct('#__kunena_users_banned', 'id', $db);
	}

	public function loadByUserid($userid, $mode = self::ACTIVE) {
		// Reset the table.
		$k = $this->_tbl_key;
		$this->$k = 0;
		$this->reset();

		// Check for a valid id to load.
		if ($userid === null || intval($userid) < 1) {
			return false;
		}

		$now = new JDate();
		// Load the user data.
		$query = "SELECT * FROM {$this->_tbl}
			WHERE userid = {$this->_db->quote($userid)}
			" . ($mode == self::ACTIVE ? "AND (expiration = {$this->_db->quote($this->_db->getNullDate())} OR expiration > {$this->_db->quote($now->toMysql())})": '') . "
			ORDER BY id DESC";
		$this->_db->setQuery($query, 0, 1);
		$data = $this->_db->loadAssoc();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (empty($data)) {
			$this->userid = $userid;
			return false;
		}

		// Bind the data to the table.
		$this->bind($data);
		return true;
	}

	public function loadByIP($ip, $mode = self::ACTIVE)
	{
		// Reset the table.
		$k = $this->_tbl_key;
		$this->$k = 0;
		$this->reset();

		// Check for a valid id to load.
		if ($ip === null || !is_string($ip)) {
			return false;
		}

		$now = new JDate();
		// Load the user data.
		$query = "SELECT * FROM {$this->_tbl}
			WHERE ip = {$this->_db->quote($ip)}
			" . ($mode == self::ACTIVE ? "AND (expiration = {$this->_db->quote($this->_db->getNullDate())} OR expiration > {$this->_db->quote($now->toMysql())})": '') . "
			ORDER BY id DESC";
		$this->_db->setQuery($query, 0, 1);
		$data = $this->_db->loadAssoc();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (empty($data)) {
			$this->ip = $ip;
			return false;
		}

		// Bind the data to the table.
		$this->bind($data);
		return true;
	}

	public function check() {
		if (!$this->userid && !$this->ip) {
			// TODO: error
			return false;
		}

		return true;
	}

	public function bind($data, $ignore=array()) {
		if (isset($data['comments'])) $data['comments'] = !is_string($data['comments']) ? json_encode($data['comments']) : $data['comments'];
		if (isset($data['params'])) $data['params'] = !is_string($data['params']) ? json_encode($data['params']) : $data['params'];
		parent::bind($data, $ignore);
	}
}
