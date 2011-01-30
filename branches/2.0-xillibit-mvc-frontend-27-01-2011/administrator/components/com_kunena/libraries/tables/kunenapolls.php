<?php
/**
 * @version $Id: kunenatopics.php 3944 2010-11-26 23:57:59Z mahagr $
 * Kunena Component - TableKunenaPolls class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');
/**
 * Kunena Polls
 * Provides access to the #__kunena_polls table
 */
class TableKunenaPolls extends KunenaTable
{
	var $id = null;
	var $title = null;
	var $threadid = null;
	var $polltimetolive = null;


	function __construct($db) {
		parent::__construct ( '#__kunena_polls', 'id', $db );
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
		$query = "SELECT * FROM #__kunena_polls WHERE id = {$this->$k}";
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