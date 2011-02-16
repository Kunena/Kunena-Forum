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
kimport ('kunena.error');

/**
 * Kunena Categories
 * Provides access to the #__kunena_categories table
 */
class TableKunenaCategories extends KunenaTable
{
	var $id = null;
	var $parent_id = null;
	var $name = null;
	var $icon_id = null;
	var $locked = null;
	var $moderated = null;
	var $accesstype = null;
	var $access = null;
	var $pub_access = null;
	var $pub_recurse = null;
	var $admin_access = null;
	var $admin_recurse = null;
	var $ordering = null;
	var $published = null;
	var $channels = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $review = null;
	var $allow_anonymous = null;
	var $post_anonymous = null;
	var $hits = null;
	var $description = null;
	var $headerdesc = null;
	var $class_sfx = null;
	var $allow_polls = null;
	var $numTopics = null;
	var $numPosts = null;
	var $last_topic_id = null;
	var $last_topic_subject = null;
	var $last_topic_posts = null;
	var $last_post_id = null;
	var $last_post_time = null;
	var $last_post_userid = null;
	var $last_post_message = null;
	var $last_post_guest_name = null;
	var $params = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_categories', 'id', $db );
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
		$query = "SELECT * FROM #__kunena_categories WHERE id = {$this->$k}";
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
		if ($this->id && $this->parent_id) {
			if ($this->id == $this->parent_id) {
				$this->setError ( JText::_ ( 'COM_KUNENA_FORUM_SAME_ERR' ) );
			} elseif ($this->isChild ( $this->parent_id )) {
				$this->setError ( JText::_ ( 'COM_KUNENA_FORUM_OWNCHILD_ERR' ) );
			}
		}
		$this->name = trim($this->name);
		if (!$this->name) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_CATEGORIES_ERROR_NO_NAME' ) );
		}
		return ($this->getError () == '');
	}

	// check if given forum is one of its own childs
	function isChild($id) {
		// FIXME: when we have category cache, replace this code
		if ($id > 0) {
			$query = "SELECT id, parent_id FROM #__kunena_categories";
			$this->_db->setQuery ( $query );
			$list = $this->_db->loadObjectList ( 'id' );
			if (KunenaError::checkDatabaseError ())
				return;
			$recurse = array ();
			while ( $id ) {
				if (in_array ( $id, $recurse )) {
					$this->setError ( get_class ( $this ) . JText::_ ( 'COM_KUNENA_RECURSION' ) );
					return 0;
				}
				$recurse [] = $id;
				if (! isset ( $list [$id] )) {
					$this->setError ( get_class ( $this ) . JText::_ ( 'COM_KUNENA_FORUM_UNKNOWN_ERR' ) );
					return 0;
				}
				$id = $list [$id]->parent_id;
				if ($id != 0 and $id == $this->id)
					return 1;
			}
			;
		}
		return 0;
	}

	function store($updateNulls = false) {
		$ret = parent::store ( $updateNulls );
		return $ret;
	}

}