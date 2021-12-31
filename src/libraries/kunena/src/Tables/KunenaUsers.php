<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Tables
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use UnexpectedValueException;

/**
 * Kunena Users Table
 * Provides access to the #__kunena_users table
 *
 * @since   Kunena 4.0
 */
class KunenaUsers extends KunenaTable
{
	/**
	 * User ID
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $userid = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $name = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $username = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $email = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $blocked = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $registerDate = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $lastvisitDate = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $view = null;

	/**
	 * Signature
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $signature = null;

	/**
	 * Is moderator?
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $moderator = null;

	/**
	 * Banned until timestamp
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $banned = null;

	/**
	 * Ordering of posts
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $ordering = null;

	/**
	 * User post count
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $posts = null;

	/**
	 * Avatar image file
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $avatar = null;

	/**
	 * User karma
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $karma = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $karma_time = null;

	/**
	 * Kunena Group ID
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $group_id = null;

	/**
	 * Kunena Profile hits
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $uhits = null;

	/**
	 * Personal text
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $personalText = null;

	/**
	 * Gender
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $gender = null;

	/**
	 * Birthdate
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $birthdate = null;

	/**
	 * User Location
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $location = null;

	/**
	 * Name of web site
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $websitename = null;

	/**
	 * URL to web site
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $websiteurl = null;

	/**
	 * User rank
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $rank = null;

	/**
	 * Hide Email address
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $hideEmail = null;

	/**
	 * Show online
	 *
	 * @var     integer
	 * @since   Kunena
	 **/
	public $showOnline = null;

	/**
	 * ICQ ID
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $icq = null;

	/**
	 * YIM ID
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $yim = null;

	/**
	 * Microsoft ID
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $microsoft = null;

	/**
	 * SKYPE ID
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $skype = null;

	/**
	 * TWITTER ID
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $twitter = null;

	/**
	 * FACEBOOK ID
	 *
	 * @var     string
	 * @since   Kunena
	 **/
	public $facebook = null;

	/**
	 * Google ID
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $google = null;

	/**
	 * Github ID
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $github = null;

	/**
	 * MYSPACE ID
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $myspace = null;

	/**
	 * LINKEDIN ID
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $linkedin = null;

	/**
	 * FRIENDFEED ID
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $friendfeed = null;

	/**
	 * $DIGG ID
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $digg = null;

	/**
	 * BLOGSPOT ID
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $blogspot = null;

	/**
	 * FLICKR ID
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $flickr = null;

	/**
	 * BEBO ID
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $bebo = null;

	/**
	 * Thankyou count
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $thankyou = null;

	/**
	 * canSubscribe
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $canSubscribe = null;

	/**
	 * userListtime
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $userListtime = null;

	/**
	 * Status
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $status = null;

	/**
	 * Status Text
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $status_text = null;

	/**
	 * Instagram
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $instagram = null;

	/**
	 * QQ
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $qq = null;

	/**
	 * Qzone
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $qzone = null;

	/**
	 * Weibo
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $weibo = null;

	/**
	 * Wechat
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $wechat = null;

	/**
	 * Apple
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $apple = null;

	/**
	 * Vk
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $vk = null;

	/**
	 * telegram
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $telegram = null;

	/**
	 * social
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $socialshare = null;

	/**
	 * odnoklassniki
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $odnoklassniki = null;

	/**
	 * what's app
	 *
	 * @var     integer
	 * @since   Kunena 6.0.0
	 */
	public $vimeo = null;

	/**
	 * what's app
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $whatsapp = null;

	/**
	 * Linkedin company
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $linkedin_company = null;

	/**
	 * Youtube
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $youtube = null;

	/**
	 * reddit
	 *
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $reddit = null;

	/**
	 * Indicates that columns fully support the NULL value in the database
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $_supportNullValue = true;

	/**
	 * @param   DatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__kunena_users', 'userid', $db);
	}

	/**
	 * @param   null  $userid  userid
	 * @param   bool  $reset   reset
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function load($userid = null, $reset = true): bool
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
		if ($this->$k === null || \intval($this->$k) < 1)
		{
			$this->$k = 0;

			return false;
		}

		// Load the user data.
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('u.name, u.username, u.email, u.block as blocked, u.registerDate, u.lastvisitDate, ku.*')
			->from($db->quoteName('#__users', 'u'))
			->leftJoin($db->quoteName($this->_tbl, 'ku') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'))
			->where($db->quoteName('u.id') . ' = ' . $db->quote($this->$k));
		$db->setQuery($query);

		try
		{
			$data = $db->loadAssoc();
		}
		catch (ExecutionFailureException $e)
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

		if ($data !== null)
		{
			$this->_exists = true;
		}

		// Bind the data to the table.
		$this->bind($data);

		return $this->_exists;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function reset()
	{
		parent::reset();
		$fields = ['name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate'];

		foreach ($fields as $field)
		{
			$this->$field = null;
		}
	}

	/**
	 * @param   mixed  $data    data
	 * @param   array  $ignore  ignore
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function bind($data, $ignore = [])
	{
		parent::bind($data, $ignore);
		$fields = ['name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate'];

		foreach ($fields as $field)
		{
			if (isset($data[$field]) && !\in_array($field, $ignore))
			{
				$this->$field = $data[$field];
			}
		}
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function check(): bool
	{
		if (!$this->userid || !Factory::getUser($this->userid))
		{
			// FIXME: find a way to throw exception because it prevent guest to post
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_USERS_ERROR_USER_INVALID', (int) $this->userid));
		}

		if ($this->status < 0 || $this->status > 3)
		{
			throw new UnexpectedValueException(Text::_('COM_KUNENA_UNKNOWN_STATUS'));
		}

		if (\strlen($this->status) < 0 || \strlen($this->status) > 255)
		{
			throw new UnexpectedValueException(Text::_('COM_KUNENA_STATUS_TOOLONG'));
		}

		return true;
	}
}
