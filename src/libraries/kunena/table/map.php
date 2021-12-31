<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Table
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

/**
 * Class KunenaTableMap
 * @since Kunena
 */
class KunenaTableMap
{
	/**
	 * Name of the database table to model.
	 *
	 * @var    string
	 * @since Kunena
	 */
	protected $_tbl = '';

	/**
	 * Name of the primary key field in the table.
	 *
	 * @var    string
	 * @since Kunena
	 */
	protected $_tbl_key = '';

	/**
	 * Name of the mapped key field in the table.
	 *
	 * @var    string
	 * @since Kunena
	 */
	protected $_tbl_mapped = '';

	/**
	 * JDatabaseDriver object.
	 *
	 * @var    JDatabaseDriver
	 * @since Kunena
	 */
	protected $_db = null;

	/**
	 * Indicator that the tables have been locked.
	 *
	 * @var    boolean
	 * @since Kunena
	 */
	protected $_locked = false;

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   string          $table  Name of the table to model.
	 * @param   string          $key    Name of the primary key field in the table.
	 * @param   string          $mapped Name of the mapped key field in the table.
	 * @param   JDatabaseDriver $db     JDatabaseDriver object.
	 *
	 * @since Kunena
	 */
	public function __construct($table, $key, $mapped, JDatabaseDriver $db = null)
	{
		// Set internal variables.
		$this->_tbl        = $table;
		$this->_tbl_key    = $key;
		$this->_tbl_mapped = $mapped;
		$this->{$mapped}   = array();
		$this->_db         = $db ? $db : Factory::getDbo();

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
	 * @throws  UnexpectedValueException
	 * @since Kunena
	 */
	public function getFields()
	{
		static $cache = array();

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
	 * @return mixed
	 * @since Kunena
	 */
	public function getMirrorInstance()
	{
		static $instance = array();

		$key = md5(serialize(array($this->_tbl, $this->_tbl_mapped, $this->_tbl_key)));

		if (!isset($instance[$key]))
		{
			$c              = get_called_class();
			$instance[$key] = new $c($this->_tbl, $this->_tbl_mapped, $this->_tbl_key);
		}

		return $instance[$key];
	}

	/**
	 * Method to get the database table name for the class.
	 *
	 * @return  string  The name of the database table being modeled.
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/getTableName
	 * @since   Kunena
	 */
	public function getTableName()
	{
		return $this->_tbl;
	}

	/**
	 * Method to get the primary key field name for the table.
	 *
	 * @return  string  The name of the primary key for the table.
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/getKeyName
	 * @since   Kunena
	 */
	public function getKeyName()
	{
		return $this->_tbl_key;
	}

	/**
	 * Method to get the mapped field name for the table.
	 *
	 * @return  string  The name of the map field for the table.
	 * @since Kunena
	 */
	public function getMappedName()
	{
		return $this->_tbl_mapped;
	}

	/**
	 * Method to get the JDatabaseDriver object.
	 *
	 * @return  JDatabaseDriver  The internal database driver object.
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/getDBO
	 * @since   Kunena
	 */
	public function getDbo()
	{
		return $this->_db;
	}

	/**
	 * Method to get the primary key.
	 *
	 * @return  integer  Get value for the primary key.
	 * @since Kunena
	 */
	public function getKey()
	{
		return $this->{$this->_tbl_key};
	}

	/**
	 * Method to set the primary key.
	 *
	 * @param   int $id Set value for the primary key.
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function setKey($id)
	{
		$this->{$this->_tbl_key} = (int) $id;

		return $this;
	}

	/**
	 * Method to get the mapped value.
	 *
	 * @return  array  Get array of mapped objects.
	 * @since Kunena
	 */
	public function getMapped()
	{
		return (array) $this->{$this->_tbl_mapped};
	}

	/**
	 * Method to add relation.
	 *
	 * @param   int $id Add Id.
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function add($id)
	{
		if (!in_array($id, $this->{$this->_tbl_mapped}))
		{
			array_push($this->{$this->_tbl_mapped}, (int) $id);
		}

		return $this;
	}

	/**
	 * Method to set the JDatabaseDriver object.
	 *
	 * @param   JDatabaseDriver $db A JDatabaseDriver object to be used by the table object.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/setDbo
	 * @since   Kunena
	 */
	public function setDbo(JDatabaseDriver $db)
	{
		$this->_db = $db;

		return true;
	}

	/**
	 * Method to bind an associative array or object to the \Joomla\CMS\Table\Table instance. This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed $src    An associative array or object to bind to the \Joomla\CMS\Table\Table instance.
	 * @param   mixed $ignore An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/bind
	 * @throws  InvalidArgumentException
	 * @since   Kunena
	 */
	public function bind($src, $ignore = array())
	{
		// If the source value is not an array or object return false.
		if (!is_object($src) && !is_array($src))
		{
			throw new InvalidArgumentException(sprintf('%s::bind(*%s*)', get_class($this), gettype($src)));
		}

		// If the source value is an object, get its accessible properties.
		if (is_object($src))
		{
			$src = get_object_vars($src);
		}

		// If the ignore value is a string, explode it over spaces.
		if (!is_array($ignore))
		{
			$ignore = explode(' ', $ignore);
		}

		// Bind the source value, excluding the ignored fields.
		foreach ($this->getProperties() as $k => $v)
		{
			// Only process fields not in the ignore array.
			if (!in_array($k, $ignore))
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
	 * @since Kunena
	 */
	public function getProperties()
	{
		$properties = (array) $this;
		$list       = array();

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
	 * Method to provide a shortcut to binding, checking and storing a \Joomla\CMS\Table\Table
	 * instance to the database table.
	 *
	 * @param   array $map    An array of mapped Ids.
	 * @param   array $filter Touch only these filtered items.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  UnexpectedValueException
	 * @since Kunena
	 */
	public function save(array $map = null, array $filter = null)
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
	 * @param   array $list Set array of mapped objects.
	 *
	 * @since Kunena
	 * @return void
	 */
	public function setMapped(array $list)
	{
		$list = ArrayHelper::toInteger($list);
		$this->{$this->_tbl_mapped} = $list;
	}

	/**
	 * Method to perform sanity checks on the \Joomla\CMS\Table\Table instance properties to ensure
	 * they are safe to store in the database.  Child classes should override this
	 * method to make sure the data they are storing in the database is safe and
	 * as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/check
	 * @since   Kunena
	 */
	public function check()
	{
		return true;
	}

	/**
	 * Method to store mapped rows in the database from the \Joomla\CMS\Table\Table instance properties.
	 *
	 * @param   array $filter Touch only these filtered items.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/store
	 * @throws  UnexpectedValueException
	 * @since   Kunena
	 */
	public function store(array $filter = null)
	{
		$k = $this->_tbl_key;
		$m = $this->_tbl_mapped;

		if (0 == $this->{$k})
		{
			throw new UnexpectedValueException(sprintf('No key specified: %s.', get_class($this)));
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
				$values = array();

				foreach ($added as $var)
				{
					$values[] = (int) $id . ',' . (int) $var;
				}

				$query = $this->_db->getQuery(true);
				$query->insert($this->_db->quoteName($this->_tbl));
				$query->columns(array($this->_db->quoteName($this->_tbl_key), $this->_db->quoteName($this->_tbl_mapped)));
				$query->values($values);
				$this->_db->setQuery($query);
				$this->_db->execute();
			}

			// Remove all deleted items.
			if ($deleted)
			{
				$query = $this->_db->getQuery(true);
				$query->delete($this->_db->quoteName($this->_tbl));
				$query->where($this->_db->quoteName($this->_tbl_key) . '=' . (int) $id);
				$query->where($this->_db->quoteName($this->_tbl_mapped) . ' IN (' . implode(',', $deleted) . ')');
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
	 * to the \Joomla\CMS\Table\Table instance properties.
	 *
	 * @param   mixed   $keys    An optional primary key value to load the row by, or an array of fields to match.  If
	 *                           not set the instance property value is used.
	 * @param   boolean $reset   True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if no rows were found.
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/load
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 * @since   Kunena
	 */
	public function load($keys = null, $reset = true)
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

			$keys = array($keyName => $keyValue);
		}
		elseif (!is_array($keys))
		{
			// Load by primary key.
			$keys = array($this->_tbl_key => $keys);
		}

		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select($this->_tbl_mapped);
		$query->from($this->_tbl);
		$fields = array_keys($this->getProperties());

		foreach ($keys as $field => $value)
		{
			// Check that $field is in the table.
			if (!in_array($field, $fields))
			{
				throw new UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
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
	 * @return  void
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/reset
	 * @since   Kunena
	 */
	public function reset()
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
	 * @param   int|array $pk An optional primary key value (or array of key=>value pairs) to delete.  If not set the
	 *                        instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/\Joomla\CMS\Table\Table/delete
	 * @throws  UnexpectedValueException
	 * @since   Kunena
	 */
	public function delete($pk = null)
	{
		$k  = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->{$k} : $pk;

		// If no primary key is given, return false.
		if ($pk === null)
		{
			throw new UnexpectedValueException('Null primary key not allowed.');
		}

		// Turn pk into array.
		if (!is_array($pk))
		{
			$pk = array($k => (int) $pk);
		}

		// Delete the row by primary key.
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from($this->_tbl);

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
	 * @since Kunena
	 */
	protected function _unlock()
	{
		$this->_db->unlockTables();
		$this->_locked = false;

		return true;
	}

	/**
	 * Method to remove relation.
	 *
	 * @param   int $id Add Id.
	 *
	 * @return $this
	 * @since Kunena
	 */
	protected function remove($id)
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
	 * @throws  RuntimeException
	 * @since Kunena
	 */
	protected function _lock()
	{
		$this->_db->lockTable($this->_tbl);
		$this->_locked = true;

		return true;
	}

}
