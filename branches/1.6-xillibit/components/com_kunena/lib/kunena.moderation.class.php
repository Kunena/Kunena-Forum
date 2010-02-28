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

class CKunenaModeration {
	// Private data and functions
	var $_db = null;
	var $_errormsg = null;
	var $_config = null;
	var $_my = null;

	function CKunenaModeration($db, $config, $my) {
		$this->_db = $db;
		$this->_ResetErrorMessage ();
		$this->_config = $config;
		$this->_my = $my;
	}

	function &getInstance() {
		static $instance = NULL;
		if (! $instance) {
			$kunena_db = & JFactory::getDBO ();
			$kunena_config = & CKunenaConfig::getInstance ();
			$kunena_my = &JFactory::getUser ();

			$instance = new CKunenaModeration ( $kunena_db, $kunena_config, $kunena_my );
		}
		return $instance;
	}

	function _ResetErrorMessage() {
		$this->_errormsg = '';
	}

	function _Move($MessageID, $TargetCatID, $TargetSubject = '', $TargetMessageID = 0, $mode = KN_MOVE_MESSAGE, $GhostThread = false) {
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


		// Add security permissions check
		// Assumption: only moderators can move messages
		// - Check that user has moderator permissions for source category (= $currentMessage->catid)
		// - Check that destination category exists and is visible to our moderator
		// - Error message if one of those fails (log entry too?)

		//check permissions for source message and category related
		$query = 'SELECT a.catid AS mesCatid,b.catid AS modCatid, b.userid FROM #__fb_messages AS a INNER JOIN #__fb_moderation AS b ON a.catid=b.catid WHERE a.id='.$MessageID;
		$this->_db->setQuery ( $query );
		$SourceMes = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load source messages." );

		if ( !empty($SourceMes) ) {
			foreach ($SourceMes as $mes) {
				if ( $mes->mesCatid != $mes->modCatid && !CKunenaTools::isModerator($this->_my->id) ) {
					//the user haven't moderator permissions
					$this->_errormsg = JText::_('COM_KUNENA_POST_NOT_MODERATOR');
					return false;
				}
			}
		}

		//check that the destination category exists
		$query = 'SELECT catid, userid FROM #__fb_moderation WHERE userid='.$this->_my->id;
		$this->_db->setQuery ( $query );
		$destMes = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load source messages." );

		if ( !empty($destMes) ) {
			foreach ($destMes as $mes) {
				if ( $mes->catid != $TargetCatID ) {
					//the user haven't moderator permissions in target category
					$this->_errormsg = JText::_('COM_KUNENA_MOD_CATOGERY_DEST_NOT');
					return false;
				}
			}
		}

		// Test parameters to see if they are valid selecions or abord

		// Check if message to move exists (also covers thread test)
		$this->_db->setQuery ( "SELECT `id`, `userid`, `catid`, `parent`, `thread`, `subject`, `time` AS timestamp FROM #__fb_messages
								WHERE `id`='$MessageID'" );
		$currentMessage = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load message." );

		if (empty ( $currentMessage[0]->id )) {
			// Message not found. Cannot proceed with move
			$this->_errormsg = JText::_('COM_KUNENA_MOD_MES_MOVE_NOT_FOUND');
			return false;
		}

		if ($TargetCatID == 0 and $TargetMessageID == 0) {
			// Nothing to move. No new category, no target message to append
			$this->_errormsg = JText::_('COM_KUNENA_MOD_MES_MOVE_NOT_TARGETS');
			return false;
		}

		if ($TargetCatID != 0) {
			$this->_db->setQuery ( "SELECT `id`, `name` FROM #__fb_categories WHERE `id`='$TargetCatID'" );
			$targetCategory = $this->_db->loadObjectList ();
			check_dberror ( "Unable to load message." );

			if (empty ( $targetCategory[0]->id )) {
				// Category not found. Cannot proceed with move
				$this->_errormsg = JText::_('COM_KUNENA_MOD_CAT_TARGET_NOT_FOUND');
				return false;
			}

			if ($TargetCatID == $currentMessage[0]->catid and $TargetMessageID == 0) {
				// Category identical and no append. Nothing to do here
				return true;
			}
		}

		if ($TargetMessageID != 0) {
			$this->_db->setQuery ( "SELECT `id`, `catid`, `parent`, `thread`, `subject`, `time` AS timestamp FROM #__fb_messages WHERE `id`='$TargetMessageID'" );
			$targetMessage = $this->_db->loadObjectList ();
			check_dberror ( "Unable to load message." );

			if (empty ( $targetMessage[0]->id )) {
				// Target message not found. Cannot proceed with move
				$this->_errormsg = JText::_('COM_KUNENA_MOD_MES_TARGET_NOT_FOUND');
				return false;
			}

			if ($targetMessage[0]->thread == $currentMessage[0]->thread) {
				// Recursive self moves not supported
				$this->_errormsg = JText::_('COM_KUNENA_MOD_TARGET_THREAD_SOURCE_IDENTICAL');
				return false;
			}

			// If $TargetMessageID has been specified and is valid,
			// overwrite $TargetCatID with the category ID of the target message
			$TargetCatID = $targetMessage[0]->catid;
		}

		// Assemble move logic based on $mode

		// Special case if the first message is moved in case 2 or 3
		if ($mode != KN_MOVE_MESSAGE && $currentMessage[0]->parent == 0)
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
				if ( $currentMessage[0]->parent == '0') {
					// We are about to pull the thread starter from the original thread.
					// Need to promote the second post of the original thread as the new starter.
					$sqlnewparent = "SELECT id, userid, MIN(time) FROM #__fb_messages WHERE `thread`='{$currentMessage[0]->thread}' AND `parent`='{$currentMessage[0]->thread}';";
					$this->_db->setQuery ( $sqlnewparent );
					$newParent = $this->_db->loadObjectList ();
					check_dberror ( 'Unable to select new message for promote parent.' );

					if ( $newParent[0]->id != null ) {
						$sqlparent = "UPDATE #__fb_messages SET `parent`=0, `thread`='{$newParent[0]->id}' WHERE `id`='{$newParent[0]->id}';";
						$this->_db->setQuery ( $sqlparent );
						$this->_db->query ();
						check_dberror ( 'Unable to promote message parent.' );
						$sqlparent1 = "UPDATE #__fb_messages SET `thread`='{$newParent[0]->id}' WHERE `parent`='{$currentMessage[0]->thread}' AND `thread`='{$currentMessage[0]->thread}';";
						$this->_db->setQuery ( $sqlparent1 );
						$this->_db->query ();
						check_dberror ( 'Unable to put child to have the new parent.' );
					}
				}

				break;
			case KN_MOVE_THREAD : // Move entire Thread
				if ($TargetMessageID == 0) {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID' $subjectupdatesql WHERE `thread`='{$currentMessage[0]->thread}';";
				} else {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `thread`='{$targetMessage[0]->thread}' $subjectupdatesql WHERE `thread`='{$currentMessage[0]->thread}';";
				}

				// Create ghost thread if requested
				if ($GhostThread == true) {
					$this->createGhostThread($MessageID,$currentMessage,$this->_my);
				}

				break;
			case KN_MOVE_NEWER : // Move message and all newer messages of thread
				if ($TargetMessageID == 0) {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `parent`=0 $subjectupdatesql WHERE id`='$MessageID';";
					$sql .= "UPDATE #__fb_messages SET `catid`='$TargetCatID' $subjectupdatesql WHERE `thread`='{$currentMessage[0]->thread}' AND `id`>'$MessageID';";
				} else {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `parent`='$TargetMessageID' $subjectupdatesql WHERE id`='$MessageID';";
					$sql .= "UPDATE #__fb_messages SET `catid`='$TargetCatID', `thread`='$TargetMessageID' $subjectupdatesql WHERE `thread`='{$currentMessage[0]->thread}' AND `id`>'$MessageID';";
				}

				break;
			case KN_MOVE_REPLIES : // Move message and all replies and quotes - 1 level deep for now
				if ($TargetMessageID == 0) {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `parent`='0', $subjectupdatesql WHERE id`='$MessageID';";
					$sql .= "UPDATE #__fb_messages SET `catid`='$TargetCatID', $subjectupdatesql WHERE `thread`='$currentMessage->thread' AND `id`>'$MessageID' AND `parent`='$MessageID';";
				} else {
					$sql = "UPDATE #__fb_messages SET `catid`='$TargetCatID', `parent`='$TargetMessageID', $subjectupdatesql WHERE id`='$MessageID';";
					$sql .= "UPDATE #__fb_messages SET `catid`='$TargetCatID', `thread`='$TargetMessageID', $subjectupdatesql WHERE `thread`='{$currentMessage[0]->thread}' AND `id`>'$MessageID' AND `parent`='$MessageID';";
				}

				break;
			default :
				// Unsupported mode - Error!
				$this->_errormsg = JText::_('COM_KUNENA_MOD_UNSUPPORTED_MODE');

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

	function _Delete($MessageID, $DeleteAttachments = false, $mode = KN_DEL_MESSAGE) {
		// Private delete function
		// $mode
		// KN_DEL_MESSAGE ... delete current message only
		// KN_DEL_THREAD  ... delete entire thread
		// KN_DEL_ATTACH  ... delete Attachements of message


		// Reset error message
		$this->_ResetErrorMessage ();

		// Sanitize parameters!
		$MessageID = intval ( $MessageID );
		$mode = intval ( $mode );
		// no need to check $DeleteAttachments as we only test for true

		$this->_db->setQuery ( "SELECT `id`, `userid`, `catid`, `parent`, `thread`, `subject`, `time` AS timestamp FROM #__fb_messages WHERE `id`='$MessageID'" );
		$currentMessage = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load message." );

		// Always check security clearance before taking action!
		// An author should be able to delete her/his own message, without deleting an
		// entire thread should there be any responses in that thread.
		if ( !CKunenaTools::isModerator($this->_my->id) ) {
			$this->_db->setQuery ( "SELECT `id` FROM #__fb_messages WHERE `userid`={$this->_my->id} AND `thread`='{$currentMessage[0]->thread}'" );
			$allReplies = $this->_db->loadResultArray ();
			check_dberror ( "Unable to load message." );

			if ( count($allReplies) != '0' ) {
				$lastReply = array_pop($allReplies);
				if ($lastReply != $MessageID) {
					//author not allowed to delete his post
					$this->_errormsg = JText::_('COM_KUNENA_MOD_DEL_MES_AUTHOR_IMPOSSIBLE');
					return false;
				}
			}
		}

		// Test parameters to see if they are valid selecions or abord

		// Check if message to delete exists
		if (empty ( $currentMessage[0]->id )) {
			// Message not found. Cannot proceed with delete
			$this->_errormsg = JText::_('COM_KUNENA_MOD_DEL_MES_NOT_FOUND');
			return false;
		}

		// Assemble delete logic based on $mode
		switch ($mode) {
			case KN_DEL_MESSAGE : //Delete only the actual message
				$sql = "UPDATE #__fb_messages SET `hold`=2 WHERE `id`='$MessageID';";
			break;
			case KN_DEL_THREAD : //Delete a complete thread
				$sql = "UPDATE #__fb_messages SET `hold`=2 WHERE `thread`='{$currentMessage[0]->thread}';";
			break;
			case KN_DEL_ATTACH : //Delete only the attachments
				jimport('joomla.filesystem.file');
				$this->_db->setQuery ( "SELECT `userid`,`filename` FROM #__kunena_attachments WHERE `mesid`='$MessageID';" );
				$fileList = $this->_db->loadObjectList ();
				check_dberror ( "Unable to load attachments." );
				$sql = "DELETE FROM #__kunena_attachments WHERE `mesid`='$MessageID';";
				$filetoDelete = JPATH_ROOT.'/media/kunena/attachments/'.$fileList[0]->userid.'/'.$fileList[0]->filename;
				if (JFile::exists($filetoDelete)) {
					JFile::delete($filetoDelete);
				} else {
					$this->_errormsg = JText::_('COM_KUNENA_MOD_DEL_ATTACH_NOT_FOUND');
				}
			break;
			default :
				// Unsupported mode - Error!
				$this->_errormsg = 'Delete mode not supported. Logic not implemented.';

				return false;
		}

		// Execute delete
		$this->_db->setQuery ( $sql );
		$this->_db->query ();
		check_dberror ( 'Unable to perform delete.' );

		// Remember to delete ghost post, too (note: replies may have ghosts)
		$this->_db->setQuery ( "SELECT mesid FROM #__fb_messages_text WHERE message='catid={$currentMessage[0]->catid}&id={$currentMessage[0]->id}';" );
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

	function _Log($Task, $MessageID = 0, $TargetCatID = 0, $TargetSubject = '', $TargetMessageID = 0, $mode = 0) {
		// Implement logging utilizing CKunenaLogger class
	}

	// Public interface


	function moveThread($ThreadID, $TargetCatID, $GhostThread = false) {
		return $this->_Move ( $ThreadID, $TargetCatID, '', 0, KN_MOVE_THREAD, $GhostThread );
	}

	function moveMessage($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0) {
		return $this->_Move ( $ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, KN_MOVE_MESSAGE );
	}

	function moveMessageAndNewer($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0) {
		return $this->_Move ( $ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, KN_MOVE_NEWER );
	}

	function moveMessageAndReplies($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0) {
		return $this->_Move ( $ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, KN_MOVE_REPLIES );
	}

	function deleteThread($ThreadID, $DeleteAttachments = false) {
		return $this->_Delete ( $ThreadID, $DeleteAttachments, KN_DEL_THREAD );
	}

	function deleteMessage($MessageID, $DeleteAttachments = false) {
		return $this->_Delete ( $MessageID, $DeleteAttachments, KN_DEL_MESSAGE );
	}

	function deleteAttachments($MessageID) {
		return $this->_Delete ( $MessageID, true, KN_DEL_ATTACH );
	}

	function disableUserAccount($UserID) {
		// Future functionality
		$this->_errormsg = 'Future feature. Logic not implemented.';

		return false;
	}

	function enableUserAccount($UserID) {
		// Future functionality
		$this->_errormsg = 'Future feature. Logic not implemented.';

		return false;
	}

	function deleteUserAccount($UserID) {
		$acl =& JFactory::getACL();

		//check for super admin
		$objectID 	= $acl->get_object_id( 'users', $UserID, 'ARO' );
		$groups 	= $acl->get_object_groups( $objectID, 'ARO' );
		$this_group = strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );

		if ( $this_group == 'super administrator' ) {
				$this->_errormsg = JText::_( 'COM_KUNENA_USER_DELETE_ADMIN_NOT' );
				return false;
		} else if ( $UserID == $this->_my->id ) {
				$this->_errormsg = JText::_( 'COM_KUNENA_USER_DELETE_YOURSELF_NOT' );
				return false;
		} else if ( ( $this_group == 'administrator' ) && ( $currentUser->get( 'gid' ) == 24 ) ) {
				$this->_errormsg = JText::_( 'COM_KUNENA_USER_DELETE_WARM' );
				return false;
		} else {
			$TargetUser =& JUser::getInstance($UserID);
			$TargetUser->delete();
			$this->_db->setQuery ( "DELETE FROM #__fb_users WHERE `userid`='$UserID';" );
			$this->_db->query ();
			check_dberror ( "Unable to delete user." );
			return true;
		}
		return false;
	}

	function logoutUser($UserID) {
		$kunena_app = & JFactory::getApplication ();
		$options = array();
		$options['clientid'][] = 0; //site
		$kunena_app->logout((int)$UserID,$options);

		return true;
	}

	// If a function failed - a detailed error message can be requested
	function getErrorMessage() {
		return $this->_errormsg;
	}

	function _createGhostThread($MessageID,$currentMessage,$my) {
		// Post time in ghost message is the same as in the last message of the thread
		$this->_db->setQuery ( "SELECT MAX(time) AS timestamp FROM #__fb_messages WHERE `thread`='$MessageID'" );
		$lastTimestamp = $this->_db->loadResult ();
		check_dberror ( "Unable to load last timestamp." );
		if ($lastTimestamp == "") {
			$lastTimestamp = $currentMessage[0]->timestamp;
		}

		// TODO: need to fetch correct user id for new ghost thread - current moderator who executed the move
		// @Oliver: we already have it. It's current user: $my->id
		// TODO: what do we do with ghost message title? JText::_('COM_KUNENA_MOVED_TOPIC') was used before
		// @Oliver: I'd like to get rid of it and add it while rendering..
		$myname = $this->_config->username ? $my->username : $my->name;

		$this->_db->setQuery ( "INSERT INTO #__fb_messages (`parent`, `subject`, `time`, `catid`, `moved`, `userid`, `name`) VALUES ('0','{$currentMessage[0]->subject}','$lastTimestamp','{$currentMessage[0]->catid}','1', '$my->id', '" . trim ( addslashes ( $myname ) ) . "')" );
		$this->_db->query ();
		check_dberror ( 'Unable to insert ghost message.' );

		//determine the new location for link composition
		$newId = $this->_db->insertid ();

		// and update the thread id on the 'moved' post for the right ordering when viewing the forum..
		$this->_db->setQuery ( "UPDATE #__fb_messages SET `thread`='$newId' WHERE `id`='$newId'" );
		$this->_db->query ();
		check_dberror ( 'Unable to update thread id of ghost thread.' );

		// TODO: we need to fix all old ghost messages and change behaviour of them
		$newURL = "id=" . $currentMessage[0]->id;
		$this->_db->setQuery ( "INSERT INTO #__fb_messages_text (`mesid`, `message`) VALUES ('$newId', '$newURL')" );
		$this->_db->query ();
		check_dberror ( 'Unable to insert ghost message.' );
	}

	function createGhostThread($MessageID,$currentMessage,$my) {
		return $this->_createGhostThread($MessageID,$currentMessage,$my);
	}
}
?>
