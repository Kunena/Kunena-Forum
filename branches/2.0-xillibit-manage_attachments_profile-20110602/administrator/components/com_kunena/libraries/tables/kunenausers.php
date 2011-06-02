<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');

/**
* Kunena Users Table
* Provides access to the #__kunena_users table
*/
class TableKunenaUsers extends KunenaTable
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
	* Banned until timestamp
	* @var int
	**/
	var $banned = null;

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
	* User rank
	* @var int
	**/
	var $rank = null;
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
	* ICQ ID
	* @var string
	**/
	var $icq = null;

	/**
	* AIM ID
	* @var string
	**/
	var $aim = null;

	/**
	* YIM ID
	* @var string
	**/
	var $yim = null;

	/**
	* MSN ID
	* @var string
	**/
	var $msn = null;

	/**
	* SKYPE ID
	* @var string
	**/
	var $skype = null;
	/**
	* TWITTER ID
	* @var string
	**/
	var $twitter = null;
	/**
	* FACEBOOK ID
	* @var string
	**/
	var $facebook = null;

	/**
	* GTALK ID
	* @var string
	**/
	var $gtalk = null;

	/**
	* MYSPACE ID
	* @var string
	**/
	var $myspace = null;
	/**
	* LINKEDIN ID
	* @var string
	**/
	var $linkedin = null;
	/**
	* DELICIOUS ID
	* @var string
	**/
	var $delicious = null;
	/**
	* FRIENDFEED ID
	* @var string
	**/
	var $friendfeed = null;
	/**
	* $DIGG ID
	* @var string
	**/
	var $digg = null;
	/**
	* BLOGSPOT ID
	* @var string
	**/
	var $blogspot = null;
	/**
	* FLICKR ID
	* @var string
	**/
	var $flickr = null;
	/**
	* BEBO ID
	* @var string
	**/
	var $bebo = null;

	function __construct($db) {
		parent::__construct('#__kunena_users', 'userid', $db);
	}

	function load($userid = null)
	{
		$this->_exists = false;
		$k = $this->_tbl_key;
		// Get the id to load.
		if ($userid !== null) {
			$this->$k = $userid;
		}

		// Reset the table.
		$this->reset();

		// Check for a valid id to load.
		if ($this->$k === null || intval($this->$k) < 1) {
			$this->$k = 0;
			return false;
		}

		// Load the user data.
		$query = "SELECT u.name, u.username, u.email, u.block as blocked, u.registerDate, u.lastvisitDate, ku.*
			FROM #__users AS u
			LEFT JOIN {$this->_tbl} AS ku ON u.id = ku.userid
			WHERE u.id = {$this->$k}";
		$this->_db->setQuery($query);
		$data = $this->_db->loadAssoc();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// User does not exist (may exist in #__kunena_users, though)
		if(!$data)
		{
			$this->$k = 0;
			return false;
		}
		if ($data['posts'] !== null) $this->_exists = true;

		// Bind the data to the table.
		$this->bind($data);
		return $this->_exists;
	}

	public function reset(){
		parent::reset();
		$fields = array('name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate');
		foreach ($fields as $field) {
			$this->$field = null;
		}
	}

	function check() {
		if (!$this->userid || !JFactory::getUser($this->userid)) {
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_USERS_ERROR_USER_INVALID', (int) $this->userid ) );
		}
		return ($this->getError () == '');
	}

	public function bind($data, $ignore=array()) {
		parent::bind($data, $ignore);
		$fields = array('name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate');
		foreach ($fields as $field) {
			if (isset($data[$field]) && !in_array($field, $ignore)) $this->$field = $data[$field];
		}
	}
}