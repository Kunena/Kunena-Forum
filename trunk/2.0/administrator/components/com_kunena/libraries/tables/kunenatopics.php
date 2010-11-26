<?php
/**
 * @version $Id$
 * Kunena Component - TableKunenaTopics class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');
kimport('kunena.forum.category.helper');
/**
 * Kunena Topics
 * Provides access to the #__kunena_topics table
 */
class TableKunenaTopics extends KunenaTable
{
	var $id = null;
	var $category_id = null;
	var $subject = null;
	var $icon_id = null;
	var $locked = null;
	var $hold = null;
	var $ordering = null;
	var $posts = null;
	var $hits = null;
	var $attachments = null;
	var $poll_id = null;
	var $moved_id = null;
	var $first_post_id = null;
	var $first_post_time = null;
	var $first_post_userid = null;
	var $first_post_message = null;
	var $first_post_guest_name = null;
	var $last_post_id = null;
	var $last_post_time = null;
	var $last_post_userid = null;
	var $last_post_message = null;
	var $last_post_guest_name = null;
	var $params = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_topics', 'id', $db );
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
		$query = "SELECT * FROM #__kunena_topics WHERE id = {$this->$k}";
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
		$category = KunenaForumCategoryHelper::get($this->category_id);
		if (!$category->exists()) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_TOPICS_ERROR_NO_CATEGORY' ) );
		} else {
			$this->category_id = $category->id;
		}
		$this->subject = trim($this->subject);
		if (!$this->subject) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TOPICS_ERROR_NO_SUBJECT' ) );
		}
		return ($this->getError () == '');
	}

}