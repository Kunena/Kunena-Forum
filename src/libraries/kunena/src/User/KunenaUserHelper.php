<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

KunenaUserHelper::initialize();

use BadMethodCallException;
use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Http\Http;
use Joomla\Http\Response;
use Joomla\Http\Transport\Stream as StreamTransport;
use Joomla\Utilities\ArrayHelper;
use Joomla\Utilities\IpHelper;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;

/**
 * Class \Kunena\Forum\Libraries\User\KunenaUserHelper
 *
 * @since   Kunena 4.0
 */
abstract class KunenaUserHelper
{
	/**
	 * @var     array|KunenaUser[]
	 * @since   Kunena 6.0
	 */
	protected static $_instances = [];

	/**
	 * @var     array|KunenaUser[]
	 * @since   Kunena 6.0
	 */
	protected static $_instances_name = [];

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected static $_online = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected static $_online_status = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected static $_lastid = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected static $_total = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected static $_topposters = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected static $_me = null;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public static function cleanup(): void
	{
		self::$_instances      = [];
		self::$_instances_name = [];
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function initialize(): void
	{
		$id        = Factory::getApplication()->getIdentity()->id;
		self::$_me = self::$_instances [$id] = new KunenaUser($id);

		// Initialize avatar if configured.
		$avatars = KunenaFactory::getAvatarIntegration();
		$avatars->load([$id]);
	}

	/**
	 * @param   mixed   $id         The user to load - Can be an integer or string - If string, it is converted to ID
	 *                              automatically.
	 * @param   string  $name       Optionnal name of user if it doesn't exist
	 *
	 * @return \Kunena\Forum\Libraries\User\KunenaUser|null
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws \Exception
	 */
	public static function getAuthor($id, $name): ?KunenaUser
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
	 * Returns the global \Kunena\Forum\Libraries\User\KunenaUser object, only creating it if it doesn't already exist.
	 *
	 * @param   mixed  $identifier  The user to load - Can be an integer or string - If string, it is converted to ID
	 *                              automatically.
	 * @param   bool   $reload      Reload user from database.
	 *
	 * @return \Kunena\Forum\Libraries\User\KunenaUser|null
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 *
	 */
	public static function get($identifier = null, $reload = false): ?KunenaUser
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if ($identifier === null || $identifier === false)
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return self::$_me;
		}

