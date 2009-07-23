<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// Defines for moves
define (KN_MOVE_MESSAGE, 0);
define (KN_MOVE_THREAD, 1);
define (KN_MOVE_NEWER, 2);
define (KN_MOVE_REPLIES, 3);

//Defines for deletes
define (KN_DEL_MESSAGE, 0);
define (KN_DEL_THREAD, 1);
define (KN_DEL_ATTACH, 2);

class CKunenaModeration
{
	// Private data and functions
	var $_db = '';
	var $_errormsg = '';

	function CKunenaModeration($db)
	{
		$this->_db = $db;
		$this->_ResetErrorMessage();
	}

	function &getInstance()
	{
		global $kunena_db;
		static $instance;
		if (!$instance) {
			$instance = new CKunenaModeration($kunena_db);
		}
		return $instance;
	}

	function _ResetErrorMessage()
	{
		$this->_errormsg = '';
	}

	function _Move($MessageID, $TargetCatID, $TargetSubject = '', $TargetMessageID = 0, $mode = KN_MOVE_MESSAGE, $GhostThread = false)
	{
		// Private move function
		// $mode
		// KN_MOVE_MESSAGE ... move current message only
		// KN_MOVE_THREAD  ... move entire thread
		// KN_MOVE_NEWER   ... move current message and all newer in current thread
		// KN_MOVE_REPLIES ... move current message and replies and quotes - 1 level deep
		//
		// if $TargetMessagID is a valid message ID, the messages will be appended to that thread

		// Reset error message
		$this->_ResetErrorMessage();

		// Sanitize parameters!
		$MessageID = intval($MessageID);
		$TargetCatID = intval($TargetCatID);
		$TargetSubject = addslashes($TargetSubject);
		$TargetMessageID = intval($TargetMessageID);
		$mode = intval($mode);
		// no need to check $GhostThread as we only test for true

		// Always check security clearance before taking action!

		// TODO: Add security permissions check
		// Assumption: only moderators can move messages
		// - Check that user has moderator permissions for source category (= $currentMessage->catid)
		// - Check that destination category exists and is visible to our moderator
		// - Error message if one of those fails (log entry too?)

		// Test parameters to see if they are valid selecions or abord

		// Check if message to move exists (also covers thread test)
		$this->_db->setQuery("SELECT `id`, `catid`, `parent`, `thread`, `subject`, `time` AS timestamp FROM #__kunena_messages WHERE `id`='$MessageID'");
		$currentMessage = $this->db->loadObjectList();
			check_dberror("Unable to load message.");

		if (empty($currentMessage->id))
		{
			// Message not found. Cannot proceed with move
			$this->_errormsg = 'Message to move not found.';
			return false;
		}

		if ($TargetCatID == 0 AND $TargetMessageID == 0)
		{
			// Nothing to move. No new category, no target message to append
			$this->_errormsg = 'No move targets specified.';
			return false;
		}

		if ($TargetCatID != 0)
		{
			$this->_db->setQuery("SELECT `id`, `name` FROM #__kunena_categories WHERE `id`='$TargetCatID'");
			$targetCategory = $this->db->loadObjectList();
				check_dberror("Unable to load message.");

			if (empty($targetCategory->id))
			{
				// Category not found. Cannot proceed with move
				$this->_errormsg = 'Target category not found.';
				return false;
			}

			if ($TargetCatID = $currentMessage->catid AND $TargetMessageID == 0)
			{
				// Category identical and no append. Nothing to do here
				return true;
			}
		}

		if ($TargetMessageID != 0)
		{
			$this->_db->setQuery("SELECT `id`, `catid`, `parent`, `thread`, `subject`, `time` AS timestamp FROM #__kunena_messages WHERE `id`='$TargetMessageID'");
			$targetMessage = $this->db->loadObjectList();
				check_dberror("Unable to load message.");

			if (empty($targetMessage->id))
			{
				// Target message not found. Cannot proceed with move
				$this->_errormsg = 'Target message for append not found.';
				return false;
			}

			if($targetMessage->thread == $currentMessage->thread)
			{
				// Recursive self moves not supported
				$this->_errormsg = 'Target thread identical to source threat. Recursive self moved not supported.';
				return false;
			}

			// If $TargetMessageID has been specified and is valid,
			// overwrite $TargetCatID with the category ID of the target message
			$TargetCatID = $targetMessage->catid;
		}

		// Assemble move logic based on $mode

		// Special case if the first message is moved in case 2 or 3
		if ($mode != KN_MOVE_MESSAGE && $currentMessage->parent == 0) $mode = KN_MOVE_THREAD;

		// partial logic to update target subject if specified
		$subjectupdatesql = $TargetSubject !='' ? "`subject`='$TargetSubject'" : "";

		switch ($mode)
		{
			case KN_MOVE_MESSAGE: // Move Single message only
				if ($TargetMessageID==0)
				{
					$sql = "UPDATE #__kunena_messages SET `catid`='$TargetCatID' `thread`='$MessageID' `parent`=0 $subjectupdatesql WHERE `id`='$MessageID';";
				}
				else
				{
					$sql = "UPDATE #__kunena_messages SET `catid`='$TargetCatID' `thread`='$TargetMessageID' `parent`='$TargetMessageID' $subjectupdatesql WHERE `id`='$MessageID';";
				}

				// TODO: If we are moving the first message of a thread only - make the second post the new thread header
				if ($MessageID == $currentMessage->thread)
				{
					// We are about to pull the thread starter from the original thread.
					// Need to promote the second post of the original thread as the new starter.

					$sql .= "";
				}

				break;
			case KN_MOVE_THREAD: // Move entire Thread
				if ($TargetMessageID==0)
				{
					$sql = "UPDATE #__kunena_messages SET `catid`='$TargetCatID' $subjectupdatesql WHERE `thread`='$currentMessage->thread';";
				}
				else
				{
					$sql = "UPDATE #__kunena_messages SET `catid`='$TargetCatID' `thread`='$targetMessage->thread' $subjectupdatesql WHERE `thread`='$currentMessage->thread';";
				}

				// Create ghost thread if requested
				// TODO: move this to a function
				if ($GhostThread==true)
				{
                    // Post time in ghost message is the same as in the last message of the thread
					$database->setQuery("SELECT MAX(time) AS timestamp FROM #__kunena_messages WHERE `thread`='$id'");
					$lastTimestamp = $database->loadResult();
						check_dberror("Unable to load last timestamp.");
					if ($lastTimestamp == "") {
						$lastTimestamp = $currentMessage->timestamp;
					}

					// TODO: need to fetch correct user id for new ghost thread - current moderator who executed the move
					// @Oliver: we already have it. It's current user: $my->id
					// TODO: obey configuration setting username vs realname
                    // TODO: what do we do with ghost message title? _MOVED_TOPIC was used before
                    // @Oliver: I'd like to get rid of it and add it while rendering..
					$this->_db->setQuery("INSERT INTO #__kunena_messages (`parent`, `subject`, `time`, `catid`, `moved`, `userid`, `name`) VALUES ('0','$currentMessage->subject','$lastTimestamp','$currentMessage->catid','1', '$my->id', '".trim(addslashes($my_name))."')");
                    $this->_db->query();
                    	check_dberror('Unable to insert ghost message.');

                    //determine the new location for link composition
                    $newId = $this->_db->insertid();

					// and update the thread id on the 'moved' post for the right ordering when viewing the forum..
					$this->_db->setQuery("UPDATE #__kunena_messages SET `thread`='$newId' WHERE `id`='$newId'");
					$this->_db->query();
						check_dberror('Unable to update thread id of ghost thread.');

					// TODO: we need to fix all old ghost messages and change behaviour of them
					$newURL = "id=" . $currentMessage->id;
					$this->_db->setQuery("INSERT INTO #__kunena_messages_text (`mesid`, `message`) VALUES ('$newId', '$newURL')");
					$this->_db->query();
						check_dberror('Unable to insert ghost message.');
				}

				break;
			case KN_MOVE_NEWER: // Move message and all newer messages of thread
				if ($TargetMessageID==0)
				{
					$sql = "UPDATE #__kunena_messages SET `catid`='$TargetCatID' `parent`=0 $subjectupdatesql WHERE id`='$MessageID';";
					$sql .= "UPDATE #__kunena_messages SET `catid`='$TargetCatID' $subjectupdatesql WHERE `thread`='$currentMessage->thread' AND `id`>'$MessageID';";
				}
				else
				{
					$sql = "UPDATE #__kunena_messages SET `catid`='$TargetCatID' `parent`='$TargetMessageID' $subjectupdatesql WHERE id`='$MessageID';";
					$sql .= "UPDATE #__kunena_messages SET `catid`='$TargetCatID' `thread`='$TargetMessageID' $subjectupdatesql WHERE `thread`='$currentMessage->thread' AND `id`>'$MessageID';";
				}

				break;
			case KN_MOVE_REPLIES: // Move message and all replies and quotes - 1 level deep for now
				if ($TargetMessageID==0)
				{
					$sql = "UPDATE #__kunena_messages SET `catid`='$TargetCatID' `parent`=0 $subjectupdatesql WHERE id`='$MessageID';";
					$sql .= "UPDATE #__kunena_messages SET `catid`='$TargetCatID' $subjectupdatesql WHERE `thread`='$currentMessage->thread' AND `id`>'$MessageID' AND `parent`='$MessageID';";
				}
				else
				{
					$sql = "UPDATE #__kunena_messages SET `catid`='$TargetCatID' `parent`='$TargetMessageID' $subjectupdatesql WHERE id`='$MessageID';";
					$sql .= "UPDATE #__kunena_messages SET `catid`='$TargetCatID' `thread`='$TargetMessageID' $subjectupdatesql WHERE `thread`='$currentMessage->thread' AND `id`>'$MessageID' AND `parent`='$MessageID';";
				}

				break;
			default:
				// Unsupported mode - Error!
				$this->_errormsg = 'Move mode not supported. Logic not implemented.';

				return false;
		}

		// Execute move
		$this->_db->setQuery($sql);
		$this->_db->query();
			check_dberror('Unable to perform move.');

		// When done log the action
		$this->_Log('Move', $MessageID, $TargetCatID, $TargetSubject, $TargetMessageID, $mode);

		// Last but not least update forum stats
		CKunenaTools::reCountBoards();

		return true;
	}

