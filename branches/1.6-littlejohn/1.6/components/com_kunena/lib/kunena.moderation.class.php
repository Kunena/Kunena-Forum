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
}

?>