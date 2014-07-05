<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Category.User
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumCategoryUser
 *
 * @property int $user_id
 * @property int $category_id
 * @property int $role
 * @property string $allreadtime
 * @property int $subscribed
 * @property string $params
 *
 */
class KunenaForumCategoryUser extends JObject {
	protected $_exists = false;
	protected $_db = null;

	/**
	 * @param int  $category
	 * @param mixed $user
	 *
	 * @internal
	 */
	public function __construct($category = 0, $user = null) {
		// Always fill empty data
		$this->_db = JFactory::getDBO ();

		// Create the table object
		$table = $this->getTable ();

		// Lets bind the data
		$this->setProperties ( $table->getProperties () );
		$this->_exists = false;
		$this->category_id = $category;
		$this->user_id = KunenaUserHelper::get($user)->userid;
	}

	/**
	 * @param null|int $id
	 * @param mixed $user
	 * @param bool $reload
	 *
	 * @return KunenaForumCategoryUser
	 */
	static public function getInstance($id = null, $user = null, $reload = false) {
		return KunenaForumCategoryUserHelper::get($id, $user, $reload);
	}

	/**
	 * @return KunenaForumCategory
	 */
	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->category_id);
	}

	/**
	 * @param null|bool $exists		True/false will change the state of the object.
	 *
	 * @return bool
	 */
	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	/**
	 * Method to get the categories table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @param string $type		The categories table name to be used
	 * @param string $prefix	The categories table prefix to be used
	 *
	 * @return JTable|TableKunenaUserCategories		The categories table object
	 */
	public function getTable($type = 'KunenaUserCategories', $prefix = 'Table') {
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance ( $tabletype ['name'], $tabletype ['prefix'] );
	}

	/**
	 * @param array $data
	 * @param array $ignore
	 */
	public function bind($data, $ignore = array()) {
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties ( $data );
	}

	/**
	 * Method to load a KunenaForumCategoryUser object by id.
	 *
	 * @param null|int	$category_id		The category id to be loaded.
	 * @param mixed		$user				The user to be loaded.
	 *
	 * @return bool
	 */
	public function load($category_id = null, $user = null) {
		if ($category_id === null) {
			$category_id = $this->category_id;
		}
		if ($user === null && $this->user_id !== null) {
			$user = $this->user_id;
		}
		$user = KunenaUserHelper::get($user);

		// Create the table object
		$table = $this->getTable ();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load ( array('user_id'=>$user->userid, 'category_id'=>$category_id) );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaForumCategoryUser object to the database.
	 *
	 * @param bool $updateOnly	Save the object only if not a new category.
	 *
	 * @return bool	True on success
	 */
	public function save($updateOnly = false) {
		// Create the categories table object
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Check and store the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		//are we creating a new category
		$isnew = ! $this->_exists;

		// If we aren't allowed to create new category return
		if ($isnew && $updateOnly) {
			return true;
		}

		//Store the category data in the database
		if (! $result = $table->store ()) {
			$this->setError ( $table->getError () );
		}

		// Fill up KunenaForumCategoryUser object in case we created a new category.
		if ($result && $isnew) {
			$this->load ();
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaForumCategoryUser object from the database.
	 *
	 * @return bool	True on success
	 */
	public function delete() {
		if (!$this->exists()) {
			return true;
		}

		// Create the table object
		$table = $this->getTable ();

		$result = $table->delete ( array('category_id'=>$this->category_id, 'user_id'=>$this->user_id) );
		if (! $result) {
			$this->setError ( $table->getError () );
		}
		$this->_exists = false;

		return $result;
	}
}
