<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined ( '_JEXEC' ) or die ();

if (!class_exists('JDatabaseQuery'))
	kimport('joomla.database.databasequery');

abstract class KunenaTable extends JTable {
	protected $_exists = false;

	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	public function load($keys = null, $reset = true) {
		if (empty($keys)) {
			// If empty, use the value of the current key
			if (!is_array($this->_tbl_key)) {
				$keyName = $this->_tbl_key;
				$keyValue = $this->$keyName;

				// If empty primary key there's is no need to load anything
				if(empty($keyValue)) return false;
				$keys = array($keyName => $keyValue);
			} else {
				$keys = array();
				foreach ($this->_tbl_key as $keyName) {
					$keyValue = $this->$keyName;
					$keys[$keyName] = $keyValue;

					// If null primary key there's is no need to load anything
					if($keyValue === null) return false;
				}
			}
		} elseif (!is_array($keys)) {
			// Load by primary key.
			$keys = array($keys);
		}

		if ($reset) $this->reset();

		// Initialise the query.
		$query	= new JDatabaseQuery();
		$query->select('*');
		$query->from($this->_tbl);
		$fields = array_keys($this->getProperties());

		if (is_array($this->_tbl_key)) reset($this->_tbl_key);
		foreach ($keys as $field => $value) {
			if (is_numeric($field)) {
				if (!is_array($this->_tbl_key)) {
					$field = $this->_tbl_key;
				} else {
					list($i,$field) = each($this->_tbl_key);
				}
				$this->$field = $value;
			}
			// Check that $field is in the table.
			if (!in_array($field, $fields)) {
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_CLASS_IS_MISSING_FIELD', get_class($this), $field));
				$this->setError($e);
				return false;
			}
			// Add the search tuple to the query.
			$query->where($this->_db->nameQuote($field).' = '.$this->_db->quote($value));
		}

		$this->_db->setQuery($query);
		$row = $this->_db->loadAssoc();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$e = new JException($this->_db->getErrorMsg());
			$this->setError($e);
			return false;
		}

		// Check that we have a result.
		if (empty($row)) {
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
			$this->setError($e);
			return false;
		}

		// Bind the object with the row and return.
		return $this->_exists = $this->bind($row);
	}

	function store($updateNulls = false) {
		if ($this->exists()) {
			$ret = $this->updateObject ( $updateNulls );
		} else {
			$ret = $this->insertObject ();
		}
		if (! $ret) {
			$this->setError ( get_class ( $this ) . '::store failed - ' . $this->_db->getErrorMsg () );
			return false;
		}
		$this->_exists = true;
		return true;
	}

	protected function insertObject()
	{
		$fmtsql = 'INSERT INTO '.$this->_db->nameQuote($this->_tbl).' (%s) VALUES (%s) ';
		$fields = array();

		foreach (get_object_vars($this) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->_db->nameQuote($k);
			$values[] = $this->_db->isQuoted($k) ? $this->_db->Quote($v) : (int) $v;
		}
		$this->_db->setQuery(sprintf($fmtsql, implode(",", $fields) ,  implode(",", $values)));
		if (!$this->_db->query()) {
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
		$fmtsql = 'UPDATE '.$this->_db->nameQuote($this->_tbl).' SET %s WHERE %s';
		$tmp = array();

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
				$val = $this->_db->isQuoted($k) ? $this->_db->Quote($v) : (int) $v;
			}
			$tmp[] = $this->_db->nameQuote($k) . '=' . $val;
		}

		// Nothing to update.
		if (empty($tmp)) {
			return true;
		}

		if (is_array($where)) {
			$where = implode(' AND ', $where);
		}
		$this->_db->setQuery(sprintf($fmtsql, implode(",", $tmp) , $where));
		return $this->_db->query();
	}
}