<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

abstract class KunenaTable extends JTable
{
	protected $_exists = false;

	/**
	 * @param   null $exists
	 *
	 * @return boolean
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
	 * @param   null $keys
	 * @param   bool $reset
	 *
	 * @return boolean
	 */
	public function load($keys = null, $reset = true)
	{
		// Implement JObservableInterface: Pre-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers))
		{
			$this->_observers->update('onBeforeLoad', array($keys, $reset));
		}

		// Workaround Joomla 3.2 change.
		// TODO: remove check when we're only supporting J!3.5+.
		$tbl_keys = isset($this->_tbl_keys) ? $this->_tbl_keys : (array) $this->_tbl_key;

		if (empty($keys))
		{
			$empty = true;
			$keys  = array();

			// If empty, use the value of the current key
			foreach ($tbl_keys as $key)
			{
				$empty      = $empty && empty($this->$key);
				$keys[$key] = $this->$key;
			}

			// If empty primary key there's is no need to load anything
			if ($empty)
			{
				return false;
			}
		}
		elseif (!is_array($keys))
		{
			// Load by primary key.
			$keyCount = count($tbl_keys);

			if (!$keyCount)
			{
				throw new RuntimeException('No table keys defined.');
			}
			elseif ($keyCount > 1)
			{
				throw new InvalidArgumentException('Table has multiple primary keys specified, only one primary key value provided.');
			}

			$keys = array($this->getKeyName() => $keys);
		}

		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true)
			->select('*')
			->from($this->_tbl);
		$fields = array_keys($this->getProperties());

		foreach ($keys as $field => $value)
		{
			// Check that $field is in the table.
			if (!in_array($field, $fields))
			{
				throw new UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
			}

			// Add the search tuple to the query.
			$query->where($this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
		}

		$this->_db->setQuery($query);

		$row = $this->_db->loadAssoc();

		if ($this->_db->getErrorNum())
		{
			throw new RuntimeException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		}

		if (empty($row))
		{
			// Check that we have a result.
			$result = false;
		}
		else
		{
			// Bind the object with the row and return.
			$result = $this->_exists = $this->bind($row);
		}

		// Implement JObservableInterface: Post-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers))
		{
			$this->_observers->update('onAfterLoad', array(&$result, $row));
		}

		// Bind the object with the row and return.
		return $result;
	}

	/**
	 * @param   bool $updateNulls
	 *
	 * @return boolean
	 */
	public function store($updateNulls = false)
	{
		// Workaround Joomla 3.2 change.
		// TODO: remove check when we're only supporting J!3.5+.
		$k = isset($this->_tbl_keys) ? $this->_tbl_keys : (array) $this->_tbl_key;

		// Implement JObservableInterface: Pre-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers))
		{
			$this->_observers->update('onBeforeStore', array($updateNulls, $k));
		}

		if ($this->exists())
		{
			$result = $this->updateObject($updateNulls);
		}
		else
		{
			$result = $this->insertObject();
		}

		if (!$result)
		{
			$this->setError(get_class($this) . '::store() failed - ' . $this->_db->getErrorMsg());

			return false;
		}

		$this->_exists = true;

		// Implement JObservableInterface: Post-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers))
		{
			$this->_observers->update('onAfterStore', array(&$result));
		}

		return true;
	}

	/**
	 * Inserts a row into a table based on an object's properties.
	 *
	 * @return  boolean    True on success.
	 *
	 * @throws  RuntimeException
	 */
	protected function insertObject()
	{
		$fields = array();
		$values = array();

		// Workaround Joomla 3.2 change.
		// TODO: remove check when we're only supporting J!3.5+.
		$tbl_keys = isset($this->_tbl_keys) ? $this->_tbl_keys : (array) $this->_tbl_key;

		// Iterate over the object variables to build the query fields and values.
		foreach (get_object_vars($this) as $k => $v)
		{
			// Only process non-null scalars.
			if (is_array($v) or is_object($v) or $v === null)
			{
				continue;
			}

			// Ignore any internal fields.
			if ($k[0] == '_')
			{
				continue;
			}

			// Prepare and sanitize the fields and values for the database query.
			$fields[] = $this->_db->quoteName($k);
			$values[] = $this->_db->quote($v);
		}

		// Create the base insert statement.
		$query = $this->_db->getQuery(true)
			->insert($this->_db->quoteName($this->_tbl))
			->columns($fields)
			->values(implode(',', $values));

		// Set the query and execute the insert.
		$this->_db->setQuery($query);

		if (!$this->_db->execute())
		{
			return false;
		}

		// Update the primary key if it exists.
		$id = $this->_db->insertid();

		if (count($tbl_keys) == 1 && $id)
		{
			$key = reset($tbl_keys);
			$this->$key = $id;
		}

		return true;
	}

