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

class CKunenaModeration
{
	// Private data and functions
	var $_db = '';
	var $_errormsg = '';

	function construct($db)
	{
		$this->_db = $db;
		$this->_ResetErrorMessage();
	}

	function _ResetErrorMessage()
	{
		$this->_errormsg = '';
	}

	function _Move($MessageID, $TargetCatID, $TargetSubject = '', $TargetMessageID = 0, $mode = 0)
	{
		// Private move function
		// $mode
		// = 0 ... move current message only
		// = 1 ... move entire thread
		// = 2 ... move current message and all newer in current thread
		// = 3 ... move current message and all replies and quotes - recursively

		// Reset error message
		$this->_ResetErrorMessage();

		// Always check security clearance before taking action!
		// TODO: Add security check

		// Test parameters to see if they are valid selecions or abord
		unset($currentMessage);
		unset($targetCategory);
		unset($targetMessage);

		$this->db->setQuery("SELECT `id`, `catid`, `parent`, `thread`, `subject`, `time` AS timestamp FROM #__fb_messages WHERE `id`='$MessageID'");
		$currentMessage = $this->db->loadObjectList();
			check_dberror("Unable to load message.");
		// TODO: Check if message exists

		if ($TargetCatID != 0)
		{
			$this->db->setQuery("SELECT `id`, `name` FROM #__fb_categories WHERE `id`='$TargetCatID'");
			$targetCategory = $this->db->loadObjectList();
				check_dberror("Unable to load message.");
			// TODO: Check if category exists

			// TODO: Check if $TargetCatID = currentMessage.catid AND $TargetMessageID == 0
			// Nothing to do...
		}

		if ($TargetMessageID != 0)
		{
			$this->db->setQuery("SELECT `id`, `catid`, `parent`, `thread`, `subject`, `time` AS timestamp FROM #__fb_messages WHERE `id`='$TargetMessageID'");
			$targetMessage = $this->db->loadObjectList();
				check_dberror("Unable to load message.");
			// TODO: Check if message exists

			// TODO: Check if $MessageID == $TargetMessage ID
			// No recursive moves allowed
		}

		// Assemble move logic based on $mode
		// TODO: Implement logic
		switch ($mode)
		{
			case 0:


				break;
			case 1:


				break;
			case 2:


				break;
			case 3:


				break;
			default:


				break;
		}

		// Execute move

		// Check result to see if we need to abord and set error message

		// When done log the action
		$this->_Log('Move', $MessageID, $TargetCatID, $TargetSubject, $TargetMessageID, $mode);

		return true;
	}

	function _Delete($MessageID, $mode = 0)
	{
		// Private delete function
		// $mode
		// = 0 ... delete current message only
		// = 1 ... delete entire thread

		// Reset error message
		$this->_ResetErrorMessage();

		// Always check security clearance before taking action!
		// An author should be able to delete her/his own message, without deleting an
		// entire thread should there be any responses in that thread.

		// Test parameters to see if they are valid selecions or abord

		// Check config if we perform deletes or only moves into a trash category
		// When configured for trash check if current delete is within trash for real delete

		// Assemble delete logic based on $mode

		// Execute delete

		// Check result to see if we need to abord and set error message

		// When done log the action
		$this->_Log('Delete', $MessageID, 0, '', 0, $mode);

		return true;
	}

	function _Log($Task, $MessageID = 0, $TargetCatID = 0, $TargetSubject = '', $TargetMessageID = 0, $mode = 0)
	{
		// Implement logging utilizing CKunenaLogger class
	}


	// Public interface

	function moveThread($ThreadID, $TargetCatID)
	{
		return $this->_Move($ThreadID, $TargetCatID, '', 0, 1);
	}

	function moveMessage($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0)
	{
		return $this->_Move($ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, 0);
	}

	function moveMessageAndNewer($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0)
	{
		return $this->_Move($ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, 2);
	}

	function moveMessageAndReplies($ThreadID, $TargetCatID, $TargetSubject = '', $TargetThreadID = 0)
	{
		return $this->_Move($ThreadID, $TargetCatID, $TargetSubject, $TargetThreadID, 3);
	}

	function deleteThread($ThreadID)
	{
		return $this->_Delete($ThreadID, 1);
	}

	function deleteMessage($MessageID)
	{
		return $this->_Delete($MessageID, 0);
	}

	function disableUserAccount($UserID)
	{
		// Future functionality
		$this->_errormsg = 'Future feature. Logic not implemented';

		return false;
	}

	function enableUserAccount($UserID)
	{
		// Future functionality
		$this->_errormsg = 'Future feature. Logic not implemented';

		return false;
	}

	// If a function failed - a detailed error message can be requested
	function getErrorMessage()
	{
		return $this->_errormsg;
	}
}
?>
