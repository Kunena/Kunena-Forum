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
defined( '_JEXEC' ) or die('Restricted access');

require_once (KUNENA_PATH_LIB .DS. "kunena.config.class.php");

class CKunenaSession extends JTable
{
	var $userid = 0;
	var $allowed = 'na';
	var $lasttime = 0;
	var $readtopics = '';
	var $currvisit = 0;
	protected $_exists = false;
	protected $_sessiontimeout = false;
	private static $_instance;

	function __construct(&$kunena_db)
	{
		$fbConfig =& CKunenaConfig::getInstance();
		parent::__construct('#__fb_sessions', 'userid', $kunena_db);
		$this->lasttime = time() + $fbConfig->board_ofset - KUNENA_SECONDS_IN_YEAR;
		$this->currvisit = time() + $fbConfig->board_ofset;
	}

	function &getInstance( $updateSessionInfo=false )
	{
		if (!self::$_instance) {
			$kunena_my = &JFactory::getUser();
			$kunena_db = &JFactory::getDBO();
			self::$_instance =& new CKunenaSession($kunena_db);
			if ($kunena_my->id) self::$_instance->load($kunena_my->id);
			if ($updateSessionInfo) self::$_instance->updateSessionInfo();
		}
		return self::$_instance;
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
		$fbConfig =& CKunenaConfig::getInstance();

		// Finally update current visit timestamp before saving
		$this->currvisit = time() + $fbConfig->board_ofset * KUNENA_SECONDS_IN_HOUR;

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
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
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
		$fbConfig =& CKunenaConfig::getInstance();
		
		$this->lasttime = time() + $fbConfig->board_ofset * KUNENA_SECONDS_IN_HOUR;
		$this->readtopics = '';
	}

	function updateSessionInfo()
	{
		$fbConfig =& CKunenaConfig::getInstance();

		// perform session timeout check
		$this->_sessiontimeout = ($this->currvisit + $fbConfig->fbsessiontimeout) < time() + $fbConfig->board_ofset * KUNENA_SECONDS_IN_HOUR;

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
			$allow_forums = CKunenaTools::getAllowedForums($my_id, $aro_group->id, $acl);

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
