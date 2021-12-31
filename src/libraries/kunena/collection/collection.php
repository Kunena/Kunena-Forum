<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Collection
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Item Collection.
 * @since Kunena
 */
class KunenaCollection implements ArrayAccess, Countable, IteratorAggregate
{
	/**
	 * The items in the collection.
	 *
	 * @var array
	 * @since Kunena
	 */
	protected $items = array();

	/**
	 * Create a new collection.
	 *
	 * @param   array $items Initial items to be added into the collection.
	 *
	 * @since Kunena
	 */
	public function __construct(array $items = array())
	{
		$this->items = $items;
	}

	/**
	 * Get all items in the collection.
	 *
	 * @return array
	 * @since Kunena
	 */
	public function all()
	{
		return $this->items;
	}

	/**
	 * Get the first item from the collection.
	 *
	 * @return mixed|null
	 * @since Kunena
	 */
	public function first()
	{
		return !empty($this->items) ? reset($this->items) : null;
	}

	/**
	 * Get the last item from the collection.
	 *
	 * @return mixed|null
	 * @since Kunena
	 */
	public function last()
	{
		return !empty($this->items) ? end($this->items) : null;
	}

	/**
	 * Run a filter over each of the items.
	 *
	 * @param   Closure $callback callback
	 *
	 * @return KunenaCollection
	 * @since Kunena
	 */
	public function filter(Closure $callback)
	{
		return new static(array_filter($this->items, $callback));
	}

	/**
	 * Execute a callback over each item.
	 *
	 * @param   Closure $callback callback
	 *
	 * @return KunenaCollection
	 * @since Kunena
	 */
	public function each(Closure $callback)
	{
		array_map($callback, $this->items);

		return $this;
	}

	/**
	 * Run a map over each of the items.
	 *
	 * @param   Closure $callback callback
	 *
	 * @return KunenaCollection
	 * @since Kunena
	 */
	public function map(Closure $callback)
	{
		return new static(array_map($callback, $this->items, array_keys($this->items)));
	}

	/**
	 * Get an iterator for the items.
	 *
	 * @return ArrayIterator
	 * @since Kunena
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->items);
	}

	/**
	 * Count the number of items in the collection.
	 *
	 * @return integer
	 * @since Kunena
	 */
	public function count()
	{
		return count($this->items);
	}

	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param   mixed $key key
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function offsetExists($key)
	{
		return array_key_exists($key, $this->items);
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param   mixed $key key
	 *
	 * @return mixed
	 * @since Kunena
	 */
	public function offsetGet($key)
	{
		return $this->items[$key];
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param   mixed $key   key
	 * @param   mixed $value value
	 *
	 * @return void
	 * @since Kunena
	 */
	public function offsetSet($key, $value)
	{
		if (is_null($key))
		{
			$this->items[] = $value;
		}
		else
		{
			$this->items[$key] = $value;
		}
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param   string $key key
	 *
	 * @return void
	 * @since Kunena
	 */
	public function offsetUnset($key)
	{
		unset($this->items[$key]);
	}
}
