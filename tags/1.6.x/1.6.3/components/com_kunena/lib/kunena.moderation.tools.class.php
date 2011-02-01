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

// Dont allow direct linking
defined( '_JEXEC' ) or die();

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
		$this->_db		= JFactory::getDBO ();
		$this->_my		= JFactory::getUser ();
		$this->_config	= KunenaFactory::getConfig ();

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
		$this->_db->setQuery ( "DELETE FROM #__kunena_users WHERE `userid`={$this->_db->Quote($UserID)};" );
		$this->_db->query ();
		if (KunenaError::checkDatabaseError()) return false;

		return true;
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
				"WHERE msgs.userid = {$this->_db->Quote( $UserID )} AND bantype=3 ".
				"GROUP BY msgs.ip ".
				"ORDER BY msgs.time ASC ".
				"LIMIT 0, ". $limit;

		$this->_db->setQuery ( $sql );
		$ipslist = $this->_db->loadObjectList ();
		KunenaError::checkDatabaseError();

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
				"FROM #__kunena_messages AS msgs ".
				"LEFT JOIN #__kunena_banned_users AS ban ON (msgs.userid = ban.userid) ".
				"WHERE msgs.ip = {$this->_db->Quote( $entry->ip )} ".
				"GROUP BY msgs.name, msgs.userid ".
				"ORDER BY msgs.time ASC ".
				"LIMIT 0, ". $usernamelimit;

			$this->_db->setQuery ( $sql );
			$useridslist[ $entry->ip ] = $this->_db->loadObjectList ();
			KunenaError::checkDatabaseError();
		}

		return $useridslist;
	}

	public function deleteUser($UserID) {
		return $this->_deleteUser( $UserID );
	}

	public function deleteUserAccount($UserID) {
		return $this->_deleteUser( $UserID );
	}

	// Public interface - IP

}
