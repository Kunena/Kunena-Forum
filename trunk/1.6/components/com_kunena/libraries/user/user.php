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
   	var $last_visit_time = null;
	var $curr_visit_time = null;

	protected $_exists = false;
	protected $_sessiontimeout = false;
	private static $_instances = array();

	function __construct()
	{
		parent::__construct('#__kunena_users', 'userid', JFactory::getDBO());
	}

	function &getInstance( $userid=null )
	{
		if ($userid === null) {
			$my = &JFactory::getUser();
			$userid = $my->id;	
		}
		
		if (isset(self::$_instances[$userid]) && self::$_instances[$userid]->userid != $userid) {
			trigger_error('KUser internal storage problem, reloading index '.$userid, E_USER_NOTICE);
			unset(self::$_instances[$userid]);
		}
		if (!isset(self::$_instances[$userid])) {
			self::$_instances[$userid] =& new KUser();

			$config =& KConfig::getInstance();
			self::$_instances[$userid]->last_visit_time = time() + ($config->board_ofset * KUNENA_SECONDS_IN_HOUR) - KUNENA_SECONDS_IN_YEAR;
			self::$_instances[$userid]->curr_visit_time = time() + ($config->board_ofset * KUNENA_SECONDS_IN_HOUR);

			if ($userid) self::$_instances[$userid]->load($userid);
		}
		return self::$_instances[$userid];
	}
	
	function update()
	{
		if (!$this->userid) return;
	    $this->_updateSessionInfo();
	    $this->_updateAllowedCategories();
	    $this->store();
	}

	function load( $userid=null )
	{
		if (!(int)$userid) return false;
		$this->_exists = parent::load($userid);
		$this->userid = (int)$userid;

		return $this->_exists;
	}

	function store( $updateNulls=false )
	{
		$config =& KConfig::getInstance();

		// Finally update current visit timestamp before saving
		$this->curr_visit_time = time() + $config->board_ofset * KUNENA_SECONDS_IN_HOUR;

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
			$this->last_visit_time = $this->curr_visit_time;
			$this->read_topics = '';
		}
	}

	protected function _updateAllowedCategories()
	{
		// check to see if we need to refresh the allowed forums cache
		// get all accessaible forums if needed (eg on forum modification, new session)
		if (!$this->allowed_categories or $this->allowed_categories == 'na' or $this->isNewSession()) {
			$allow_categories = $this->getAllowedCategories();

			if (!$allow_categories)
			{
				$allow_categories = '0';
			}

			$this->allowed_categories = $allow_categories;
		}
	}

	private function _hasRights(&$kunena_acl, $gid, $access, $recurse)
	{
		// cache results to save db queries
		static $childs = array();
		
		if ($gid == $access) return 1;
		if ($recurse) {
			if (!isset($childs[$access]))
				$childs[$access] = $kunena_acl->get_group_children($access, 'ARO', 'RECURSE');
			return (is_array($childs[$access]) && in_array($gid, $childs[$access]));
		}
		return 0;
	}

	function getAllowedCategories()
	{
		$catlist = '';

		$query = new KQuery();

		$query->select('c.id, c.pub_access, c.pub_recurse, c.admin_access, c.admin_recurse, c.moderated');
		$query->select('(m.userid IS NOT NULL) AS ismod');
		$query->from('#__kunena_categories AS c');
		$query->leftJoin('#__kunena_moderation AS m ON c.id=m.catid AND m.userid='.intval($this->userid));
		$query->where('c.published=1');

		$this->_db->setQuery($query->toString());
		// echo nl2br(str_replace('#__','jos_',$query->toString())).'<hr/>';
		$rows = $this->_db->loadObjectList();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if ($rows)
		{
			$acl = JFactory::getACL();
			$user = JFactory::getUser();

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
						($row->pub_access == -1 and $this->userid > 0) or
						($row->pub_access > 0 and $this->_hasRights($acl, $user->gid, $row->pub_access, $row->pub_recurse)) or
						($row->admin_access > 0 and $this->_hasRights($acl, $user->gid, $row->admin_access, $row->admin_recurse))
					) $catlist .= (($catlist == '')?'':',').$row->id;
			}
		}

		return $catlist;
	}
}