		if ($identifier instanceof KunenaUser)
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return $identifier;
		}

		// Find the user id
		if ($identifier instanceof User)
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
			$id = (int) UserHelper::getUserId((string) $identifier);
		}

		// Always return fresh user if id is anonymous/not found
		if ($id === 0)
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return new KunenaUser($id);
		}
		elseif ($reload || empty(self::$_instances [$id]))
		{
			self::$_instances [$id] = new KunenaUser($id);

			// Preload avatar if configured.
			$avatars = KunenaFactory::getAvatarIntegration();
			$avatars->load([$id]);
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$_instances [$id];
	}

	/**
	 * @param   array  $userids  userids
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function loadUsers(array $userids = []): array
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		// Make sure that userids are unique and that indexes are correct
		$e_userids = [];

		foreach ($userids as $userid)
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

			$db = Factory::getContainer()->get('DatabaseDriver');

			$query = $db->getQuery(true);
			$query->select('u.name, u.username, u.email, u.block AS blocked, u.registerDate, u.lastvisitDate, ku.*, u.id AS userid')
				->from($db->quoteName('#__users', 'u'))
				->leftJoin($db->quoteName('#__kunena_users') . ' AS ' . $db->quoteName('ku') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'))
				->where($db->quoteName('u.id') . ' IN (' . $userlist . ')');
			$db->setQuery($query);

			try
			{
				$results = $db->loadAssocList();
			}
			catch (ExecutionFailureException $e)
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

		$list = [];

		foreach ($userids as $userid)
		{
			if (isset(self::$_instances [$userid]))
			{
				$list [$userid] = self::$_instances [$userid];
			}
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $list;
	}

	/**
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getLastId(): int
	{
		if (self::$_lastid === null)
		{
			self::getTotalCount();
		}

		return (int) self::$_lastid;
	}

	/**
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getTotalCount(): int
	{
		if (self::$_total === null)
		{
			$db     = Factory::getContainer()->get('DatabaseDriver');
			$config = KunenaFactory::getConfig();

			if ($config->userlistCountUsers == '1')
			{
				$where = '(' . $db->quoteName('block') . ' = 0 OR activation="")';
			}
			elseif ($config->userlistCountUsers == '2')
			{
				$where = '(' . $db->quoteName('block') . ' = 0 AND activation="")';
			}
			elseif ($config->userlistCountUsers == '3')
			{
				$where = $db->quoteName('block') . ' = 0';
			}
			else
			{
				$where = '1';
			}

			$query = $db->getQuery(true);
			$query->select('COUNT(*), MAX(id)')
				->from($db->quoteName('#__users'))
				->where($where);
			$db->setQuery($query);

			try
			{
				list(self::$_total, self::$_lastid) = $db->loadRow();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return (int) self::$_total;
	}

	/**
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getTotalRanks(): int
	{
		$total = 0;
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
			->from($db->quoteName('#__kunena_ranks'));
		$db->setQuery($query);

		try
		{
			$total = $db->setQuery($query)->loadResult();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return (int) $total;
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return array|null
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 *
	 */
	public static function getTopPosters($limit = 0): ?array
	{
		$limit = $limit ? $limit : KunenaFactory::getConfig()->popUserCount;

		if (self::$_topposters < $limit)
		{
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select($db->quoteName(['u.id', 'ku.posts'], [null, 'count']));
			$query->from($db->quoteName(['#__kunena_users'], ['ku']));
			$query->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'));
			$query->where($db->quoteName('ku.posts') . ' > 0');
			$query->order($db->quoteName('ku.posts') . ' DESC');

			if (KunenaFactory::getConfig()->superAdminUserlist)
			{
				$filter = Access::getUsersByGroup(8);
				$query->where($db->quoteName('u.id') . ' NOT IN (' . implode(',', $filter) . ')');
			}

			$query->setLimit($limit);
			$db->setQuery($query);

			try
			{
				self::$_topposters = (array) $db->loadObjectList();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return self::$_topposters;
	}

	/**
	 * Method returns a list of users with their user groups.
	 *
	 * @param   array|null  $groupIds   List of Group Ids (null for all).
	 * @param   array|null  $userIds    List of User Ids (null for all).
	 * @param   boolean     $recursive  Recursively include all child groups (optional)
	 *
	 * @return  array
	 *
	 * @since   Kunena 5.0
	 */
	public static function getGroupsForUsers(array $groupIds = null, array $userIds = null, $recursive = false): array
	{
		// Check for bad calls.
		if (\is_null($userIds) && \is_null($groupIds))
		{
			throw new BadMethodCallException(__CLASS__ . '::' . __FUNCTION__ . '(): Cannot load all groups for all users.');
		}

		// Check if there's anything to load.
		if ((\is_array($groupIds) && empty($groupIds)) || (\is_array($userIds) && empty($userIds)))
		{
			return [];
		}

		$recurs = $recursive ? ' >= ' : ' = ';

		// Find users and their groups.
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true)
			->select($db->quoteName('m.*'))
			->from($db->quoteName('#__usergroups', 'ug1'))
			->innerJoin($db->quoteName('#__usergroups', 'ug2') . ' ON ' . $db->quoteName('ug2.lft') . $recurs . $db->quoteName('ug1.lft') . ' AND ' . $db->quoteName('ug1.rgt') . $recurs . $db->quoteName('ug2.rgt'))
			->innerJoin($db->quoteName('#__user_usergroup_map', 'm') . ' ON ' . $db->quoteName('ug2.id') . ' = ' . $db->quoteName('m.group_id'));

		if ($groupIds)
		{
			$groupIds  = ArrayHelper::toInteger($groupIds);
			$groupList = implode(',', $groupIds);
			$query->where($db->quoteName('ug1.id') . ' IN (' . $groupList . ')');
		}

		if ($userIds)
		{
			$userIds  = ArrayHelper::toInteger($userIds);
			$userList = implode(',', $userIds);
			$query->where($db->quoteName('user_id') . ' IN (' . $userList . ')');
		}

		$db->setQuery($query);
		$results = (array) $db->loadObjectList();
		$list    = [];

		// Make sure that we list all given users (if provided).
		if ($userIds)
		{
			foreach ($userIds as $userId)
			{
				$list[$userId] = [];
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
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getOnlineCount(): array
	{
		static $counts = null;

		if ($counts === null)
		{
			$app    = Factory::getApplication();
			$config = KunenaFactory::getConfig();
			$db     = Factory::getContainer()->get('DatabaseDriver');
			$query  = $db->getQuery(true);
			$query->select('COUNT(*)')
				->from($db->quoteName('#__session'))
				->where($db->quoteName('client_id') . ' = 0')
				->where($db->quoteName('userid') . ' = 0');

			if ($config->showSessionType == 2 && $config->showSessionStartTime != 0)
			{
				// Calculate x minutes by using Kunena setting.
				$time = Factory::getDate()->toUnix() - $config->showSessionStartTime;
				$query->where($db->quoteName('time') . ' > ' . $db->quote($time));
			}
			elseif ($config->showSessionType > 0)
			{
				// Calculate Joomla session expiration point.
				$time = Factory::getDate()->toUnix() - ($app->get('lifetime', 15) * 60);
				$query->where($db->quoteName('time') . ' > ' . $db->quote($time));
			}

			$db->setQuery($query);

			try
			{
				$count = $db->loadResult();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			$counts          = [];
			$counts['user']  = \count(self::getOnlineUsers());
			$counts['guest'] = $count;
		}

		return $counts;
	}

	/**
	 * Get the list of users online by giving list of userid
	 *
	 * @return array|null
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 *
	 */
	public static function getOnlineUsers(): ?array
	{
		if (self::$_online === null)
		{
			$app    = Factory::getApplication();
			$config = KunenaFactory::getConfig();
			$db     = Factory::getContainer()->get('DatabaseDriver');
			$query  = $db->getQuery(true);
			$query->select('userid, MAX(time) AS time')
				->from($db->quoteName('#__session'))
				->where($db->quoteName('client_id') . ' = 0')
				->where($db->quoteName('userid') . ' > 0')
				->group($db->quoteName('userid'))
				->order($db->quoteName('time') . ' DESC');

			if ($config->showSessionType == 2 && $config->showSessionStartTime != 0)
			{
				// Calculate x minutes by using Kunena setting.
				$time = Factory::getDate()->toUnix() - $config->showSessionStartTime;
				$query->where($db->quoteName('time') . ' > ' . $db->quote($time));
			}
			elseif ($config->showSessionType > 0)
			{
				// Calculate Joomla session expiration point.
				$time = Factory::getDate()->toUnix() - ($app->get('lifetime', 15) * 60);
				$query->where($db->quoteName('time') . ' > ' . $db->quote($time));
			}

			$db->setQuery($query);

			try
			{
				self::$_online = (array) $db->loadObjectList('userid');
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return self::$_online;
	}

	/**
	 * Returns the status of a user. If as session exists, we can return the type of status the user set.
	 *
	 * @param   mixed  $user  The user object to get the status
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getStatus($user)
	{
		$config = KunenaFactory::getConfig();
		$status = $config->userStatus;

		if (!$status)
		{
			return false;
		}

		$user   = self::get($user);
		$online = false;

		if (\intval($user->userid) > 0)
		{
			// First check if the user is actually has an active session regardless of the status the user set
			if (self::$_online === null)
			{
				self::getOnlineUsers();
			}

			$online = isset(self::$_online [$user->userid]) ? (self::$_online [$user->userid]->time > time() - Factory::getApplication()->get('lifetime', 15) * 60) : false;
		}

		if (!$online || ($user->status == 3 && !$user->isMyself() && !self::getMyself()->isModerator()))
		{
			return -1;
		}

		if ($online && self::$_online [$user->userid]->time < time() - 30)
		{
			return 1;
		}

		return $user->status;
	}

	/**
	 * @return \Kunena\Forum\Libraries\User\KunenaUser|null
	 *
	 * @since   Kunena 6.0
	 */
	public static function getMyself(): ?KunenaUser
	{
		return self::$_me;
	}

	/**
	 * @return  boolean|integer
	 *
	 * @since   Kunena 5.1
	 *
	 * @throws  Exception
	 */
	public static function recount()
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		/*
		$query = $db->getQuery(true);
		$query->insert($db->quoteName('#__kunena_users') . ' (userid, posts)')
			->select("user_id AS userid, SUM(posts) AS posts")
			->from($db->quoteName('#__kunena_user_topics'))
			->group('user_id' . ' ON DUPLICATE KEY UPDATE posts = VALUES(posts)');*/

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
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return $db->getAffectedRows();
	}

	/**
	 * @return  boolean|integer
	 *
	 * @since   Kunena 5.1
	 *
	 * @throws  Exception
	 */
	public static function recountBanned()
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Update banned state
		$query = "UPDATE #__kunena_users AS u
			INNER JOIN (
				SELECT userid, MAX(expiration) AS banned FROM #__kunena_users_banned GROUP BY userid
			) AS b ON u.userid=b.userid
			SET u.banned=b.banned";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return $db->getAffectedRows();
	}

	/**
	 * @return  boolean|integer
	 *
	 * @since   Kunena 5.1
	 *
	 * @throws  Exception
	 */
	public static function recountPostsNull()
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		// If user has no user_topics, set posts into 0
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__kunena_users', 'u'))
			->leftJoin($db->quoteName('#__kunena_user_topics', 'ut') . ' ON ' . $db->quoteName('ut.user_id') . ' = ' . $db->quoteName('u.userid'))
			->set($db->quoteName('u.posts') . ' = 0')
			->where($db->quoteName('ut.user_id') . ' IS NULL');
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return $db->getAffectedRows();
	}

	/**
	 * Return the IP used by the user
	 *
	 * @return  string
	 *
	 * @since   Kunena 6
	 */
	public static function getUserIp(): string
	{
		return IpHelper::getIp();
	}

	/**
	 * Return is the user IP is ipv6 or not
	 *
	 * @param   string  $ip  ip
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6
	 */
	public static function isIPv6(string $ip): bool
	{
		return IpHelper::isIPv6($ip);
	}

	/**
	 * Method to send data about spammer or to check if user is blacklisted in stopforumspam database
	 *
	 * @param   array   $data  With username, ip, email, api key, evidence
	 * @param   string  $type  Add ou just call api
	 *
	 * @return  boolean|Response
	 *
	 * @since   Kunena 6
	 *
	 * @throws Exception
	 */
	public static function storeCheckStopforumspam(array $data, string $type)
	{
		$options = [];

		try
		{
			$transport = new StreamTransport($options);
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());

			return false;
		}

		// Create a 'stream' transport.
		$http = new Http($options, $transport);

		// TODO : prevent to do a request with a private or local IP
		if ($type == 'add')
		{
			$datatosend = ['username' => $data['username'], 'ip_addr' => $data['ip'], 'email' => $data['email'], 'api_key' => $data['stopForumSpamKey'], 'evidence' => $data['evidence']];

			$response = $http->post('https://www.stopforumspam.com/add', $datatosend);
		}
		elseif ($type == 'api')
		{
			$datatosend = ['username' => $data['username'], 'ip_addr' => $data['ip'], 'email' => $data['email']];

			$response = $http->post('https://www.stopforumspam.com/api', $datatosend);
		}
		else
		{
			return false;
		}

		if ($response->code == '200')
		{
			if (KunenaFactory::getConfig()->logModeration)
			{
				$log = KunenaLog::LOG_USER_REPORT_STOPFORUMSPAM;

				KunenaLog::log(
					KunenaLog::TYPE_ACTION,
					$log,
					[
						'user_ip_reported'  => $data['ip'],
						'username_reported' => $data['username'],
						'email_reported'    => $data['email'],
					],
					null,
					null,
					null
				);
			}

			return $response;
		}

		return false;
	}
}
