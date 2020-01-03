<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Kunena Forum Topic Rate Class
 *
 * @since 5.0
 */
class KunenaForumTopicRate extends CMSObject
{
	/**
	 * @var integer
	 * @since   Kunena 6.0
	 */
	public $topic_id = 0;

	/**
	 * @var integer
	 * @since   Kunena 6.0
	 */
	public $stars = 0;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $userid = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $time = null;

	/**
	 * @var integer
	 * @since   Kunena 6.0
	 */
	public $rate;

	/**
	 * @var   boolean
	 * @since   Kunena 6.0
	 */
	protected $_exists = false;

	/**
	 * @var JDatabaseDriver|null
	 * @since   Kunena 6.0
	 */
	protected $_db = null;

	/**
	 * @var array
	 * @since   Kunena 6.0
	 */
	protected $users = array();

	/**
	 * @param   int  $identifier  identifier
	 *
	 * @since   Kunena
	 * @throws  Exception
	 */
	public function __construct($identifier = 0)
	{
		// Always load the topic -- if rate does not exist: fill empty data
		$this->_db = Factory::getDBO();
		$this->load($identifier);
	}

	/**
	 * Method to load a KunenaForumTopicPoll object by id.
	 *
	 * @param   int  $id  The poll id to be loaded.
	 *
	 * @return  boolean
	 * @since   Kunena
	 * @throws  Exception
	 */
	public function load($id)
	{
		// Create the table object
		$table = $this->getTable();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load($id);

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());

		return $this->_exists;
	}

	/**
	 * Method to get the rate table object.
	 *
	 * @param   string  $type    Polls table name to be used.
	 * @param   string  $prefix  Polls table prefix to be used.
	 *
	 * @return  boolean|Joomla\CMS\Table\Table|KunenaTable|TableKunenaRate
	 * @since   Kunena 6.0
	 */
	public function getTable($type = 'KunenaRate', $prefix = 'Table')
	{
		static $tabletype = null;

		// Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name']   = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return Joomla\CMS\Table\Table::getInstance($tabletype ['name'], $tabletype ['prefix']);
	}

	/**
	 * Returns KunenaForumMessage object
	 *
	 * @access    public
	 *
	 * @param   int   $identifier  The message to load - Can be only an integer.
	 * @param   bool  $reload      reload
	 *
	 * @return  KunenaForumMessage|KunenaForumTopicRate
	 * @since     Kunena
	 * @throws  Exception
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		return KunenaForumTopicRateHelper::get($identifier, $reload);
	}

	/**
	 * Perform insert the rate into table
	 *
	 * @internal param int $userid
	 *
	 * @param   string  $user  user
	 *
	 * @return  boolean|Joomla\CMS\Response\JsonResponse
	 * @since   Kunena 2.0
	 * @throws  Exception
	 */
	public function save($user)
	{
		$user  = KunenaFactory::getUser($user);
		$topic = KunenaForumTopicHelper::get($this->topic_id);

		$this->getUsers();

		if (!$user->exists())
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_LOGIN', 500);

			return new Joomla\CMS\Response\JsonResponse($exception);
		}

		if ($user->isBanned())
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_NOT_ALLOWED_WHEN_BANNED', 500);

			return new Joomla\CMS\Response\JsonResponse($exception);
		}

		if ($user->userid == $topic->first_post_userid)
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_NOT_YOURSELF', 500);

			return new Joomla\CMS\Response\JsonResponse($exception);
		}

		if ($this->exists($user->userid))
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_ALLREADY', 500);

			return new Joomla\CMS\Response\JsonResponse($exception);
		}

		$time  = Factory::getDate();
		$query = $this->_db->getQuery(true);
		$query->insert($this->_db->quoteName('#__kunena_rate'))
			->set($this->_db->quoteName('topic_id') . ' = ' . $this->_db->quote($this->topic_id))
			->set($this->_db->quoteName('userid') . ' = ' . $this->_db->quote($user->userid))
			->set($this->_db->quoteName('rate') . ' = ' . $this->_db->quote($this->stars))
			->set($this->_db->quoteName('time') . ' = ' . $this->_db->quote($time->toSQL()));
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
			$activityIntegration = KunenaFactory::getActivityIntegration();

			$topic = KunenaForumTopicHelper::get($this->topic_id);
			$activityIntegration->onAfterRate($user->userid, $topic);

			$response = new Joomla\CMS\Response\JsonResponse(null, 'COM_KUNENA_RATE_SUCCESSFULLY_SAVED');
		}
		catch (Exception $e)
		{
			$response = new Joomla\CMS\Response\JsonResponse($e);
		}

		return $response;
	}

	/**
	 * Get the users to check which one are already rated the topic
	 *
	 * @param   int  $start  start
	 * @param   int  $limit  limit
	 *
	 * @return  void
	 * @since   Kunena
	 * @throws  Exception
	 */
	public function getUsers($start = 0, $limit = 0)
	{
		$query = $this->_db->getQuery(true);
		$query->select('*')
			->from($this->_db->quoteName('#__kunena_rate'))
			->where($this->_db->quoteName('topic_id') . ' = ' . $this->_db->quote($this->topic_id));
		$query->setLimit($limit, $start);
		$this->_db->setQuery($query);

		try
		{
			$users = (array) $this->_db->loadObjectList();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		foreach ($users as $user)
		{
			$this->_add($user->userid, $user->time);
		}
	}

	/**
	 * @param   integer  $userid  userid
	 * @param   integer  $time    time
	 *
	 * @return  void
	 * @since   Kunena 6.0
	 */
	public function _add($userid, $time)
	{
		$this->users[$userid] = $time;
	}

	/**
	 * @internal param int $pid
	 *
	 * @param   int  $userid  userid
	 *
	 * @return  boolean userid if hes in table else empty
	 * @since   Kunena 2.0
	 */
	public function exists($userid)
	{
		return isset($this->users[$userid]);
	}

	/**
	 * Get rate for the specified topic and user
	 *
	 * @return  mixed
	 * @since   Kunena
	 * @throws  Exception
	 */
	public function getTopicUserRate()
	{
		$me = KunenaFactory::getUser();

		if ($this->userid == $me->userid)
		{
			return $this->rate;
		}

		return 0;
	}
}
