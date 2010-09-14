<?php
/**
 * @version $Id$
 * Kunena Component - KunenaUser class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Category Class
 */
class KunenaCategory extends JObject {
	// Global for every instance
	protected static $_instances = array ();

	protected $_exists = false;
	protected $_db = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct($identifier = 0) {
		// Always load the category -- if category does not exist: fill empty data
		$this->_db = JFactory::getDBO ();
		$this->_app = JFactory::getApplication ();
		$this->load ( $identifier );
	}

	/**
	 * Returns the global KunenaCategory object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$id		The category to load - Can be only an integer.
	 * @return	KunenaCategory	The Category object.
	 * @since	1.6
	 */
	static public function getInstance($identifier = null, $reset = false) {
		$c = __CLASS__;

		if ($identifier instanceof KunenaCategory) {
			return $identifier;
		} else if (is_numeric ( $identifier )) {
			$id = intval ( $identifier );
		}
		if ($id < 1)
			return new $c ();

		if (! $reset && empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new $c ( $id );
		}

		return self::$_instances [$id];
	}

	public function exists() {
		return $this->_exists;
	}

	static public function loadCategories($ids = false) {
		// Now that we have all users to cache, dedup the list
		if (! empty ($ids) ) {
			JArrayHelper::toInteger($ids);
			$ids = array_unique($ids);
			$idlist = implode ( ',', $ids );
		}

		$c = __CLASS__;
		$db = JFactory::getDBO ();
		$query = "SELECT c.* FROM #__kunena_categories AS c";
		if (isset($idlist))
			$query .= " WHERE id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = $db->loadAssocList ();
		KunenaError::checkDatabaseError ();

		$list = array ();
		foreach ( $results as $category ) {
			$instance = new $c ();
			$instance->bind ( $category, true );
			self::$_instances [$instance->id] = $instance;
			$list [$instance->id] = $instance;
		}

		return $list;
	}

	/**
	 * Method to get the category table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The category table name to be used
	 * @param	string	The category table prefix to be used
	 * @return	object	The category table object
	 * @since	1.6
	 */
	public function getTable($type = 'KunenaCategory', $prefix = 'Table') {
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance ( $tabletype ['name'], $tabletype ['prefix'] );
	}

	protected function bind($data, $exists = false) {
		$this->setProperties ( $data );
		$this->_exists = $exists;
	}

	/**
	 * Method to load a KunenaCategory object by catid
	 *
	 * @access	public
	 * @param	mixed	$identifier The category id of the user to load
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id) {
		// Create the user table object
		$table = &$this->getTable ();

		// Load the KunenaTableUser object based on the user id
		$this->_exists = $table->load ( $id );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaCategory object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new category
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false) {
		// Create the user table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Check and store the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		//are we creating a new user
		$isnew = ! $this->_exists;

		// If we aren't allowed to create new users return
		if (! $this->id || ($isnew && $updateOnly)) {
			return true;
		}

		//Store the user data in the database
		if (! $result = $table->store ()) {
			$this->setError ( $table->getError () );
		}

		// Set the id for the KunenaUser object in case we created a new user.
		if ($result && $isnew) {
			$this->load ( $table->get ( 'id' ) );
			self::$_instances [$table->get ( 'id' )] = $this;
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaCategory object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function delete() {
		if (!$this->id)
			return false;

		// Create the user table object
		$table = &$this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
		}
		$this->_exists = false;
		return $result;
	}

	/**
	 * Method to check out the KunenaCategory object
	 *
	 * @access	public
	 * @param	integer	$who
	 * @return	boolean	True if checked out by somebody else
	 * @since 1.6
	 */
	public function checkout($who) {
		if (!$this->_exists)
			return true;

		// Create the user table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$result = $table->checkout($who);

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );

		return $result;
	}

	/**
	 * Method to check in the KunenaCategory object
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function checkin() {
		if (!$this->_exists)
			return true;

		// Create the user table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$result = $table->checkin();

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );

		return $result;
	}

	/**
	 * Method to check if an item is checked out
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function isCheckedOut($with) {
		if (!$this->_exists)
			return false;

		// Create the user table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$result = $table->isCheckedOut($with);
		return $result;
	}
}
