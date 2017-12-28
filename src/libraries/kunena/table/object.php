<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Table
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Abstract Table Object class
 *
 * Parent class to all table objects.
 *
 * Note: If you set default values for the fields, please keep at least one default key value as null!
 *
 * @since       K4.0
 */
abstract class KunenaTableObject
{
	/**
	 * Store all instances of this type by Id.
	 * Always override this variable in your own class!
	 *
	 * @var array  If you want to store instances, initialise to array()
	 * @since  K4.0
	 */
	static protected $instances = null;

	/**
	 * Name of the database table to model.
	 * Always override this variable in your own class!
	 *
	 * @var    string
	 * @since  K4.0
	 */
	static protected $tbl = '';

	/**
	 * Array of table fields with their default value (field=>default).
	 * You can either fill up your table structure or leave base class to fetch the fields for you.
	 *
	 * @var array
	 * @since  K4.0
	 */
	static protected $tbl_fields;

	/**
	 * Array of primary key fields in the table.
	 * Always override this variable in your own class!
	 *
	 * @var    string
	 * @since  K4.0
	 */
	static protected $tbl_keys = array();

	/**
	 * JDatabaseDriver object.
	 *
	 * @var    JDatabaseDriver
	 * @since  K4.0
	 */
	static protected $db;

	/**
	 * Indicator that the tables have been locked.
	 *
	 * @var    boolean
	 * @since  K4.0
	 */
	static protected $_locked = false;

	/**
	 * Flag whether the object exists in the database or not.
	 *
	 * @var bool
	 * @since  K4.0
	 */
	protected $_exists = false;

	/**
	 * Serialized key for the object.
	 * @var string
	 * @since  K4.0
	 */
	protected $_key;

	/**
	 * Constructor to create the table object instance.  All objects with key set are stored into global instances.
	 *
	 * @param   array            $keys   Name of the primary key field in the table.
	 *
	 * @since  K4.0
	 */
	public function __construct($keys = null)
	{
		// First run: Initialise the table properties.
		if (is_null(static::$tbl_fields))
		{
			static::getFields();
		}

		$exists = false;
		$tbl_keys = array();

		if ($keys === null)
		{
			// We need to check a special case where object properties have already been set.
			// This happens only if $db->loadObject() or $db->loadObjectList() are called with a class type.

			// Build storage key for the object.
			foreach (static::$tbl_keys as $keyName)
			{
				$keyValue = isset($this->$keyName) ? $this->$keyName : null;
				$exists |= ($keyValue !== null);
				$tbl_keys[$keyName] = $keyValue;
			}

			if ($exists)
			{
				if (is_array(static::$instances))
				{
					// Yes, we are in the special case.
					$this->_key = count($tbl_keys) > 1 ? json_encode($tbl_keys) : reset($tbl_keys);
					if (isset(static::$instances[$this->_key]))
					{
						// If we already had loaded the object, we can stop now.
						// This is because of we keep only one global instance from the object.
						return;
					}
				}

				// Initialise the object.
				$this->_exists = true;
				$this->initialise(true);
			}
		}

		if (!$exists)
		{
			$exists = $this->load($keys);
			if ($exists)
			{
				// Build storage key for the object.
				foreach (static::$tbl_keys as $keyName)
				{
					$tbl_keys[$keyName] = isset($this->$keyName) ? $this->$keyName : null;
				}
			}
		}

		if ($exists && is_array(static::$instances))
		{
			$this->_key = count($tbl_keys) > 1 ? json_encode($tbl_keys) : reset($tbl_keys);
			if (!isset(static::$instances[$this->_key]))
			{
				// Add the new object into the instances.
				static::$instances[$this->_key] = $this;
			}
		}
	}

	/**
	 * Method to get the JDatabaseDriver object.
	 *
	 * @return  JDatabaseDriver  The internal database driver object.
	 *
	 * @since  K4.0
	 */
	public static function getDbo()
	{
		return static::$db;
	}

	/**
	 * Method to set the JDatabaseDriver object.
	 *
	 * @param   JDatabaseDriver  $db  A JDatabaseDriver object to be used by the table object.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    https://docs.joomla.org/JTable/setDbo
	 * @since  K4.0
	 */
	public static function setDbo(JDatabaseDriver $db)
	{
		static::$db = $db;

		return true;
	}

