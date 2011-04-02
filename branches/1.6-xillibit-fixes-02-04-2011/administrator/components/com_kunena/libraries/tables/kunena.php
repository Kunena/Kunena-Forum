<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

abstract class KunenaTable extends JTable {
	protected $_exists = false;

	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	function load($oid = null) {
		$ret = parent::load ( $oid );
		if ($ret === true)
			$this->_exists = true;
		return $ret;
	}

	function store($updateNulls = false) {
		$k = $this->_tbl_key;

		if ($this->$k && $this->_exists === true) {
			$ret = $this->_db->updateObject ( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		} else {
			$ret = $this->_db->insertObject ( $this->_tbl, $this, $this->_tbl_key );
		}
		if (! $ret) {
			$this->setError ( get_class ( $this ) . '::store failed - ' . $this->_db->getErrorMsg () );
			return false;
		} else {
			return true;
		}
	}
}