	function _Delete($MessageID, $DeleteAttachments=false, $mode = KN_DEL_MESSAGE)
	{
		// Private delete function
		// $mode
		// KN_DEL_MESSAGE ... delete current message only
		// KN_DEL_THREAD  ... delete entire thread
		// KN_DEL_ATTACH  ... delete Attachements of message

		// Reset error message
		$this->_ResetErrorMessage();

		// TODO: Sanitize parameters!


		// Always check security clearance before taking action!
		// An author should be able to delete her/his own message, without deleting an
		// entire thread should there be any responses in that thread.

		// Test parameters to see if they are valid selecions or abord

		// Check config if we perform deletes or only moves into a trash category
		// When configured for trash check if current delete is within trash for real delete

		// Assemble delete logic based on $mode

		// Execute delete

		// Remember to delete ghost post, too (note: replies may have ghosts)

		// Check result to see if we need to abord and set error message

		// When done log the action
		$this->_Log('Delete', $MessageID, 0, '', 0, $mode);

		// TODO: Last but not least update forum stats

		return true;
	}

	function _Log($Task, $MessageID = 0, $TargetCatID = 0, $TargetSubject = '', $TargetMessageID = 0, $mode = 0)
	{
		// Implement logging utilizing CKunenaLogger class
	}


	// Public interface

