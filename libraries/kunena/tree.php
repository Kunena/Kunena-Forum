<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Tree Class
 */
class KunenaTree implements Iterator
{
	protected $_instances = array();
	protected $_tree = array ();
	protected $_parents = array();
	protected $_levels = array ();
	protected $_id = null;
	protected $_parent = null;
	protected $_level = null;
	protected $heap = null;
	//	protected $_count = null;

	public function __construct(&$items, $id = 'id', $parent = 'parent_id', $level = 'level')
	{
		$this->_tree[0] = array();
		$this->_id = $id;
		$this->_parent = $parent;
		$this->_level = $level;
		$this->add($items);
		$this->rewind();
	}

	public function rewind()
	{
		$this->heap = array(0);

		if (!isset($this->_instances[0]))
		{
			$this->next();
		}
	}

	public function current()
	{
		$id = reset($this->heap);

		return $this->_instances[$id];
	}

	public function key()
	{
		return reset($this->heap);
	}

	public function next()
	{
		$id = array_shift($this->heap);

		if ($id === false)
		{
			return;
		}

		// Add children into the beginning of the array
		$this->heap = array_merge(array_keys($this->_tree[$id]), $this->heap);
		// Skip missing items
		$id = reset($this->heap);

		if ($id !== false && !isset($this->_instances[$id]))
		{
			$this->next();
		}
	}

	public function valid()
	{
		return !empty($this->heap);
	}

	public function add(&$items)
	{
		// Prepare tree
		foreach ( $items as $item )
		{
			$itemid = $item->{$this->_id};
			$itemparent = $item->{$this->_parent};
			$this->_instances[$itemid] = $item;
			$this->_parents[$itemid] = $itemparent;
			$this->_tree [$itemid] = array();
			$item->indent = array('gap', 'leaf');

			if (isset($item->{$this->_level}))
			{
				$this->_levels [$itemid] = $item->{$this->_level};
			}
			else
			{
				$item->{$this->_level} = isset($this->_levels [$itemparent]) ? $this->_levels [$itemparent]+1 : 0;
			}
		}

		// Build tree (take ordering from the original array)
		foreach ( $items as $item )
		{
			$itemid = $item->{$this->_id};
			$itemparent = $item->{$this->_parent};

			if ($itemparent && !isset($this->_tree [$itemparent]))
			{
				$this->_parents[$itemparent] = -1;
				$this->_tree [$itemparent] = array();
				$this->_tree [0][$itemparent] = &$this->_tree [$itemparent];
			}

			$this->_tree [$itemparent][$itemid] = &$this->_tree [$itemid];
		}

		// Figure out tree levels if objects do not contain the information
		if (empty($this->_levels))
		{
			$heap = array(0);
			while (($parent = array_shift($heap)) !== null)
			{
				$heap = array_merge($heap, array_keys($this->_tree[$parent]));

				foreach ($this->_tree [$parent] as $id=>$children)
				{
					if (isset($this->_instances [$id]))
					{
						$level = isset($this->_levels [$parent]) ? $this->_levels [$parent]+1 : 0;
						$this->_levels [$id] = $this->_instances[$id]->{$this->_level} = $level;
					}
				}
			}
		}
	}

	public function getLevel($id)
	{
		return isset($this->_levels [$id]) ? $this->_levels [$id] : false;
	}

	public function getTree($parent = 0)
	{
		if ($parent === false)
		{
			return $this->_tree;
		}

		return isset($this->_tree[$parent]) ? $this->_tree[$parent] : array();
	}

	public function &getIndentation($parent_id = 0, $itemIndent = array(), $gap = false)
	{
		$parent_tree = &$this->_tree[$parent_id];
		end($parent_tree);
		$last_id = key($parent_tree);

		$list = array();
		foreach ($parent_tree as $id=>$tree)
		{
			$indent = $itemIndent;

			if ($this->_parents[$id] < 0)
			{
				// Item isn't available (but there's at least one child)
				$indent[] = 'gap';
				$list += $this->getIndentation($id, $indent, true);

				continue;
			}

			$list[$id] = $this->_instances[$id];
			$list[$id]->indent = $indent;

			if ($gap)
			{
				// Parent isn't available, so we need to do some tricks to make it to look good
				array_pop($indent);
				$indent[] = 'empty';
				$itemIndent = $indent;

				if (count($parent_tree) > 1)
				{
					$list[$id]->indent[] = $id != $last_id ? 'zzznode' : 'zzzleaf';
					$indent[] = $id != $last_id ? 'edge' : 'empty';
					$gap = false;
				}

			}
			elseif ($this->_parents[$id] > 0)
			{
				// Parent is available: we need to bind the item to the parent
				$list[$id]->indent[] = $id != $last_id ? 'crossedge' : 'lastedge';
				$indent[] = $id != $last_id ? 'edge' : 'empty';
			}

			if (empty($this->_tree[$id]))
			{
				// No child nodes
				$list[$id]->indent[] = $parent_id ? 'leaf' : 'single';
			}
			else
			{
				// Has child nodes
				$list[$id]->indent[] = $parent_id ? 'node' : 'root';
				$list += $this->getIndentation($id, $indent);
			}
		}

		return $list;
	}
}

/*
class KunenaTreeItem {
	protected $_id = 0;
	protected $_data = null;
	protected $_parent = null;
	protected $_level = null;
	protected $_children = null;

	public function __construct($id, $data=null) {
		$this->_id = $id;
		$this->_data = $data;
	}

	public function isLeaf() {
		return empty($this->_children);
	}
	public function isFirst() {
		return $this->_parent ? $this->_parent->getFirst()->getId() == $this->getId() : true;
	}
	public function isLast() {
		return $this->_parent ? $this->_parent->getLast()->getId() == $this->getId() : true;
	}

	public function getId() {
		return $this->_id;
	}
	public function getData() {
		return $this->_data;
	}
	public function getRoot() {
		return $this->_parent ? $this->_parent->getRoot() : $this;
	}
	public function getLevel() {
		if ($this->_level === null) {
			$this->_level = $this->_parent ? $this->_parent->getLevel()+1 : 0;
		}
		return $this->_level;
	}
	public function getParent() {
		return $this->_parent;
	}
	public function getParents($reverse = false) {
		if (!$this->_parent) {
			return array();
		}
		$parents = $this->_parent->getParents();
		if ($reverse) array_unshift($parents, $this->_parent);
		else array_push($parents, $this->_parent);
		return $parents;
	}
	public function getChild($id) {
		return isset($this->_children[$id]) ? $this->_children[$id] : null;
	}
	public function getChildren() {
		return $this->_children;
	}
	public function getFirst() {
		return empty($this->_children) ? null : reset($this->_children);
	}
	public function getLast() {
		return empty($this->_children) ? null : end($this->_children);
	}

	public function setParent($parent) {
		if ($this->_parent && $this->_parent != $parent) {
			$this->_parent->removeChild($this);
		}
		$this->_parent = $parent;
		if ($this->_parent) $this->_parent->addChild($this);
	}
	public function addChild($child) {
		$this->_children[$child->getId()] = $child;
		if ($child->getParent() != $this) $child->setParent($this);
	}
	public function removeChild($child) {
		unset($this->_children[$child->getId()]);
		if ($child->getParent() == $this) $child->setParent(null);
	}
}
*/
