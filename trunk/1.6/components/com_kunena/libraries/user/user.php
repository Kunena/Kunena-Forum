<?php
/**
* @version $Id: $
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

kimport('application.config');

class KUser extends JTable
{
	var $userid = null;
	var $view = null;
	var $signature = null;
	var $moderator = null;
	var $ordering = null;
	var $posts = null;
	var $avatar = null;
	var $karma = null;
	var $karma_time = null;
	var $group_id = null;
	var $uhits = null;
	var $personalText = null;
	var $gender = null;
	var $birthdate = null;
	var $location = null;
	var $ICQ = null;
	var $AIM = null;
	var $YIM = null;
	var $MSN = null;
	var $SKYPE = null;
	var $GTALK = null;
	var $websitename = null;
	var $websiteurl = null;
	var $rank = null;
	var $hideEmail = null;
	var $showOnline = null;
   	var $allowed_categories = 'na';
	var $read_topics = '';
   	var $last_visit_time = 0;
	var $curr_visit_time = 0;

	protected $_exists = false;
	protected $_sessiontimeout = false;
	private static $_instance;

	function __construct($db)
	{
		$config =& KConfig::getInstance();
		parent::__construct('#__kunena_users', 'userid', $db);
		$this->last_visit_time = time() + $config->board_ofset - KUNENA_SECONDS_IN_YEAR;
		$this->curr_visit_time = time() + $config->board_ofset;
	}

	function &getInstance( $updateSessionInfo=false )
	{
		if (!self::$_instance) {
			$kunena_my = &JFactory::getUser();
			$kunena_db = &JFactory::getDBO();
			self::$_instance =& new KUser($kunena_db);
			if ($kunena_my->id) self::$_instance->load($kunena_my->id);
			if ($updateSessionInfo) {
			    self::$_instance->_updateSessionInfo();
			    self::$_instance->_updateAllowedForums();
			    self::$_instance->store();
			}
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
		$config =& KConfig::getInstance();

		// Finally update current visit timestamp before saving
		$this->currvisit = time() + $config->board_ofset * KUNENA_SECONDS_IN_HOUR;

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
		$config =& KConfig::getInstance();

		$this->last_visit_time = time() + $config->board_ofset * KUNENA_SECONDS_IN_HOUR;
		$this->read_topics = '';

		$this->store();
	}

	protected function _updateSessionInfo()
	{
		$config =& KConfig::getInstance();

		// perform session timeout check
		$this->_sessiontimeout = ($this->curr_visit_time + $config->kunenasessiontimeout) < time() + $config->board_ofset * KUNENA_SECONDS_IN_HOUR;

		// If this is a new session, reset the lasttime colum with the timestamp
		// of the last saved currvisit - only after that can we reset currvisit to now before the store
		if ($this->isNewSession())
		{
			$this->last_visit_time = $this->currvisit;
			$this->read_topics = '';
		}
	}

	protected function _updateAllowedForums()
	{
		// check to see if we need to refresh the allowed forums cache
		// get all accessaible forums if needed (eg on forum modification, new session)
		if (!$this->allowed_categories or $this->allowed_categories == 'na' or $this->isNewSession()) {
			$allow_categories = $this->getAllowedForums();

			if (!$allow_categories)
			{
				$allow_categories = '0';
			}

			$this->allowed_categories = $allow_categories;
		}
	}

	private function _has_rights(&$kunena_acl, $gid, $access, $recurse)
	{
		if ($gid == $access) return 1;
		if ($recurse) {
			$childs = $kunena_acl->get_group_children($access, 'ARO', 'RECURSE');
			return (is_array($childs) and in_array($gid, $childs));
		}
		return 0;
	}

	function getAllowedForums()
	{
		$catlist = '';

		$query = new KQuery();

		$query->select('c.id, c.pub_access, c.pub_recurse, c.admin_access, c.admin_recurse, c.moderated');
		$query->select('(m.userid IS NOT NULL) AS ismod');
		$query->from('#__kunena_categories AS c');
		$query->leftJoin('#__kunena_moderation AS m ON c.id=m.catid AND m.userid='.$this->userid);
		$query->where('c.published=1');

		$this->_db->setQuery($query->toString());
		$rows = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if ($rows)
		{
			$acl = &JFactory::getACL();

			if ($this->userid != 0)
			{
			    $aro_group = $acl->getAroGroup($this->userid);
			}
			else
			{
			    $aro_group = new StdClass();
			    $aro_group->id = 0;
			}

		    foreach($rows as $row)
		    {
					if (($row->moderated and $row->ismod) or
						($row->pub_access == 0) or
						($row->pub_access == -1 and $uid > 0) or
						($row->pub_access > 0 and _has_rights($acl, $gid, $row->pub_access, $row->pub_recurse)) or
						($row->admin_access > 0 and _has_rights($acl, $gid, $row->admin_access, $row->admin_recurse))
					) $catlist .= (($catlist == '')?'':',').$row->id;
			}
		}

		return $catlist;
	}
}