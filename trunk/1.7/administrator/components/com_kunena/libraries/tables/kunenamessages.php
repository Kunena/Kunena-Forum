<?php
/**
 * @version $Id$
 * Kunena Component - TableKunenaMessages class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');

/**
 * Kunena Messages
 * Provides access to the #__kunena_topics table
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

	function __construct($db) {
		parent::__construct ( '#__kunena_messages', 'id', $db );
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
		$query = "SELECT * FROM #__kunena_messages WHERE id = {$this->$k}";
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
}