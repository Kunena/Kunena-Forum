<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    User
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Date\Date;
use \Joomla\CMS\Table\Table;

jimport('joomla.utilities.date');

/**
 * Class KunenaUserBan
 *
 * @property    integer $expiration
 * @property    integer $created_time
 *
 * @since Kunena
 */
class KunenaUserBan extends CMSObject
{
	/**
	 * @since Kunena
	 */
	const ANY = 0;

	/**
	 * @since Kunena
	 */
	const ACTIVE = 1;

	/**
	 * @var array|KunenaUserBan[]
	 * @since Kunena
	 */
	protected static $_instances = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $_instancesByUserid = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $_instancesByIP = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $_useridcache = array();

	/**
	 * @var Date|null
	 * @since Kunena
	 */
	protected static $_now = null;

	/**
	 * @var \Joomla\CMS\User\User|null
	 * @since Kunena
	 */
	protected static $_my = null;

	/**
	 * @var JDatabaseDriver|null
	 * @since Kunena
	 */
	protected $_db = null;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $_exists = false;

	/**
	 * Constructor
	 *
	 * @access    protected
	 *
	 * @param   null $identifier identifier
	 *
	 * @since     Kunena
	 */
	public function __construct($identifier = null)
	{
		if (self::$_now === null)
		{
			self::$_now = new Date;
		}

		if (self::$_my === null)
		{
			self::$_my = Factory::getUser();
		}

		// Always load the data -- if item does not exist: fill empty data
		$this->load($identifier);
		$this->_db = Factory::getDBO();
	}

	/**
	 * Method to load a KunenaUserBan object by ban id
	 *
	 * @access    public
	 *
	 * @param   int $id The ban id of the item to load
	 *
	 * @return    boolean            True on success
	 * @since     1.6
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
	 * Method to get the ban table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access    public
	 *
	 * @param   string $type   The user table name to be used
	 * @param   string $prefix The user table prefix to be used
	 *
	 * @return    object    The user table object
	 * @since     1.6
	 */
	public function getTable($type = 'KunenaUserBans', $prefix = 'Table')
	{
		static $tabletype = null;

		// Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype['name'] || $prefix != $tabletype['prefix'])
		{
			$tabletype['name']   = $type;
			$tabletype['prefix'] = $prefix;
		}

		// Create the user table object
		return Table::getInstance($tabletype['name'], $tabletype['prefix']);
	}

	/**
	 * @param   mixed $data data
	 *
	 * @since Kunena
	 * @return void
	 */
	protected function bind($data)
	{
		$this->setProperties($data);
		$this->comments = !empty($this->comments) ? json_decode($this->comments) : array();
		$this->params   = !empty($this->params) ? json_decode($this->params) : array();
	}

	/**
	 * Returns the global KunenaUserBan object, only creating it if it doesn't already exist.
	 *
	 * @access    public
	 *
	 * @param   int $identifier The ban object to be loaded
	 *
	 * @return    KunenaUserBan            The ban object.
	 * @since     1.6
	 */
	public static function getInstance($identifier = null)
	{
		$c = __CLASS__;

		if (intval($identifier) < 1)
		{
			return new $c;
		}

		if (!isset(self::$_instances[$identifier]))
		{
			$instance = new $c($identifier);
			self::storeInstance($instance);
		}

		return isset(self::$_instances[$identifier]) ? self::$_instances[$identifier] : null;
	}

	/**
	 * @param   KunenaUserBan $instance instance
	 *
	 * @since Kunena
	 * @return void
	 */
	private static function storeInstance($instance)
	{
		// Fill userid cache
		self::cacheUserid($instance->userid);
		self::cacheUserid($instance->created_by);
		self::cacheUserid($instance->modified_by);

		foreach ($instance->comments as $comment)
		{
			self::cacheUserid($comment->userid);
		}

		if ($instance->id)
		{
			self::$_instances[$instance->id] = $instance;
		}

		if ($instance->userid && ($instance->isEnabled() || !$instance->id))
		{
			self::$_instancesByUserid[$instance->userid] = $instance;
		}

		if ($instance->ip && ($instance->isEnabled() || !$instance->id))
		{
			self::$_instancesByIP[$instance->ip] = $instance;
		}
	}

