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

// Defines for moves
define ( 'KN_MOVE_MESSAGE', 0 );
define ( 'KN_MOVE_THREAD', 1 );
define ( 'KN_MOVE_NEWER', 2 );
define ( 'KN_MOVE_REPLIES', 3 );

//Defines for deletes
define ( 'KN_DEL_MESSAGE', 0 );
define ( 'KN_DEL_THREAD', 1 );
define ( 'KN_DEL_ATTACH', 2 );

require_once (KUNENA_PATH_LIB .DS. "kunena.session.class.php");

class CKunenaModeration {
	// Private data and functions
	protected $_db = null;
	protected $_my = null;
	protected $_session = null;
	protected $_errormsg = null;
	protected $_config = null;

	protected function __construct($db, $config) {
		$this->_db = $db;
		$this->_my = &JFactory::getUser ();
		$this->_session = CKunenaSession::getInstance ();
		$this->_allowed = ($this->_session->allowed != '') ? explode ( ',', $this->_session->allowed ) : array();
		$this->_ResetErrorMessage ();
		$this->_config = $config;
	}

	public function &getInstance() {
		static $instance = NULL;
		if (! $instance) {
			$kunena_db = & JFactory::getDBO ();
			$kunena_config = & CKunenaConfig::getInstance ();

			$instance = new CKunenaModeration ( $kunena_db, $kunena_config );
		}
		return $instance;
	}

	protected function _ResetErrorMessage() {
		$this->_errormsg = '';
	}

