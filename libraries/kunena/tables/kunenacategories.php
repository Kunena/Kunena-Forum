<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena Categories
 * Provides access to the #__kunena_categories table
 */
class TableKunenaCategories extends KunenaTable
{
	public $id = null;
	public $parent_id = null;
	public $name = null;
	public $alias = null;
	public $icon = null;
	public $icon_id = null;
	public $locked = null;
	public $accesstype = null;
	public $access = null;
	public $pub_access = null;
	public $pub_recurse = null;
	public $admin_access = null;
	public $admin_recurse = null;
	public $ordering = null;
	public $published = null;
	public $channels = null;
	public $checked_out = null;
	public $checked_out_time = null;
	public $review = null;
	public $allow_anonymous = null;
	public $post_anonymous = null;
	public $hits = null;
	public $description = null;
	public $headerdesc = null;
	public $class_sfx = null;
	public $allow_polls = null;
	public $topic_ordering = null;
	public $iconset = null;
	public $numTopics = null;
	public $numPosts = null;
	public $last_topic_id = null;
	public $last_post_id = null;
	public $last_post_time = null;
	public $params = null;

	public function __construct($db)
	{
		parent::__construct ( '#__kunena_categories', 'id', $db );
	}

	public function bind($array, $ignore = '')
	{
		if (is_object($array))
		{
			$array = get_object_vars($array);
		}

		if (isset($array['params']) && !is_string($array['params']))
		{
			if ($array['params'] instanceof JRegistry)
			{
				$registry = $array['params'];
			}
			elseif (is_array($array['params']))
			{
				$registry = new JRegistry;
				$registry->loadArray($array['params']);
			}
			else
			{
				$registry = new JRegistry;
			}
			// TODO: convert to J!2.5: (string) $registry
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	public function load($id = null, $reset = true)
	{
		$this->_exists = false;
		$k = $this->_tbl_key;

		// Get the id to load.
		if ($id !== null)
		{
			$this->$k = $id;
		}

		// Reset the table.
		if ($reset)
		{
			$this->reset();
		}

		// Check for a valid id to load.
		if ($this->$k === null || intval($this->$k) < 1)
		{
			$this->$k = 0;
			return false;
		}

		// Load the data.
		$query = "SELECT * FROM #__kunena_categories WHERE id = {$this->$k}";
		$this->_db->setQuery($query);
		$data = $this->_db->loadAssoc();

		// Check for an error message.
		if ($this->_db->getErrorNum())
		{
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
	public function check()
	{
		if ($this->id && $this->parent_id)
		{
			if ($this->id == $this->parent_id)
			{
				$this->setError ( JText::_ ( 'COM_KUNENA_FORUM_SAME_ERR' ) );
			}
			elseif ($this->isChild ( $this->parent_id ))
			{
				$this->setError ( JText::_ ( 'COM_KUNENA_FORUM_OWNCHILD_ERR' ) );
			}
		}

		$this->name = trim($this->name);
		if (!$this->name) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TABLE_CATEGORIES_ERROR_NO_NAME' ) );
		}

		if ($this->params instanceof JRegistry) {
			$this->params = $this->params->toString();
		}

		return ($this->getError () == '');
	}

	// check if given forum is one of its own childs
	public function isChild($id) {
		// FIXME: when we have category cache, replace this code
		if ($id > 0)
		{
			$query = "SELECT id, parent_id FROM #__kunena_categories";
			$this->_db->setQuery ( $query );
			$list = $this->_db->loadObjectList ( 'id' );

			if (KunenaError::checkDatabaseError ())
			{
				return;
			}

			$recurse = array ();
			while ($id)
			{
				if (in_array ($id, $recurse))
				{
					$this->setError ( get_class ( $this ) . JText::_ ( 'COM_KUNENA_RECURSION' ) );
					return 0;
				}

				$recurse [] = $id;
				if (!isset ( $list [$id])) {
					$this->setError ( get_class ( $this ) . JText::_ ( 'COM_KUNENA_LIB_TABLE_CATEGORIES_ERROR_INVALID' ) );
					return 0;
				}

				$id = $list [$id]->parent_id;
				if ($id != 0 and $id == $this->id)
					return 1;
			}
		}
		return 0;
	}

	public function reorder($where='')
	{
		if (!$where)
		{
			$db = JFactory::getDbo();
			$query = "SELECT parent_id FROM #__kunena_categories GROUP BY parent_id";
			$db->setQuery($query);
			$parents = $db->loadColumn();
			$success = true;
			foreach ($parents as $parent_id)
			{
				$success &= parent::reorder("parent_id={$db->quote($parent_id)}");
			}

			return $success;
		}
		else
		{
			return parent::reorder($where);
		}
	}

	public function store($updateNulls = false)
	{
		$ret = parent::store ( $updateNulls );

		return $ret;
	}
}
