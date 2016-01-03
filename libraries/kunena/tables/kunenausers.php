<?php
/**
 * Kunena Component
 * @package     Kunena.Framework
 * @subpackage  Tables
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die ();

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena Users Table
 * Provides access to the #__kunena_users table
 *
 * @since  K4.0
 */
class TableKunenaUsers extends KunenaTable
{

	/**
	 * User ID
	 * @var int
	 **/
	public $userid = null;

	// From Joomla
	public $name = null;
	public $username = null;
	public $email = null;
	public $blocked = null;
	public $registerDate = null;
	public $lastvisitDate = null;

	public $view = null;

	/**
	 * Signature
	 * @var string
	 **/
	public $signature = null;

	/**
	 * Is moderator?
	 * @var int
	 **/
	public $moderator = null;

	/**
	 * Banned until timestamp
	 * @var int
	 **/
	public $banned = null;

	/**
	 * Ordering of posts
	 * @var int
	 **/
	public $ordering = null;

	/**
	 * User post count
	 * @var int
	 **/
	public $posts = null;

	/**
	 * Avatar image file
	 * @var string
	 **/
	public $avatar = null;

	/**
	 * User karma
	 * @var int
	 **/
	public $karma = null;

	public $karma_time = null;

	/**
	 * Kunena Group ID
	 * @var int
	 **/
	public $group_id = null;

	/**
	 * Kunena Profile hits
	 * @var int
	 **/
	public $uhits = null;

	/**
	 * Personal text
	 * @var string
	 **/
	public $personalText = null;

	/**
	 * Gender
	 * @var int
	 **/
	public $gender = null;

	/**
	 * Birthdate
	 * @var string
	 **/
	public $birthdate = null;

	/**
	 * User Location
	 * @var string
	 **/
	public $location = null;

	/**
	 * Name of web site
	 * @var string
	 **/
	public $websitename = null;

	/**
	 * URL to web site
	 * @var string
	 **/
	public $websiteurl = null;

	/**
	 * User rank
	 * @var int
	 **/
	public $rank = null;

	/**
	 * Hide Email address
	 * @var int
	 **/
	public $hideEmail = null;

	/**
	 * Show online
	 * @var int
	 **/
	public $showOnline = null;

	/**
	 * ICQ ID
	 * @var string
	 **/
	public $icq = null;

	/**
	 * AIM ID
	 * @var string
	 **/
	public $aim = null;

	/**
	 * YIM ID
	 * @var string
	 **/
	public $yim = null;

	/**
	 * MSN ID
	 * @var string
	 **/
	public $msn = null;

	/**
	 * SKYPE ID
	 * @var string
	 **/
	public $skype = null;

	/**
	 * TWITTER ID
	 * @var string
	 **/
	public $twitter = null;

	/**
	 * FACEBOOK ID
	 * @var string
	 **/
	public $facebook = null;

	/**
	 * GTALK ID
	 * @var string
	 **/
	public $gtalk = null;

	/**
	 * MYSPACE ID
	 * @var string
	 **/
	public $myspace = null;

	/**
	 * LINKEDIN ID
	 * @var string
	 **/
	public $linkedin = null;

	/**
	 * DELICIOUS ID
	 * @var string
	 **/
	public $delicious = null;

	/**
	 * FRIENDFEED ID
	 * @var string
	 **/
	public $friendfeed = null;

	/**
	 * $DIGG ID
	 * @var string
	 **/
	public $digg = null;

	/**
	 * BLOGSPOT ID
	 * @var string
	 **/
	public $blogspot = null;

	/**
	 * FLICKR ID
	 * @var string
	 **/
	public $flickr = null;

	/**
	 * BEBO ID
	 * @var string
	 **/
	public $bebo = null;

	/**
	 * Thankyou count
	 * @var int
	 **/
	public $thankyou = null;

	/**
	 * canSubscribe
	 * @var int
	 **/
	public $canSubscribe = null;

	/**
	 * userListtime
	 * @var int
	 **/
	public $userListtime = null;

	/**
	 * Status
	 * @var int
	 **/
	public $status = null;

	/**
	 * Status Text
	 * @var string
	 **/
	public $status_text = null;

	public function __construct($db)
	{
		parent::__construct('#__kunena_users', 'userid', $db);
	}

	public function load($userid = null, $reset = true)
	{
		$this->_exists = false;
		$k = $this->_tbl_key;

		// Get the id to load.
		if ($userid !== null) {
			$this->$k = $userid;
		}

		// Reset the table.
		if ($reset) $this->reset();

		// Check for a valid id to load.
		if ($this->$k === null || intval($this->$k) < 1)
		{
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

		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		// User does not exist (may exist in #__kunena_users, though)
		if (!$data)
		{
			$this->$k = 0;

			return false;
		}

		if ($data['posts'] !== null) $this->_exists = true;

		// Bind the data to the table.
		$this->bind($data);

		return $this->_exists;
	}

	public function reset()
	{
		parent::reset();
		$fields = array('name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate');

		foreach ($fields as $field)
		{
			$this->$field = null;
		}
	}

	public function bind($data, $ignore = array())
	{
		parent::bind($data, $ignore);
		$fields = array('name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate');

		foreach ($fields as $field)
		{
			if (isset($data[$field]) && !in_array($field, $ignore)) $this->$field = $data[$field];
		}
	}

	public function check()
	{
		if (!$this->userid || !JFactory::getUser($this->userid))
		{
			$this->setError(JText::sprintf('COM_KUNENA_LIB_TABLE_USERS_ERROR_USER_INVALID', (int)$this->userid));
		}

		if ($this->status < 0 || $this->status > 3)
		{
			$this->setError ( JText::_('COM_KUNENA_UNKNOWN_STATUS'));
		}

		if (strlen($this->status) < 0 || strlen($this->status) > 255)
		{
			$this->setError ( JText::_('COM_KUNENA_STATUS_TOOLONG'));
		}

		return ($this->getError() == '');
	}
}
