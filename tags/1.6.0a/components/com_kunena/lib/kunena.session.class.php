<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once (KUNENA_PATH .DS. "class.kunena.php");
require_once (KUNENA_PATH_LIB .DS. "kunena.config.class.php");
require_once (KUNENA_PATH_LIB .DS. "kunena.timeformat.class.php");

class CKunenaSession extends JTable
{
	var $userid = 0;
	var $allowed = 'na';
	var $allowedcats = null;
	var $lasttime = 0;
	var $readtopics = '';
	var $currvisit = 0;
	protected $_exists = false;
	protected $_sessiontimeout = false;
	private static $_instance;

	function __construct($db)
	{
		$kconfig = CKunenaConfig::getInstance();
		parent::__construct('#__fb_sessions', 'userid', $db);
		// New user gets a month of unread messages
		$this->lasttime = CKunenaTimeformat::internalTime() - 3600*24*30;
		$this->currvisit = CKunenaTimeformat::internalTime();
	}

	function &getInstance( $update=false )
	{
		if (!self::$_instance) {
			$my = JFactory::getUser();
			$db = JFactory::getDBO();
			self::$_instance = new CKunenaSession($db);
			if ($my->id) self::$_instance->load($my->id);
			if ($update) self::$_instance->updateSessionInfo();
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
		// Finally update current visit timestamp before saving
		$this->currvisit = CKunenaTimeformat::internalTime();

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
		$kunena_config =& CKunenaConfig::getInstance();

		// perform session timeout check
		$this->_sessiontimeout = ($this->currvisit + $kunena_config->fbsessiontimeout < CKunenaTimeformat::internalTime());
		return $this->_sessiontimeout;
	}

	function markAllCategoriesRead()
	{
		$this->lasttime = CKunenaTimeformat::internalTime();
		$this->readtopics = '';
	}

	function updateSessionInfo()
	{
		// If this is a new session, reset the lasttime colum with the timestamp
		// of the last saved currvisit - only after that can we reset currvisit to now before the store
		if ($this->isNewSession())
		{
			$this->lasttime = $this->currvisit;
			$this->readtopics = '';
		}
		$this->updateAllowedForums();
	}

	function updateAllowedForums()
	{
		// check to see if we need to refresh the allowed forums cache
		// get all accessaible forums if needed (eg on forum modification, new session)
		if (!$this->allowed or $this->allowed == 'na' or $this->isNewSession()) {
			$allow_forums = CKunenaTools::getAllowedForums($this->userid);

			if (!$allow_forums)
			{
				$allow_forums = '0';
			}

			$this->allowed = $allow_forums;
		}
	}

	function canRead($catid) {
		if ($this->allowedcats === null) {
			$this->updateAllowedForums();
			$this->allowedcats = ($this->allowed) ? explode ( ',', $this->allowed ) : array ();
		}
		return in_array ( $catid, $this->allowedcats );
	}
}

?>
