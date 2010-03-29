<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die();


require_once (KUNENA_PATH_LIB .DS. "kunena.moderation.tools.class.php");


class CKunenaModeration extends CKunenaModerationTools {
	static $instance = null;
	
	public function &getInstance() {
		if ( !self::$instance ) {
			self::$instance = new CKunenaModeration ( );
		}
		return self::$instance;
	}
	
	
	// Public interface - Threads and posts
	
	
	public function moveThread($ThreadID, $TargetCatID, $GhostThread = false) {
		return $this->_movePost ( $ThreadID, $TargetCatID, '', 0, self::KN_MOVE_THREAD, $GhostThread );
	}

	public function moveMessage($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0) {
		return $this->_movePost ( $ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, self::KN_MOVE_MESSAGE );
	}

	public function moveMessageAndNewer($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0) {
		return $this->_movePost ( $ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, self::KN_MOVE_NEWER );
	}

	public function moveMessageAndReplies($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0) {
		return $this->_movePost ( $ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, self::KN_MOVE_REPLIES );
	}

	public function deleteThread($ThreadID, $DeleteAttachments = false) {
		return $this->_deletePost ( $ThreadID, $DeleteAttachments, self::KN_DEL_THREAD );
	}

	public function deleteMessage($MessageID, $DeleteAttachments = false) {
		return $this->_deletePost ( $MessageID, $DeleteAttachments, self::KN_DEL_MESSAGE );
	}

	public function deleteAttachments($MessageID) {
		return $this->_deletePost ( $MessageID, true, self::KN_DEL_ATTACH );
	}

	public function createGhostPost($MessageID, $currentMessage) {
		return $this->_createGhostPost( $MessageID, $currentMessage );
	}
	
	public function createGhostThread($MessageID, $currentMessage) {
		return $this->createGhostPost($MessageID, $currentMessage);
	}
	
	
	// Public interface - Users
	
	
	public function blockUser($UserID, $expiry, $message, $comment) {
		return $this->_blockUser( $UserID, $expiry, $message, $comment );
	}

	public function unblockUser($UserID) {
		return $this->_unblockUser( $UserID );
	}

	public function banUser($UserID, $expiry, $message, $comment) {
		return $this->_banUser( $UserID, $expiry, $message, $comment );
	}

	public function unbanUser($UserID) {
		return $this->_unbanUser( $UserID );
	}

	public function logoutUser($UserID) {
		return $this->_logoutUser( $UserID );
	}

	public function deleteUser($UserID) {
		return $this->_deleteUser( $UserID );
	}
	
	public function blockUserAccount($UserID, $block) {
		if ( $block ) {
			return $this->_blockUser( $UserID );
		}
		return $this->_unblockUser( $UserID );
	}

	public function deleteUserAccount($UserID) {
		return $this->_deleteUser( $UserID );
	}
	
	// Public interface - IP
	
	
	public function banIP($ip, $expiry, $message, $comment) {
		// Todo
	}

	public function unbanIP($ip) {
		// Todo
	}

	public function disableIPban($id) {
		// Todo
	}
	
	
	// Public interface - Getters (will be moved to tools later)
	
	
	public function getUserIPs ($UserID) {
		// Sanitize parameters!
		$UserID = intval ( $UserID );

		$this->_db->setQuery ( "SELECT ip FROM #__fb_messages WHERE userid=$UserID GROUP BY ip" );
		$ipslist = $this->_db->loadObjectList ();
		check_dberror ( 'Unable to load ip for user.' );

		return $ipslist;
	}

	public function getUsernameMatchingIPs ($UserID) {
		// Sanitize parameters!
		$UserID = intval ( $UserID );

		$ipslist = $this->getUserIPs ($UserID);

		$useridslist = array();
		foreach ($ipslist as $ip) {
			$this->_db->setQuery ( "SELECT name,userid FROM #__fb_messages WHERE ip='$ip->ip' GROUP BY name" );
			$useridslist[$ip->ip] = $this->_db->loadObjectList ();
			check_dberror ( 'Unable to load ip for user.' );
		}

		return $useridslist;
	}
	
