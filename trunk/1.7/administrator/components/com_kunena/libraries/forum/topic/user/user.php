<?php
/**
 * @version $Id: topic.php 3759 2010-10-20 13:48:28Z mahagr $
 * Kunena Component - KunenaForumTopicUser Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.user');
kimport ('kunena.forum.topic.user.helper');

/**
 * Kunena Forum Topic User Class
 */
class KunenaForumTopicUser extends JObject {
	protected $_exists = false;
	protected $_db = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct($id = 0, $user = null) {
		// Always load user topic -- if topic does not exist: fill empty data
		$this->_db = JFactory::getDBO ();
		$this->load ( $id, $user );
	}

	static public function getInstance($id = null, $user = null, $reload = false) {
		return KunenaForumTopicUserHelper::get($id, $user, $reload);
	}

	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	/**
	 * Method to get the topics table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The topics table name to be used
	 * @param	string	The topics table prefix to be used
	 * @return	object	The topics table object
	 * @since	1.6
	 */
	public function getTable($type = 'KunenaUserTopics', $prefix = 'Table') {
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance ( $tabletype ['name'], $tabletype ['prefix'] );
	}

	public function bind($data, $ignore = array()) {
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties ( $data );
	}

	/**
	 * Method to load a KunenaForumTopicUser object by id
	 *
	 * @access	public
	 * @param	mixed	$id The topic id to be loaded
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id = null, $user = null) {
		if ($id === null) {
			$id = $this->id;
		}
		$user = KunenaUser::getInstance($user);

		// Create the table object
		$table = &$this->getTable ();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load ( array($user->userid, $id) );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaForumTopicUser object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new topic
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false) {
		// Create the topics table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Check and store the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		//are we creating a new topic
		$isnew = ! $this->_exists;

		// If we aren't allowed to create new topic return
		if ($isnew && $updateOnly) {
			return true;
		}

		//Store the topic data in the database
		if (! $result = $table->store ()) {
			$this->setError ( $table->getError () );
		}

		// Fill up KunenaForumTopicUser object in case we created a new topic.
		if ($result && $isnew) {
			$this->load ();
			self::$_instances [$this->user_id][$this->topic_id] = $this;
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaForumTopicUser object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function delete() {
		if (!$this->exists()) {
			return true;
		}

		// Create the table object
		$table = &$this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
		}
		$this->_exists = false;

		return $result;
	}

	function update() {

	}
}