	/**
	 * Get the database columns.
	 *
	 * @return  mixed  An array of the field names, or false if an error occurs.
	 *
	 * @since  K4.0
	 * @throws  UnexpectedValueException
	 */
	static public function getFields()
	{
		if (static::$tbl_fields === null)
		{
			// Lookup the fields for this table only once.
			static::$tbl_fields = static::$db->getTableColumns(static::$tbl, false);

			if (empty(static::$tbl_fields))
			{
				throw new UnexpectedValueException(sprintf('No columns found for %s table', static::$tbl));
			}
		}

		return array_keys(static::$tbl_fields);
	}

	public function getId()
	{
		return $this->_key;
	}

	/**
	 * Override this function if you need to initialise object right after creating it.
	 *
	 * Can be used for example if the database fields need to be converted to array or JRegistry.
	 *
	 * @param   bool  $sqlFetch  True only if properties were assigned before constructor was called.
	 * @since  K4.0
	 */
	protected function initialise($sqlFetch = false)
	{

	}

	/**
	 * Create almost identical copy of the object, but clean up the key fields.
	 *
	 * New object will also return false on $new->exists() until it gets saved.
	 * @since  K4.0
	 */
	public function __clone()
	{
		foreach (static::$tbl_keys as $keyName)
		{
			$this->$keyName = null;
		}

		$this->_exists = false;
	}

	/**
	 * Returns the global instance to the object.
	 *
	 * Note that using array of fields will always make a query to the database, but it's very useful feature if you want to search
	 * one item by using arbitrary set of matching fields. If there are more than one matching object, first one gets returned.
	 *
	 * @param   int|array  $keys        An optional primary key value to load the object by, or an array of fields to match.
	 *
	 * @return  KunenaDatabaseObject
	 * @throw   RuntimeException
	 * @since  K4.0
	 */
	static public function getInstance($keys)
	{
		$k = json_encode(self::resolveKeys($keys));
		// FIXME:
		$k = (int) $keys;

		// If we are creating or loading a new item or we load instance by alternative keys,
		// we need to create a new object.
		if (!isset(static::$instances[$k])) {
			$c = get_called_class();
			$instance = new $c($keys);
			// @var KunenaTableObject $instance

			if (!$instance->exists())
			{
				return $instance;
			}

			// Instance exists: make sure that we return the global instance.
			$k = $instance->_key;
		}

		// Return global instance from the identifier.
		$instance = static::$instances[$k];

		// But before that, check that we have valid item.
		if ($k != $instance->_key)
		{
			throw new RuntimeException(get_called_class() . ": Identifier doesn't match ({$k} != {$instance->_key})");
		}

		return $instance;
	}

	/**
	 * For internal use only.
	 * @return array
	 */
	static public function &getInstances()
	{
		return static::$instances;
	}

	/**
	 * Removes all or selected instances from the object cache.
	 *
	 * @param   null|int|array  $ids
	 * @since  K4.0
	 */
	static public function freeInstances($ids = null)
	{
		if (!isset(static::$instances))
		{
			return;
		}

		if ($ids === null)
		{
			$ids = array_keys(static::$instances);
		}

		$ids = (array) $ids;

		foreach ($ids as $id)
		{
			unset(static::$instances[$id]);
		}
	}

	/**
	 * Returns true if the object exists in the database.
	 *
	 * @param   boolean  $exists  Internal parameter to change state.
	 *
	 * @return  boolean  True if object exists in database.
	 * @since  K4.0
	 */
	public function exists($exists = null)
	{
		$return = $this->_exists;

		if ($exists !== null)
		{
			$this->_exists = $exists;
		}

		return $return;
	}


	/**
	 * Method to reset class properties to the defaults set in the class
	 * definition. It will ignore the primary key.
	 *
	 * If you want to reset other properties, you need to override the function.
	 *
	 * @return  KunenaTableObject
	 * @since  K4.0
	 */
	public function reset()
	{
		// Get the default values for the class from the table.
		foreach (static::$tbl_fields as $k => $v)
		{
			// If the property is not the primary key.
			if (!in_array($k, static::$tbl_keys))
			{
				$this->$k = $v->Default;
			}
		}

		return $this;
	}

