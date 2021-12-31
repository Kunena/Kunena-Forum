<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tree;

use Iterator;

\defined('_JEXEC') or die();

/**
 * Kunena Tree Class
 *
 * @since   Kunena 6.0
 */
class KunenaTree implements Iterator
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_instances = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_tree = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_parents = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_levels = [];

	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $_id = null;

	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $_parent = null;

	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $_level = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $heap = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_count = null;

	/**
	 * @param   mixed   $items   items
	 * @param   string  $id      id
	 * @param   string  $parent  parent
	 * @param   string  $level   level
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(&$items, $id = 'id', $parent = 'parentid', $level = 'level')
	{
		$this->_tree[0] = [];
		$this->_id      = $id;
		$this->_parent  = $parent;
		$this->_level   = $level;
		$this->add($items);
		$this->rewind();
	}

	/**
	 * @param   mixed  $items  items
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function add($items): void
	{
		// Prepare tree
		foreach ($items as $item)
		{
			$itemid                    = $item->{$this->_id};
			$itemparent                = $item->{$this->_parent};
			$this->_instances[$itemid] = $item;
			$this->_parents[$itemid]   = $itemparent;
			$this->_tree [$itemid]     = [];
			$item->indent              = ['gap', 'leaf'];

			if (isset($item->{$this->_level}))
			{
				$this->_levels [$itemid] = $item->{$this->_level};
			}
			else
			{
				$item->{$this->_level} = isset($this->_levels [$itemparent]) ? $this->_levels [$itemparent] + 1 : 0;
			}
		}

		// Build tree (take ordering from the original array)
		foreach ($items as $item)
		{
			$itemid     = $item->{$this->_id};
			$itemparent = $item->{$this->_parent};

			if ($itemparent && !isset($this->_tree [$itemparent]))
			{
				$this->_parents[$itemparent]  = -1;
				$this->_tree [$itemparent]    = [];
				$this->_tree [0][$itemparent] = &$this->_tree [$itemparent];
			}

			$this->_tree [$itemparent][$itemid] = &$this->_tree [$itemid];
		}

		// Figure out tree levels if objects do not contain the information
		if (empty($this->_levels))
		{
			$heap = [0];

			while (($parent = array_shift($heap)) !== null)
			{
				$heap = array_merge($heap, array_keys($this->_tree[$parent]));

				foreach ($this->_tree [$parent] as $id => $children)
				{
					if (isset($this->_instances [$id]))
					{
						$level               = isset($this->_levels [$parent]) ? $this->_levels [$parent] + 1 : 0;
						$this->_levels [$id] = $this->_instances[$id]->{$this->_level} = $level;
					}
				}
			}
		}
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	#[\ReturnTypeWillChange]
	public function rewind()
	{
		$this->heap = [0];

		if (!isset($this->_instances[0]))
		{
			$this->next();
		}
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	#[\ReturnTypeWillChange]
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

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	#[\ReturnTypeWillChange]
	public function current()
	{
		$id = reset($this->heap);

		return $this->_instances[$id];
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	#[\ReturnTypeWillChange]
	public function key()
	{
		return reset($this->heap);
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function valid(): bool
	{
		return !empty($this->heap);
	}

	/**
	 * @param   mixed  $id  id
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function getLevel($id): bool
	{
		return isset($this->_levels [$id]) ? $this->_levels [$id] : false;
	}

	/**
	 * @param   int  $parent  parent
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getTree($parent = 0): array
	{
		if ($parent === false)
		{
			return $this->_tree;
		}

		return isset($this->_tree[$parent]) ? $this->_tree[$parent] : [];
	}

	/**
	 * @param   int    $parentid    parent id
	 * @param   array  $itemIndent  item indent
	 * @param   bool   $gap         gap
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function &getIndentation($parentid = 0, $itemIndent = [], $gap = false): array
	{
		$parent_tree = &$this->_tree[$parentid];
		end($parent_tree);
		$last_id = key($parent_tree);

		$list = [];

		foreach ($parent_tree as $id => $tree)
		{
			$indent = $itemIndent;

			if ($this->_parents[$id] < 0)
			{
				// Item isn't available (but there's at least one child)
				$indent[] = 'gap';
				$list     += $this->getIndentation($id, $indent, true);

				continue;
			}

			$list[$id]         = $this->_instances[$id];
			$list[$id]->indent = $indent;

			if ($gap)
			{
				// Parent isn't available, so we need to do some tricks to make it to look good
				array_pop($indent);
				$indent[]   = 'empty';
				$itemIndent = $indent;

				if (\count($parent_tree) > 1)
				{
					$list[$id]->indent[] = $id != $last_id ? 'zzznode' : 'zzzleaf';
					$indent[]            = $id != $last_id ? 'edge' : 'empty';
					$gap                 = false;
				}
			}
			elseif ($this->_parents[$id] > 0)
			{
				// Parent is available: we need to bind the item to the parent
				$list[$id]->indent[] = $id != $last_id ? 'crossedge' : 'lastedge';
				$indent[]            = $id != $last_id ? 'edge' : 'empty';
			}

			if (empty($this->_tree[$id]))
			{
				// No child nodes
				$list[$id]->indent[] = $parentid ? 'leaf' : 'single';
			}
			else
			{
				// Has child nodes
				$list[$id]->indent[] = $parentid ? 'node' : 'root';
				$list                += $this->getIndentation($id, $indent);
			}
		}

		return $list;
	}
}
