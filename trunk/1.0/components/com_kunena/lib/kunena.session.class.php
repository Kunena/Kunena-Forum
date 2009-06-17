<?php
/**
* @version $Id: kunena.search.class.php 661 2009-05-01 08:28:21Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
**/
// Dont allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $database;

class CKunenaSession extends mosDBTable
{
	var $userid = 0;
	var $allowed = 'na';
	var $lasttime = 0;
	var $readtopics = '';
	var $currvisit = 0;
	var $_exists = false;
	var $_sessiontimeout = false;

	function CKunenaSession($database)
	{
		$this->mosDBTable('#__fb_sessions', 'userid', $database);
		$this->lasttime = time() + KUNENA_OFFSET_BOARD - KUNENA_SECONDS_IN_YEAR;
		$this->currvisit = time() + KUNENA_OFFSET_BOARD;
	}

	function &getInstance( $updateSessionInfo=false )
	{
		global $database, $my;
		static $instance;
		if (!$instance) {
			$instance = new CKunenaSession($database);
			$instance->load($my->id);
			if ($updateSessionInfo) $instance->updateSessionInfo();
		}
		return $instance;
	}

	function load( $oid=null )
	{
		$ret = parent::load($oid);
		if ($ret === true) $this->_exists = true;
		$this->userid = (int)$oid;

		return $ret;
	}

	function store( $updateNulls=false )
	{
		// Finally update current visit timestamp before saving
		$this->currvisit = time() + KUNENA_OFFSET_BOARD;

		$k = $this->_tbl_key;

		if( $this->$k && $this->_exists === true )
		{
			$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		}
		else
		{
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}
		if( !$ret )
		{
			if (CKunenaTools::isJoomla15())
				$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			else
				$this->_error = strtolower(get_class($this))."::store failed <br />" . $this->_db->getErrorMsg();
			return false;
		}
		else
		{
			return true;
		}
	}

	function save( $obj )
	{
		if(CKunenaTools::isJoomla15())
		{
			$ret = parent::save($obj);
		}
		else
		{
			$ret = $obj->store();
		}
		return $ret;
	}

	function isNewUser()
	{
		return !$this->_exists;
	}

	function isNewSession()
	{
		return $this->_sessiontimeout;
	}

	function markAllCategoriesRead()
	{
		$this->lasttime = time() + KUNENA_OFFSET_BOARD;
		$this->readtopics = '';
	}

	function updateSessionInfo()
	{
		$fbConfig =& CKunenaConfig::getInstance();

		// perform session timeout check
		$this->_sessiontimeout = ($this->currvisit + $fbConfig->fbsessiontimeout) < time() + KUNENA_OFFSET_BOARD;

		// If this is a new session, reset the lasttime colum with the timestamp
		// of the last saved currvisit - only after that can we reset currvisit to now before the store
		if ($this->isNewSession())
		{
			$this->lasttime = $this->currvisit;
			$this->readtopics = '';
		}
	}

	function updateAllowedForums($my_id, $aro_group, $acl)
	{
		// check to see if we need to refresh the allowed forums cache
		// get all accessaible forums if needed (eg on forum modification, new session)
		if (!$this->allowed or $this->allowed == 'na' or $this->isNewSession()) {
			$allow_forums = CKunenaTools::getAllowedForums($my_id, $aro_group->group_id, $acl);

			if (!$allow_forums)
			{
				$allow_forums = '0';
			}

			if ($allow_forums != $this->allowed)
			{
				$this->allowed = $allow_forums;
			}
		}
	}
}

?>
