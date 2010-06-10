<?php
/**
* @version $Id$
* Kunena Component - KunenaUserBan class
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

jimport ('joomla.utilities.date');

/**
* Kunena User Ban
*
* Provides access to the #__kunena_users_banlist table
*/
class KunenaUserBan extends JObject
{
	// Global for every instance
	protected static $_instances = array();
	protected static $_instancesByUserid = array();
	protected static $_instancesByIP = array();
	protected static $_now = null;
	protected static $_my = null;

	protected $_db = null;
	protected $_exists = false;


	const ANY = 0;
	const ACTIVE = 1;

	/**
	* Constructor
	*
	* @access	protected
	*/
	public function __construct($identifier = null)
	{
		if (self::$_now === null) {
			self::$_now = new JDate();
		}
		if (self::$_my === null) {
			self::$_my = JFactory::getUser();
		}

		// Always load the data -- if item does not exist: fill empty data
		$this->load($identifier);
		$this->_db = JFactory::getDBO ();
	}

	static private function storeInstance($instance) {
		if (!$instance->id) return;
		self::$_instances[$instance->id] = $instance;
		if ($instance->userid) {
			self::$_instancesByUserid[$instance->userid] = $instance;
		}
		if ($instance->ip) {
			self::$_instancesByIP[$instance->ip] = $instance;
		}
	}

	/**
	 * Returns the global KunenaUserBan object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int $id	The ban object to be loaded
	 * @return	KunenaUserBan			The ban object.
	 * @since	1.6
	 */
	static public function getInstance($identifier = null, $reset = false)
	{
		$c = __CLASS__;

		if (intval($identifier) < 1)
			return new $c();

		if (!$reset && empty(self::$_instances[$identifier])) {
			$instance = new $c($identifier);
			self::storeInstance($instance);
		}

		return isset(self::$_instances[$identifier]) ? self::$_instances[$identifier] : null;
	}

	/**
	 * Returns the global KunenaUserBan object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int $id	The ban object to be loaded
	 * @return	KunenaUserBan			The ban object.
	 * @since	1.6
	 */
	static public function getInstanceByUserid($identifier = null, $reset = false)
	{
		$c = __CLASS__;

		if (intval($identifier) < 1)
			return new $c();

		if (!$reset && empty(self::$_instancesByUserid[$identifier])) {
			$instance = new $c();
			$instance->loadByUserid($identifier);
			self::storeInstance($instance);
		}

		return isset(self::$_instancesByUserid[$identifier]) ? self::$_instancesByUserid[$identifier] : null;
	}


	/**
	 * Returns the global KunenaUserBan object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int $id	The ban object to be loaded
	 * @return	KunenaUserBan			The ban object.
	 * @since	1.6
	 */
	static public function getInstanceByIP($identifier = null, $reset = false)
	{
		$c = __CLASS__;

		if (empty($identifier))
			return new $c();

		if (!$reset && empty(self::$_instancesByIP[$identifier])) {
			$instance = new $c();
			$instance->loadByIP($identifier);
			self::storeInstance($instance);
		}

		return isset(self::$_instancesByIP[$identifier]) ? self::$_instancesByIP[$identifier] : null;
	}

	function exists() {
		return $this->_exists;
	}

	/**
	 * Method to get the ban table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The user table name to be used
	 * @param	string	The user table prefix to be used
	 * @return	object	The user table object
	 * @since	1.6
	 */
	function getTable($type = 'KunenaUserBan', $prefix = 'Table')
	{
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype['name'] || $prefix != $tabletype['prefix']) {
			$tabletype['name']		= $type;
			$tabletype['prefix']	= $prefix;
		}