	/**
	 * Updates a row in a table based on an object's properties.
	 *
	 * @param   boolean  $nulls    True to update null fields or false to ignore them.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  RuntimeException
	 */
	public function updateObject($nulls = false)
	{
		$fields = array();
		$where = array();

		// Workaround Joomla 3.2 change.
		// TODO: remove check when we're only supporting J!3.5+.
		$tbl_keys = isset($this->_tbl_keys) ? $this->_tbl_keys : (array) $this->_tbl_key;

		// Create the base update statement.
		$statement = 'UPDATE ' . $this->_db->quoteName($this->_tbl) . ' SET %s WHERE %s';

		// Iterate over the object variables to build the query fields/value pairs.
		foreach (get_object_vars($this) as $k => $v)
		{
			// Only process scalars that are not internal fields.
			if (is_array($v) or is_object($v) or $k[0] == '_')
			{
				continue;
			}

			// Set the primary key to the WHERE clause instead of a field to update.
			if (in_array($k, $tbl_keys))
			{
				$where[] = $this->_db->quoteName($k) . '=' . $this->_db->quote($v);
				continue;
			}

			// Prepare and sanitize the fields and values for the database query.
			if ($v === null)
			{
				// If the value is null and we want to update nulls then set it.
				if ($nulls)
				{
					$val = 'NULL';
				}
				// If the value is null and we do not want to update nulls then ignore this field.
				else
				{
					continue;
				}
			}
			// The field is not null so we prep it for update.
			else
			{
				$val = $this->_db->quote($v);
			}

			// Add the field to be updated.
			$fields[] = $this->_db->quoteName($k) . '=' . $val;
		}

		// We don't have any fields to update.
		if (empty($fields))
		{
			return true;
		}

		// Set the query and execute the update.
		$this->_db->setQuery(sprintf($statement, implode(",", $fields), implode(' AND ', $where)));

		return $this->_db->execute();
	}

	public function delete($pk = null)
	{
		// Workaround Joomla 3.2 change.
		// TODO: remove check when we're only supporting J!3.5+.
		$tbl_keys = isset($this->_tbl_keys) ? $this->_tbl_keys : (array) $this->_tbl_key;

		if (is_null($pk))
		{
			$pk = array();

			foreach ($tbl_keys AS $key) {
				$pk[$key] = $this->$key;
			}
		}
		elseif (!is_array($pk))
		{
			$key = reset($tbl_keys);
			$pk = array($key => $pk);
		}

		foreach ($tbl_keys AS $key)
		{
			$pk[$key] = is_null($pk[$key]) ? $this->$key : $pk[$key];

			if ($pk[$key] === null)
			{
				throw new UnexpectedValueException('Null primary key not allowed.');
			}

			$this->$key = $pk[$key];
		}

		// Implement JObservableInterface: Pre-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers))
		{
			$this->_observers->update('onBeforeDelete', array($pk));
		}

		// Delete the row by primary key.
		$query = $this->_db->getQuery(true)
			->delete($this->_tbl);

		foreach ($pk as $key => $value)
		{
			$query->where("{$this->_db->quoteName($key)} = {$this->_db->quote($value)}");
		}

		$this->_db->setQuery($query);

		// Check for a database error.
		$this->_db->execute();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			throw new RuntimeException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		}

		// Implement JObservableInterface: Post-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers))
		{
			$this->_observers->update('onAfterDelete', array($pk));
		}

		return true;
	}
}
