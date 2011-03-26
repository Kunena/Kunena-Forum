<?php
/**
 * @version $Id: kunenacategories.php 3944 2010-11-26 23:57:59Z mahagr $
 * Kunena Component - TableKunenaTopicsIcons  class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');
kimport ('kunena.error');

/**
 * Kunena TopicsIcons
 * Provides access to the #__kunena_topics_icons table
 */
class TableKunenaTopicsIcons extends KunenaTable
{
	var $id = null;
	var $name = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_topics_icons', 'id', $db );
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

		$query = "SELECT * FROM #__kunena_topics_icons WHERE id = {$this->$k}";
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

	// check for potential problems
	function check() {
		$this->name = trim($this->name);
		if (!$this->name) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_TOPICICONS_ERROR_NO_NAME' ) );
		}
		return ($this->getError () == '');
	}
