<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage User
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ('joomla.utilities.date');

/**
 * Class KunenaUserBan
 */
class KunenaUserBan extends JObject
{
	/**
	 * @var array|KunenaUserBan[]
	 */
	protected static $_instances = array();
	protected static $_instancesByUserid = array();
	protected static $_instancesByIP = array();
	protected static $_useridcache = array();
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

	public function getUser() {
		return KunenaUserHelper::get((int) $this->userid);
	}

	public function getCreator() {
		return KunenaUserHelper::get((int) $this->created_by);
	}

	public function getModifier() {
		return KunenaUserHelper::get((int) $this->modified_by);
	}

	/**
	 * @param KunenaUserBan $instance
	 */
	private static function storeInstance($instance) {
		// Fill userid cache
		self::cacheUserid($instance->userid);
		self::cacheUserid($instance->created_by);
		self::cacheUserid($instance->modified_by);
		foreach ($instance->comments as $comment) {
			self::cacheUserid($comment->userid);
		}

		if ($instance->id) self::$_instances[$instance->id] = $instance;
		if ($instance->userid && ($instance->isEnabled() || !$instance->id)) {
			self::$_instancesByUserid[$instance->userid] = $instance;
		}
		if ($instance->ip && ($instance->isEnabled() || !$instance->id)) {
			self::$_instancesByIP[$instance->ip] = $instance;
		}
	}

	private static function cacheUserid($userid) {
		if ($userid > 0) self::$_useridcache[$userid] = $userid;
	}

	/**
	 * Returns the global KunenaUserBan object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int $identifier	The ban object to be loaded
	 * @return	KunenaUserBan			The ban object.
	 * @since	1.6
	 */
	public static function getInstance($identifier = null)
	{
		$c = __CLASS__;

		if (intval($identifier) < 1)
			return new $c();

		if (!isset(self::$_instances[$identifier])) {
			$instance = new $c($identifier);
			self::storeInstance($instance);
		}

		return isset(self::$_instances[$identifier]) ? self::$_instances[$identifier] : null;
	}

	/**
	 * Returns the global KunenaUserBan object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int $identifier	The ban object to be loaded
	 * @param	bool	$create
	 * @return	KunenaUserBan			The ban object.
	 * @since	1.6
	 */
	public static function getInstanceByUserid($identifier = null, $create = false)
	{
		$c = __CLASS__;

		if (intval($identifier) < 1)
			return new $c();

		if (!isset(self::$_instancesByUserid[$identifier])) {
			$instance = new $c();
			$instance->loadByUserid($identifier);
			self::storeInstance($instance);
		}
		return $create || !empty(self::$_instancesByUserid[$identifier]->id) ? self::$_instancesByUserid[$identifier] : null;
	}


	/**
	 * Returns the global KunenaUserBan object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int $identifier	The ban object to be loaded
	 * @param	bool $create
	 * @return	KunenaUserBan			The ban object.
	 * @since	1.6
	 */
	public static function getInstanceByIP($identifier = null, $create = false)
	{
		$c = __CLASS__;

		if (empty($identifier))
			return new $c();

		if (!isset(self::$_instancesByIP[$identifier])) {
			$instance = new $c();
			$instance->loadByIP($identifier);
			self::storeInstance($instance);
		}
		return $create || !empty(self::$_instancesByIP[$identifier]->id) ? self::$_instancesByIP[$identifier] : null;
	}

	public static function getBannedUsers($start=0, $limit=50) {
		$c = __CLASS__;
		$db = JFactory::getDBO ();
		$now = new JDate();
		$query = "SELECT b.*
			FROM #__kunena_users_banned AS b
			INNER JOIN #__users AS u ON u.id=b.userid
			WHERE (b.expiration = {$db->quote($db->getNullDate())} OR b.expiration > {$db->quote($now->toSql())})
			ORDER BY b.created_time DESC";
		$db->setQuery ( $query, $start, $limit );
		$results = $db->loadAssocList ();
		KunenaError::checkDatabaseError();

		$list = array();
		foreach ($results as $ban) {
			$instance = new $c();
			$instance->bind($ban);
			$instance->_exists = true;
			self::storeInstance($instance);
			$list[$instance->userid] = $instance;
		}
		return $list;
	}

	public static function getUserHistory($userid) {
		if (!$userid) return array();
		$c = __CLASS__;
		$db = JFactory::getDBO ();
		$query = "SELECT *
			FROM #__kunena_users_banned
			WHERE `userid`={$db->quote($userid)}
			ORDER BY id DESC";
		$db->setQuery ( $query );
		$results = $db->loadAssocList ();
		KunenaError::checkDatabaseError();

		$list = array();
		foreach ($results as $ban) {
			$instance = new $c();
			$instance->bind($ban);
			$instance->_exists = true;
			self::storeInstance($instance);
			$list[] = $instance;
		}
		return $list;
	}