	function moveThread($ThreadID, $TargetCatID, $GhostThread=false)
	{
		return $this->_Move($ThreadID, $TargetCatID, '', 0, KN_MOVE_THREAD, $GhostThread);
	}

	function moveMessage($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0)
	{
		return $this->_Move($ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, KN_MOVE_MESSAGE);
	}

	function moveMessageAndNewer($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0)
	{
		return $this->_Move($ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, KN_MOVE_NEWER);
	}

	function moveMessageAndReplies($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0)
	{
		return $this->_Move($ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, KN_MOVE_REPLIES);
	}

	function deleteThread($ThreadID, $DeleteAttachments = false)
	{
		return $this->_Delete($ThreadID, $DeleteAttachments, KN_DEL_THREAD);
	}

	function deleteMessage($MessageID, $DeleteAttachments = false)
	{
		return $this->_Delete($MessageID, $DeleteAttachments, KN_DEL_MESSAGE);
	}

	function deleteAttachments($MessageID)
	{
		return $this->_Delete($MessageID, true, KN_DEL_ATTACH);
	}

	function disableUserAccount($UserID)
	{
		// Future functionality
		$this->_errormsg = 'Future feature. Logic not implemented.';

		return false;
	}

	function enableUserAccount($UserID)
	{
		// Future functionality
		$this->_errormsg = 'Future feature. Logic not implemented.';

		return false;
	}

	// If a function failed - a detailed error message can be requested
	function getErrorMessage()
	{
		return $this->_errormsg;
	}
}
?>
