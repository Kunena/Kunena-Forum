<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once(__DIR__ . '/kunena.php');

/**
* Kunena User Bans
* Provides access to the #__kunena_users_banned table
*/
class TableKunenaUserBans extends JTable
{
	public $id = null;
	public $userid = null;
	public $ip = null;
	public $blocked = null;
	public $expiration = null;
	public $created_by = null;
	public $created_time = null;
	public $reason_private = null;
	public $reason_public = null;
	public $modified_by = null;
	public $modified_time = null;
	public $comments = null;
	public $params = null;

	const ANY = 0;
	const ACTIVE = 1;

	public function __construct($db)
	{
		parent::__construct('#__kunena_users_banned', 'id', $db);
	}

	public function loadByUserid($userid, $mode = self::ACTIVE)
	{
		// Reset the table.
		$k = $this->_tbl_key;
		$this->$k = 0;
		$this->reset();

		// Check for a valid id to load.
		if ($userid === null || intval($userid) < 1)
		{
			return false;
		}

		$now = new JDate();
		// Load the user data.
		$query = "SELECT * FROM {$this->_tbl}
			WHERE userid = {$this->_db->quote($userid)}
			" . ($mode == self::ACTIVE ? "AND (expiration = {$this->_db->quote($this->_db->getNullDate())} OR expiration > {$this->_db->quote($now->toSql())})": '') . "
			ORDER BY id DESC";
		$this->_db->setQuery($query, 0, 1);
		$data = $this->_db->loadAssoc();

		// Check for an error message.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		if (empty($data))
		{
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
			" . ($mode == self::ACTIVE ? "AND (expiration = {$this->_db->quote($this->_db->getNullDate())} OR expiration > {$this->_db->quote($now->toSql())})": '') . "
			ORDER BY id DESC";
		$this->_db->setQuery($query, 0, 1);
		$data = $this->_db->loadAssoc();

		// Check for an error message.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		if (empty($data))
		{
			$this->ip = $ip;

			return false;
		}

		// Bind the data to the table.
		$this->bind($data);

		return true;
	}

	public function check()
	{
		if (!$this->ip)
		{
			$user = KunenaUserHelper::get($this->userid);
			if (!$user->exists())
			{
				$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_USERBANS_ERROR_USER_INVALID', (int) $user->userid ) );
			}
		}

		return ($this->getError () == '');
	}

	public function bind($data, $ignore=array())
	{
		if (isset($data['comments']))
		{
			$data['comments'] = !is_string($data['comments']) ? json_encode($data['comments']) : $data['comments'];
		}

		if (isset($data['params']))
		{
			$data['params'] = !is_string($data['params']) ? json_encode($data['params']) : $data['params'];
		}

		parent::bind($data, $ignore);
	}
}
