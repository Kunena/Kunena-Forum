<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena Messages
 * Provides access to the #__kunena_messages table
 */
class TableKunenaMessages extends KunenaTable {
	public $id = null;
	public $parent = null;
	public $thread = null;
	public $catid = null;
	public $name = null;
	public $userid = null;
	public $email = null;
	public $subject = null;
	public $time = null;
	public $ip = null;
	public $topic_emoticon = null;
	public $locked = null;
	public $hold = null;
	public $ordering = null;
	public $hits = null;
	public $moved = null;
	public $modified_by = null;
	public $modified_time = null;
	public $modified_reason = null;
	public $params = null;
	public $message = null;

	public function __construct($db) {
		parent::__construct ( '#__kunena_messages', 'id', $db );
	}

	public function reset() {
		parent::reset();
		$this->message = null;
	}

	public function load($id = null, $reset = true)
	{
		$this->_exists = false;
		$k = $this->_tbl_key;
		// Get the id to load.
		if ($id !== null) {
			$this->$k = $id;
		}

		// Reset the table.
		if ($reset) $this->reset();

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

	public function check() {
		$category = KunenaForumCategoryHelper::get($this->catid);
		if (!$category->exists()) {
			// TODO: maybe we should have own error message? or not?
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_TOPICS_ERROR_CATEGORY_INVALID', $this->catid ) );
		} else {
			$this->catid = $category->id;
		}
		$this->subject = trim($this->subject);
		if (!$this->subject) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_SUBJECT' ) );
		}
		$this->message = trim($this->message);
		if (!$this->message) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE' ) );
		}
		if (!$this->time) {
			$this->time = JFactory::getDate()->toUnix();
		}
		$this->modified_reason = trim($this->modified_reason);
		return ($this->getError () == '');
	}

	/**
	 * @param boolean $updateNulls has no effect.
	 * @return bool
	 * @see KunenaTable::store()
	 */
	public function store($updateNulls = false) {
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
