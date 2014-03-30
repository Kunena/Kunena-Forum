<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Object
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaDatabaseObject
 */
abstract class KunenaDatabaseObject extends JObject {
	public $id = null;

	protected $_name = null;
	protected $_table = null;
	protected $_exists = false;
	protected $_saving = false;

	/**
	 * Returns the global object.
	 *
	 * @param  int      $identifier  Object identifier to load.
	 * @param  boolean  $reload      Force object reload from the database.
	 *
	 * @return  KunenaDatabaseObject
	 */
	static public function getInstance($identifier = null, $reload = false) {}

	/**
	 * Returns true if the object exists in the database.
	 *
	 * @param   boolean  $exists  Internal parameter to change state.
	 * @return  boolean  True if object exists in database.
	 */
	public function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = (bool) $exists;
		return $return;
	}

	/**
	 * Method to bind an associative array to the instance.
	 *
	 * This method optionally takes an array of properties to ignore or allow when binding.
	 *
	 * @param   array    $src     An associative array or object to bind to the JTable instance.
	 * @param   array    $fields  An optional array list of properties to ignore / include only while binding.
	 * @param   boolean  $include  True to include only listed fields, false to ignore listed fields.
	 *
	 * @return  boolean  True on success.
	 */
	public function bind(array $src = null, array $fields = null, $include = false) {
		if (empty($src)) return false;

		if (!empty($fields)) {
			$src = $include ? array_intersect_key($src, array_flip($fields)) : array_diff_key($src, array_flip($fields));
		}
		$this->setProperties ( $src );
		return true;
	}

	/**
	 * Method to load object from the database.
	 *
	 * @param   mixed    $id  Id to be loaded.
	 *
	 * @return  boolean  True on success.
	 */
	public function load($id = null) {
		if ($id !== null) $this->id = intval($id);

		// Create the table object
		$table = $this->getTable ();

		// Load the object based on id
		if ($this->id) $this->_exists = $table->load ( $this->id );

		// Always set id
		$table->id = $this->id;

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );

		return $this->_exists;
	}

	/**
	 * Method to save the object to the database.
	 *
	 * Before saving the object, this method checks if object can be safely saved.
	 * It will also trigger onKunenaBeforeSave and onKunenaAfterSave events.
	 *
	 * @return  boolean  True on success.
	 */
	public function save() {
		$this->_saving = true;

		// Check the object.
		if (! $this->check ()) {
			return $this->_saving = false;
		}

		// Initialize table object.
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$isNew = ! $this->_exists;

		// Check the table object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return $this->_saving = false;
		}

		// Include the Kunena plugins for the on save events.
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		// Trigger the onKunenaBeforeSave event.
		$result = $dispatcher->trigger('onKunenaBeforeSave', array("com_kunena.{$this->_name}", &$table, $isNew));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return $this->_saving = false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return $this->_saving = false;
		}

		// If item was created, load the object.
		if ($isNew) {
			$this->load ( $table->id );
		}

		$this->saveInternal();

		// Trigger the onKunenaAfterSave event.
		$dispatcher->trigger('onKunenaAfterSave', array("com_kunena.{$this->_name}", &$table, $isNew));

		$this->_saving = false;
		return true;
	}

	/**
	 * Method to delete the object from the database.
	 *
	 * @return	boolean	True on success.
	 */
	public function delete() {
		// TODO: return false
		if (!$this->exists()) {
			return true;
		}

		// Initialize table object.
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Include the Kunena plugins for the on save events.
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		// Trigger the onKunenaBeforeDelete event.
		$result = $dispatcher->trigger('onKunenaBeforeDelete', array("com_kunena.{$this->_name}", $table));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		if (!$table->delete()) {
			$this->setError($table->getError());
			return false;
		}
		$this->_exists = false;

		// Trigger the onKunenaAfterDelete event.
		$dispatcher->trigger('onKunenaAfterDelete', array("com_kunena.{$this->_name}", $table));

		return true;
	}

	/**
	 * Method to perform sanity checks on the instance properties to ensure
	 * they are safe to store in the database.
	 *
	 * Child classes should override this method to make sure the data they are storing in
	 * the database is safe and as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 */
	public function check() {
		return true;
	}

	// Internal functions

	/**
	 * Class constructor, overridden in descendant classes.
	 *
	 * @param   mixed  $properties  Associative array to set the initial properties of the object.
	 *                              If not profided, default values will be used.
	 *
	 * @return  KunenaDatabaseObject
	 * @internal
	 */
	public function __construct(array $properties = null)
	{
		if (!$this->_name) $this->_name = get_class ($this);
		if ($properties) {
			$this->bind ($properties);
		} else {
			$this->load ();
		}
	}

	/**
	 * Method to get the table object.
	 *
	 * @return  JTable|KunenaTable  The table object.
	 */
	protected function getTable() {
		return JTable::getInstance ( $this->_table, 'Table' );
	}

	/**
	 * Internal save method.
	 *
	 * @return  boolean  True on success.
	 */
	protected function saveInternal() {
		return true;
	}
}
