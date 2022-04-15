<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Session;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUserHelper;
use RuntimeException;

/**
 * Class KunenaSession
 *
 * @since   Kunena 6.0
 */
class KunenaSession extends CMSObject
{
	/**
	 * @var     mixed
	 * @since   Kunena 6.0
	 */
	private static $_instance;

	/**
	 * @var     integer|string
	 * @since   Kunena 6.0
	 */
	public $lasttime;

	/**
	 * @var     integer|string
	 * @since   Kunena 6.0
	 */
	public $currvisit;

	/**
	 * @var     integer|string
	 * @since   Kunena 6.0
	 */
	public $readtopics;

	public $userid;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_exists = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_sessionTimeOut = false;

	/**
	 * @var     integer|string
	 * @since   Kunena 6.0
	 */
	protected $allreadtime;

	/**
	 * @param   mixed|null  $identifier  identifier
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function __construct($identifier)
	{
		$this->load($identifier);

		if (!$this->currvisit)
		{
			// For new users new indication displays 14 days
			$now = Factory::getDate()->toUnix();

			// 14 days ago
			$this->lasttime    = $now - 14 * 24 * 60 * 60;
			$this->allreadtime = $this->lasttime;
			$this->currvisit   = $now;
			$this->readtopics  = 0;
		}
		else
		{
			// Deal with users who do not (yet) have all read time set.
			$userCategory      = KunenaCategoryUserHelper::get(0, (int) $identifier);
			$this->allreadtime = $userCategory->allreadtime ? $userCategory->allreadtime : $this->lasttime;
		}

		parent::__construct($identifier);
	}

	/**
	 * Method to load a KunenaSession object by userid
	 *
	 * @access    public
	 *
	 * @param   int  $userid  The user id of the user to load
	 *
	 * @return    boolean            True on success
	 * @since     1.5
	 */
	public function load(int $userid): bool
	{
		// Create the user table object
		$table = $this->getTable();

		// Load the KunenaTableUser object based on the user id
		if ($table->load($userid))
		{
			$this->_exists = true;
		}

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());
		$this->userid = $userid;

		return true;
	}

	/**
	 * Method to get the session table object
	 *
	 * This function uses a static variable to store the table name of the session table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access    public
	 *
	 * @param   string  $type    The session table name to be used
	 * @param   string  $prefix  The session table prefix to be used
	 *
	 * @return    object    The session table object
	 * @since     1.5
	 */
	public function getTable($type = 'Kunena\\Forum\\Libraries\\Tables\\', $prefix = 'KunenaSessions')
	{
		static $tabletype = null;

		// Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype['name'] || $prefix != $tabletype['prefix'])
		{
			$tabletype['name']   = $type;
			$tabletype['prefix'] = $prefix;
		}

		// Create the user table object
		return Table::getInstance($tabletype ['prefix'], $tabletype ['name']);
	}

	/**
	 * @param   bool  $update  update
	 * @param   null  $userid  userid
	 *
	 * @return  KunenaSession
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getInstance($update = false, $userid = null): KunenaSession
	{
		if (!self::$_instance)
		{
			$my              = Factory::getApplication()->getIdentity();
			self::$_instance = new KunenaSession($userid !== null ? $userid : $my->id);

			if ($update)
			{
				self::$_instance->updateSessionInfo();
			}
		}

		return self::$_instance;
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function updateSessionInfo(): void
	{
		// If this is a new session, reset the lasttime column with the timestamp
		// of the last saved currvisit - only after that can we reset currvisit to now before the store
		if ($this->isNewSession())
		{
			$this->lasttime   = $this->currvisit;
			$this->readtopics = 0;
		}

		$this->currvisit = Factory::getDate()->toUnix();
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function isNewSession(): bool
	{
		// Perform session timeout check
		$lifetime              = max(
			\intval(Factory::getApplication()->get('config.lifetime')) * 60,
			\intval(KunenaFactory::getConfig()->sessionTimeOut)
		);
		$this->_sessionTimeOut = ($this->currvisit + $lifetime < Factory::getDate()->toUnix());

		return $this->_sessionTimeOut;
	}

	/**
	 * Method to save the KunenaSession object to the database
	 *
	 * @access  public
	 *
	 * @param   boolean  $updateOnly  Save the object only if not a new session
	 *
	 * @return  boolean True on success
	 *
	 * @throws  Exception
	 * @since   1.5
	 */
	public function save($updateOnly = false): bool
	{
		// Do not save session for anonymous users
		if (!$this->userid)
		{
			return false;
		}

		// Create the user table object
		$table = $this->getTable();
		$table->bind($this->getProperties());
		$table->exists($this->_exists);

		// Check and store the object.
		try
		{
			if (!$table->check())
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			throw new RuntimeException($e->getMessage());
		}

		// Are we creating a new user
		$isnew = !$this->_exists;

		// If we aren't allowed to create new users return
		if ($isnew && $updateOnly)
		{
			return true;
		}

		// Store the user data in the database
		if (!$result = $table->store())
		{
			$this->setError($table->getError());
		}

		// Set the id for the Joomla\CMS\User\User object in case we created a new user.
		if (empty($this->userid))
		{
			$this->userid = $table->get('userid');
		}

		// Read indication has moved outside of the session table -- let's update it too.
		$userCategory = KunenaCategoryUserHelper::get(0, $this->userid);

		if ($userCategory->allreadtime != $this->allreadtime)
		{
			$userCategory->params      = '';
			$userCategory->allreadtime = $this->allreadtime;
			$userCategory->save();
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaSession object from the database
	 *
	 * @access    public
	 *
	 * @return    boolean            True on success
	 *
	 * @since     1.5
	 */
	public function delete(): bool
	{
		// Create the user table object
		$table = $this->getTable();

		$result = $table->delete($this->userid);

		if (!$result)
		{
			$this->setError($table->getError());
		}

		return $result;
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function isNewUser(): bool
	{
		return !$this->_exists;
	}

	/**
	 * @return  integer|string
	 *
	 * @since   Kunena 6.0
	 */
	public function getAllReadTime()
	{
		// For existing users new indication expires after 3 months
		$monthsAgo = Factory::getDate()->toUnix() - 91 * 24 * 60 * 60;

		return $this->allreadtime > $monthsAgo ? $this->allreadtime : $monthsAgo;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function markAllCategoriesRead(): void
	{
		$this->allreadtime = Factory::getDate()->toUnix();
		$this->readtopics  = 0;
	}
}