	protected function _Move($MessageID, $TargetCatID, $TargetSubject = '', $TargetMessageID = 0, $mode = KN_MOVE_MESSAGE, $GhostThread = false) {
		// Private move function
		// $mode
		// KN_MOVE_MESSAGE ... move current message only
		// KN_MOVE_THREAD  ... move entire thread
		// KN_MOVE_NEWER   ... move current message and all newer in current thread
		// KN_MOVE_REPLIES ... move current message and replies and quotes - 1 level deep
		//
		// if $TargetMessagID is a valid message ID, the messages will be appended to that thread


		// Reset error message
		$this->_ResetErrorMessage ();

		// Sanitize parameters!
		$MessageID = intval ( $MessageID );
		$TargetCatID = intval ( $TargetCatID );
		$TargetSubject = addslashes ( $TargetSubject );
		$TargetMessageID = intval ( $TargetMessageID );
		$mode = intval ( $mode );
		// no need to check $GhostThread as we only test for true

		// Always check security clearance before taking action!

		// Assumption: only moderators can move messages
		// This test is made to prevent user to guess existing message ids
		if ( !CKunenaTools::isModerator($this->_my->id) ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR');
			return false;
		}

		$query = 'SELECT `id`, `catid`, `parent`, `thread`, `subject` FROM #__fb_messages WHERE `id`='.$MessageID;
		$this->_db->setQuery ( $query );
		$currentMessage = $this->_db->loadObject ();
		check_dberror ( "Unable to load current message." );

		// Check that message to be moved actually exists
		if ( !is_object($currentMessage) ) {
			$this->_errormsg = JText::sprintf('COM_KUNENA_MODERATION_ERROR_MESSAGE_NOT_FOUND', $MessageID);
			return false;
		}

		// Check that thread can't be move into a section
		$query = 'SELECT `parent` FROM #__fb_categories WHERE `id`='.$TargetCatID;
		$this->_db->setQuery ( $query );
		$catParent = $this->_db->loadResult ();
		check_dberror ( "Unable to load category detail." );
		if ( $catParent == '0' ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MOVE_SECTION');
			return false;
		}

		// Check that user has moderator permissions in source category
		if ( !CKunenaTools::isModerator($this->_my->id, $currentMessage->catid) ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR_IN_CATEGORY', $currentMessage->id, $currentMessage->catid);
			return false;
		}

		// Check that we have target category or message
		if ($TargetCatID == 0 && $TargetMessageID == 0) {
			$this->_errormsg = JText::printf('COM_KUNENA_MODERATION_ERROR_NO_TARGET', $currentMessage->id);
			return false;
		}

		if ($TargetMessageID != 0) {
			// Check that target message actually exists
			$this->_db->setQuery ( "SELECT `id`, `catid`, `parent`, `thread`, `subject` FROM #__fb_messages WHERE `id`='$TargetMessageID'" );
			$targetMessage = $this->_db->loadObject ();
			check_dberror ( "Unable to load message." );

			if ( !is_object( $targetMessage )) {
				// Target message not found. Cannot proceed with move
				$this->_errormsg = JText::printf('COM_KUNENA_MODERATION_ERROR_TARGET_MESSAGE_NOT_FOUND', $currentMessage->id, $TargetMessageID);
				return false;
			}

			if ($targetMessage->thread == $currentMessage->thread) {
				// Recursive self moves not supported
				$this->_errormsg = JText::printf('COM_KUNENA_MODERATION_ERROR_SAME_TARGET_THREAD', $currentMessage->id, $currentMessage->thread);
				return false;
			}

			// If $TargetMessageID has been specified and is valid,
			// overwrite $TargetCatID with the category ID of the target message
			$TargetCatID = $targetMessage->catid;
		} else {
			if ($TargetCatID == $currentMessage->catid) {
				$this->_errormsg = JText::printf('COM_KUNENA_MODERATION_ERROR_SAME_TARGET_CATEGORY', $currentMessage->id, $TargetCatID);
				return false;
			}
		}

		// Check that target category exists and is visible to our moderator
		if (! in_array ( $TargetCatID, $this->_allowed ) ) {
			//the user haven't moderator permissions in target category
			$this->_errormsg = JText::printf('COM_KUNENA_MODERATION_ERROR_TARGET_CATEGORY_NOT_FOUND', $currentMessage->id, $TargetCatID);
			return false;
		}

		//remove favorite and subscription if exists when do a merge thread
		if ( $TargetMessageID != '0' ) {
			$this->_db->setQuery ( "SELECT c.thread AS favorite, d.thread AS sub
									FROM #__fb_favorites AS c
									LEFT JOIN #__fb_subscriptions AS d ON d.thread=c.thread WHERE c.thread='{$currentMessage->thread}'" );
			$mesFavSub = $this->_db->loadObject ();
			check_dberror ( "Unable to load the favorite and subscription details." );

			if ( !empty($mesFavSub->favorite ) ) {
				CKunenaTools::removeFavorite ($currentMessage->thread, $mes->userid );
			}
			if ( !empty($mesFavSub->sub ) ) {
				CKunenaTools::removeSubscritpion ($currentMessage->thread,$mes->userid );
			}
		}

		// Assemble move logic based on $mode

		// Special case if the first message is moved in case 2 or 3
		if ($mode != KN_MOVE_MESSAGE && $currentMessage->parent == 0)
			$mode = KN_MOVE_THREAD;

		// partial logic to update target subject if specified
		$subjectupdatesql = $TargetSubject != '' ? ",`subject`='$TargetSubject'" : "";

		switch ($mode) {
			case KN_MOVE_MESSAGE : // Move Single message only
				if ($TargetMessageID == 0) {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `thread`='$MessageID', `parent`=0 $subjectupdatesql WHERE `id`='$MessageID';";
				} else {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `thread`='$TargetMessageID', `parent`='$TargetMessageID' $subjectupdatesql WHERE `id`='$MessageID';";
				}

				// If we are moving the first message of a thread only - make the second post the new thread header
				// FIXME: Does this make any sense? What happens in here is that first message maybe gets moved into another category/thread, while all replies
				// get moved, too: into new thread. So I do not see this useful option before we have threads table.
				if ( $currentMessage->parent == 0 ) {
					// We are about to pull the thread starter from the original thread.
					// Need to promote the second post of the original thread as the new starter.
					$sqlnewparent = "SELECT `catid`, `parent`, `thread`, `subject` FROM #__fb_messages WHERE `id`!={$MessageID} AND `thread`='{$currentMessage->thread}' ORDER BY `id` ASC";
					$this->_db->setQuery ( $sqlnewparent, 0, 1 );
					$newParent = $this->_db->loadObject ();
					check_dberror ( 'Unable to select new message for promote parent.' );

					if ( is_object( $newParent ) ) {
						// Rest of the thread will become new thread with different thread id
						$sqlparent = "UPDATE #__fb_messages SET `parent`=0, `thread`='{$newParent->id}' $subjectupdatesql WHERE `id`='{$newParent->id}'";
						$this->_db->setQuery ( $sqlparent );
						$this->_db->query ();
						check_dberror ( 'Unable to promote message parent.' );
						$sqlparent = "UPDATE #__fb_messages SET `thread`='{$newParent->id}' WHERE `thread`='{$currentMessage->thread}'";
						$this->_db->setQuery ( $sqlparent );
						$this->_db->query ();
						check_dberror ( 'Unable to put child to have the new parent.' );
					}
				}

				break;
			case KN_MOVE_THREAD : // Move entire Thread
				if ($TargetMessageID == 0) {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID' $subjectupdatesql WHERE `thread`='{$currentMessage->thread}';";
				} else {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `thread`='{$targetMessage->thread}' $subjectupdatesql WHERE `thread`='{$currentMessage->thread}';";
				}

				// Create ghost thread if requested
				if ($GhostThread == true) {
					$this->createGhostThread($MessageID,$currentMessage);
				}

				break;
			case KN_MOVE_NEWER : // Move message and all newer messages of thread
				if ($TargetMessageID == 0) {
					$sql1 = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `parent`=0 $subjectupdatesql WHERE `id`='$MessageID';";
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID' $subjectupdatesql WHERE `thread`='{$currentMessage->thread}' AND `id`>'$MessageID';";
				} else {
					$sql1 = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `parent`='$TargetMessageID' $subjectupdatesql WHERE `id`='$MessageID';";
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `thread`='$TargetMessageID' $subjectupdatesql WHERE `thread`='{$currentMessage->thread}' AND `id`>'$MessageID';";
				}

				// Execute move
				$this->_db->setQuery ( $sql1 );
				$this->_db->query ();
				check_dberror ( 'Unable to perform move.' );

				break;
			case KN_MOVE_REPLIES : // Move message and all replies and quotes - 1 level deep for now
				if ($TargetMessageID == 0) {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `parent`='0', $subjectupdatesql WHERE id`='$MessageID';";
					$sql .= "UPDATE #__fb_messages SET `catid`='$TargetCatID', $subjectupdatesql WHERE `thread`='$currentMessage->thread' AND `id`>'$MessageID' AND `parent`='$MessageID';";
				} else {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `parent`='$TargetMessageID', $subjectupdatesql WHERE `id`='$MessageID';";
					$sql .= "UPDATE #__fb_messages SET `catid`='$TargetCatID', `thread`='$TargetMessageID', $subjectupdatesql WHERE `thread`='{$currentMessage->thread}' AND `id`>'$MessageID' AND `parent`='$MessageID';";
				}

				break;
			default :
				// Unsupported mode - Error!
				$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_UNSUPPORTED_MODE');

				return false;
		}

		// Execute move
		$this->_db->setQuery ( $sql );
		$this->_db->query ();
		check_dberror ( 'Unable to perform move.' );

		// When done log the action
		$this->_Log ( 'Move', $MessageID, $TargetCatID, $TargetSubject, $TargetMessageID, $mode );

		// Last but not least update forum stats
		CKunenaTools::reCountBoards ();

		return true;
	}

	protected function _Delete($MessageID, $DeleteAttachments = false, $mode = KN_DEL_MESSAGE) {
		// Private delete function
		// $mode
		// KN_DEL_MESSAGE ... delete current message only
		// KN_DEL_THREAD  ... delete entire thread
		// KN_DEL_ATTACH  ... delete Attachments of message

		// Reset error message
		$this->_ResetErrorMessage ();

		// Sanitize parameters!
		$MessageID = intval ( $MessageID );
		$mode = intval ( $mode );
		// no need to check $DeleteAttachments as we only test for true

		// Always check security clearance before taking action!
		// Only moderators can delete messages by using this function
		if ( !CKunenaTools::isModerator($this->_my->id) ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR');
			return false;
		}

		$this->_db->setQuery ( "SELECT `id`, `userid`, `catid`, `parent`, `thread`, `subject`, `time` AS timestamp FROM #__fb_messages WHERE `id`='$MessageID'" );
		$currentMessage = $this->_db->loadObject ();
		check_dberror ( "Unable to load message." );

		// Check that message to be moved actually exists
		if ( !is_object($currentMessage) ) {
			$this->_errormsg = JText::sprintf('COM_KUNENA_MODERATION_ERROR_MESSAGE_NOT_FOUND', $MessageID);
			return false;
		}

		// Check that user has moderator permissions in the category
		if ( !CKunenaTools::isModerator($this->_my->id, $currentMessage->catid) ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR_IN_CATEGORY', $currentMessage->id, $currentMessage->catid);
			return false;
		}

		// Assemble delete logic based on $mode
		switch ($mode) {
			case KN_DEL_MESSAGE : //Delete only the actual message
				$sql = "UPDATE #__fb_messages SET `hold`=2 WHERE `id`='$MessageID';";
				break;
			case KN_DEL_THREAD : //Delete a complete thread
				$sql = "UPDATE #__fb_messages SET `hold`=2 WHERE `thread`='{$currentMessage->thread}';";
				break;
			case KN_DEL_ATTACH : //Delete only the attachments
				jimport('joomla.filesystem.file');
				$this->_db->setQuery ( "SELECT `userid`,`filename` FROM #__kunena_attachments WHERE `mesid`='$MessageID';" );
				$fileList = $this->_db->loadObjectList ();
				check_dberror ( "Unable to load attachments." );
				$sql = "DELETE FROM #__kunena_attachments WHERE `mesid`='$MessageID';";
				// FIXME: need to delete attachment from the old location, too..
				// TODO: what to do with files that should not be deleted (attachments from other components, multiple occurances of the same file)?
				$filetoDelete = JPATH_ROOT.'/media/kunena/attachments/'.$fileList[0]->userid.'/'.$fileList[0]->filename;
				if (JFile::exists($filetoDelete)) {
					JFile::delete($filetoDelete);
				}
				break;
			default :
				// Unsupported mode - Error!
				$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_UNSUPPORTED_MODE');
				return false;
		}

		// Execute delete
		$this->_db->setQuery ( $sql );
		$this->_db->query ();
		check_dberror ( 'Unable to perform delete.' );

		// Remember to delete ghost post
		// FIXME: replies may have ghosts, too. What to do with them?
		// FIXME: this query does not match if message gets moved again to another category
		$this->_db->setQuery ( "SELECT m.id FROM #__fb_messages AS m INNER JOIN #__fb_messages_text AS t ON m.`id`=t.`mesid`
			WHERE `moved`=1 AND `message`='catid={$currentMessage->catid}&id={$currentMessage->id}';" );
		$ghostMessageID = $this->_db->loadResult ();
		check_dberror ( "Unable to load ghost message." );

		if ( !empty($ghostMessageID) ) {
			$this->_db->setQuery ( "UPDATE #__fb_messages SET `hold`=2 WHERE `id`='$ghostMessageID' AND `moved`=1;" );
			$this->_db->query ();
			check_dberror ( "Unable to perform delete ghost message." );
		}

		// Check result to see if we need to abord and set error message


		// When done log the action
		$this->_Log ( 'Delete', $MessageID, 0, '', 0, $mode );

		// Last but not least update forum stats
		CKunenaTools::reCountBoards();

		return true;
	}

	protected function _Log($Task, $MessageID = 0, $TargetCatID = 0, $TargetSubject = '', $TargetMessageID = 0, $mode = 0) {
		// Implement logging utilizing CKunenaLogger class
	}

	// Public interface


	public function moveThread($ThreadID, $TargetCatID, $GhostThread = false) {
		return $this->_Move ( $ThreadID, $TargetCatID, '', 0, KN_MOVE_THREAD, $GhostThread );
	}

	public function moveMessage($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0) {
		return $this->_Move ( $ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, KN_MOVE_MESSAGE );
	}

	public function moveMessageAndNewer($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0) {
		return $this->_Move ( $ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, KN_MOVE_NEWER );
	}

	public function moveMessageAndReplies($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0) {
		return $this->_Move ( $ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, KN_MOVE_REPLIES );
	}

	public function deleteThread($ThreadID, $DeleteAttachments = false) {
		return $this->_Delete ( $ThreadID, $DeleteAttachments, KN_DEL_THREAD );
	}

	public function deleteMessage($MessageID, $DeleteAttachments = false) {
		return $this->_Delete ( $MessageID, $DeleteAttachments, KN_DEL_MESSAGE );
	}

	public function deleteAttachments($MessageID) {
		return $this->_Delete ( $MessageID, true, KN_DEL_ATTACH );
	}

	public function blockUserAccount($UserID, $block) {
		// Sanitize parameters!
		$UserID = intval ( $UserID );
		$block = intval ( $block );

		if ( !CKunenaTools::isModerator($this->_my->id) ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR');
			return false;
		}
		if ( $UserID == $this->_my->id ) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USER_BLOCK_YOURSELF' );
			return false;
		}
		if (!$UserID) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USER_BLOCK_ANONYMOUS' );
			return false;
		}
		$user = JUser::getInstance($UserID);
		if (!$user->id) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USER_BLOCK_NO_USER', $UserID );
			return false;
		}
		// Nobody can block admins
		if ( CKunenaTools::isAdmin($UserID) ) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USER_BLOCK_ADMIN', $user->username );
			return false;
		}
		// Only admins can block moderators
		if ( !CKunenaTools::isAdmin($this->_my->id) && CKunenaTools::isModerator($UserID) ) {
			$this->_errormsg = JText::_( 'COM_KUNENA_MODERATION_ERROR_USER_BLOCK_MODERATOR', $user->username );
			return false;
		}

		$user->block = $block;
		$user->save();

		// Logout user
		$this->logoutUser($UserID);
		return true;
	}

	public function deleteUserAccount($UserID) {
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
		$this->_db->setQuery ( "DELETE FROM #__fb_users WHERE `userid`='$UserID';" );
		$this->_db->query ();
		check_dberror ( "Unable to delete user from kunena." );

		return true;
	}

	public function logoutUser($UserID) {
		// Sanitize parameters!
		$UserID = intval ( $UserID );

		if ( !CKunenaTools::isModerator($this->_my->id) ) {
			$this->_errormsg = JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR');
			return false;
		}

		$kunena_app = & JFactory::getApplication ();
		$options = array();
		$options['clientid'][] = 0; //site
		$kunena_app->logout((int)$UserID,$options);

		return true;
	}

	// If a function failed - a detailed error message can be requested
	public function getErrorMessage() {
		return $this->_errormsg;
	}

	protected function _createGhostThread($MessageID,$currentMessage) {
		// Post time in ghost message is the same as in the last message of the thread
		$this->_db->setQuery ( "SELECT time AS timestamp FROM #__fb_messages WHERE `thread`='$MessageID' ORDER BY id DESC", 0, 1 );
		$lastTimestamp = $this->_db->loadResult ();
		check_dberror ( "Unable to load last timestamp." );
		if ($lastTimestamp == "") {
			$lastTimestamp = $currentMessage->timestamp;
		}

		// TODO: what do we do with ghost message title? JText::_('COM_KUNENA_MOVED_TOPIC') was used before
		// @Oliver: I'd like to get rid of it and add it while rendering..
		$myname = $this->_config->username ? $this->_my->username : $this->_my->name;

		$this->_db->setQuery ( "INSERT INTO #__fb_messages (`parent`, `subject`, `time`, `catid`, `moved`, `userid`, `name`) VALUES ('0','{$currentMessage->subject}','$lastTimestamp','{$currentMessage->catid}','1', '{$this->_my->id}', '" . trim ( addslashes ( $myname ) ) . "')" );
		$this->_db->query ();
		check_dberror ( 'Unable to insert ghost message.' );

		//determine the new location for link composition
		$newId = $this->_db->insertid ();

		// and update the thread id on the 'moved' post for the right ordering when viewing the forum..
		$this->_db->setQuery ( "UPDATE #__fb_messages SET `thread`='$newId' WHERE `id`='$newId'" );
		$this->_db->query ();
		check_dberror ( 'Unable to update thread id of ghost thread.' );

		// TODO: we need to fix all old ghost messages and change behaviour of them
		$newURL = "id=" . $currentMessage->id;
		$this->_db->setQuery ( "INSERT INTO #__fb_messages_text (`mesid`, `message`) VALUES ('$newId', '$newURL')" );
		$this->_db->query ();
		check_dberror ( 'Unable to insert ghost message.' );
	}

	public function createGhostThread($MessageID,$currentMessage) {
		return $this->_createGhostThread($MessageID,$currentMessage);
	}

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
