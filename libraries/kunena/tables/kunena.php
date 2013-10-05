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

abstract class KunenaTable extends JTable {
	protected $_exists = false;

	public function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	public function load($keys = null, $reset = true) {
		// Implement JObservableInterface: Pre-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers)) $this->_observers->update('onBeforeLoad', array($keys, $reset));

		// Workaround Joomla 3.2 change.
		// TODO: remove check when we're only supporting J!3.5+.
		$tbl_keys = isset($this->_tbl_keys) ? $this->_tbl_keys : (array) $this->_tbl_key;

		if (empty($keys)) {
			$empty = true;
			$keys  = array();

			// If empty, use the value of the current key
			foreach ($tbl_keys as $key) {
				$empty      = $empty && empty($this->$key);
				$keys[$key] = $this->$key;
			}

			// If empty primary key there's is no need to load anything
			if ($empty) {
				return false;
			}

		} elseif (!is_array($keys)) {
			// Load by primary key.
			$keyCount = count($tbl_keys);

			if (!$keyCount) {
				throw new RuntimeException('No table keys defined.');
			} elseif ($keyCount > 1) {
				throw new InvalidArgumentException('Table has multiple primary keys specified, only one primary key value provided.');
			}
			$keys = array($this->getKeyName() => $keys);
		}

		if ($reset) $this->reset();

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

		if ($this->_db->getErrorNum()) {
			throw new RuntimeException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		}

		if (empty($row)) {
			// Check that we have a result.
			$result = false;
		} else {
			// Bind the object with the row and return.
			$result = $this->_exists = $this->bind($row);
		}

		// Implement JObservableInterface: Post-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers)) $this->_observers->update('onAfterLoad', array(&$result, $row));

		// Bind the object with the row and return.
		return $result;
	}

	public function store($updateNulls = false) {
		$k = $this->_tbl_keys;

		// Implement JObservableInterface: Pre-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers)) $this->_observers->update('onBeforeStore', array($updateNulls, $k));

		if ($this->exists()) {
			$result = $this->updateObject($updateNulls);
		} else {
			$result = $this->insertObject();
		}

		if (!$result) {
			$this->setError(get_class($this) . '::store() failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		$this->_exists = true;

		// Implement JObservableInterface: Post-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers)) $this->_observers->update('onAfterStore', array(&$result));

		return true;
	}

	protected function insertObject()
	{
		$fmtsql = 'INSERT INTO '.$this->_db->quoteName($this->_tbl).' (%s) VALUES (%s) ';
		$fields = array();
		$values = array();

		foreach (get_object_vars($this) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->_db->quoteName($k);
			$values[] = $this->_db->Quote($v);
		}
		$this->_db->setQuery(sprintf($fmtsql, implode(",", $fields) ,  implode(",", $values)));
		if (!$this->_db->execute()) {
			return false;
		}
		$id = $this->_db->insertid();
		if ($this->_tbl_key && !is_array($this->_tbl_key) && $id) {
			$k = $this->_tbl_key;
			$this->$k = $id;
		}
		return true;
	}

	/**
	 * Description
	 *
	 * @param [type] $updateNulls
	 */
	protected function updateObject($updateNulls=false)
	{
		$fmtsql = 'UPDATE '.$this->_db->quoteName($this->_tbl).' SET %s WHERE %s';
		$tmp = array();
		$where = '';
		// TODO: what if where is empty?

		foreach (get_object_vars($this) as $k => $v) {
			if (is_array($v) or is_object($v) or $k[0] == '_') { // internal or NA field
				continue;
			}

			if (is_array($this->_tbl_key) && in_array($k, $this->_tbl_key)) {
				// PK not to be updated
				$where[] = $k . '=' . $this->_db->Quote($v);
				continue;
			} elseif ($k == $this->_tbl_key) {
				// PK not to be updated
				$where = $k . '=' . $this->_db->Quote($v);
				continue;
			}

			if ($v === null) {
				if ($updateNulls) {
					$val = 'NULL';
				} else {
					continue;
				}
			} else {
				$val = $this->_db->Quote($v);
			}
			$tmp[] = $this->_db->quoteName($k) . '=' . $val;
		}

		// Nothing to update.
		if (empty($tmp)) {
			return true;
		}

		if (is_array($where)) {
			$where = implode(' AND ', $where);
		}
		$this->_db->setQuery(sprintf($fmtsql, implode(",", $tmp) , $where));
		return $this->_db->execute();
	}

	public function delete($pk = null)
	{
		// Workaround Joomla 3.2 change.
		// TODO: remove check when we're only supporting J!3.5+.
		$tbl_keys = isset($this->_tbl_keys) ? $this->_tbl_keys : (array) $this->_tbl_key;

		if (is_null($pk)) {
			$pk = array();

			foreach ($tbl_keys AS $key) {
				$pk[$key] = $this->$key;
			}
		} elseif (!is_array($pk)) {
			$key = reset($tbl_keys);
			$pk = array($key => $pk);
		}

		foreach ($tbl_keys AS $key) {
			$pk[$key] = is_null($pk[$key]) ? $this->$key : $pk[$key];

			if ($pk[$key] === null) {
				throw new UnexpectedValueException('Null primary key not allowed.');
			}
			$this->$key = $pk[$key];
		}

		// Implement JObservableInterface: Pre-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers)) $this->_observers->update('onBeforeDelete', array($pk));

		// Delete the row by primary key.
		$query = $this->_db->getQuery(true)
			->delete($this->_tbl);
		foreach ($pk as $key=>$value) {
			$query->where("{$this->_db->quoteName($key)} = {$this->_db->quote($value)}");
		}

		$this->_db->setQuery($query);

		// Check for a database error.
		$this->_db->execute();

		// Check for a database error.
		if (!$this->_db->getErrorNum()) {
			throw new RuntimeException($this->_db->getErrorMsg(), $this->_db->getErrorNum());
		}

		// Implement JObservableInterface: Post-processing by observers
		// TODO: remove if when we're only supporting J!3.5+.
		if (isset($this->_observers)) $this->_observers->update('onAfterDelete', array($pk));

		return true;
	}
}
