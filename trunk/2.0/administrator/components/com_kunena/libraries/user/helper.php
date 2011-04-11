<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport('kunena.error');
kimport('kunena.user');

KunenaUserHelper::initialize();

/**
 * Kunena User Helper Class
 */
class KunenaUserHelper {
	protected static $_instances = array ();
	protected static $_online = null;
	protected static $_lastid = null;
	protected static $_total = null;
	protected static $_topposters = null;
	protected static $_me = null;

	private function __construct() {}

	public function initialize() {
		$id = JFactory::getUser()->id;
		self::$_me = self::$_instances [$id] = new KunenaUser ( $id );
	}
	/**
	 * Returns the global KunenaUserHelper object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$id	The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 * @return	JUser			The User object.
	 * @since	1.6
	 */
	static public function get($identifier = null, $reload = false) {
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
		if ($reload || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaUser ( $id );
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return self::$_instances [$id];
	}

	static public function getMyself() {
		return self::$_me;
	}

	static public function loadUsers($userids = array()) {
		if (!is_array($userids)) {
			JError::raiseError ( 500, __CLASS__ . '::' . __FUNCTION__.'(): Parameter $userids is not array' );
		}

		// Make sure that userids are unique and that indexes are correct
		$e_userids = array();
		foreach($userids as $userid){
			if (empty ( self::$_instances [intval($userid)] )) $e_userids[intval($userid)] = intval($userid);
		}
		unset($e_userids[0]);
		if (empty($e_userids)) return array();

		$userlist = implode ( ',', $e_userids );

		$db = JFactory::getDBO ();
		$query = "SELECT u.name, u.username, u.email, u.block as blocked, u.registerDate, u.lastvisitDate, ku.*
			FROM #__users AS u
			LEFT JOIN #__kunena_users AS ku ON u.id = ku.userid
			WHERE u.id IN ({$userlist})";
		$db->setQuery ( $query );
		$results = $db->loadAssocList ();
		KunenaError::checkDatabaseError ();

		$list = array ();
		foreach ( $results as $user ) {
			$instance = new KunenaUser (false);
			$instance->setProperties ( $user );
			$instance->exists(true);
			self::$_instances [$instance->userid] = $instance;
			$list [$instance->userid] = $instance;
		}

		// Finally call integration preload as well
		// Preload avatars if configured
		$avatars = KunenaFactory::getAvatarIntegration();
		$avatars->load($userids);

		return $list;
	}

	static public function getLastId() {
		if (self::$_lastid === null) {
			$db = JFactory::getDBO ();
			$config = KunenaFactory::getConfig();
			if ($config->userlist_count_users == '0' ) $where = '1';
			elseif ($config->userlist_count_users == '1' ) $where = 'block=0 OR activation=""';
			elseif ($config->userlist_count_users == '2' ) $where = 'block=0 AND activation=""';
			$db->setQuery ( "SELECT id FROM #__users WHERE {$where} ORDER BY id DESC", 0, 1 );
			self::$_lastid = (int) $db->loadResult ();
			KunenaError::checkDatabaseError();
		}
		return self::$_lastid;
	}

	static public function getTotalCount() {
		if (self::$_total === null) {
			$db = JFactory::getDBO ();
			$config = KunenaFactory::getConfig();
			if ($config->userlist_count_users == '0' ) $where = '1';
			elseif ($config->userlist_count_users == '1' ) $where = 'block=0 OR activation=""';
			elseif ($config->userlist_count_users == '2' ) $where = 'block=0 AND activation=""';
			$db->setQuery ( "SELECT COUNT(*) FROM #__users WHERE {$where}" );
			self::$_total = (int) $db->loadResult ();
			KunenaError::checkDatabaseError();
		}
		return self::$_total;
	}

	static public function getTopPosters($limit=0) {
		$limit = $limit ? $limit : KunenaFactory::getConfig()->popusercount;
		if (count(self::$_topposters) < $limit) {
			$db = JFactory::getDBO ();
			$query = "SELECT userid as id, posts AS count FROM #__kunena_users WHERE posts>0 ORDER BY posts DESC";
			$db->setQuery ( $query, 0, $limit );
			self::$_topposters = $db->loadObjectList ();
			KunenaError::checkDatabaseError();
		}
		return self::$_topposters;
	}

	public static function getOnlineUsers() {
		if (self::$_online === null) {
			$db = JFactory::getDBO ();
			$query = "SELECT s.userid, s.time
				FROM #__session AS s
				WHERE s.client_id=0 AND s.userid>0
				GROUP BY s.userid
				ORDER BY s.time DESC";

			$db->setQuery($query);
			self::$_online = $db->loadObjectList('userid');
			KunenaError::checkDatabaseError();
		}
		return self::$_online;
	}

	public static function getOnlineCount() {
		static $count = null;
		if ($count === null) {
			$kunena_config = KunenaFactory::getConfig ();
			$kunena_app = JFactory::getApplication ();
			$db = JFactory::getDBO ();

			$result = array ();
			$user_array = 0;
			$guest_array = 0;

			// need to calcute the time less the time selected by user, user
			$querytime = '';
			if ( $kunena_config->show_session_starttime != 0 ) {
				$time = JFactory::getDate()->toUnix() - $kunena_config->show_session_starttime;
				$querytime = 'AND time > '.$time;
			}

			$query = 'SELECT guest, time, usertype, client_id
				FROM #__session
				WHERE client_id = 0 ' . $querytime;
			$db->setQuery ( $query );
			$sessions = $db->loadObjectList ();
			KunenaError::checkDatabaseError ();

			// need to calculate the joomla session lifetime in timestamp, to check if the sessions haven't expired
			$j_session_lifetime = JFactory::getDate()->toUnix() - ( $kunena_app->getCfg('lifetime') * 60 );

			if (count($sessions)) {
				foreach ($sessions as $session) {
					// we check that the session hasn't expired
					if ( $kunena_config->show_session_type == 0 || $kunena_config->show_session_type == 2 || ($session->time > $j_session_lifetime && $kunena_config->show_session_type == 1 ) ) {
						// if guest increase guest count by 1
						if ($session->guest == 1 && !$session->usertype) {
							$guest_array ++;
						}
						// if member increase member count by 1
						if ($session->guest == 0) {
							$user_array ++;
						}
					}
				}
			}

			$result ['user'] = $user_array;
			$result ['guest'] = $guest_array;
		}
		//require_once JPATH_ROOT.'/modules/mod_whosonline/helper.php';
		//$count = modWhosonlineHelper::getOnlineCount();
		return $result;
	}

	public static function isOnline($user, $yes = false, $no = 'offline') {
		$user = self::get($user);
		$online = false;
		if (intval($user->userid) > 0) {
			if (self::$_online === null) {
				self::getOnlineUsers();
			}
			$online = isset(self::$_online [$user->userid]) ? (self::$_online [$user->userid]->time > $user->_session_timeout) : false;
		}
		if ($yes) return $online ? $yes : $no;
		return $online;
	}

	function recount() {
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
		return $rows;
	}
}
