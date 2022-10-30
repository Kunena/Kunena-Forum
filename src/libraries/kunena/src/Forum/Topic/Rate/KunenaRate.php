<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic\Rate;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUser;
use RuntimeException;

/**
 * Kunena Forum Topic Rate Class
 *
 * @since 5.0
 */
class KunenaRate extends CMSObject
{
	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $topic_id = 0;

	/**
	 * @var     integer
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
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $rate;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_exists = false;

	/**
	 * @var     DatabaseDriver|null
	 * @since   Kunena 6.0
	 */
	protected $_db = null;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $users = [];

	/**
	 * @param   int  $identifier  identifier
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function __construct($identifier = 0)
	{
		// Always load the topic -- if rate does not exist: fill empty data
		$this->_db = Factory::getContainer()->get('DatabaseDriver');
		$this->load($identifier);

		parent::__construct($identifier);
	}

	/**
	 * Method to load a \Kunena\Forum\Libraries\Forum\Topic\TopicPoll object by id.
	 *
	 * @param   int  $id  The poll id to be loaded.
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function load(int $id): bool
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
	 * @return  boolean|Table
	 *
	 * @since   Kunena 6.0
	 */
	public function getTable($type = 'Kunena\\Forum\\Libraries\\Tables\\', $prefix = 'TableKunenaRate')
	{
		static $tabletype = null;

		// Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name']   = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return Table::getInstance($tabletype ['prefix'], $tabletype ['name']);
	}

	/**
	 * Returns \Kunena\Forum\Libraries\Forum\Message\Message object
	 *
	 * @access  public
	 *
	 * @param   int   $identifier  The message to load - Can be only an integer.
	 * @param   bool  $reload      reload
	 *
	 * @return  KunenaRate
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		return KunenaRateHelper::get($identifier, $reload);
	}

	/**
	 * Perform insert the rate into table
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return  JsonResponse
	 *
	 * @throws Exception
	 * @since    Kunena 2.0
	 *
	 * @internal param int $userid
	 */
	public function save(KunenaUser $user)
	{
		$user  = KunenaFactory::getUser($user);
		$topic = KunenaTopicHelper::get($this->topic_id);

		$this->getUsers();

		if (!$user->exists())
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_LOGIN', 500);

			return new JsonResponse($exception);
		}

		if ($user->isBanned())
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_NOT_ALLOWED_WHEN_BANNED', 500);

			return new JsonResponse($exception);
		}

		if ($user->userid == $topic->first_post_userid)
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_NOT_YOURSELF', 500);

			return new JsonResponse($exception);
		}

		if ($this->exists($user->userid))
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_ALLREADY', 500);

			return new JsonResponse($exception);
		}

		$time  = Factory::getDate();
		$query = $this->_db->getQuery(true);
		$values = [
			$this->_db->quote($this->topic_id),
			$this->_db->quote($user->userid),
			$this->_db->quote($this->stars),
			$this->_db->quote($time->toSQL()),
		];

		$query->insert($this->_db->quoteName('#__kunena_rate'))
			->columns(
				[
					$this->_db->quoteName('topic_id'),
					$this->_db->quoteName('userid'),
					$this->_db->quoteName('rate'),
					$this->_db->quoteName('time'),
				]
			)
			->values(implode(', ', $values));
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
			$activityIntegration = KunenaFactory::getActivityIntegration();

			$topic = KunenaTopicHelper::get($this->topic_id);
			$activityIntegration->onAfterRate($user->userid, $topic);

			$response = new JsonResponse(null, 'COM_KUNENA_RATE_SUCCESSFULLY_SAVED');
		}
		catch (Exception $e)
		{
			$response = new JsonResponse($e);
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
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getUsers($start = 0, $limit = 0): void
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
	 *
	 * @since   Kunena 6.0
	 */
	public function _add(int $userid, string $time): void
	{
		$this->users[$userid] = $time;
	}

	/**
	 * @param   int  $userid  userid
	 *
	 * @return  boolean userid if hes in table else empty
	 *
	 * @internal param int $pid
	 *
	 * @since    Kunena 2.0
	 */
	public function exists(int $userid): bool
	{
		return isset($this->users[$userid]);
	}

	/**
	 * Get rate for the specified topic and user
	 *
	 * @return  mixed
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getTopicUserRate(): int
	{
		$me = KunenaFactory::getUser();

		if ($this->userid == $me->userid)
		{
			return $this->rate;
		}

		return 0;
	}
}
