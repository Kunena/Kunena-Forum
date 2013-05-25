<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaTable
 */
abstract class KunenaTable extends JTable {
	protected $_exists = false;

	public function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	public function load($keys = null, $reset = true) {
		try {
			$keys = $this->getKeyValues($keys);
		} catch (UnexpectedValueException $e) {
			// TODO: Current
			//$this->setError($e->getMessage());
			return false;
		}

		if ($reset) {
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true)->select('*')->from($this->_tbl);

		foreach ($keys as $field => $value) {
			// Make sure the object contains the search fields.
			// TODO: What to do.. This is incompatible to JTable, but almost needed on multi-key items.
			$this->$field = $value;

			// Add the search tuple to the query.
			$query->where($this->_db->quoteName($field).' = '.$this->_db->quote($value));
		}

		$this->_db->setQuery($query, 0, 1);

		$row = $this->_db->loadAssoc();

		// Check that we have a result.
		if (empty($row)) {
			return false;
		}

		// Bind the object with the row and return.
		return $this->_exists = $this->bind($row);
	}

	public function store($updateNulls = false) {
		if ($this->exists()) {
			$ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
		} else {
			$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}
		if (!$ret) {
			$this->setError(get_class($this) . '::store failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		$this->_exists = true;
		return true;
	}

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
	 * @link	http://docs.joomla.org/JTable/delete
	 * @since   Joomla 11.1
	 */
	public function delete($keys = null)
	{
		try {
			$keys = $this->getKeyValues($keys);
		} catch (UnexpectedValueException $e) {
			throw $e;
		}

		// Delete the row by given keys/fields.
		$query = $this->_db->getQuery(true)->delete()->from($this->_tbl);
		foreach ($keys as $key=>$value) {
			$query->where($this->_db->quoteName($key) . ' = ' . $this->_db->quote($value));
		}
		$this->_db->setQuery($query);
		$this->_db->execute();

		return true;
	}

	/**
	 * Returns all keys and their values as an array.
	 *
	 * @param array|string $fields
	 * @return array
	 * @throws UnexpectedValueException
	 */
	protected function getKeyValues($fields = null) {
		static $fieldNames = null;

		$tableKeys = (array) $this->_tbl_key;

		$keys = array();
		if (is_null($fields)) {
			// No fields were given as parameter: use table instance.
			foreach ($tableKeys as $keyName) {
				$keyValue = $this->$keyName;
				$keys[$keyName] = $keyValue;

				// If null primary keys aren't allowed
				if(is_null($keyValue)) {
					throw new UnexpectedValueException('%s: Null primary key not allowed &#160; %s..', get_class($this), $keyName);
				}
			}
		} else {
			if (is_null($fieldNames)) {
				// Lazy initialize fields list.
				$fieldNames = array_keys($this->getProperties());
			}

			if (!is_array($fields)) {
				$fields = (array) $fields;
			}

			foreach ($fields as $keyName => $keyValue) {
				// Check if key in given numeric location exists.
				if (is_numeric($keyName)) {
					if (!isset($tableKeys[$keyName])) {
						throw new UnexpectedValueException(sprintf('%s: Missing key in index %s.', get_class($this), $keyName));
					}
					// Find out key name in given numeric location and use it.
					$keyName = $tableKeys[$keyName];
				}
				$keys[$keyName] = $keyValue;

				// Verify that the used key exists in the table.
				if (!in_array($keyName, $fieldNames)) {
					throw new UnexpectedValueException(sprintf('%s: Missing field in database &#160; %s.', get_class($this), $keyName));
				}
			}
		}

		// Make sure user didn't pass empty array.
		if (empty($keys)) {
			throw new UnexpectedValueException(sprintf('%s: No fields given.', get_class($this)));
		}

		return $keys;
	}
}
