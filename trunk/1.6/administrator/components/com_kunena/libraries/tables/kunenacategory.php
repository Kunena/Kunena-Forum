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
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . '/kunena.php');
kimport ('error');

/**
 * Kunena Category Table
 * Provides access to the #__kunena_category table
 */
class TableKunenaCategory extends KunenaTable
{
	var $id = null;
	var $parent = null;
	var $name = null;
	var $cat_emoticon = null;
	var $locked = null;
	var $alert_admin = null;
	var $moderated = null;
	var $moderators = null;
	var $accesstype = null;
	var $access = null;
	var $pub_access = null;
	var $pub_recurse = null;
	var $admin_access = null;
	var $admin_recurse = null;
	var $ordering = null;
	var $future2 = null;
	var $published = null;
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
	var $id_last_msg = null;
	var $numTopics = null;
	var $numPosts = null;
	var $time_last_msg = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_categories', 'id', $db );
	}

	// check for potential problems
	function check() {
		if ($this->id && $this->parent) {
			if ($this->id == $this->parent) {
				$this->setError ( JText::_ ( 'COM_KUNENA_FORUM_SAME_ERR' ) );
			} elseif ($this->isChild ( $this->parent )) {
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
			$query = "SELECT id, parent FROM #__kunena_categories";
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
				$id = $list [$id]->parent;
				if ($id != 0 and $id == $this->id)
					return 1;
			}
			;
		}
		return 0;
	}

	function store($updateNulls = false) {
		$ret = parent::store ( $updateNulls );

		if ($ret) {
			// we must reset session, when forum record was changed
			$this->_db->setQuery ( "UPDATE #__kunena_sessions SET allowed='na'" );
			$this->_db->query ();
			KunenaError::checkDatabaseError ();
		}
		return $ret;
	}

}