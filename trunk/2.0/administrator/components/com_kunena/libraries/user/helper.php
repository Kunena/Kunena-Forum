<?php
/**
 * @version $Id$
 * Kunena Component - KunenaUserHelper Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport('kunena.error');
kimport('kunena.user');

/**
 * Kunena User Helper Class
 */
class KunenaUserHelper {
	protected static $_instances = array ();
	protected static $_online = null;

	private function __construct() {}

	/**
	 * Returns the global KunenaUserHelper object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$id	The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 * @return	JUser			The User object.
	 * @since	1.6
	 */
	static public function get($identifier = null, $reload = false) {
		if ($identifier instanceof KunenaUser) {
			return $identifier;
		}
		if ($identifier === null || $identifier === false) {
			$identifier = JFactory::getUser ();
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

		return self::$_instances [$id];
	}

	static public function loadUsers($userids = array()) {
		if (!is_array($userids)) {
			JError::raiseError ( 500, __CLASS__ . '::' . __FUNCTION__.'(): Parameter $userids is not array' );
		}

		// Make sure that userids are unique and that indexes are correct
		$e_userids = array();
		foreach($userids as $userid){
			$e_userids[intval($userid)] = intval($userid);
		}
		unset($e_userids[0]);
		if (empty($e_userids)) return array();

		$userlist = implode ( ',', $e_userids );

		$db = JFactory::getDBO ();
		$query = "SELECT u.name, u.username, u.block as blocked, ku.*
			FROM #__users AS u
			LEFT JOIN #__kunena_users AS ku ON u.id = ku.userid
			WHERE u.id IN ({$userlist})";
		$db->setQuery ( $query );
		$results = $db->loadAssocList ();
		KunenaError::checkDatabaseError ();

		$list = array ();
		foreach ( $results as $user ) {
			$instance = new KunenaUser ();
			$instance->bind ( $user );
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

	public static function getOnlineUsers() {
		if (self::$_online === null) {
			$db = JFactory::getDBO ();
			$query = "SELECT s.userid, s.time
				FROM #__session AS s
				INNER JOIN #__kunena_users AS k ON k.userid=s.userid
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
				$time = CKunenaTimeformat::internalTime() - $kunena_config->show_session_starttime;
				$querytime = 'AND time > '.$time;
			}

			$query = 'SELECT guest, time, usertype, client_id
				FROM #__session
				WHERE client_id = 0 ' . $querytime;
			$db->setQuery ( $query );
			$sessions = $db->loadObjectList ();
			KunenaError::checkDatabaseError ();

			// need to calculate the joomla session lifetime in timestamp, to check if the sessions haven't expired
			$j_session_lifetime = CKunenaTimeformat::internalTime() - ( $kunena_app->getCfg('lifetime') * 60 );

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

	public static function isOnline($user, $yesno = false) {
		$user = self::get($user);
		$online = false;
		if (intval($user->userid) > 0) {
			if (self::$_online === null) {
				self::getOnlineUsers();
			}
			$online = isset(self::$_online [$user->userid]) ? (self::$_online [$user->userid]->time > $user->_session_timeout) : false;
		}
		if ($yesno) return $online ? 'yes' : 'no';
		return $online;
	}

	function recount() {
		$db = JFactory::getDBO ();

		// Reset category counts as next query ignores users which have written no messages
		$db->setQuery ( "UPDATE #__kunena_users SET posts=0" );
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Update user post count (ignore unpublished categories and hidden messages)
		$db->setQuery ( "INSERT INTO #__kunena_users (userid, posts)
			SELECT m.userid, COUNT(m.userid)
			FROM #__kunena_messages AS m
			INNER JOIN #__kunena_users AS u ON u.userid = m.userid
			WHERE m.hold=0 AND m.moved=0 AND m.catid IN (SELECT id FROM #__kunena_categories WHERE published=1)
			GROUP BY m.userid
			ON DUPLICATE KEY UPDATE posts=VALUES(posts)" );
		$db->query ();
		KunenaError::checkDatabaseError ();
	}
}
