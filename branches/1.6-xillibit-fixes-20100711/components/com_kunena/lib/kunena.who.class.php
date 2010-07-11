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
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

class CKunenaWhoIsOnline {
	public $db = null;
	public $my = null;
	public $app = null;
	public $config = null;
	protected $myip = null;
	protected $name = null;
	protected $datenow = null;

	protected function __construct($db, $config, $app) {
		$this->db = $db;
		$this->my = &JFactory::getUser ();
		$this->config = $config;
		$this->app = $app;
		$this->name = $this->config->username ? "username" : "name";
		$this->datenow = CKunenaTimeformat::internalTime();
	}

	public function &getInstance() {
		static $instance = NULL;
		if (! $instance) {
			$kunena_db = & JFactory::getDBO ();
			$kunena_config = KunenaFactory::getConfig ();
			$kunena_app = & JFactory::getApplication ();

			$instance = new CKunenaWhoIsOnline ( $kunena_db, $kunena_config, $kunena_app );
		}
		return $instance;
	}

	/**
	* Escapes a value for output in a view script.
	*
	* If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	* {@link $_encoding} setting.
	*
	* @param  mixed $var The output to escape.
	* @return mixed The escaped value.
	*/
	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	public function getActiveUsersList() {
		static $users = null;
		if ($users) return $users;
		$query
        = "SELECT u.{$this->name} AS username, u.id, k.moderator, k.showOnline "
        . " FROM #__users AS u "
        . " LEFT JOIN #__kunena_users AS k ON k.userid=u.id "
		# filter real public session logouts
        . " INNER JOIN #__session AS s ON s.guest='0' AND s.userid=u.id "
        . " GROUP BY u.id "
        . " ORDER BY username ASC";

    	$this->db->setQuery($query);
    	$users = $this->db->loadObjectList();
    	KunenaError::checkDatabaseError();

    	return $users;
	}

	public function getTotalRegistredUsers () {
		$users =$this->getActiveUsersList();
		return $totaluser = count($users);
	}

	public function getTotalGuestUsers () {
		$query = "SELECT COUNT(*) FROM #__session WHERE guest='1'";
    	$this->db->setQuery($query);
    	$totalguests = $this->db->loadResult();
    	KunenaError::checkDatabaseError();

    	return $totalguests;
	}

	public function getTitleWho ($totaluser,$totalguests) {
		$who_name = '<strong>'.$totaluser.' </strong>';
       	if($totaluser==1) {
        	$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBER').'&nbsp;';
        } else {
           	$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBERS').'&nbsp;';
        }
        $who_name .= JText::_('COM_KUNENA_WHO_AND');
        $who_name .= '<strong> '. $totalguests.' </strong>';
        if($totalguests==1) {
           	$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_GUEST').'&nbsp;';
        } else {
           	$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_GUESTS').'&nbsp;';
        }
		$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_NOW');

		return $who_name;
	}

	public function getUsersList () {
		$query = "SELECT w.*, u.id, u.{$this->name}, f.showOnline FROM #__kunena_whoisonline AS w
        LEFT JOIN #__users AS u ON u.id=w.userid
        LEFT JOIN #__kunena_users AS f ON u.id=f.userid
        ORDER BY w.time DESC";
        $this->db->setQuery($query);
        $users = $this->db->loadObjectList();
        KunenaError::checkDatabaseError();

        return $users;
	}

	protected function _deleteUsersOnline () {
		$past = $this->datenow - $this->config->fbsessiontimeout;
		$this->db->setQuery("DELETE FROM #__kunena_whoisonline WHERE time < {$this->db->Quote($past)}");
		$this->db->query();
		KunenaError::checkDatabaseError();
	}

	protected function _getOnlineUsers () {
		$this->db->setQuery("SELECT COUNT(*) FROM #__kunena_whoisonline WHERE userip={$this->db->Quote($this->myip)} AND userid={$this->db->Quote($this->my->id)}");
		$online = $this->db->loadResult();
		KunenaError::checkDatabaseError();

		return $online;
	}

	protected function _IsUser() {
		if ($this->my->id > 0) {
    		$isuser = 1;
    	} else {
    		$isuser = 0;
    	}

    	return $isuser;
	}