	public function exists() {
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
	 * @param	string	$type	The user table name to be used
	 * @param	string	$prefix	The user table prefix to be used
	 * @return	object	The user table object
	 * @since	1.6
	 */
	public function getTable($type = 'KunenaUserBans', $prefix = 'Table')
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

	protected function bind($data)
	{
		$this->setProperties($data);
		$this->comments = !empty($this->comments) ? json_decode($this->comments) : array();
		$this->params = !empty($this->params) ? json_decode($this->params) : array();
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
		$exists = $table->load($id);

		$this->bind($table->getProperties());
		$this->_exists = $exists;
		return $exists;
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
		$exists = $table->loadByUserid($userid, $mode);
		$this->bind($table->getProperties());
		$this->_exists = $exists;
		return $exists;
	}

	/**
	 * Method to load a KunenaUserBan object by user id
	 *
	 * @access	public
	 * @param	string	$ip
	 * @param	int $mode KunenaUserBan::ANY or KunenaUserBan::ACTIVE
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function loadByIP($ip, $mode = self::ACTIVE)
	{
		// Create the user table object
		$table = $this->getTable();

		// Load the KunenaTableUser object based on the user id
		$exists = $table->loadByIP($ip, $mode);
		$this->bind($table->getProperties());
		$this->_exists = $exists;
		return $exists;
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
			$this->modified_time = self::$_now->toSql();
			$this->modified_by = self::$_my->id;
		}
	}

	public function canBan() {
		$userid = $this->userid;
		$me = KunenaUserHelper::getMyself();
		$user = KunenaUserHelper::get($userid);
		if (!$me->isModerator()) {
			$this->setError(JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR'));
			return false;
		}
		if (!$user->exists()) {
			$this->setError( JText::_( 'COM_KUNENA_LIB_USER_BAN_ERROR_NOT_USER', $userid ));
			return false;
		}
		if ($userid == $me->userid) {
			$this->setError( JText::_( 'COM_KUNENA_LIB_USER_BAN_ERROR_YOURSELF' ));
			return false;
		}
		if ($user->isAdmin()) {
			$this->setError(JText::sprintf( 'COM_KUNENA_LIB_USER_BAN_ERROR_ADMIN', $user->getName() ));
			return false;
		}
		if ($user->isModerator()) {
			$this->setError(JText::sprintf( 'COM_KUNENA_LIB_USER_BAN_ERROR_MODERATOR', $user->getName() ));
			return false;
		}
		return true;
	}

	public function isEnabled() {
		if ($this->isLifetime()) return true;
		$expiration = new JDate($this->expiration);
		if ($expiration->toUnix() > self::$_now->toUnix()) return true;
		return false;
	}

	public function isLifetime() {
		return $this->expiration == $this->_db->getNullDate();
	}

	public function addComment($comment) {
		if (is_string($comment) && !empty($comment)) {
			$c = new stdClass();
			$c->userid = self::$_my->id;
			$c->time = self::$_now->toSql();
			$c->comment = $comment;
			$this->comments[] = $c;
		}
	}

	public function setExpiration($expiration, $comment = '') {
		// Cannot change expiration if ban is not enabled
		if (!$this->isEnabled() && $this->id) return;

		if (!$expiration || $expiration == $this->_db->getNullDate()) {
			$this->expiration = $this->_db->getNullDate();
		} else {
			$date = new JDate($expiration);
			$this->expiration = $date->toUnix() > self::$_now->toUnix() ? $date->toSql() : self::$_now->toSql();
		}
		if ($this->_exists) {
			$this->modified_time = self::$_now->toSql();
			$this->modified_by = self::$_my->id;
		}
		$this->addComment($comment);
	}

	public function ban($userid=null, $ip=null, $block=0, $expiration=null, $reason_private='', $reason_public='', $comment='') {
		$this->userid = intval($userid) > 0 ? (int)$userid : null;
		$this->ip = $ip ? (string)$ip : null;
		$this->blocked = (int)$block;
		$this->setExpiration($expiration);
		$this->reason_private = (string)$reason_private;
		$this->reason_public = (string)$reason_public;
		$this->addComment($comment);
	}

	public function unBan($comment = '') {
		// Cannot change expiration if ban is not enabled
		if (!$this->isEnabled()) return;

		$this->expiration = self::$_now->toSql();
		$this->modified_time = self::$_now->toSql();
		$this->modified_by = self::$_my->id;
		$this->addComment($comment);
	}

	/**
	 * Method to save the KunenaUserBan object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new ban
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false)
	{
		if (!$this->canBan()) {
			return false;
		}

		if (!$this->id) {
			// If we have new ban, add creation date and user if they do not exist
			if (!$this->created_time) {
				$now = new JDate();
				$this->created_time = $now->toSql();
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
			$block = 0;
			if ($this->isEnabled()) $block = $this->blocked;
			if ($user->block != $block) {
				$user->block = $block;
				$user->save();
			}

			// Change user state also in #__kunena_users
			$profile = KunenaFactory::getUser($this->userid);
			$profile->banned = $this->expiration;
			$profile->save(true);

			if ($block) {
				// Logout blocked user
				$app = JFactory::getApplication ();
				$options = array();
				$options['clientid'][] = 0; // site
				$app->logout( (int) $this->userid, $options);
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
			self::storeInstance($this);
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
	public function delete()
	{
		// Create the user table object
		$table	= $this->getTable();

		$result = $table->delete($this->id);
		if (!$result) {
			$this->setError($table->getError());
		}
		return $result;

	}
}
