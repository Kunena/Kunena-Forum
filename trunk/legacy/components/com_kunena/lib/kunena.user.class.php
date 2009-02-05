<?php
/**
* @version $Id$
* Kunena Component - CKunenaUser class
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// Kunena User class
class CKunenaUser
{
	var $id = 0;
	var $joomlaProperties = NULL;
	var $kunenaProperties = NULL;

	function CKunenaUser($userid)
	{
		if (!is_numeric($userid)) return;
		$this->id = $userid;
	}

	function getID() {
		return $this->id;
	}

	function get($property) {
		$users = CKunenaUsers::getInstance();
		$mapping =& $users->mapping;
		if (array_key_exists($property, $mapping)) {
			list($scope, $field) = $mapping[$property];
		} else {
			return FALSE;
		}

		switch ($scope) {
			case "joomla":
				return $this->_getJoomlaProperty($field);
			case "kunena":
				return $this->_getKunenaProperty($field);
			default:
				return FALSE;
		}
	}

	function display($property) {
		return htmlspecialchars((string)$this->get($property));
	}

	function _getJoomlaProperty($field) {
		global $database;

		if ($this->id == 0) return FALSE;
		if ($this->joomlaProperties == NULL)
		{
			$this->joomlaProperties = new mosUser($database);
			$this->joomlaProperties->load($this->id);
		}

		$vars = get_object_vars($this->joomlaProperties);
		if (array_key_exists($field, $vars)) {
			return $vars[$field];
		} else {
			return FALSE;
		}
	}

	function _getKunenaProperty($field) {
		global $database;

		if ($this->id == 0) return FALSE;
		if ($this->kunenaProperties == NULL)
		{
			$database->setQuery("SELECT * FROM #__fb_users WHERE userid='{$this->id}' LIMIT 1");
			$this->kunenaProperties = $database->loadAssoc();
			check_dberror("Unable to load Kunena user information.");
		}

		if (array_key_exists($field, $this->kunenaProperties)) {
			return $this->kunenaProperties[$field];
		} else {
			return FALSE;
		}
	}
}

class CKunenaUsers
{
	var $mapping = array(
		'name' => array('joomla', 'name'),
		'realname' => array('joomla', 'name'),
		'username' => array('joomla', 'username'),
		'email' => array('joomla', 'email'),
		'usertype' => array('joomla', 'usertype'),
		'registerDate' => array('joomla', 'registerDate'),
		'lastvisitDate' => array('joomla', 'lastvisitDate'),
		'view' => array('kunena', 'view'),
		'signature' => array('kunena', 'signature'),
		'moderator' => array('kunena', 'moderator'),
		'ordering' => array('kunena', 'ordering'), //
		'posts' => array('kunena', 'posts'),
		'avatar' => array('kunena', 'avatar'),
		'karma' => array('kunena', 'karma'),
		'karma_time' => array('kunena', 'karma_time'),
		'group_id' => array('kunena', 'group_id'),
		'uhits' => array('kunena', 'uhits'),
		'personalText' => array('kunena', 'personalText'),
		'gender' => array('kunena', 'gender'),
		'birthdate' => array('kunena', 'birthdate'),
		'location' => array('kunena', 'location'),
		'ICQ' => array('kunena', 'ICQ'),
		'AIM' => array('kunena', 'AIM'),
		'YIM' => array('kunena', 'YIM'),
		'MSN' => array('kunena', 'MSN'),
		'SKYPE' => array('kunena', 'SKYPE'),
		'GTALK' => array('kunena', 'GTALK'),
		'websitename' => array('kunena', 'websitename'),
		'websiteurl' => array('kunena', 'websiteurl'),
		'hideEmail' => array('kunena', 'hideEmail'), //
		'showOnline' => array('kunena', 'showOnline'), //
	);

	var $users = array();

	function CKunenaUsers()
	{
		global $fbConfig;
		if ($fbConfig->username == 1) $this->mapping['name'] = $this->mapping['username'];
	}

	function &getInstance() {
		static $instance;
		if (!$instance) $instance = new CKunenaUsers();
		return $instance;
	}

	function &get($userid) {
		if (!is_numeric($userid)) return FALSE;
		if (in_array($userid, $this->users)) return $this->users[$userid];

		$user = new CKunenaUser($userid);
		if ($user->getID() != $userid) return FALSE;
		$this->users[$userid] = $user;
		return $user;
	}

	function &getMyself() {
		global $my;
		return $this->get($my->id);
	}
}

?>