	public function insertOnlineDatas () {
		$id = JRequest::getInt('id');
		$catid = JRequest::getInt('catid');
		$func = JString::strtolower ( JRequest::getCmd ( 'func', 'listcat' ) );
		$task = JRequest::getCmd('task');
		$do = JRequest::getCmd('do');

		$isuser = $this->_IsUser();
		$this->myip = getenv('REMOTE_ADDR');

		$online = $this->_getOnlineUsers();

		if ( $func == 'showcat') {
    		$this->db->setQuery("SELECT name FROM #__kunena_categories WHERE id={$this->db->Quote($catid)}");
    		$what = JText::_('COM_KUNENA_WHO_VIEW_SHOWCAT').' '.$this->db->loadResult();
    		KunenaError::checkDatabaseError();
		} else if ($func == 'listcat') {
    		$what = JText::_('COM_KUNENA_WHO_VIEW_LISCAT');
		} else if ($func == 'latest') {
    		$what = JText::_('COM_KUNENA_WHO_ALL_DISCUSSIONS');
    	} else if ($func == 'mylatest') {
    		$what = JText::_('COM_KUNENA_WHO_MY_DISCUSSIONS');
    	} else if ($func == 'view') {
    		$this->db->setQuery("SELECT subject FROM #__kunena_messages WHERE id={$this->db->Quote($id)}");
    		$what = JText::_('COM_KUNENA_WHO_VIEW_TOPIC').' '.$this->db->loadResult();
    		KunenaError::checkDatabaseError();
    	} else if ($func == 'post' && $do == 'reply') {
    		$this->db->setQuery("SELECT subject FROM #__kunena_messages WHERE id={$this->db->Quote($id)}");
    		$what = JText::_('COM_KUNENA_WHO_REPLY_TOPIC').' '.$this->db->loadResult();
    		KunenaError::checkDatabaseError();
    	} else if ($func == 'post' && $do == 'edit') {
    		$this->db->setQuery("SELECT name FROM #__kunena_messages WHERE id={$this->db->Quote($id)}");
    		$what = JText::_('COM_KUNENA_WHO_POST_EDIT').' '.$this->db->loadResult();
    		KunenaError::checkDatabaseError();
    	} else if ($func == 'who') {
    		$what = JText::_('COM_KUNENA_WHO_VIEW_WHO');
    	} else if ($func== 'search') {
  			$what = JText::_('COM_KUNENA_WHO_SEARCH');
		} else if ($func== 'profile') {
  			$what = JText::_('COM_KUNENA_WHO_PROFILE');
		} else if ($func== 'stats') {
  			$what = JText::_('COM_KUNENA_WHO_STATS');
		} else if ($func== 'userlist') {
  			$what = JText::_('COM_KUNENA_WHO_USERLIST');
		} else {
  			$what = JText::_('COM_KUNENA_WHO_MAINPAGE');
		}

		$link = JURI::current();

		if ($online == 1) {
    		$sql = "UPDATE #__kunena_whoisonline SET ".
    		" time=".$this->db->quote($this->datenow).", ".
    		" what=".$this->db->quote($what).", ".
    		" do=".$this->db->quote($do).", ".
    		" task=".$this->db->quote($task).", ".
    		" link=".$this->db->quote($link).", ".
    		" func=".$this->db->quote($func).
            " WHERE userid=".$this->db->quote($this->my->id).
            " AND userip=".$this->db->quote($this->myip);
    		$this->db->setQuery($sql);
    	} else {
    		$this->_deleteUsersOnline();
    		$sql = "INSERT INTO #__kunena_whoisonline (`userid` , `time`, `what`, `task`, `do`, `func`,`link`, `userip`, `user`) "
            . " VALUES (".
            $this->db->quote($this->my->id).",".
            $this->db->quote($this->datenow).",".
            $this->db->quote($what).",".
            $this->db->quote($task).",".
            $this->db->quote($do).",".
            $this->db->quote($func).",".
            $this->db->quote($link).",".
            $this->db->quote($this->myip).",".
            $this->db->quote($isuser).")";

    		$this->db->setQuery($sql);
    	}

		$this->db->query();
		KunenaError::checkDatabaseError();
	}

	public function displayWho () {
		CKunenaTools::loadTemplate('/plugin/who/who.php');
	}

	public function displayWhoIsOnline () {
		CKunenaTools::loadTemplate('/plugin/who/whoisonline.php');
	}
}
?>
