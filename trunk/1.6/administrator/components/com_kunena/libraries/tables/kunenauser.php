<?php
/**
* @version $Id$
* Kunena Component - CKunenaUser class
* @package Kunena
*
* @Copyright (C) 2008-2011 www.kunena.org All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once(dirname(__FILE__).'/kunena.php');

/**
* Kunena User Table
* Provides access to the #__kunena_users table
*/
class TableKunenaUser extends KunenaTable
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
	* TWITTER ID
	* @var string
	**/
	var $TWITTER = null;
	/**
	* FACEBOOK ID
	* @var string
	**/
	var $FACEBOOK = null;

	/**
	* GTALK ID
	* @var string
	**/
	var $GTALK = null;

	/**
	* MYSPACE ID
	* @var string
	**/
	var $MYSPACE = null;
	/**
	* LINKEDIN ID
	* @var string
	**/
	var $LINKEDIN = null;
	/**
	* DELICIOUS ID
	* @var string
	**/
	var $DELICIOUS = null;
	/**
	* FRIENDFEED ID
	* @var string
	**/
	var $FRIENDFEED = null;
	/**
	* $DIGG ID
	* @var string
	**/
	var $DIGG = null;
	/**
	* BLOGSPOT ID
	* @var string
	**/
	var $BLOGSPOT = null;
	/**
	* FLICKR ID
	* @var string
	**/
	var $FLICKR = null;
	/**
	* BEBO ID
	* @var string
	**/
	var $BEBO = null;

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
		$query = "SELECT u.name, u.username, u.block as blocked, ku.*
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
		$fields = array('name', 'username', 'blocked');
		foreach ($fields as $field) {
			$this->$field = '';
		}
	}

	public function bind($data, $ignore=array()) {
		parent::bind($data, $ignore);
		$fields = array('name', 'username', 'blocked');
		foreach ($fields as $field) {
			if (isset($data[$field]) && !in_array($field, $ignore)) $this->$field = $data[$field];
		}
	}
}