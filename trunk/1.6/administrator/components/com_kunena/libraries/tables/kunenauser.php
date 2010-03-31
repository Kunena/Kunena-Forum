<?php
/**
* @version $Id$
* Kunena Component - CKunenaUser class
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once(dirname(__FILE__).DS.'kunena.php');

/**
* Kunena User Table
* Provides access to the #__fb_users table
*/
class TableKunenaUser extends TableKunena
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
		parent::__construct('#__fb_users', 'userid', $db);
	}

	/**
	 * Method to load a user, user groups, and any other necessary data
	 * from the database so that it can be bound to the user object.
	 *
	 * @access	public
	 * @param	integer		$userid		An optional user id.
	 * @return	boolean		True on success, false on failure.
	 * @since	1.0
	 */
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
		$query = 'SELECT u.name, u.username, ku.* FROM #__users AS u LEFT JOIN #__fb_users AS ku ON u.id = ku.userid WHERE u.id = '.(int) $this->$k;
		$this->_db->setQuery($query);
		$data = $this->_db->loadAssoc();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		if(!$data)
		{
			$this->$k = 0;
			return false;
		}

		// Bind the data to the table.
		$return = $this->bind($data);
		if ($return && $data['posts'] !== null) $this->_exists = true;
		return $this->_exists;
	}

	public function reset(){
		parent::reset();
		$fields = array('name', 'username');
		foreach ($fields as $field) {
			$this->$field = '';
		}
	}

	public function bind($data){
		parent::bind($data);
		$fields = array('name', 'username');
		foreach ($fields as $field) {
			if (isset($data[$field])) $this->$field = $data[$field];
		}
	}
}
