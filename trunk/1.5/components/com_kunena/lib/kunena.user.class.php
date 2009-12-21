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
defined( '_JEXEC' ) or die('Restricted access');

require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');
require_once (KUNENA_PATH_LIB . DS . 'kunena.config.class.php');

/**
* Kunena Users Table Class
* Provides access to the #__fb_users table
*/
class CKunenaUserprofile extends JTable
{
	/**
	* User ID
	* @var int
	**/
	var $userid = null;
	var $view = null;
	/**
	* Signature
	* @var string
	**/
	var $signature = null;
	/**
	* Is moderator?
	* @var int
	**/
	var $moderator = null;
	/**
	* Ordering of posts
	* @var int
	**/
	var $ordering = null;
	/**
	* User post count
	* @var int
	**/
	var $posts = null;
	/**
	* Avatar image file
	* @var string
	**/
	var $avatar = null;
	/**
	* User karma
	* @var int
	**/
	var $karma = null;
	var $karma_time = null;
	/**
	* Kunena Group ID
	* @var int
	**/
	var $group_id = null;
	/**
	* Kunena Profile hits
	* @var int
	**/
	var $uhits = null;
	/**
	* Personal text
	* @var string
	**/
	var $personalText = null;
	/**
	* Gender
	* @var int
	**/
	var $gender = null;
	/**
	* Birthdate
	* @var string
	**/
	var $birthdate = null;
	/**
	* User Location
	* @var string
	**/
	var $location = null;
	/**
	* ICQ ID
	* @var string
	**/
	var $ICQ = null;
	/**
	* AIM ID
	* @var string
	**/
	var $AIM = null;
	/**
	* YIM ID
	* @var string
	**/
	var $YIM = null;
	/**
	* MSN ID
	* @var string
	**/
	var $MSN = null;
	/**
	* SKYPE ID
	* @var string
	**/
	var $SKYPE = null;
	/**
	* GTALK ID
	* @var string
	**/
	var $GTALK = null;
	/**
	* Name of web site
	* @var string
	**/
	var $websitename = null;
	/**
	* URL to web site
	* @var string
	**/
	var $websiteurl = null;
	/**
	* Hide Email address
	* @var int
	**/
	var $hideEmail = null;
	/**
	* Show online
	* @var int
	**/
	var $showOnline = null;
	/**
	* @param userid NULL=current user
	*/
	function CKunenaUserprofile($userid=null)
	{
		$kunena_db = &JFactory::getDBO();
		parent::__construct('#__fb_users', 'userid', $kunena_db);
		if ($userid === null) {
			$user =& JFactory::getUser();
			$userid = $user->get('id');
		}
		$this->load($userid);
	}
}

/**
* Kunena User Class
*/
class CKunenaUser
{
	var $id = 0;
	var $joomlaProperties = NULL;
	var $kunenaProperties = NULL;
	var $CBProperties = NULL;

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
		$kunena_db = &JFactory::getDBO();

		if ($this->id == 0) return FALSE;
		if ($this->joomlaProperties == NULL)
		{
			$this->joomlaProperties = new JUser($kunena_db);
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
		$kunena_db = &JFactory::getDBO();

		if ($this->id == 0) return FALSE;
		if ($this->kunenaProperties == NULL)
		{
			$kunena_db->setQuery("SELECT * FROM #__fb_users WHERE userid='{$this->id}'", 0, 1);
			$this->kunenaProperties = $kunena_db->loadAssoc();
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
		'gid' => array('joomla', 'gid'),
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
		$fbConfig =& CKunenaConfig::getInstance();
		if ($fbConfig->username == 1) $this->mapping['name'] = $this->mapping['username'];
	}

	function &getInstance() {
		static $instance = NULL;
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
		$kunena_my = &JFactory::getUser();
		return $this->get($kunena_my->id);
	}
}
?>
