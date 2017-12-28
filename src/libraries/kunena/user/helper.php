<?php
/**
 * Kunena Component
 * @package     Kunena.Framework
 * @subpackage  User
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

KunenaUserHelper::initialize();
use Joomla\Utilities\ArrayHelper;

/**
 * Class KunenaUserHelper
 *
 * @since  K4.0
 */
abstract class KunenaUserHelper
{
	/**
	 * @var array|KunenaUser[]
	 */
	protected static $_instances = array ();
	/**
	 * @var array|KunenaUser[]
	 */
	protected static $_instances_name = array ();

	protected static $_online = null;

	protected static $_online_status = null;

	protected static $_lastid = null;

	protected static $_total = null;

	protected static $_topposters = null;

	protected static $_me = null;

	/**
	 *
	 */
	public static function cleanup()
	{
		self::$_instances = array();
		self::$_instances_name = array();
	}

	/**
	 *
	 */
	public static function initialize()
	{
		$id = JFactory::getUser()->id;
		self::$_me = self::$_instances [$id] = new KunenaUser($id);

		// Initialize avatar if configured.
		$avatars = KunenaFactory::getAvatarIntegration();
		$avatars->load(array($id));
	}

	/**
	 * Returns the global KunenaUser object, only creating it if it doesn't already exist.
	 *
	 * @param   mixed $identifier	The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 * @param   bool $reload		Reload user from database.
	 *
	 * @return KunenaUser
	 */
	public static function get($identifier = null, $reload = false)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if ($identifier === null || $identifier === false)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
			return self::$_me;
		}

		if ($identifier instanceof KunenaUser)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
			return $identifier;
		}

		// Find the user id
		if ($identifier instanceof JUser)
		{
			$id = (int) $identifier->id;
		}
		elseif (((string) (int) $identifier) === ((string) $identifier))
		{
			// Ignore imported users, which haven't been mapped to Joomla (id<0).
			$id = (int) max($identifier, 0);
		}
		else
		{
			// Slow, don't use usernames!
			$id = (int) JUserHelper::getUserId((string) $identifier);
		}

		// Always return fresh user if id is anonymous/not found
		if ($id === 0)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return new KunenaUser($id);
		}
		elseif ($reload || empty(self::$_instances [$id]))
		{
			self::$_instances [$id] = new KunenaUser($id);

			// Preload avatar if configured.
			$avatars = KunenaFactory::getAvatarIntegration();
			$avatars->load(array($id));
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$_instances [$id];
	}

	/**
	 * @param   int $id
	 * @param   string $name
	 *
	 * @return KunenaUser
	 */
	public static function getAuthor($id, $name)
	{
		$id = (int) $id;

		if ($id && !empty(self::$_instances [$id]))
		{
			return self::$_instances [$id];
		}

		if (!empty(self::$_instances_name [$name]))
		{
			return self::$_instances_name [$name];
		}

		$user = self::get($id);

		if (!$user->exists())
		{
			$user->username = $user->name = $name;
		}

		self::$_instances_name [$name] = $user;

		return $user;
	}

	/**
	 * @return KunenaUser
	 */
	public static function getMyself()
	{
		return self::$_me;
	}

	/**
	 * @param   array $userids
	 *
	 * @return array
	 */
	public static function loadUsers(array $userids = array())
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		// Make sure that userids are unique and that indexes are correct
		$e_userids = array();

		foreach($userids as $userid)
		{
			// Ignore guests and imported users, which haven't been mapped to Joomla (id<0).
			if ($userid > 0 && empty(self::$_instances[$userid]))
			{
				$e_userids[(int) $userid] = (int) $userid;
			}
		}

		if (!empty($e_userids))
		{
			$userlist = implode(',', $e_userids);

			$db = JFactory::getDBO();
			$query = "SELECT u.name, u.username, u.email, u.block as blocked, u.registerDate, u.lastvisitDate, ku.*, u.id AS userid
				FROM #__users AS u
				LEFT JOIN #__kunena_users AS ku ON u.id = ku.userid
				WHERE u.id IN ({$userlist})";
			$db->setQuery($query);

			try
			{
				$results = $db->loadAssocList();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			foreach ($results as $user)
			{
				$instance = new KunenaUser(false);
				$instance->setProperties($user);
				$instance->exists(isset($user['posts']));
				self::$_instances[$instance->userid] = $instance;
			}

			// Preload avatars if configured
			$avatars = KunenaFactory::getAvatarIntegration();
			$avatars->load($e_userids);
		}

		$list = array ();

		foreach ($userids as $userid)
		{
			if (isset(self::$_instances [$userid])) { $list [$userid] = self::$_instances [$userid]; }
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $list;
	}

	/**
	 * @return integer
	 */
	public static function getLastId()
	{
		if (self::$_lastid === null)
		{
			self::getTotalCount();
		}

		return (int) self::$_lastid;
	}

	/**
	 * @return integer
	 */
	public static function getTotalCount()
	{
		if (self::$_total === null)
		{
			$db = JFactory::getDBO();
			$config = KunenaFactory::getConfig();

			if ($config->userlist_count_users == '1')
			{
				$where = '(block=0 OR activation="")';
			}
			elseif ($config->userlist_count_users == '2')
			{
				$where = '(block=0 AND activation="")';
			}
			elseif ($config->userlist_count_users == '3')
			{
				$where = 'block=0';
			}
			else
			{
				$where = '1';
			}

			$db->setQuery("SELECT COUNT(*), MAX(id) FROM #__users WHERE {$where}");

			try
			{
				list (self::$_total, self::$_lastid) = $db->loadRow();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return (int) self::$_total;
	}

	/**
	 * @param   int $limit
	 *
	 * @return array
	 */
	public static function getTopPosters($limit=0)
	{
		$limit = $limit ? $limit : KunenaFactory::getConfig()->popusercount;

		if (count(self::$_topposters) < $limit)
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('u.id', 'ku.posts'), array(null, 'count')));
			$query->from($db->quoteName(array('#__kunena_users'), array('ku')));
			$query->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'));
			$query->where($db->quoteName('ku.posts') . '>0');
			$query->order($db->quoteName('ku.posts') . ' DESC');

			if (KunenaFactory::getConfig()->superadmin_userlist)
			{
				$filter = JAccess::getUsersByGroup(8);
				$query->where('u.id NOT IN (' . implode(',', $filter) . ')');
			}

			$db->setQuery($query, 0, $limit);

			try
			{
				self::$_topposters = (array) $db->loadObjectList();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return self::$_topposters;
	}

	/**
	 * Get the list of users online by giving list of userid
	 *
	 * @return array
	 */
	public static function getOnlineUsers()
	{
		if (self::$_online === null)
		{
			$app = JFactory::getApplication();
			$config = KunenaFactory::getConfig();
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('userid, MAX(time) AS time')
				->from('#__session')
				->where('client_id=0 AND userid>0')
				->group('userid')
				->order('time DESC');

			if ($config->show_session_type == 2 && $config->show_session_starttime != 0)
			{
				// Calculate x minutes by using Kunena setting.
				$time = JFactory::getDate()->toUnix() - $config->show_session_starttime;
				$query->where('time > ' . $time);
			}
			elseif ($config->show_session_type > 0)
			{
				// Calculate Joomla session expiration point.
				$time = JFactory::getDate()->toUnix() - ($app->get('lifetime', 15) * 60);
				$query->where('time > ' . $time);
			}

			$db->setQuery($query);

			try
			{
				self::$_online = (array) $db->loadObjectList('userid');
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return self::$_online;
	}

	/**
	 * Method returns a list of users with their user groups.
	 *
	 * @param	array		$groupIds	List of Group Ids (null for all).
	 * @param	array		$userIds	List of User Ids (null for all).
	 * @param	boolean		$recursive	Recursively include all child groups (optional)
	 *
	 * @return  array  List of userid => array(group, group, ...).
	 * @throws  BadMethodCallException  If first two parameters are both null.
	 *
	 * @since 5.0
	 */
	public static function getGroupsForUsers(array $groupIds = null, array $userIds = null, $recursive = false)
	{
		// Check for bad calls.
		if (is_null($userIds) && is_null($groupIds))
		{
			throw new BadMethodCallException(__CLASS__ . '::' . __FUNCTION__ . '(): Cannot load all groups for all users.');
		}

		// Check if there's anything to load.
		if ((is_array($groupIds) && empty($groupIds)) || (is_array($userIds) && empty($userIds)))
		{
			return array();
		}

		$test = $recursive ? '>=' : '=';
		// Find users and their groups.
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true)
		->select('m.*')
		->from('#__usergroups AS ug1')
		->join('INNER','#__usergroups AS ug2 ON ug2.lft'.$test.'ug1.lft AND ug1.rgt'.$test.'ug2.rgt')
		->join('INNER','#__user_usergroup_map AS m ON ug2.id=m.group_id');

		if ($groupIds)
		{
			ArrayHelper::toInteger($groupIds);
			$groupList = implode(',', $groupIds);
			$query->where("ug1.id IN ({$groupList})");
		}

		if ($userIds)
		{
			ArrayHelper::toInteger($userIds);
			$userList = implode(',', $userIds);
			$query->where("user_id IN ({$userList})");
		}

		$db->setQuery($query);
		$results = (array) $db->loadObjectList();
		$list = array();

		// Make sure that we list all given users (if provided).
		if ($userIds)
		{
			foreach ($userIds as $userId)
			{
				$list[$userId] = array();
			}
		}

		// Fill up the user groups.
		foreach ($results as $result)
		{
			$list[$result->user_id][$result->group_id] = $result->group_id;
		}

		return $list;
	}

	/**
	 * Get the number of users online
	 *
	 * @return array
	 */
	public static function getOnlineCount()
	{
		static $counts = null;

		if ($counts === null)
		{
			$app = JFactory::getApplication();
			$config = KunenaFactory::getConfig();
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('COUNT(*)')
				->from('#__session')
				->where('client_id=0 AND userid=0');

			if ($config->show_session_type == 2 && $config->show_session_starttime != 0)
			{
				// Calculate x minutes by using Kunena setting.
				$time = JFactory::getDate()->toUnix() - $config->show_session_starttime;
				$query->where('time > ' . $time);
			}
			elseif ($config->show_session_type > 0)
			{
				// Calculate Joomla session expiration point.
				$time = JFactory::getDate()->toUnix() - ($app->get('lifetime', 15) * 60);
				$query->where('time > ' . $time);
			}

			$db->setQuery($query);

			try
			{
				$count = $db->loadResult();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			$counts = array();
			$counts['user'] = count(self::getOnlineUsers());
			$counts['guest'] = $count;
		}

		return $counts;
	}

	/**
	 * Returns the status of a user. If as session exists, we can return the type of status the user set.
	 *
	 * @param   mixed  $user  The user object to get the status
	 *
	 * @return integer
	 */
	public static function getStatus($user)
	{
		$config = KunenaFactory::getConfig();
		$status = $config->user_status;

		if (!$status)
		{
			return false;
		}

		$user = self::get($user);
		$online = false;

		if (intval($user->userid) > 0)
		{
			// First check if the user is actually has an active session regardless of the status the user set
			if (self::$_online === null)
			{
				self::getOnlineUsers();
			}

			$online = isset(self::$_online [$user->userid]) ? (self::$_online [$user->userid]->time > time() - JFactory::getApplication()->get('lifetime', 15) * 60) : false;
		}

		if (!$online || ($user->status == 3 && !$user->isMyself() && !self::getMyself()->isModerator()))
		{
			return -1;
		}
		elseif ($online && self::$_online [$user->userid]->time < time() - 30)
		{
			return 1;
		}

		return $user->status;
	}

	/**
	 * @return boolean|integer
	 */
	public static function recount()
	{
		$db = JFactory::getDBO();

		// Update user post count
		$query = "INSERT INTO #__kunena_users (userid, posts)
				SELECT user_id AS userid, SUM(posts) AS posts
				FROM #__kunena_user_topics
				GROUP BY user_id
			ON DUPLICATE KEY UPDATE posts=VALUES(posts)";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows = $db->getAffectedRows();

		return $rows;
	}

	/**
	 * @return boolean|integer
	 */
	public static function recountBanned()
	{
		$db = JFactory::getDBO();

		// Update banned state
		$query = "UPDATE #__kunena_users AS u
			LEFT JOIN (
				SELECT userid, MAX(expiration) AS banned FROM #__kunena_users_banned GROUP BY userid
			) AS b ON u.userid=b.userid
			SET u.banned=b.banned";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows = $db->getAffectedRows();

		return $rows;
	}

	public static function recountPostsNull()
	{
		$db = JFactory::getDBO();

		// If user has no user_topics, set posts into 0
		$query = "UPDATE #__kunena_users AS u
			LEFT JOIN #__kunena_user_topics AS ut ON ut.user_id=u.userid
			SET u.posts = 0
			WHERE ut.user_id IS NULL";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows = $db->getAffectedRows();

		return $rows;
	}
}
