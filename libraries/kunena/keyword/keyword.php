<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Keyword
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Keyword Class
 */
class KunenaKeyword extends JObject {
	protected $_exists = false;
	protected $_db = null;

	public $public_count = 0;

	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct($identifier) {
		$this->_db = JFactory::getDBO ();
		$this->id = 0;
		$this->name = $identifier;
		$this->global_count = 0;
		$this->total_count = 0;
	}

	/**
	 * Returns KunenaKeyword object
	 *
	 * @param	int	$identifier		The keyword to load - Can be either string or integer
	 * @param	bool	$reset
	 * @return	KunenaKeyword		The topic object.
	 * @since	1.7
	 */
	static public function getInstance($identifier, $reset = false) {
		return KunenaKeywordHelper::get($identifier, $reset);
	}

	public function addTopic($topic_id, $user_id) {
		if (!$user_id) $this->public_count++;
		$this->total_count++;
		if (!$this->save()) {
			return false;
		}
		$query = "INSERT INTO #__kunena_keywords_map (keyword_id, user_id, topic_id) VALUES ({$this->id}, {$user_id}, {$topic_id})";
		$this->_db->setQuery($query);
		$this->_db->query();
		KunenaError::checkDatabaseError ();
		return true;
	}

	public function delTopic($topic_id, $user_id) {
		if (!$user_id) $this->public_count--;
		$this->total_count--;
		if (!$this->save()) {
			return false;
		}
		$query = "DELETE FROM #__kunena_keywords_map WHERE keyword_id={$this->id} AND topic_id={$topic_id} AND user_id={$user_id}";
		$this->_db->setQuery($query);
		$this->_db->query();
		KunenaError::checkDatabaseError ();
		return true;
	}

	public function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	/**
	 * Method to get the keywords table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	$type	The keywords table name to be used
	 * @param	string	$prefix	The keywords table prefix to be used
	 * @return	object	The keywords table object
	 * @since	1.6
	 */
	public function getTable($type = 'KunenaKeywords', $prefix = 'Table') {
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
	 * Method to load a KunenaKeyword object by id
	 *
	 * @access	public
	 * @param	mixed	$id The keyword id to be loaded
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id) {
		// Create the table object
		$table = $this->getTable ();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load ( $id );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		return $this->_exists;
	}

	public function bind($data, $allow = array()) {
		if (!empty($allow)) $data = array_intersect_key($data, array_flip($allow));
		$this->setProperties ( $data );
	}

	/**
	 * Method to save the KunenaKeyword object to the database
	 *
	 * @access	public
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save() {
		//are we creating a new topic
		$isnew = ! $this->_exists;

		// Create the topics table object
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Check and store the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		//Store the topic data in the database
		if (! $result = $table->store ()) {
			$this->setError ( $table->getError () );
		}

		// Set the id for the KunenaKeyword object in case we created a new topic.
		if ($result && $isnew) {
			$this->load ( $table->id );
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaKeyword object from the database
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
		$table = $this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
		}
		$this->_exists = false;

		$db = JFactory::getDBO ();
		// Delete all keyword mappings
		$queries[] = "DELETE FROM #__kunena_keywords_map WHERE keyword_id={$db->quote($this->id)}";

		$result = true;
		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->query();
			$result = $result && KunenaError::checkDatabaseError ();
		}

		return $result;
	}

	public function recount() {
	}
}