	/**
	 * @param   integer $userid userid
	 *
	 * @since Kunena
	 * @return void
	 */
	private static function cacheUserid($userid)
	{
		if ($userid > 0)
		{
			self::$_useridcache[$userid] = $userid;
		}
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function isEnabled()
	{
		if ($this->isLifetime())
		{
			return true;
		}

		$expiration = new Date($this->expiration);

		if ($expiration->toUnix() > self::$_now->toUnix())
		{
			return true;
		}

		return false;
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function isLifetime()
	{
		return $this->expiration == '9999-12-31 23:59:59';
	}

	/**
	 * Returns the global KunenaUserBan object, only creating it if it doesn't already exist.
	 *
	 * @access    public
	 *
	 * @param   int  $identifier The ban object to be loaded
	 * @param   bool $create     create
	 *
	 * @return    KunenaUserBan            The ban object.
	 * @since     1.6
	 */
	public static function getInstanceByUserid($identifier = null, $create = false)
	{
		$c = __CLASS__;

		if (intval($identifier) < 1)
		{
			return new $c;
		}

		if (!isset(self::$_instancesByUserid[$identifier]))
		{
			$instance = new $c;
			$instance->loadByUserid($identifier);
			self::storeInstance($instance);
		}

		return $create || !empty(self::$_instancesByUserid[$identifier]->id) ? self::$_instancesByUserid[$identifier] : null;
	}

	/**
	 * Returns the global KunenaUserBan object, only creating it if it doesn't already exist.
	 *
	 * @access    public
	 *
	 * @param   int  $identifier The ban object to be loaded
	 * @param   bool $create     create
	 *
	 * @return    KunenaUserBan            The ban object.
	 * @since     1.6
	 */
	public static function getInstanceByIP($identifier = null, $create = false)
	{
		$c = __CLASS__;

		if (empty($identifier))
		{
			return new $c;
		}

		if (!isset(self::$_instancesByIP[$identifier]))
		{
			$instance = new $c;
			$instance->loadByIP($identifier);
			self::storeInstance($instance);
		}

		return $create || !empty(self::$_instancesByIP[$identifier]->id) ? self::$_instancesByIP[$identifier] : null;
	}

	/**
	 * @param   int $start start
	 * @param   int $limit limit
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getBannedUsers($start = 0, $limit = 50)
	{
		$c   = __CLASS__;
		$db  = Factory::getDBO();
		$now = new Date;

		$query = $db->getQuery(true);
		$query->select('b.*')
			->from($db->quoteName('#__kunena_users_banned') . ' AS b')
			->innerJoin($db->quoteName('#__users') . ' AS u ON u.id=b.userid')
			->where('b.expiration = ' . $db->quote('9999-12-31 23:59:59') . ' OR b.expiration > ' . $db->quote($now->toSql()))
			->order('b.created_time DESC');
		$db->setQuery($query, $start, $limit);

		try
		{
			$results = $db->loadAssocList();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		$list = array();

		foreach ($results as $ban)
		{
			$instance = new $c;
			$instance->bind($ban);
			$instance->_exists = true;
			self::storeInstance($instance);
			$list[$instance->userid] = $instance;
		}

		return $list;
	}

	/**
	 * @param   integer $userid userid
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getUserHistory($userid)
	{
		if (!$userid)
		{
			return array();
		}

		$c  = __CLASS__;
		$db = Factory::getDBO();

		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_users_banned'))
			->where('userid = ' . $db->quote($userid))
			->order('id DESC');
		$db->setQuery($query);

		try
		{
			$results = $db->loadAssocList();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		$list = array();

		foreach ($results as $ban)
		{
			$instance = new $c;
			$instance->bind($ban);
			$instance->_exists = true;
			self::storeInstance($instance);
			$list[] = $instance;
		}

		return $list;
	}

	/**
	 * @return KunenaUser
	 * @throws Exception
	 * @since Kunena
	 */
	public function getUser()
	{
		return KunenaUserHelper::get((int) $this->userid);
	}

	/**
	 * @return KunenaUser
	 * @throws Exception
	 * @since Kunena
	 */
	public function getCreator()
	{
		return KunenaUserHelper::get((int) $this->created_by);
	}

	/**
	 * @return KunenaUser
	 * @throws Exception
	 * @since Kunena
	 */
	public function getModifier()
	{
		return KunenaUserHelper::get((int) $this->modified_by);
	}

	/**
	 * Return ban creation date.
	 *
	 * @return KunenaDate
	 * @since Kunena
	 */
	public function getCreationDate()
	{
		return KunenaDate::getInstance($this->created_time);
	}

	/**
	 * Return ban expiration date.
	 *
	 * @return KunenaDate
	 * @since Kunena
	 */
	public function getExpirationDate()
	{
		return KunenaDate::getInstance($this->expiration);
	}

	/**
	 * Return ban modification date.
	 *
	 * @return KunenaDate
	 * @since Kunena
	 */
	public function getModificationDate()
	{
		return KunenaDate::getInstance($this->modified_time);
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function exists()
	{
		return $this->_exists;
	}

	/**
	 * Method to load a KunenaUserBan object by user id
	 *
	 * @access    public
	 *
	 * @param   int $userid The user id of the user to load
	 * @param   int $mode   KunenaUserBan::ANY or KunenaUserBan::ACTIVE
	 *
	 * @return    boolean            True on success
	 * @since     1.6
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
	 * @access    public
	 *
	 * @param   string $ip   ip
	 * @param   int    $mode KunenaUserBan::ANY or KunenaUserBan::ACTIVE
	 *
	 * @return    boolean            True on success
	 * @since     1.6
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

	/**
	 * @param   null $public  public
	 * @param   null $private private
	 *
	 * @since Kunena
	 * @return void
	 */
	public function setReason($public = null, $private = null)
	{
		$set = false;

		if ($public !== null && $public != $this->reason_public)
		{
			$this->reason_public = (string) $public;
			$set                 = true;
		}

		if ($private !== null && $private != $this->reason_private)
		{
			$this->reason_private = (string) $private;
			$set                  = true;
		}

		if ($this->_exists && $set)
		{
			$this->modified_time = self::$_now->toSql();
			$this->modified_by   = self::$_my->id;
		}
	}

	/**
	 * Do the ban for the specified user from site or forum
	 * 
	 * @param   null   $userid         userid
	 * @param   null   $ip             The address IP of the user
	 * @param   bool   $banlevel       Set if the user from forum or from site, from forum is set to 0 and from site is set to 1
	 * @param   null   $expiration     The expiration date of the ban, if no date is specified the ban is set for lifetime
	 * @param   string $reason_private The private reason
	 * @param   string $reason_public  The public reason
	 * @param   string $comment        Set a comment for others moderators
	 *
	 * @since Kunena 1.6
	 * @return void
	 */
	public function ban($userid = null, $ip = null, $banlevel = 0, $expiration = null, $reason_private = '', $reason_public = '', $comment = '')
	{
		$this->userid  = intval($userid) > 0 ? (int) $userid : null;
		$this->ip      = $ip ? (string) $ip : null;
		$this->blocked = (int) $banlevel;
		$this->setExpiration($expiration);
		$this->reason_private = (string) $reason_private;
		$this->reason_public  = (string) $reason_public;
		$this->addComment($comment);
	}

	/**
	 * @param   mixed  $expiration expiration
	 * @param   string $comment    comment
	 *
	 * @since Kunena
	 * @return void
	 */
	public function setExpiration($expiration, $comment = '')
	{
		// Cannot change expiration if ban is not enabled
		if (!$this->isEnabled() && $this->id)
		{
			return;
		}

		if (!$expiration || $expiration == '0000-00-00 00:00:00')
		{
			$this->expiration = '9999-12-31 23:59:59';
		}
		else
		{
			$date             = new Date($expiration);
			$this->expiration = $date->toUnix() > self::$_now->toUnix() ? $date->toSql() : self::$_now->toSql();
		}

		if ($this->_exists)
		{
			$this->modified_time = self::$_now->toSql();
			$this->modified_by   = self::$_my->id;
		}

		$this->addComment($comment);
	}

	/**
	 * @param   string $comment comment
	 *
	 * @since Kunena
	 * @return void
	 */
	public function addComment($comment)
	{
		if (is_string($comment) && !empty($comment))
		{
			$c                = new stdClass;
			$c->userid        = self::$_my->id;
			$c->time          = self::$_now->toSql();
			$c->comment       = $comment;
			$this->comments[] = $c;
		}
	}

	/**
	 * @param   string $comment comment
	 *
	 * @since Kunena
	 * @return void
	 */
	public function unBan($comment = '')
	{
		// Cannot change expiration if ban is not enabled
		if (!$this->isEnabled())
		{
			return;
		}

		$this->expiration    = self::$_now->toSql();
		$this->modified_time = self::$_now->toSql();
		$this->modified_by   = self::$_my->id;
		$this->addComment($comment);
	}

	/**
	 * Method to save the KunenaUserBan object to the database
	 *
	 * @access    public
	 *
	 * @param   boolean $updateOnly Save the object only if not a new ban
	 *
	 * @return boolean True on success
	 * @throws Exception
	 * @since     1.6
	 */
	public function save($updateOnly = false)
	{
		try
		{
			$this->canBan();
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}

		if (!$this->id)
		{
			// If we have new ban, add creation date and user if they do not exist
			if (!$this->created_time)
			{
				$now                = new Date;
				$this->created_time = $now->toSql();
			}

			if (!$this->created_by)
			{
				$my               = Factory::getUser();
				$this->created_by = $my->id;
			}
		}

		// Create the user table object
		$table = $this->getTable();
		$table->bind($this->getProperties());

		// Check and store the object.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Are we creating a new ban
		$isnew = !$this->_exists;

		// If we aren't allowed to create new ban, return
		if ($isnew && $updateOnly)
		{
			return true;
		}

		if ($this->userid)
		{
			$user = Factory::getUser($this->userid);

			// Change user block also in Joomla
			if (!$user)
			{
				$this->setError("User {$this->userid} does not exist!");

				return false;
			}

			$banlevel = 0;

			if ($this->isEnabled())
			{
				$banlevel = $this->blocked;

				$app  = Factory::getApplication();
				$app->logout((int) $this->userid);
			}

			if ($user->block != $banlevel)
			{
				$user->block = $banlevel;
				$user->save();
			}
			echo 'banlevel '.$banlevel;
			
			// Change user state also in #__kunena_users
			$profile         = KunenaFactory::getUser($this->userid);
			$profile->banned = $this->expiration;
			$profile->save(true);
		}

		// Store the ban data in the database
		$result = $table->store();

		if (!$result)
		{
			$this->setError($table->getError());
		}

		// Set the id for the KunenaUserBan object in case we created a new ban.
		if ($result && $isnew)
		{
			$this->load($table->get('id'));
			self::storeInstance($this);
		}

		return $result;
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function canBan()
	{
		$userid = $this->userid;
		$me     = KunenaUserHelper::getMyself();
		$user   = KunenaUserHelper::get($userid);

		if (!$me->isModerator())
		{
			throw new Exception(Text::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR'));
		}

		if (!$user->exists())
		{
			throw new Exception(Text::_('COM_KUNENA_LIB_USER_BAN_ERROR_NOT_USER', $userid));
		}

		if ($userid == $me->userid)
		{
			throw new Exception(Text::_('COM_KUNENA_LIB_USER_BAN_ERROR_YOURSELF'));
		}

		if ($user->isAdmin())
		{
			throw new Exception(Text::sprintf('COM_KUNENA_LIB_USER_BAN_ERROR_ADMIN', $user->getName()));
		}

		if ($user->isModerator() && !$me->isAdmin())
		{
			throw new Exception(Text::sprintf('COM_KUNENA_LIB_USER_BAN_ERROR_MODERATOR', $user->getName()));
		}

		return true;
	}

	/**
	 * Method to delete the KunenaUserBan object from the database
	 *
	 * @access    public
	 * @return    boolean    True on success
	 * @since     1.6
	 */
	public function delete()
	{
		// Create the user table object
		$table = $this->getTable();

		$result = $table->delete($this->id);

		if (!$result)
		{
			$this->setError($table->getError());
		}

		return $result;
	}
}
