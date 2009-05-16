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

	function construct($db)
	{
		$this->_db = $db;
	}

	function _Move($MessageID, $TargetCatID, $TargetTitle = '', $TargetMessageID = 0, $mode = 0)
	{
		// Private move function
		// $mode
		// = 0 ... move current message only
		// = 1 ... move entire thread
		// = 2 ... move current message and all newer in current thread
		// = 3 ... move current message and all replies and quotes - recursively

		// Always check security clearance before taking action!


		// When done log the action
		$this->_Log('Move', $MessageID, $TargetCatID, $TargetTitle, $TargetMessageID, $mode);

		return true;
	}

	function _Delete($MessageID, $mode = 0)
	{
		// Private delete function
		// $mode
		// = 0 ... delete current message only
		// = 1 ... delete entire thread

		// Always check security clearance before taking action!


		// When done log the action
		$this->_Log('Delete', $MessageID, 0, '', 0, $mode);

		return true;
	}

	function _Log($Task, $MessageID = 0, $TargetCatID = 0, $TargetTitle = '', $TargetMessageID = 0, $mode = 0)
	{
		// Implement logging utilizing CKunenaLogger class
	}


	// Public interface

	function MoveThread($ThreadID, $TargetCatID)
	{
		return $this->_Move($ThreadID, $TargetCatID, '', 0, 1);
	}

	function MoveMessage($ThreadID, $TargetCatID, $TargetTitle = '', $TargetThreadID = 0)
	{
		return $this->_Move($ThreadID, $TargetCatID, $TargetTitle, $TargetThreadID, 0);
	}

	function MoveMessageAndNewer($ThreadID, $TargetCatID, $TargetTitle = '', $TargetThreadID = 0)
	{
		return $this->_Move($ThreadID, $TargetCatID, $TargetTitle, $TargetThreadID, 2);
	}

	function MoveMessageAndReplies($ThreadID, $TargetCatID, $TargetTitle = '', $TargetThreadID = 0)
	{
		return $this->_Move($ThreadID, $TargetCatID, $TargetTitle, $TargetThreadID, 3);
	}

	function DeleteThread($ThreadID)
	{
		return $this->_Delete($ThreadID, 1);
	}

	function DeleteMessage($MessageID)
	{
		return $this->_Delete($MessageID, 0);
	}

	function DisableUserAccount($UserID)
	{
		// Future functionality
		return true;
	}

	function EnableUserAccount($UserID)
	{
		// Future functionality
		return true;
	}

}
?>
