<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Table
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Table;

\defined('_JEXEC') or die();

use InvalidArgumentException;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Utilities\ArrayHelper;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class KunenaTableMap
 *
 * @since   Kunena 6.0
 */
class KunenaTableMap
{
	/**
	 * Name of the database table to model.
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_tbl = '';

	/**
	 * Name of the primary key field in the table.
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_tbl_key = '';

	/**
	 * Name of the mapped key field in the table.
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_tbl_mapped = '';

	/**
	 * JDatabaseDriver object.
	 *
	 * @var     DatabaseDriver
	 * @since   Kunena 6.0
	 */
	protected $_db = null;

	/**
	 * Indicator that the tables have been locked.
	 *
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_locked = false;

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   string               $table   Name of the table to model.
	 * @param   string               $key     Name of the primary key field in the table.
	 * @param   string               $mapped  Name of the mapped key field in the table.
	 * @param   DatabaseDriver|null  $db      DatabaseDriver object.
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(string $table, string $key, string $mapped, DatabaseDriver $db = null)
	{
		// Set internal variables.
		$this->_tbl        = $table;
		$this->_tbl_key    = $key;
		$this->_tbl_mapped = $mapped;
		$this->{$mapped}   = [];
		$this->_db         = $db ? $db : Factory::getContainer()->get('DatabaseDriver');

		// Initialise the table properties.
		$fields = $this->getFields();

		foreach ($fields as $name => $v)
		{
			// Add the field if it is not already present.
			if (!property_exists($this, $name))
			{
				$this->{$name} = null;
			}
		}
	}

	/**
	 * Get the columns from database table.
	 *
	 * @return  mixed  An array of the field names, or false if an error occurs.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  UnexpectedValueException
	 */
	public function getFields(): array
	{
		static $cache = [];

		$name = $this->_tbl;

		if (!isset($cache[$name]))
		{
			// Lookup the fields for this table only once.
			$fields = $this->_db->getTableColumns($name, false);

			if (empty($fields))
			{
				throw new UnexpectedValueException(sprintf('No columns found for %s table', $name));
			}

			$cache[$name] = $fields;
		}

		return (array) $cache[$name];
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getMirrorInstance()
	{
		static $instance = [];

		$key = md5(serialize([$this->_tbl, $this->_tbl_mapped, $this->_tbl_key]));

		if (!isset($instance[$key]))
		{
			$c              = \get_called_class();
			$instance[$key] = new $c($this->_tbl, $this->_tbl_mapped, $this->_tbl_key);
		}

		return $instance[$key];
	}

	/**
	 * Method to get the database table name for the class.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/getTableName
	 *
	 * @return  string  The name of the database table being modeled.
	 *
	 * @since   Kunena 6.0
	 */
	public function getTableName(): string
	{
		return $this->_tbl;
	}

	/**
	 * Method to get the primary key field name for the table.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/getKeyName
	 *
	 * @return  string  The name of the primary key for the table.
	 *
	 * @since   Kunena 6.0
	 */
	public function getKeyName(): string
	{
		return $this->_tbl_key;
	}

	/**
	 * Method to get the mapped field name for the table.
	 *
	 * @return  string  The name of the map field for the table.
	 *
	 * @since   Kunena 6.0
	 */
	public function getMappedName(): string
	{
		return $this->_tbl_mapped;
	}

	/**
	 * Method to get the JDatabaseDriver object.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/getDBO
	 *
	 * @return \Joomla\Database\DatabaseDriver|null The internal database driver object.
	 *
	 * @since   Kunena 6.0
	 */
	public function getDbo(): ?DatabaseDriver
	{
		return $this->_db;
	}

	/**
	 * Method to get the primary key.
	 *
	 * @return  integer  Get value for the primary key.
	 *
	 * @since   Kunena 6.0
	 */
	public function getKey(): int
	{
		return $this->{$this->_tbl_key};
	}

	/**
	 * Method to set the primary key.
	 *
	 * @param   int  $id  Set value for the primary key.
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function setKey(int $id): KunenaTableMap
	{
		$this->{$this->_tbl_key} = (int) $id;

		return $this;
	}

	/**
	 * Method to get the mapped value.
	 *
	 * @return  array  Get array of mapped objects.
	 *
	 * @since   Kunena 6.0
	 */
	public function getMapped(): array
	{
		return (array) $this->{$this->_tbl_mapped};
	}

	/**
	 * Method to add relation.
	 *
	 * @param   int  $id  Add Id.
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function add(int $id): KunenaTableMap
	{
		if (!\in_array($id, $this->{$this->_tbl_mapped}))
		{
			array_push($this->{$this->_tbl_mapped}, (int) $id);
		}

		return $this;
	}

	/**
	 * Method to set the DatabaseDriver object.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/setDbo
	 *
	 * @param   DatabaseDriver  $db  A DatabaseDriver object to be used by the table object.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 */
	public function setDbo(DatabaseDriver $db): bool
	{
		$this->_db = $db;

		return true;
	}

	/**
	 * Method to bind an associative array or object to the Joomla\CMS\Table\Table instance. This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/bind
	 *
	 * @param   mixed  $src     An associative array or object to bind to the Joomla\CMS\Table\Table instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  InvalidArgumentException
	 */
	public function bind($src, $ignore = []): bool
	{
		// If the source value is not an array or object return false.
		if (!\is_object($src) && !\is_array($src))
		{
			throw new InvalidArgumentException(sprintf('%s::bind(*%s*)', \get_class($this), \gettype($src)));
		}

		// If the source value is an object, get its accessible properties.
		if (\is_object($src))
		{
			$src = get_object_vars($src);
		}

		// If the ignore value is a string, explode it over spaces.
		if (!\is_array($ignore))
		{
			$ignore = explode(' ', $ignore);
		}

		// Bind the source value, excluding the ignored fields.
		foreach ($this->getProperties() as $k => $v)
		{
			// Only process fields not in the ignore array.
			if (!\in_array($k, $ignore))
			{
				if (isset($src[$k]))
				{
					$this->{$k} = $src[$k];
				}
			}
		}

		return true;
	}

	/**
	 * Returns an associative array of public object properties.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getProperties(): array
	{
		$properties = (array) $this;
		$list       = [];

		foreach ($properties as $property => $value)
		{
			if ($property[0] != "\0")
			{
				$list[$property] = $value;
			}
		}

		return $list;
	}

	/**
	 * Method to provide a shortcut to binding, checking and storing a Joomla\CMS\Table\Table
	 * instance to the database table.
	 *
	 * @param   array|null  $map     An array of mapped Ids.
	 * @param   array|null  $filter  Touch only these filtered items.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 */
	public function save(array $map = null, array $filter = null): bool
	{
		if ($map !== null)
		{
			$this->setMapped($map);
		}

		// Run any sanity checks on the instance and verify that it is ready for storage.
		if (!$this->check())
		{
			return false;
		}

		// Attempt to store the properties to the database table.
		if (!$this->store($filter))
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to set the mapped value.
	 *
	 * @param   array  $list  Set array of mapped objects.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function setMapped(array $list): void
	{
		$list                       = ArrayHelper::toInteger($list);
		$this->{$this->_tbl_mapped} = $list;
	}

	/**
	 * Method to perform sanity checks on the Joomla\CMS\Table\Table instance properties to ensure
	 * they are safe to store in the database.  Child classes should override this
	 * method to make sure the data they are storing in the database is safe and
	 * as expected before storage.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/check
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @since   Kunena 6.0
	 */
	public function check(): bool
	{
		return true;
	}

	/**
	 * Method to store mapped rows in the database from the Joomla\CMS\Table\Table instance properties.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/store
	 *
	 * @param   array|null  $filter  Touch only these filtered items.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 */
	public function store(array $filter = null): bool
	{
		$k = $this->_tbl_key;
		$m = $this->_tbl_mapped;

		if (0 == $this->{$k})
		{
			throw new UnexpectedValueException(sprintf('No key specified: %s.', \get_class($this)));
		}

		$id    = $this->{$k};
		$items = $this->{$m};

		if (!empty($items))
		{
			// Load currently mapped variables from database.
			$this->load();
			$filtered = ($filter !== null) ? array_intersect($this->{$m}, $filter) : $this->{$m};

			// Calculate difference (added and deleted items).
			$added   = array_diff($items, $filtered);
			$deleted = array_diff($filtered, $items);

			// Create all added items.
			if ($added)
			{
				$values = [];

				foreach ($added as $var)
				{
					$values[] = (int) $id . ',' . (int) $var;
				}

				$query = $this->_db->getQuery(true)
					->insert($this->_db->quoteName($this->_tbl))
					->columns([$this->_db->quoteName($this->_tbl_key), $this->_db->quoteName($this->_tbl_mapped)])
					->values($values);
				$this->_db->setQuery($query);
				$this->_db->execute();
			}

			// Remove all deleted items.
			if ($deleted)
			{
				$query = $this->_db->getQuery(true)
					->delete($this->_db->quoteName($this->_tbl))
					->where($this->_db->quoteName($this->_tbl_key) . ' = ' . $this->_db->quote((int) $id))
					->andWhere($this->_db->quoteName($this->_tbl_mapped) . ' IN (' . implode(',', $deleted) . ')');
				$this->_db->setQuery($query);
				$this->_db->execute();
			}
		}
		else
		{
			$this->delete();
		}

		$this->{$m} = $items;

		if ($this->_locked)
		{
			$this->_unlock();
		}

		return true;
	}

	/**
	 * Method to load all mapped values from the database by primary key and bind the fields
	 * to the Joomla\CMS\Table\Table instance properties.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/load
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If
	 *                           not set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if no rows were found.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  UnexpectedValueException
	 * @throws  RuntimeException
	 */
	public function load($keys = null, $reset = true): bool
	{
		if (empty($keys))
		{
			// If empty, use the value of the current key
			$keyName  = $this->_tbl_key;
			$keyValue = $this->{$keyName};

			// If empty primary key there's is no need to load anything
			if (empty($keyValue))
			{
				return true;
			}

			$keys = [$keyName => $keyValue];
		}
		elseif (!\is_array($keys))
		{
			// Load by primary key.
			$keys = [$this->_tbl_key => $keys];
		}

		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true)
			->select($this->_db->quoteName($this->_tbl_mapped))
			->from($this->_db->quoteName($this->_tbl));

		$fields = array_keys($this->getProperties());

		foreach ($keys as $field => $value)
		{
			// Check that $field is in the table.
			if (!\in_array($field, $fields))
			{
				throw new UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', \get_class($this), $field));
			}

			// Add the search tuple to the query.
			$this->{$field} = $value;
			$query->where($this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
		}

		$this->_db->setQuery($query);

		$mapName          = $this->_tbl_mapped;
		$this->{$mapName} = (array) $this->_db->loadColumn();

		return !empty($this->{$mapName});
	}

	/**
	 * Method to reset class properties to the defaults set in the class
	 * definition. It will ignore the primary key as well as any private class
	 * properties.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/reset
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function reset(): void
	{
		// Get the default values for the class from the table.
		foreach ($this->getFields() as $k => $v)
		{
			// If the property is not the primary key or private, reset it.
			if ($k != $this->_tbl_key && (strpos($k, '_') !== 0))
			{
				$this->{$k} = $v->Default;
			}
		}
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @link    http://docs.joomla.org/Joomla\CMS\Table\Table/delete
	 *
	 * @param   int|array  $pk  An optional primary key value (or array of key=>value pairs) to delete.  If not set the
	 *                          instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  UnexpectedValueException
	 */
	public function delete($pk = null): bool
	{
		$k  = $this->_tbl_key;
		$pk = (\is_null($pk)) ? $this->{$k} : $pk;

		// If no primary key is given, return false.
		if ($pk === null)
		{
			throw new UnexpectedValueException('Null primary key not allowed.');
		}

		// Turn pk into array.
		if (!\is_array($pk))
		{
			$pk = [$k => (int) $pk];
		}

		// Delete the row by primary key.
		$query = $this->_db->getQuery(true)
			->delete()
			->from($this->_tbl);

		foreach ($pk as $key => $value)
		{
			$query->where($key . ' = ' . $this->_db->quote($value));
		}

		$this->_db->setQuery($query);

		// Delete items.
		$this->_db->execute();

		return true;
	}

	/**
	 * Method to unlock the database table for writing.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 */
	protected function _unlock(): bool
	{
		$this->_db->unlockTables();
		$this->_locked = false;

		return true;
	}

	/**
	 * Method to remove relation.
	 *
	 * @param   int  $id  Add Id.
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	protected function remove(int $id): KunenaTableMap
	{
		$key = array_search((int) $id, $this->{$this->_tbl_mapped});

		if ($key !== false)
		{
			unset($this->{$this->_tbl_mapped}[$key]);
		}

		return $this;
	}

	/**
	 * Method to lock the database table for writing.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  RuntimeException
	 */
	protected function _lock(): bool
	{
		$this->_db->lockTable($this->_tbl);
		$this->_locked = true;

		return true;
	}
}