		// Create the user table object
		return JTable::getInstance($tabletype['name'], $tabletype['prefix']);
	}

	/**
	 * Method to load a KunenaUserBan object by ban id
	 *
	 * @access	public
	 * @param	int	$id The ban id of the item to load
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id)
	{
		// Create the user table object
		$table = $this->getTable();

		// Load the KunenaTableUser object based on the user id
		$this->_exists = $table->load($id);

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());
		$this->comments = !empty($this->comments) ? json_decode($this->comments) : array();
		$this->params = !empty($this->params) ? json_decode($this->params) : array();
		return $this->_exists;
	}

	/**
	 * Method to load a KunenaUserBan object by user id
	 *
	 * @access	public
	 * @param	int	$userid The user id of the user to load
	 * @param	int $mode KunenaUserBan::ANY or KunenaUserBan::ACTIVE
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function loadByUserid($userid, $mode = self::ACTIVE)
	{
		// Create the user table object
		$table = $this->getTable();

		// Load the KunenaTableUser object based on the user id
		$this->_exists = $table->loadByUserid($userid, $mode);

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());
		$this->comments = !empty($this->comments) ? json_decode($this->comments) : array();
		$this->params = !empty($this->params) ? json_decode($this->params) : array();
		return $this->_exists;
	}

	/**
	 * Method to load a KunenaUserBan object by user id
	 *
	 * @access	public
	 * @param	int	$userid The user id of the user to load
	 * @param	int $mode KunenaUserBan::ANY or KunenaUserBan::ACTIVE
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function loadByIP($ip, $mode = self::ACTIVE)
	{
		// Create the user table object
		$table = $this->getTable();

		// Load the KunenaTableUser object based on the user id
		$this->_exists = $table->loadByIP($ip, $mode);

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());
		$this->comments = !empty($this->comments) ? json_decode($this->comments) : array();
		$this->params = !empty($this->params) ? json_decode($this->params) : array();
		return $this->_exists;
	}

	public function setReason($public=null, $private=null) {
		$set = false;
		if ($public !== null && $public != $this->reason_public) {
			$this->reason_public = (string) $public;
			$set = true;
		}
		if ($private !== null &&  $private != $this->reason_private) {
			$this->reason_private = (string) $private;
			$set = true;
		}

		if ($this->_exists && $set) {
			$this->modified_time = self::$_now->toMysql();
			$this->modified_by = self::$_my->id;
		}
	}

	public function isEnabled() {
		if (!$this->expiration) return true;
		$expiration = new JDate($this->expiration);
		if ($expiration->toUnix() > self::$_now->toUnix()) return true;
		return false;
	}

	public function setComment($comment) {
		if (is_string($comment) && !empty($comment)) {
			$this->comments[] = array ('userid'=>self::$_my->id, 'time'=>self::$_now->toMysql(), 'comment'=>$comment);
		}
	}

	public function setExpiration($expiration, $comment = '') {
		// Cannot change expiration if ban is not enabled
		if (!$this->isEnabled()) return;

		if (!$expiration) {
			$this->expiration = null;
		} else {
			$date = new JDate($expiration);
			$this->expiration = $date->toUnix() > self::$_now->toUnix() ? $date->toMysql() : self::$_now->toMysql();
		}
		if ($this->_exists) {
			$this->modified_time = self::$_now->toMysql();
			$this->modified_by = self::$_my->id;
		}
		if (is_string($comment) && !empty($comment)) {
			$this->comments[] = array ('userid'=>self::$_my->id, 'time'=>self::$_now->toMysql(), 'comment'=>$comment);
		}
	}

	public function ban($userid=null, $ip=null, $block=0, $expiration=null, $reason_private='', $reason_public='') {
		$this->userid = intval($userid) > 0 ? (int)$userid : null;
		$this->ip = $ip ? (string)$ip : null;
		$this->blocked = (int)$block;
		$this->setExpiration($expiration);
		$this->reason_private = (string)$reason_private;
		$this->reason_public = (string)$reason_public;
	}

	public function unBan($comment = '') {
		// Cannot change expiration if ban is not enabled
		if (!$this->isEnabled()) return;

		$this->expiration = self::$_now->toMysql();
		$this->modified_time = self::$_now->toMysql();
		$this->modified_by = self::$_my->id;
		if (is_string($comment) && !empty($comment)) {
			$this->comments[] = array ('userid'=>self::$_my->id, 'time'=>self::$_now->toMysql(), 'comment'=>$comment);
		}
	}

	function getBannedUsers() {
		$config = KunenaFactory::getConfig();
		$namefield = $config->username ? "username" : "name";
		$query = "SELECT b.*, u.{$namefield} as username, c.{$namefield} AS created_username, m.{$namefield} AS modified_username
			FROM #__kunena_users_banned AS b
			INNER JOIN #__users AS u ON b.userid=u.id
			LEFT JOIN #__users AS c ON b.created_by=c.id
			LEFT JOIN #__users AS m ON b.modified_by=m.id
			WHERE (b.expiration IS NULL OR b.expiration > NOW())
			GROUP BY b.id";
		$this->_db->setQuery ( $query );
		$banned_users = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load search result." );

		foreach ($banned_users as $key=>$ban) {
			$banned_users[$key]->comments = !empty($ban->comments) ? json_decode($ban->comments) : array();
			$banned_users[$key]->params = !empty($ban->params) ? json_decode($ban->params) : array();
		}
		return $banned_users;
	}

	function getUserHistory($userid) {
		if (!$userid) return array();
		$config = KunenaFactory::getConfig();
		$namefield = $config->username ? "username" : "name";
		$query = "SELECT b.*, u.{$namefield} as username, c.{$namefield} AS created_username, m.{$namefield} AS modified_username
			FROM #__kunena_users_banned AS b
			INNER JOIN #__users AS u ON b.userid=u.id
			LEFT JOIN #__users AS c ON b.created_by=c.id
			LEFT JOIN #__users AS m ON b.modified_by=m.id
			WHERE `userid`={$this->_db->quote($userid)} ORDER BY id DESC";
		$this->_db->setQuery ( $query );
		$user_history = $this->_db->loadObjectList ();
		check_dberror ( 'Unable to load ban history.' );

		foreach ($user_history as $key=>$ban) {
			$banned_users[$key]->comments = !empty($ban->comments) ? json_decode($ban->comments) : array();
			$banned_users[$key]->params = !empty($ban->params) ? json_decode($ban->params) : array();
		}
		return $user_history;
	}

	/**
	 * Method to save the KunenaUserBan object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new ban
	 * @return	boolean True on success
	 * @since 1.6
	 */
	function save($updateOnly = false)
	{
		if (!$this->id) {
			// If we have new ban, add creation date and user if they do not exist
			if (!$this->created_time) {
				$now = new JDate();
				$this->created_time = $now->toMysql();
			}
			if (!$this->created_by) {
				$my = JFactory::getUser();
				$this->created_by = $my->id;
			}
		}

		// Create the user table object
		$table	= $this->getTable();
		$table->bind($this->getProperties());

		// Check and store the object.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		//are we creating a new ban
		$isnew = !$this->_exists;

		// If we aren't allowed to create new ban, return
		if ($isnew && $updateOnly) {
			return true;
		}

		if ($this->userid) {
			// Change user block also in Joomla
			$user = JFactory::getUser($this->userid);
			if (!$user) {
				$this->setError("User {$this->userid} does not exist!");
				return false;
			}
			if ($user->block != $this->blocked) {
				$user->block = $this->blocked;
				$user->save();
			}
		}

		//Store the ban data in the database
		$result = $table->store();
		if (!$result) {
			$this->setError($table->getError());
		}

		// Set the id for the KunenaUserBan object in case we created a new ban.
		if ($result && $isnew) {
			$this->load($table->get('id'));
			self::$_instances[$table->get('id')] = $this;
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaUserBan object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	function delete()
	{
		// Create the user table object
		$table	= &$this->getTable();

		$result = $table->delete($this->id);
		if (!$result) {
			$this->setError($table->getError());
		}
		return $result;

	}
}
