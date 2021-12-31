<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Collection
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Collection;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;

\defined('_JEXEC') or die();

/**
 * Kunena Item Collection.
 *
 * @since   Kunena 6.0
 */
class KunenaCollection implements ArrayAccess, Countable, IteratorAggregate
{
	/**
	 * The items in the collection.
	 *
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $items = [];

	/**
	 * Create a new collection.
	 *
	 * @param   array  $items  Initial items to be added into the collection.
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(array $items = [])
	{
		$this->items = $items;
	}

	/**
	 * Get all items in the collection.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function all(): array
	{
		return $this->items;
	}

	/**
	 * Get the first item from the collection.
	 *
	 * @return  mixed|null
	 *
	 * @since   Kunena 6.0
	 */
	public function first()
	{
		return !empty($this->items) ? reset($this->items) : null;
	}

	/**
	 * Get the last item from the collection.
	 *
	 * @return  mixed|null
	 *
	 * @since   Kunena 6.0
	 */
	public function last()
	{
		return !empty($this->items) ? end($this->items) : null;
	}

	/**
	 * Run a filter over each of the items.
	 *
	 * @param   Closure  $callback  callback
	 *
	 * @return  KunenaCollection
	 *
	 * @since   Kunena 6.0
	 */
	public function filter(Closure $callback): KunenaCollection
	{
		return new static(array_filter($this->items, $callback));
	}

	/**
	 * Execute a callback over each item.
	 *
	 * @param   Closure  $callback  callback
	 *
	 * @return  KunenaCollection
	 *
	 * @since   Kunena 6.0
	 */
	public function each(Closure $callback): KunenaCollection
	{
		array_map($callback, $this->items);

		return $this;
	}

	/**
	 * Run a map over each of the items.
	 *
	 * @param   Closure  $callback  callback
	 *
	 * @return  KunenaCollection
	 *
	 * @since   Kunena 6.0
	 */
	public function map(Closure $callback): KunenaCollection
	{
		return new static(array_map($callback, $this->items, array_keys($this->items)));
	}

	/**
	 * Get an iterator for the items.
	 *
	 * @return  arrayIterator
	 *
	 * @since   Kunena 6.0
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->items);
	}

	/**
	 * Count the number of items in the collection.
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 */
	public function count(): int
	{
		return \count($this->items);
	}

	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param   mixed  $key  key
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function offsetExists($key): bool
	{
		return \array_key_exists($key, $this->items);
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param   mixed  $key  key
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($key)
	{
		return $this->items[$key];
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param   mixed  $key    key
	 * @param   mixed  $value  value
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet($key, $value)
	{
		if (\is_null($key))
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
	 * @param   string  $key  key
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset($key)
	{
		unset($this->items[$key]);
	}
}