	/**
	 * Returns an associative array of object properties.
	 *
	 * @return  array
	 *
	 * @since  K4.0
	 */
	public function getProperties()
	{
		// Use closure to return public variables only.
		$self = $this;
		return function () use ($self) {

			return get_object_vars($self);
		};
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  KunenaTableObject
	 *
	 * @since  K4.0
	 * @throws  InvalidArgumentException
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
					$this->$k = $src[$k];
				}
			}
		}

		return $this;
	}

	/**
	 * @param   null $keys
	 * @param   bool $reset
	 *
	 * @return boolean|KunenaTableObject
	 */
	protected function load($keys = null, $reset = true)
	{
		try
		{
			$keys = $this->getKeyValues($keys);
		}
		catch (UnexpectedValueException $e)
		{
			if ($e->getCode() == 0)
			{
				// Key not fully given, no need to load the item.
				foreach ($keys as $field => $value)
				{
					// Make sure the object contains the search fields.
					$this->$field = $value;
				}

				return false;
			}

			// Error, throw it forward.
			throw $e;
		}

		if ($reset) {
			$this->reset();
		}

		// Initialise the query.
		$query = static::$db->getQuery(true)->select('*')->from(static::$tbl);

		foreach ($keys as $field => $value)
		{
			// Make sure the object contains the search fields.
			// This is incompatible to JTable, but needed by this class.
			$this->$field = $value;

			// Add the search tuple to the query.
			$query->where(static::$db->quoteName($field) . ' = ' . static::$db->quote($value));
		}

		static::$db->setQuery($query, 0, 1);

		$row = static::$db->loadAssoc();

		// Check that we have a result.
		if (empty($row))
		{
			return false;
		}

		// Bind the object with the row and return.
		return $this->_exists = $this->bind($row);
	}


	/**
	 * Method to perform sanity checks on the JTableObject instance properties to ensure
	 * they are safe to store in the database.  Child classes should override this
	 * method to make sure the data they are storing in the database is safe and
	 * as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @since  K4.0
	 */
	public function check()
	{
		return true;
	}

	/**
	 * @param   bool $updateNulls
	 *
	 * @return boolean
	 */
	public function store($updateNulls = false)
	{
		if ($this->exists())
		{
			$ret = static::$db->updateObject(static::$tbl, $this, static::$tbl_keys, $updateNulls);
		}
		else
		{
			$ret = static::$db->insertObject(static::$tbl, $this, static::$tbl_keys);
		}

		if (static::$locked)
		{
			$this->unlock();
		}

		if (!$ret)
		{
			$this->setError(get_class($this) . '::store failed - ' . static::$db->getErrorMsg());

			return false;
		}

		$this->_exists = true;

		return true;
	}

	/**
	 * Method to provide a shortcut to binding, checking and storing a JTable
	 * instance to the database table.  The method will check a row in once the
	 * data has been stored and if an ordering filter is present will attempt to
	 * reorder the table rows based on the filter.  The ordering filter is an instance
	 * property name.  The rows that will be reordered are those whose value matches
	 * the JTable instance for the property specified.
	 *
	 * @param   mixed   $src             An associative array or object to bind to the JTable instance.
	 * @param   string  $orderingFilter  Filter for the order updating
	 * @param   mixed   $ignore          An optional array or space separated list of properties
	 *                                   to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  K4.0
	 */