	/**
	 * 
	 * @param unknown_type $UserID
	 * @param unknown_type $limit
	 */
	public function getBanHistory ( $UserID, $limit = 1000 ) {
		// Sanitize parameters!
		$UserID	= intval ( $UserID );
		$limit	= intval ( $limit );
		
		$sql = "SELECT ban.id, ban.enabled, ban.userid, ban.bantype, ban.expiry, ban.message, ban.created, ban.created_userid, user.username AS created_name, ban.comment ".
				"FROM #__fb_banned_users ban ".
				"LEFT JOIN #__users user ON (user.id = ban.created_userid) ".
				"WHERE ban.userid = '". $UserID ."' ". 
				"ORDER BY ban.created ASC ".
				"LIMIT 0, ". $limit;
		
		$this->_db->setQuery ( $sql );
		$blocklist = $this->_db->loadObjectList ();
		check_dberror ( 'Unable to load blocks for user.' );
		
		return $blocklist;
	}
	
	
	/**
	 * 
	 * @param $UserID
	 */
	public function getIPBanHistory ( $UserID, $limit = 1000 ) {
		// Sanitize parameters!
		$UserID	= intval ( $UserID );
		$limit	= intval ( $limit );
		
		$iplist = $this->getIPs( $UserID, $limit );
		
		// extract ips to string
		$ips = '';
		foreach ($iplist as $entry) {
			$ips .= "'". $entry->ip ."',";
		}
		
		if ($ips) {
			$ips = substr($ips, 0, -1);	// remove last comma
			
			$sql = "SELECT ban.id, ban.enabled, ban.ip, ban.expiry, ban.message, ban.created, ban.created_userid, user.username AS created_name, ban.comment ".
				"FROM #__fb_banned_ips ban ".
				"LEFT JOIN #__users user ON (user.id = ban.created_userid) ".
				"WHERE ban.ip IN (". $ips .") ". 
				"ORDER BY ban.created ASC ".
				"LIMIT 0, ". $limit;
			
			$this->_db->setQuery ( $sql );
			$blocklist = $this->_db->loadObjectList ();
			check_dberror ( 'Unable to load blocks for user.' );
		}
		else {
			$blocklist = array();
		}
		
		return $blocklist;
	}
	
	
	/**
	 * 
	 * @param unknown_type $UserID
	 * @param unknown_type $limit
	 */
	public function getIPs ( $UserID, $limit = 100 ) {
		// Sanitize parameters!
		$UserID	= intval ( $UserID );
		$limit	= intval ( $limit );
		
		$sql = "SELECT msgs.ip AS ip, MAX(ban.enabled) AS enabled ". 
				"FROM #__fb_messages msgs ".
				"LEFT JOIN #__fb_banned_ips ban ON (ban.ip = msgs.ip) ".
				"WHERE msgs.userid = '". $UserID ."' ". 
				"GROUP BY msgs.ip ".
				"ORDER BY msgs.time ASC ".
				"LIMIT 0, ". $limit;
		
		$this->_db->setQuery ( $sql );
		$ipslist = $this->_db->loadObjectList ();
		check_dberror ( 'Unable to load ips for user.' );

		return $ipslist;
	}
	
	
	/**
	 * 
	 * @param $UserID
	 * @param $limit
	 * @param $usernamelimit
	 */
	public function getUsersMatchingUserIP ( $UserID, $limit = 1000, $usernamelimit = 100 ) {
		// Sanitize parameters!
		$UserID			= intval ( $UserID );
		$limit			= intval ( $limit );
		$usernamelimit	= intval ( $usernamelimit );
		
		$iplist = $this->getIPs( $UserID, $limit );
		
		$useridslist = array();
		foreach ($iplist as $entry) {
			$sql = "SELECT msgs.name, msgs.userid, MAX(ban.enabled) AS enabled ".
				"FROM #__fb_messages msgs ".
				"LEFT JOIN #__fb_banned_users ban ON (msgs.userid = ban.userid) ".
				"WHERE msgs.ip = '". $entry->ip ."' ".
				"GROUP BY msgs.name, msgs.userid ".
				"ORDER BY msgs.time ASC ".
				"LIMIT 0, ". $usernamelimit;
			
			$this->_db->setQuery ( $sql );
			$useridslist[ $entry->ip ] = $this->_db->loadObjectList ();
			check_dberror ( 'Unable to load usernames for ip.' );
		}
		
		return $useridslist;
	}
}

?>