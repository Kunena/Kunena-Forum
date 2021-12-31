<?php
/**
 * Kunena Component
 * @package         Kunena.Framework
 * @subpackage      Tables
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

require_once __DIR__ . '/kunena.php';

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
	 * @var integer
	 * @since Kunena
	 **/
	public $userid = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $name = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $username = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $email = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $blocked = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $registerDate = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $lastvisitDate = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $view = null;

	/**
	 * Signature
	 * @var string
	 * @since Kunena
	 **/
	public $signature = null;

	/**
	 * Is moderator?
	 * @var integer
	 * @since Kunena
	 **/
	public $moderator = null;

	/**
	 * Banned until timestamp
	 * @var integer
	 * @since Kunena
	 **/
	public $banned = null;

	/**
	 * Ordering of posts
	 * @var integer
	 * @since Kunena
	 **/
	public $ordering = null;

	/**
	 * User post count
	 * @var integer
	 * @since Kunena
	 **/
	public $posts = null;

	/**
	 * Avatar image file
	 * @var string
	 * @since Kunena
	 **/
	public $avatar = null;

	/**
	 * User karma
	 * @var integer
	 * @since Kunena
	 **/
	public $karma = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $karma_time = null;

	/**
	 * Kunena Group ID
	 * @var integer
	 * @since Kunena
	 **/
	public $group_id = null;

	/**
	 * Kunena Profile hits
	 * @var integer
	 * @since Kunena
	 **/
	public $uhits = null;

	/**
	 * Personal text
	 * @var string
	 * @since Kunena
	 **/
	public $personalText = null;

	/**
	 * Gender
	 * @var integer
	 * @since Kunena
	 **/
	public $gender = null;

	/**
	 * Birthdate
	 * @var string
	 * @since Kunena
	 **/
	public $birthdate = null;

	/**
	 * User Location
	 * @var string
	 * @since Kunena
	 **/
	public $location = null;

	/**
	 * Name of web site
	 * @var string
	 * @since Kunena
	 **/
	public $websitename = null;

	/**
	 * URL to web site
	 * @var string
	 * @since Kunena
	 **/
	public $websiteurl = null;

	/**
	 * User rank
	 * @var integer
	 * @since Kunena
	 **/
	public $rank = null;

	/**
	 * Hide Email address
	 * @var integer
	 * @since Kunena
	 **/
	public $hideEmail = null;

	/**
	 * Show online
	 * @var integer
	 * @since Kunena
	 **/
	public $showOnline = null;

	/**
	 * ICQ ID
	 * @var string
	 * @since Kunena
	 **/
	public $icq = null;

	/**
	 * YIM ID
	 * @var string
	 * @since Kunena
	 **/
	public $yim = null;

	/**
	 * Microsoft ID
	 * @var string
	 * @since Kunena
	 **/
	public $microsoft = null;

	/**
	 * SKYPE ID
	 * @var string
	 * @since Kunena
	 **/
	public $skype = null;

	/**
	 * TWITTER ID
	 * @var string
	 * @since Kunena
	 **/
	public $twitter = null;

	/**
	 * FACEBOOK ID
	 * @var string
	 * @since Kunena
	 **/
	public $facebook = null;

	/**
	 * Google ID
	 * @var string
	 * @since Kunena
	 */
	public $google = null;

	/**
	 * MYSPACE ID
	 * @var string
	 * @since Kunena
	 */
	public $myspace = null;

	/**
	 * LINKEDIN ID
	 * @var string
	 * @since Kunena
	 */
	public $linkedin = null;

	/**
	 * DELICIOUS ID
	 * @var string
	 * @since Kunena
	 */
	public $delicious = null;

	/**
	 * FRIENDFEED ID
	 * @var string
	 * @since Kunena
	 */
	public $friendfeed = null;

	/**
	 * $DIGG ID
	 * @var string
	 * @since Kunena
	 */
	public $digg = null;

	/**
	 * BLOGSPOT ID
	 * @var string
	 * @since Kunena
	 */
	public $blogspot = null;

	/**
	 * FLICKR ID
	 * @var string
	 * @since Kunena
	 */
	public $flickr = null;

	/**
	 * BEBO ID
	 * @var string
	 * @since Kunena
	 */
	public $bebo = null;

	/**
	 * Thankyou count
	 * @var integer
	 * @since Kunena
	 */
	public $thankyou = null;

	/**
	 * canSubscribe
	 * @var integer
	 * @since Kunena
	 */
	public $canSubscribe = null;

	/**
	 * userListtime
	 * @var integer
	 * @since Kunena
	 */
	public $userListtime = null;

	/**
	 * Status
	 * @var integer
	 * @since Kunena
	 */
	public $status = null;

	/**
	 * Status Text
	 * @var string
	 * @since Kunena
	 */
	public $status_text = null;

	/**
	 * Instagram
	 * @var integer
	 * @since Kunena
	 */
	public $instagram = null;

	/**
	 * QQ
	 * @var integer
	 * @since Kunena
	 */
	public $qq = null;

	/**
	 * Qzone
	 * @var integer
	 * @since Kunena
	 */
	public $qzone = null;

	/**
	 * Weibo
	 * @var integer
	 * @since Kunena
	 */
	public $weibo = null;

	/**
	 * Wechat
	 * @var integer
	 * @since Kunena
	 */
	public $wechat = null;

	/**
	 * Apple
	 * @var integer
	 * @since Kunena
	 */
	public $apple = null;

	/**
	 * Vk
	 * @var integer
	 * @since Kunena
	 */
	public $vk = null;

	/**
	 * telegram
	 * @var integer
	 * @since Kunena
	 */
	public $telegram = null;

	/**
	 * social
	 * @var integer
	 * @since Kunena
	 */
	public $socialshare = null;

	/**
	 * odnoklassniki
	 * @var integer
	 * @since Kunena
	 */
	public $odnoklassniki = null;

	/**
	 * what's app
	 * @var integer
	 * @since Kunena
	 */
	public $whatsapp = null;

	/**
	 * Linkedin company
	 * @var integer
	 * @since Kunena
	 */
	public $linkedin_company = null;

	/**
	 * Youtube
	 * @var integer
	 * @since Kunena
	 */
	public $youtube = null;

	/**
	 * @param   JDatabaseDriver $db Database driver
	 *
	 * @since Kunena
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_users', 'userid', $db);
	}

	/**
	 * @param   null $userid userid
	 * @param   bool $reset  reset
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function load($userid = null, $reset = true)
	{
		$this->_exists = false;
		$k             = $this->_tbl_key;

		// Get the id to load.
		if ($userid !== null)
		{
			$this->$k = $userid;
		}

		// Reset the table.
		if ($reset)
		{
			$this->reset();
		}

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

		try
		{
			$data = $this->_db->loadAssoc();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		// User does not exist (may exist in #__kunena_users, though)
		if (!$data)
		{
			$this->$k = 0;

			return false;
		}

		if ($data['posts'] !== null)
		{
			$this->_exists = true;
		}

		// Bind the data to the table.
		$this->bind($data);

		return $this->_exists;
	}

	/**
	 * @since Kunena
	 * @return void
	 */
	public function reset()
	{
		parent::reset();
		$fields = array('name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate');

		foreach ($fields as $field)
		{
			$this->$field = null;
		}
	}

	/**
	 * @param   mixed $data   data
	 * @param   array $ignore ignore
	 *
	 * @return void
	 * @since Kunena
	 */
	public function bind($data, $ignore = array())
	{
		parent::bind($data, $ignore);
		$fields = array('name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate');

		foreach ($fields as $field)
		{
			if (isset($data[$field]) && !in_array($field, $ignore))
			{
				$this->$field = $data[$field];
			}
		}
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function check()
	{
		if (!$this->userid || !Factory::getUser($this->userid))
		{
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_USERS_ERROR_USER_INVALID', (int) $this->userid));
		}

		if ($this->status < 0 || $this->status > 3)
		{
			$this->setError(Text::_('COM_KUNENA_UNKNOWN_STATUS'));
		}

		if (strlen($this->status) < 0 || strlen($this->status) > 255)
		{
			$this->setError(Text::_('COM_KUNENA_STATUS_TOOLONG'));
		}

		return $this->getError() == '';
	}
}