/*
 	public function save($src, $orderingFilter = '', $ignore = '')
	{
		// Attempt to bind the source to the instance.
		if (!$this->bind($src, $ignore))
		{
			return false;
		}

		// Run any sanity checks on the instance and verify that it is ready for storage.
		if (!$this->check())
		{
			return false;
		}

		// Attempt to store the properties to the database table.
		if (!$this->store())
		{
			return false;
		}

		// Attempt to check the row in, just in case it was checked out.
		if (!$this->checkin())
		{
			return false;
		}

		// If an ordering filter is set, attempt reorder the rows in the table based on the filter and value.
		if ($orderingFilter)
		{
			$filterValue = $this->$orderingFilter;
			$this->reorder($orderingFilter ?
				static::$db->quoteName($orderingFilter) . ' = ' . static::$db->Quote($filterValue) : '');
		}

		// Set the error to empty and return true.
		$this->setError('');

		return true;
	}
*/

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed    $keys  An optional primary key value to delete.  If not set the
	 *                          instance property value is used.  If array given, you can
	 *                          use arbitrary fields to delete more than one item.
	 *
	 * @return  boolean  True on success.
	 * @throws UnexpectedValueException
	 *
	 * @since  K4.0
	 */
	public function delete($keys = null)
	{
		try
		{
			$keys = $this->getKeyValues($keys);
		}
		catch (UnexpectedValueException $e)
		{
			throw $e;
		}

		// Delete the row by given keys/fields.
		$query = static::$db->getQuery(true)->delete()->from(static::$tbl);
		foreach ($keys as $key => $value) {
			$query->where(static::$db->quoteName($key) . ' = ' . static::$db->quote($value));
		}

		static::$db->setQuery($query, 0, 1);
		static::$db->execute();

		return true;
	}

	/**
	 * Method to check a row out if the necessary properties/fields exist.  To
	 * prevent race conditions while editing rows in a database, a row can be
	 * checked out if the fields 'checked_out' and 'checked_out_time' are available.
	 * While a row is checked out, any attempt to store the row by a user other
	 * than the one who checked the row out should be held until the row is checked
	 * in again.
	 *
	 * @param   integer  $userId  The Id of the user checking out the row.
	 * @param   mixed    $pk      An optional primary key value to check out.  If not set
	 *                            the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  K4.0
	 */
	public function checkOut($userId, $pk = null)
	{
		// If there is no checked_out or checked_out_time field, just return true.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			return true;
		}

		$k = static::$tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// If no primary key is given, return false.
		if ($pk === null)
		{
			throw new UnexpectedValueException('Null primary key not allowed.');
		}

		// Get the current time in MySQL format.
		$time = JFactory::getDate()->toSql();

		// Check the row out by primary key.
		$query = static::$db->getQuery(true);
		$query->update(static::$tbl);
		$query->set(static::$db->quoteName('checked_out') . ' = ' . (int) $userId);
		$query->set(static::$db->quoteName('checked_out_time') . ' = ' . static::$db->quote($time));
		$query->where(static::$tbl_key . ' = ' . static::$db->quote($pk));
		static::$db->setQuery($query);
		static::$db->execute();

		// Set table values in the object.
		$this->checked_out = (int) $userId;
		$this->checked_out_time = $time;

		return true;
	}

	/**
	 * Method to check a row in if the necessary properties/fields exist.  Checking
	 * a row in will allow other users the ability to edit the row.
	 *
	 * @param   mixed  $pk  An optional primary key value to check out.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  K4.0
	 */
	public function checkIn($pk = null)
	{
		// If there is no checked_out or checked_out_time field, just return true.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			return true;
		}

		$k = static::$tbl_keys;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// If no primary key is given, return false.
		if ($pk === null)
		{
			throw new UnexpectedValueException('Null primary key not allowed.');
		}

		// Check the row in by primary key.
		$query = static::$db->getQuery(true);
		$query->update(static::$tbl);
		$query->set(static::$db->quoteName('checked_out') . ' = 0');
		$query->set(static::$db->quoteName('checked_out_time') . ' = ' . static::$db->quote(static::$db->getNullDate()));
		$query->where(static::$tbl_key . ' = ' . static::$db->quote($pk));
		static::$db->setQuery($query);

		// Check for a database error.
		static::$db->execute();

		// Set table values in the object.
		$this->checked_out = 0;
		$this->checked_out_time = '';

		return true;
	}

	/**
	 * Method to increment the hits for a row if the necessary property/field exists.
	 *
	 * @return bool True on success.
	 *
	 * @internal param mixed $pk An optional primary key value to increment. If not set the instance property value is used.
	 *
	 * @since    K4.0
	 */
	public function hit()
	{
		$pk = null;

		// If there is no hits field, just return true.
		if (!property_exists($this, 'hits'))
		{
			return true;
		}

		$k = static::$tbl_keys;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// If no primary key is given, return false.
		if ($pk === null)
		{
			return false;
		}

		// Check the row in by primary key.
		$query = static::$db->getQuery(true);
		$query->update(static::$tbl);
		$query->set(static::$db->quoteName('hits') . ' = (' . static::$db->quoteName('hits') . ' + 1)');
		$query->where(static::$tbl_key . ' = ' . static::$db->quote($pk));
		static::$db->setQuery($query);
		static::$db->execute();

		// Set table values in the object.
		$this->hits++;

		return true;
	}

	/**
	 * Method to determine if a row is checked out and therefore uneditable by
	 * a user. If the row is checked out by the same user, then it is considered
	 * not checked out -- as the user can still edit it.
	 *
	 * @param   integer  $with     The userid to preform the match with, if an item is checked
	 *                             out by this user the function will return false.
	 * @param   integer  $against  The userid to perform the match against when the function
	 *                             is used as a static function.
	 *
	 * @return  boolean  True if checked out.
	 *
	 * @since  K4.0
	 */
	public function isCheckedOut($with = 0, $against = null)
	{
		// Handle the non-static case.
		if (isset($this) && ($this instanceof JTable) && is_null($against))
		{
			$against = $this->get('checked_out');
		}

		// The item is not checked out or is checked out by the same user.
		if (!$against || ($against == $with))
		{
			return false;
		}

		static::$db = JFactory::getDBO();
		static::$db->setQuery('SELECT COUNT(userid)' . ' FROM ' . static::$db->quoteName('#__session') . ' WHERE ' . static::$db->quoteName('userid') . ' = ' . (int) $against);
		$checkedOut = (boolean) static::$db->loadResult();

		// If a session exists for the user then it is checked out.
		return $checkedOut;
	}

	/**
	 * Method to lock the database table for writing.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  K4.0
	 * @throws  RuntimeException
	 */
	protected function lock()
	{
		static::$db->lockTable(static::$tbl);
		static::$_locked = true;

		return true;
	}

	/**
	 * Method to unlock the database table for writing.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  K4.0
	 */
	protected function unlock()
	{
		static::$db->unlockTables();
		static::$_locked = false;

		return true;
	}

	/**
	 * @internal
	 */
	static public function getQuery()
	{
		$db = static::$db;
		$query = $db->getQuery(true)->select('a.*')->from(static::$tbl . ' AS a');

		return $query;
	}

	/**
	 * @internal
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return array
	 */
	static public function &loadInstances(JDatabaseQuery $query)
	{
		$db = JFactory::getDbo();
		$db->setQuery($query);
		$items = (array) $db->loadObjectList('id', get_called_class());

		if (is_array(static::$instances))
		{
			static::$instances += $items;
		}

		return $items;
	}

	/**
	 * Returns all keys and their values as an array.
	 *
	 * @param   array|string $fields
	 * @param bool           $throw
	 *
	 * @return array
	 * @since  K4.0
	 */
	protected function getKeyValues($fields = null, $throw = true)
	{
		// FIXME: bug...
		static $fieldNames = null;

		$tableKeys = static::$tbl_keys;

		$keys = array();
		if (is_null($fields))
		{
			// No fields were given as parameter: use table instance.
			foreach ($tableKeys as $i => $keyName)
			{
				$keyValue = isset($this->$keyName) ? $this->$keyName : null;
				$keys[$keyName] = $keyValue;

				// If null primary keys aren't allowed
				if($throw && is_null($keyValue))
				{
					throw new UnexpectedValueException(sprintf('%s: Null primary key not allowed &#160; %s..', get_class($this), $keyName), 0);
				}
			}
		}
		else
		{
			if (is_null($fieldNames))
			{
				// Lazy initialize fields list.
				$fieldNames = static::getFields();
			}

			if (!is_array($fields))
			{
				$fields = (array) $fields;
			}

			foreach ($fields as $keyName => $keyValue)
			{
				// Check if key in given numeric location exists.
				if (is_numeric($keyName))
				{
					if (!isset($tableKeys[$keyName]))
					{
						throw new UnexpectedValueException(sprintf('%s: Missing key in index %s.', get_class($this), $keyName), 1);
					}

					// Find out key name in given numeric location and use it.
					$keyName = $tableKeys[$keyName];
				}

				$keys[$keyName] = $keyValue;

				// Verify that the used key exists in the table.
				if (!in_array($keyName, $fieldNames))
				{
					throw new UnexpectedValueException(sprintf('%s: Missing field in database: %s.', get_class($this), $keyName), 2);
				}
			}
		}

		// Make sure user didn't pass empty array.
		if (empty($keys))
		{
			throw new UnexpectedValueException(sprintf('%s: No fields given.', get_class($this)), 3);
		}

		return $keys;
	}

	/**
	 * Returns all keys and their values as an array.
	 *
	 * @param   array|string $fields
	 * @return array
	 * @since  K4.0
	 * @throws UnexpectedValueException
	 */
	static protected function resolveKeys($fields)
	{
		// First run: Initialise the table properties.
		if (is_null(static::$tbl_fields))
		{
			static::getFields();
		}

		$keys = array();

		if (!is_array($fields))
		{
			$fields = (array) $fields;
		}

		foreach ($fields as $keyName => $keyValue)
		{
			// Check if key in given numeric location exists.
			if (is_numeric($keyName))
			{
				if (!isset(static::$tbl_keys[$keyName]))
				{
					throw new UnexpectedValueException(sprintf('%s: Missing key in index: %s.', get_called_class(), $keyName), 1);
				}

				// Find out key name in given numeric location and use it.
				$keyName = static::$tbl_keys[$keyName];
			}

			$keys[$keyName] = $keyValue;

			// Verify that the used key exists in the table.
			if (!isset(static::$tbl_fields[$keyName]))
			{
				throw new UnexpectedValueException(sprintf('%s: Missing field in database: %s.', get_called_class(), $keyName), 2);
			}
		}

		// Make sure user didn't pass empty array.
		if (empty($keys))
		{
			throw new UnexpectedValueException(sprintf('%s: No fields given.', get_called_class()), 3);
		}

		return $keys;
	}
}

KunenaTableObject::setDbo(JFactory::getDbo());
