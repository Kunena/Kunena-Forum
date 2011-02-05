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

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');

/**
 * Kunena Messages
 * Provides access to the #__kunena_messages table
 */
class TableKunenaMessages extends KunenaTable
{
	var $id = null;
	var $parent = null;
	var $thread = null;
	var $catid = null;
	var $name = null;
	var $userid = null;
	var $email = null;
	var $subject = null;
	var $time = null;
	var $ip = null;
	var $topic_emoticon = null;
	var $locked = null;
	var $hold = null;
	var $ordering = null;
	var $hits = null;
	var $moved = null;
	var $modified_by = null;
	var $modified_time = null;
	var $modified_reason = null;
	var $params = null;
	var $message = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_messages', 'id', $db );
	}

	function reset() {
		parent::reset();
		$this->message = null;
	}

	function load($id = null)
	{
		$this->_exists = false;
		$k = $this->_tbl_key;
		// Get the id to load.
		if ($id !== null) {
			$this->$k = $id;
		}

		// Reset the table.
		$this->reset();

		// Check for a valid id to load.
		if ($this->$k === null || intval($this->$k) < 1) {
			$this->$k = 0;
			return false;
		}

		// Load the user data.
		$query = "SELECT m.*, t.message FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.id = {$this->$k}";
		$this->_db->setQuery($query);
		$data = $this->_db->loadAssoc();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if(!$data)
		{
			$this->$k = 0;
			return false;
		}
		$this->_exists = true;

		// Bind the data to the table.
		$this->bind($data);
		return $this->_exists;
	}

	function check() {
		$this->subject = trim($this->subject);
		if (!$this->subject) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_SUBJECT' ) );
		}
		$this->message = trim($this->message);
		if (!$this->message) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE' ) );
		}
		// Do not allow no posting date or dates from the future
		$now = JFactory::getDate()->toUnix();
		if (!$this->time || $this->time > $now) {
			$this->time = $now;
		}
		if ($this->modified_time > $now) {
			$this->modified_time = $now;
		}
		$this->modified_reason = trim($this->modified_reason);
		return ($this->getError () == '');
	}

	function store() {
		$k = $this->_tbl_key;
		$update = $this->_exists;
		$message = $this->message;
		unset($this->message);
		if (!parent::store())
			return false;
		$this->message = $message;

		if ($update) {
			$query = "UPDATE #__kunena_messages_text SET message={$this->_db->quote($this->message)} WHERE mesid = {$this->$k}";
		} else {
			$query = "INSERT INTO #__kunena_messages_text (mesid, message) VALUES ({$this->$k}, {$this->_db->quote($this->message)})";
		}
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}