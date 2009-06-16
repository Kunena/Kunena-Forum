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
	var $_exists = false;
	private static $_instance;

	function __construct(&$kunena_db)
	{
		$fbConfig =& CKunenaConfig::getInstance();
		parent::__construct('#__fb_sessions', 'userid', $kunena_db);
		$this->lasttime = time() + $fbConfig->board_ofset - KUNENA_SECONDS_IN_YEAR;
		$this->currvisit = time() + $fbConfig->board_ofset;
	}

	function &getInstance()
	{
		if (!self::$_instance) {
			$kunena_my = &JFactory::getUser();
			$kunena_db = &JFactory::getDBO();
			self::$_instance =& new CKunenaSession($kunena_db);
			if ($kunena_my->id) self::$_instance->load($kunena_my->id);
		}
		return self::$_instance;
	}

	function load( $oid=null )
	{
		$ret = parent::load($oid);
		if ($ret === true) $this->_exists = true;
		return $ret;
	}

	function store( $updateNulls=false )
	{
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
}

?>
