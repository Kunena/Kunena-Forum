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

define ( 'KN_USER_BLOCK',1);	// block in joomla (even block login)
define ( 'KN_USER_BAN',2);	// ban in kunena (read-only mode)
define ( 'KN_USER_BAN_IP',3);	// ban IP in kunena (read-only mode)

class CKunenaModerationTools {
	// Private data and functions
	protected	$_db				= null;
	protected	$_my				= null;
	protected	$_config			= null;

	private		$_allowed			= null;
	private		$_errormsg			= null;

	/**
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->_db		= & JFactory::getDBO ();
		$this->_my		= & JFactory::getUser ();
		$this->_config	= & CKunenaConfig::getInstance ();

		$this->_ResetErrorMessage ();
	}


	/**
	 * Get latest error message if a function fails
	 *
	 * @access public
	 * @return string Errormessage
	 */
	public function getErrorMessage() {
		return $this->_errormsg;
	}


	/**
	 * Reset internal error message
	 *
	 * @access protected
	 * @return void
	 */
	private function _ResetErrorMessage() {
		$this->_errormsg = '';
	}

	/**
	 * Ban a user
	 *
	 * @access protected
	 * @param unknown_type $UserID
	 * @param unknown_type $expiry
	 * @param unknown_type $message
	 * @param unknown_type $comment
	 * @return boolean
	 * @uses self::_canChangeUserstate()
	 * @uses self::_addUserstate()
	 */
	protected function _banUser($UserID, $expiry, $banstart, $public_reason, $private_reason, $on_profile, $on_message, $ip) {
		// sub functions sanitizes input

		$this->_ResetErrorMessage ();

		if ( !$this->_canChangeUserstate( $UserID ) ) {
			return false;
		}

		if ( !$this->_addUserstate( $UserID, KN_USER_BAN, $expiry, $banstart, $public_reason, $private_reason, $on_profile, $on_message, $ip ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Unban a user
	 *
	 * @access protected
	 * @param unknown_type $UserID
	 * @return boolean
	 * @uses self::_canChangeUserstate()
	 * @uses self::_addUserstate()
	 */
	protected function _unbanUser($UserID) {
		// sub functions sanitizes input

		$this->_ResetErrorMessage ();

		if ( !$this->_canChangeUserstate( $UserID ) ) {
			return false;
		}

		// Disable only, so we keep the history on the user
		if ( !$this->_disableUserstates( $UserID, KN_USER_BAN ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Block a user from login
	 *
	 * @access protected
	 * @param unknown_type $UserID
	 * @param unknown_type $expiry
	 * @param unknown_type $message
	 * @param unknown_type $comment
	 * @return boolean
	 * @uses self::_canChangeUserstate()
	 * @uses self::_addUserstate()
	 */
	protected function _blockUser($UserID, $public_reason, $private_reason, $on_profile, $on_message, $ip) {
		// sub functions sanitizes input

		$this->_ResetErrorMessage ();

		if ( !$this->_canChangeUserstate( $UserID ) ) {
			return false;
		}

		if ( !$this->_addUserstate( $UserID, KN_USER_BLOCK, '', '', $public_reason, $private_reason, $on_profile, $on_message, $ip ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Unblock a user
	 *
	 * @access protected
	 * @param unknown_type $UserID
	 * @return boolean
	 * @uses self::_canChangeUserstate()
	 * @uses self::_addUserstate()
	 */
	protected function _unblockUser($UserID) {
		// sub functions sanitizes input

		$this->_ResetErrorMessage ();

		if ( !$this->_canChangeUserstate( $UserID ) ) {
			return false;
		}

		// Disable only, so we keep the history on the user
		if ( !$this->_disableUserstates( $UserID, KN_USER_BLOCK ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Add a userstate for a userid
	 *
	 * @access private
	 * @param unknown_type $UserID
	 * @param unknown_type $mode
	 * @param unknown_type $expiry
	 * @param unknown_type $message
	 * @param unknown_type $comment
	 * @return boolean
	 * @todo Implement config defaults for expiry, message and comment
	 */
	private function _addUserstate($UserID, $mode, $expiry, $banstart, $public_reason, $private_reason, $on_profile, $on_message, $ip) {
		// Sanitize parameters!
		$UserID		= intval ( $UserID );
		$mode		= intval ( $mode );
		$expiry		= trim ( $expiry );
		$banstart		= trim ( $banstart );
		$public_reason	= trim ( $public_reason );
		$private_reason	= trim ( $private_reason );
		$on_profile	= intval ( $on_profile );
		$on_message		= intval ( $on_message );
		$ip	= trim ( $ip );

		$user = JUser::getInstance($UserID);
		if (!$user->id) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USERSTATE_NO_USER', $UserID );
			return false;
		}

		switch ( $mode ) {
			case KN_USER_BAN:
				$sql = "SELECT userid FROM #__kunena_banned_users WHERE `userid`='$UserID' AND `bantype`=2";
				$this->_db->setQuery ( $sql );
				$userbannedexist = $this->_db->loadResult ();
				check_dberror ( 'Unable to load users banned.' );

				break;
			case KN_USER_BLOCK:
				$sql = "SELECT userid FROM #__kunena_banned_users WHERE `userid`='$UserID' AND `bantype`=1";
				$this->_db->setQuery ( $sql );
				$userbannedexist = $this->_db->loadResult ();
				check_dberror ( 'Unable to load users banned.' );

				$user->block = 1;
				$user->save();

				$this->_logoutUser($UserID);

				break;
			default:
				// Unsupported mode - Error!
				$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_UNSUPPORTED_MODE');
				return false;
		}

		if ( !$userbannedexist ) {
			$query = "INSERT INTO #__kunena_banned_users ( `enabled`, `userid`, `bantype`, `expiry`,  `ban_start` , `created`, `created_userid`, `on_profile`, `on_message`,`private_reason`,`public_reason`,`ip`)
					VALUES ( 1, '$UserID', '$mode', '$expiry', '$banstart' , NOW(), '{$this->_my->id}', '$on_profile', '$on_message', {$this->_db->Quote($public_reason)}, {$this->_db->Quote($private_reason)},'$ip')";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			check_dberror ( 'Unable to insert user state.' );
		} else {
			$query = "UPDATE #__kunena_banned_users SET `enabled`=1 WHERE `userid`=$UserID";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			check_dberror ( 'Unable to insert user state.' );
		}

		return true;
	}


	/**
	 * Disables all userstates for a given user
	 *
	 * @access private
	 * @param unknown_type $UserID
	 * @param unknown_type $mode
	 * @return boolean
	 */
	private function _disableUserstates($UserID, $mode) {
		// Sanitize parameters!
		$UserID		= intval ( $UserID );
		$mode		= intval ( $mode );

		$user = JUser::getInstance($UserID);
		if (!$user->id) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USERSTATE_NO_USER', $UserID );
			return false;
		}

		// appended this extra text to comment
		$extra = "(Disabled by ". $this->_my->id ." at ". date('r') .")";

		switch ( $mode ) {
			case KN_USER_BAN:
				$query = "UPDATE #__kunena_banned_users SET `enabled`=0, comment=CONCAT(comment, '". $extra ."') WHERE bantype=2 AND `userid`='{$UserID}' AND `enabled`=1";

				break;
			case KN_USER_BLOCK:
				$query = "UPDATE #__kunena_banned_users SET `enabled`=0, comment=CONCAT(comment, '". $extra ."') WHERE bantype=1 AND `userid`='{$UserID}' AND `enabled`=1";

				$user->block = 0;
				$user->save();

				$this->_logoutUser($UserID);	// is this needed?
				break;
			default:
				// Unsupported mode - Error!
				$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_UNSUPPORTED_MODE');
				return false;
		}

		$this->_db->setQuery ( $query );
		$this->_db->query ();
		check_dberror ( 'Unable to delete user state.' );

		return true;
	}


	/**
	 * Check if current user can change state for a given userid
	 *
	 * @access private
	 * @param unknown_type $UserID
	 * @return boolean
	 */
	private function _canChangeUserstate($UserID) {
		// Sanitize parameters!
		$UserID = intval ( $UserID );

		if ( !CKunenaTools::isModerator($this->_my->id) ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR');
			return false;
		}

		if ( !$UserID ) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USERSTATE_ANONYMOUS' );
			return false;
		}

		if ( $UserID == $this->_my->id ) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USERSTATE_YOURSELF' );
			return false;
		}

		$user = JUser::getInstance($UserID);
		if (!$user->id) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USERSTATE_NO_USER', $UserID );
			return false;
		}

		// Nobody can ban/block admins
		if ( CKunenaTools::isAdmin($UserID) ) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USERSTATE_ADMIN', $user->username );
			return false;
		}

		// Only admins can ban/block moderators
		if ( !CKunenaTools::isAdmin($this->_my->id) && CKunenaTools::isModerator($UserID) ) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USERSTATE_MODERATOR', $user->username );
			return false;
		}

		return true;
	}


	/**
	 * Log out a user on demand
	 *
	 * @access protected
	 * @param unknown_type $UserID
	 * @return boolean
	 */
	protected function _logoutUser($UserID) {
		// Sanitize parameters!
		$UserID = intval ( $UserID );

		if ( !CKunenaTools::isModerator($this->_my->id) ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR');
			return false;
		}

		$kunena_app = & JFactory::getApplication ();
		$options = array();
		$options['clientid'][] = 0; //site
		$kunena_app->logout( (int) $UserID, $options);

		return true;
	}


	/**
	 * Delete a user
	 *
	 * @access protected
	 * @param unknown_type $UserID
	 * @return boolean
	 */
	protected function _deleteUser($UserID) {
		// Sanitize parameters!
		$UserID = intval ( $UserID );

		if ( !CKunenaTools::isAdmin($this->_my->id) ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_ADMIN');
			return false;
		}
		if ( $UserID == $this->_my->id ) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USER_DELETE_YOURSELF' );
			return false;
		}
		if (!$UserID) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USER_DELETE_ANONYMOUS' );
			return false;
		}
		$user = JUser::getInstance($UserID);
		if (!$user->id) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USER_DELETE_NO_USER', $UserID );
			return false;
		}
		// Nobody can delete admins
		if ( CKunenaTools::isAdmin($UserID) ) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USER_DELETE_ADMIN', $user->username );
			return false;
		}

		$user->delete();
		$this->_db->setQuery ( "DELETE FROM #__kunena_users WHERE `userid`='$UserID';" );
		$this->_db->query ();
		check_dberror ( "Unable to delete user from kunena." );

		return true;
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
				"FROM #__kunena_banned_users ban ".
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
				"FROM #__kunena_banned_users ban ".
				"LEFT JOIN #__users user ON (user.id = ban.created_userid) ".
				"WHERE ban.ip IN (". $ips .") AND bantype=3 ".
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
	 * @param int $UserID
	 * @param int $limit
	 */
	public function getIPs ( $UserID, $limit = 100 ) {
		// Sanitize parameters!
		$UserID	= intval ( $UserID );
		$limit	= intval ( $limit );

		$sql = "SELECT msgs.ip AS ip, MAX(ban.enabled) AS enabled ".
				"FROM #__kunena_messages msgs ".
				"LEFT JOIN #__kunena_banned_users ban ON (ban.ip = msgs.ip) ".
				"WHERE msgs.userid = '". $UserID ."' AND bantype=3 ".
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
	 * @param int $UserID
	 * @param int $limit
	 * @param int $usernamelimit
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
				"FROM #__kunena_messages msgs ".
				"LEFT JOIN #__kunena_banned_users ban ON (msgs.userid = ban.userid) ".
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

	/**
	 *
	 */
	protected function _banIP($ip, $expiry, $message, $comment) {
		$sql = "SELECT ip FROM #__kunena_banned_ips WHERE ip='$ip'";
		$this->_db->setQuery ( $sql );
		$ipexist = $this->_db->loadResult ();
		check_dberror ( 'Unable to load usernames for ip.' );

	if ( !$ipexist ) {
			$sql = "INSERT INTO #__kunena_banned_users (enabled,ip,expiry,message,comment,on_profile,on_message,private_reason,public_reason) VALUES ('1',$ip', '$expiry', '$message', '$comment')";
			$this->_db->setQuery ( $sql );
			$this->_db->Query ();
			check_dberror ( 'Unable to insert new element in ip table.' );
		} else {
			$query = "UPDATE #__kunena_banned_users SET `enabled`=1 WHERE `ip`=$ip AND `bantype`=3";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			check_dberror ( 'Unable to insert user state.' );
		}
	}

	/**
	 *
	 * @param int $UserID
	 */
	protected function _unbanIP($ip) {
		$sql = "UPDATE #__kunena_banned_users SET `enabled`=0 WHERE `ip`=$ip";
		$this->_db->setQuery ( $sql );
		$this->_db->Query ();
		check_dberror ( 'Unable to disable ban ip.' );
	}

	// Public interface - Users


	public function blockUser($UserID, $expiry, $public_reason, $private_reason, $on_profile, $on_message, $ip ) {
		return $this->_blockUser( $UserID, $expiry, $public_reason, $private_reason, $on_profile, $on_message, $ip );
	}

	public function unblockUser($UserID) {
		return $this->_unblockUser( $UserID );
	}

	public function banUser($UserID, $expiry, $banstart, $public_reason, $private_reason, $on_profile, $on_message, $ip ) {
		return $this->_banUser( $UserID, $expiry, $banstart, $public_reason, $private_reason, $on_profile, $on_message, $ip );
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
		return $this->_banIP($ip, $expiry, $message, $comment);
	}

	public function unbanIP($ip) {
		return $this->_unbanIP($ip);
	}
}

?>
