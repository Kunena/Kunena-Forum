<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Framework
 * @subpackage  Forum.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Forum Topic Rate Class
 *
 * @since 5.0
 */
class KunenaForumTopicRate extends JObject
{
	protected $_exists = false;
	protected $_db = null;
	public $topic_id = 0;
	public $stars = 0;
	public $userid = null;
	public $time = null;
	protected $users = array();

	/**
	 * @param   int $identifier
	 */
	public function __construct($identifier = 0)
	{
		// Always load the topic -- if rate does not exist: fill empty data
		$this->_db = JFactory::getDBO();
		$this->load($identifier);
	}

	/**
	 * Returns KunenaForumMessage object
	 *
	 * @access    public
	 *
	 * @param int  $identifier The message to load - Can be only an integer.
	 * @param bool $reload
	 *
	 * @return    KunenaForumMessage        The message object.
	 */
	static public function getInstance($identifier = null, $reload = false)
	{
		return KunenaForumTopicRateHelper::get($identifier, $reload);
	}

	/**
	 * @param int $userid
	 *
	 * @return int userid if hes in table else empty
	 * @internal param int $pid
	 * @since    2.0
	 */
	public function exists($userid)
	{
		return isset($this->users[$userid]);
	}

	/**
	 * @param $userid
	 * @param $time
	 */
	public function _add($userid, $time)
	{
		$this->users[$userid] = $time;
	}

	/**
	 * Get the users to check which one are already rated the topic
	 *
	 * @param   int $start
	 * @param   int $limit
	 *
	 * @return array
	 */
	public function getUsers($start = 0, $limit = 0)
	{
		/*if ($this->users === false)
			{  */
		$query = $this->_db->getQuery(true);
		$query->select('*')->from($this->_db->quoteName('#__kunena_rate'))->where($this->_db->quoteName('topic_id') . '=' . $this->_db->Quote($this->topic_id));
		$this->_db->setQuery($query, $start, $limit);

		try
		{
			$users = (array) $this->_db->loadObjectList();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		foreach($users as $user)
		{
		$this->_add($user->userid, $user->time);
		}
			//}

			//return $this->users;
	}

	/**
	 * @param $topicid
	 *
	 * @return array
	 * @internal param int $start
	 * @internal param int $limit
	 *
	 */
	static public function getTotalUsers($topicid)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')->from($db->quoteName('#__kunena_rate'))->where($db->quoteName('topic_id') . '=' . $db->Quote($topicid));
		$db->setQuery($query);

		try
		{
			$total = $db->loadResult();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $total;
	}

	/**
	 * Perform insert the rate into table
	 *
	 * @param $user
	 *
	 * @return bool true if success
	 * @internal param int $userid
	 *
	 * @since    2.0
	 */
	public function save($user)
	{
		$user  = KunenaFactory::getUser($user);
		$topic = KunenaForumTopicHelper::get($this->topic_id);

		$this->getUsers();

		if (!$user->exists())
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_LOGIN', 500);

			return new JResponseJson($exception);
		}

		if ($user->userid == $topic->first_post_userid)
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_NOT_YOURSELF', 500);

			return new JResponseJson($exception);
		}

		if ($this->exists($user->userid))
		{
			$exception = new RuntimeException('COM_KUNENA_RATE_ALLREADY', 500);

			return new JResponseJson($exception);
		}

		$time  = JFactory::getDate();
		$query = $this->_db->getQuery(true);
		$query->insert('#__kunena_rate')
			->set('topic_id=' . $this->_db->quote($this->topic_id))
			->set("userid={$this->_db->quote($user->userid)}")
			->set("rate={$this->_db->quote($this->stars)}")
			->set("time={$this->_db->quote($time->toSQL())}");
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
			$activityIntegration = KunenaFactory::getActivityIntegration();

			$topic = KunenaForumTopicHelper::get($this->topic_id);
			$activityIntegration->onAfterRate($user->userid, $topic);

			$response = new JResponseJson(null, 'COM_KUNENA_RATE_SUCCESSFULLY_SAVED');
		}
		catch(Exception $e)
		{
			$response = new JResponseJson($e);
		}

		return $response;
	}

	/**
	 * Get rate for the specified topic and user
	*/
	public function getTopicUserRate()
	{
		$me  = KunenaFactory::getUser();

		if ( $this->userid == $me->userid )
		{
			return $this->rate;
		}

		return 0;
	}

	/**
	 * Method to get the rate table object.
	 *
	 * @param   string $type		Polls table name to be used.
	 * @param   string $prefix	Polls table prefix to be used.
	 *
	 * @return KunenaTable|TableKunenaRate
	 */
	public function getTable($type = 'KunenaRate', $prefix = 'Table')
	{
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance($tabletype ['name'], $tabletype ['prefix']);
	}

	/**
	 * Method to load a KunenaForumTopicPoll object by id.
	 *
	 * @param   int $id	The poll id to be loaded.
	 *
	 * @return boolean
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
}
