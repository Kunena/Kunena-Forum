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

KunenaUserHelper::initialize();

/**
 * Class KunenaUserHelper
 */
abstract class KunenaUserHelper {
	/**
	 * @var array|KunenaUser[]
	 */
	protected static $_instances = array ();
	/**
	 * @var array|KunenaUser[]
	 */
	protected static $_instances_name = array ();
	protected static $_online = null;
	protected static $_lastid = null;
	protected static $_total = null;
	protected static $_topposters = null;
	protected static $_me = null;

	public static function initialize() {
		$id = JFactory::getUser()->id;
		self::$_me = self::$_instances [$id] = new KunenaUser ( $id );
	}

	/**
	 * Returns the global KunenaUser object, only creating it if it doesn't already exist.
	 *
	 * @param mixed $identifier	The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 * @param bool $reload		Reload user from database.
	 *
	 * @return KunenaUser
	 */
	public static function get($identifier = null, $reload = false) {
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		if ($identifier === null || $identifier === false) {
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return self::$_me;
		}
		if ($identifier instanceof KunenaUser) {
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return $identifier;
		}
		// Find the user id
		if ($identifier instanceof JUser) {
			$id = intval ( $identifier->id );
		} else if (is_numeric ( $identifier )) {
			$id = intval ( $identifier );
		} else {
			jimport ( 'joomla.user.helper' );
			$id = intval ( JUserHelper::getUserId ( ( string ) $identifier ) );
		}

		// Always return fresh user if id is anonymous/not found
		if ($id === 0) {
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return new KunenaUser ( $id );
		}
		else if ($reload || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaUser ( $id );
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return self::$_instances [$id];
	}

	/**
	 * @param int $id
	 * @param string $name
	 *
	 * @return KunenaUser
	 */
	public static function getAuthor($id, $name) {
		$id = (int) $id;
		if ($id && !empty ( self::$_instances [$id] )) {
			return self::$_instances [$id];
		}
		if (!empty ( self::$_instances_name [$name] )) {
			return self::$_instances_name [$name];
		}
		$user = self::get($id);
		if (!$user->exists()) {
			$user->username = $user->name = $name;
		}
		self::$_instances_name [$name] = $user;
		return $user;
	}

	/**
	 * @return KunenaUser
	 */
	public static function getMyself() {
		return self::$_me;
	}

	/**
	 * @param array $userids
	 *
	 * @return array
	 */
	public static function loadUsers(array $userids = array()) {
		// Make sure that userids are unique and that indexes are correct
		$e_userids = array();
		foreach($userids as $userid){
			if (intval($userid) && empty ( self::$_instances [$userid] )) {
				$e_userids[$userid] = $userid;
			}
		}

		if (!empty($e_userids)) {
			$userlist = implode ( ',', $e_userids );

			$db = JFactory::getDBO ();
			$query = "SELECT u.name, u.username, u.email, u.block as blocked, u.registerDate, u.lastvisitDate, ku.*, u.id AS userid
				FROM #__users AS u
				LEFT JOIN #__kunena_users AS ku ON u.id = ku.userid
				WHERE u.id IN ({$userlist})";
			$db->setQuery ( $query );
			$results = $db->loadAssocList ();
			KunenaError::checkDatabaseError ();

			foreach ($results as $user) {
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
		foreach ($userids as $userid) {
			if (isset(self::$_instances [$userid])) $list [$userid] = self::$_instances [$userid];
		}
		return $list;
	}

	/**
	 * @return int
	 */
	public static function getLastId() {
		if (self::$_lastid === null) {
			self::getTotalCount();
		}
		return (int) self::$_lastid;
	}

	/**
	 * @return int
	 */
	public static function getTotalCount() {
		if (self::$_total === null) {
			$db = JFactory::getDBO ();
			$config = KunenaFactory::getConfig();
			if ($config->userlist_count_users == '1' ) $where = '(block=0 OR activation="")';
			elseif ($config->userlist_count_users == '2' ) $where = '(block=0 AND activation="")';
			elseif ($config->userlist_count_users == '3' ) $where = 'block=0';
			else $where = '1';
			$db->setQuery ( "SELECT COUNT(*), MAX(id) FROM #__users WHERE {$where}" );
			list (self::$_total, self::$_lastid) = $db->loadRow ();
			KunenaError::checkDatabaseError();
		}
		return (int) self::$_total;
	}

	/**
	 * @param int $limit
	 *
	 * @return array
	 */
	public static function getTopPosters($limit=0) {
		$limit = $limit ? $limit : KunenaFactory::getConfig()->popusercount;
		if (count(self::$_topposters) < $limit) {
			$db = JFactory::getDBO ();
			$query = "SELECT u.id, ku.posts AS count
				FROM #__kunena_users AS ku
				INNER JOIN #__users AS u ON u.id=ku.userid
				WHERE ku.posts>0
				ORDER BY ku.posts DESC";
			$db->setQuery ( $query, 0, $limit );
			self::$_topposters = (array) $db->loadObjectList ();
			KunenaError::checkDatabaseError();
		}
		return self::$_topposters;
	}

	/**
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
				$time = JFactory::getDate()->toUnix() - ($app->getCfg('lifetime', 15) * 60);
				$query->where('time > ' . $time);
			}

			$db->setQuery($query);
			self::$_online = (array) $db->loadObjectList('userid');
			KunenaError::checkDatabaseError();
		}

		return self::$_online;
	}

	/**
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
				$time = JFactory::getDate()->toUnix() - ($app->getCfg('lifetime', 15) * 60);
				$query->where('time > ' . $time);
			}

			$db->setQuery($query);
			$count = $db->loadResult();
			KunenaError::checkDatabaseError();

			$counts = array();
			$counts['user'] = count(self::getOnlineUsers());
			$counts['guest'] = $count;
		}
		return $counts;
	}

	/**
	 * @param mixed  $user
	 * @param bool|string   $yes
	 * @param string $no
	 *
	 * @return bool|string
	 */
	public static function isOnline($user, $yes = false, $no = 'offline') {
		$user = self::get($user);
		if (!$user->showOnline && !self::getMyself()->isModerator()) return $yes ? $no : false;
		$online = false;
		if (intval($user->userid) > 0) {
			if (self::$_online === null) {
				self::getOnlineUsers();
			}
			$online = isset(self::$_online [$user->userid]) ? (self::$_online [$user->userid]->time >  time() - JFactory::getApplication()->getCfg ( 'lifetime', 15 ) * 60) : false;
		}
		if ($yes) return $online ? $yes : $no;
		return $online;
	}

	public static function recount() {
		$db = JFactory::getDBO ();

		// If user has no user_topics, set posts into 0
		$query ="UPDATE #__kunena_users AS u
			LEFT JOIN #__kunena_user_topics AS ut ON ut.user_id=u.userid
			SET u.posts = 0
			WHERE ut.user_id IS NULL";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		$rows = $db->getAffectedRows ();

		// Update user post count
		$query = "INSERT INTO #__kunena_users (userid, posts)
				SELECT user_id AS userid, SUM(posts) AS posts
				FROM #__kunena_user_topics
				GROUP BY user_id
			ON DUPLICATE KEY UPDATE posts=VALUES(posts)";
		$db->setQuery ($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		$rows += $db->getAffectedRows ();

		// Update banned state
		// TODO: move out of here, it's slow
		$query = "UPDATE #__kunena_users AS u
			LEFT JOIN (
				SELECT userid, MAX(expiration) AS banned FROM #__kunena_users_banned GROUP BY userid
			) AS b ON u.userid=b.userid
			SET u.banned=b.banned";
		$db->setQuery ($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return false;
		$rows += $db->getAffectedRows ();

		return $rows;
	}
}
