<?php
/**
 * Kunena Component
 * @package     Kunena.Framework
 * @subpackage  User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined ('_JEXEC') or die ();

jimport ( 'joomla.utilities.date' );

/**
 * Class KunenaUser
 *
 * @property	int		$userid
 * @property	int		$status
 * @property	string	$status_text
 * @property	string	$name
 * @property	string	$username
 * @property	string	$email
 * @property	int		$blocked
 * @property	string	$registerDate
 * @property	string	$lastvisitDate
 * @property	string	$signature
 * @property	int		$moderator
 * @property	int		$banned
 * @property	int		$ordering
 * @property	int		$posts
 * @property	string	$avatar
 * @property	int		$karma
 * @property	int		$karma_time
 * @property	int		$uhits
 * @property	string	$personalText
 * @property	int		$gender
 * @property	string	$birthdate
 * @property	string	$location
 * @property	string	$websitename
 * @property	string	$websiteurl
 * @property	int		$rank
 * @property	int		$view
 * @property	int		$hideEmail
 * @property	int		$showOnline
 * @property	string	$icq
 * @property	string	$aim
 * @property	string	$yim
 * @property	string	$msn
 * @property	string	$skype
 * @property	string	$twitter
 * @property	string	$facebook
 * @property	string	$gtalk
 * @property	string	$myspace
 * @property	string	$linkedin
 * @property	string	$delicious
 * @property	string	$friendfeed
 * @property	string	$digg
 * @property	string	$blogspot
 * @property	string	$flickr
 * @property	string	$bebo
 * @property	int		$thankyou
*/
class KunenaUser extends JObject
{
	// Global for every instance
	protected static $_ranks = null;

	protected $_allowed = null;
	protected $_link = array();

	protected $_time;
	protected $_pm;
	protected $_email;
	protected $_website;
	protected $_personalText;
	protected $_signature;

	protected $_exists = false;
	protected $_db = null;

	/**
	 * @param int $identifier
	 *
	 * @internal
	 */
	public function __construct($identifier = 0)
	{
		// Always load the user -- if user does not exist: fill empty data
		if ($identifier !== false)
		{
			$this->load ( $identifier );
		}

		if (!isset($this->userid))
		{
			$this->userid = 0;
		}

		$this->_db = JFactory::getDBO ();
		$this->_app = JFactory::getApplication ();
		$this->_config = KunenaFactory::getConfig ();
	}

	/**
	 * Returns the global KunenaUser object, only creating it if it doesn't already exist.
	 *
	 * @param null|int $identifier	The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 * @param bool $reload		Reload user from database.
	 *
	 * @return KunenaUser
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		return KunenaUserHelper::get($identifier, $reload);
	}

	/**
	 * @param null|bool $exists
	 *
	 * @return bool
	 */
	public function exists($exists = null)
	{
		$return = $this->_exists;

		if ($exists !== null)
		{
			$this->_exists = $exists;
		}

		return $return;
	}

	/**
	 * Is the user me?
	 *
	 * @return bool
	 */
	public function isMyself()
	{
		$result = KunenaUserHelper::getMyself()->userid == $this->userid;

		return $result;
	}

	/**
	 * Returns true if user is authorised to do the action.
	 *
	 * @param string     $action
	 * @param KunenaUser $user
	 *
	 * @return bool
	 *
	 * @since  K4.0
	 */
	public function isAuthorised($action='read', KunenaUser $user = null)
	{
		return !$this->tryAuthorise($action, $user, false);
	}

	/**
	 * Throws an exception if user isn't authorised to do the action.
	 *
	 * @param string      $action
	 * @param KunenaUser  $user
	 * @param bool        $throw
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @throws KunenaExceptionAuthorise
	 * @throws InvalidArgumentException
	 *
	 * @since  K4.0
	 */
	public function tryAuthorise($action='read', KunenaUser $user = null, $throw = true)
	{
		// Special case to ignore authorisation.
		if ($action == 'none')
		{
			return null;
		}

		// Load user if not given.
		if ($user === null)
		{
			$user = KunenaUserHelper::getMyself();
		}

		$config = KunenaConfig::getInstance();
		$exception = null;

		switch ($action)
		{
			case 'read' :
				if (!isset($this->registerDate) || (!$user->exists() && !$config->pubprofile))
				{
					$exception = new KunenaExceptionAuthorise(JText::_('COM_KUNENA_PROFILEPAGE_NOT_ALLOWED_FOR_GUESTS'), $user->exists() ? 403 : 401);
				}
				break;
			case 'edit' :
				if (!isset($this->registerDate) || !$this->isMyself())
				{
					$exception = new KunenaExceptionAuthorise(JText::sprintf('COM_KUNENA_VIEW_USER_EDIT_AUTH_FAILED', $this->getName()), $user->exists() ? 403 : 401);
				}
				break;
			case 'ban' :
				$banInfo = KunenaUserBan::getInstanceByUserid($this->userid, true);
				if (!$banInfo->canBan())
				{
					$exception =  new KunenaExceptionAuthorise($banInfo->getError(), $user->exists() ? 403 : 401);
				}
				break;
			default :
				throw new InvalidArgumentException(JText::sprintf('COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action), 500);
		}

		// Throw or return the exception.
		if ($throw && $exception)
		{
			throw $exception;
		}

		return $exception;
	}

	/**
	 * Method to get the user table object.
	 *
	 * @param	string	$type	The user table name to be used.
	 * @param	string	$prefix	The user table prefix to be used.
	 *
	 * @return	JTable|TableKunenaUsers	The user table object.
	 */
	public function getTable($type = 'KunenaUsers', $prefix = 'Table')
	{
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance ( $tabletype ['name'], $tabletype ['prefix'] );
	}

	/**
	 * @param mixed $data
	 * @param array $ignore
	 */
	public function bind($data, array $ignore = array())
	{
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties ( $data );
	}

	/**
	 * Method to load a KunenaUser object by userid.
	 *
	 * @param	mixed	$id The user id of the user to load.
	 *
	 * @return	boolean			True on success
	 */
	public function load($id)
	{
		// Create the user table object
		$table = $this->getTable ();

		// Load the KunenaTableUser object based on the user id
		if ($id > 0)
		{
			$this->_exists = $table->load($id);
		}

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );

		// Set showOnline if user doesn't exists (if we will save the user)
		if (!$this->_exists)
		{
			$this->showOnline = 1;
		}

		return $this->_exists;
	}

	/**
	 * Method to save the KunenaUser object to the database.
	 *
	 * @param	boolean $updateOnly Save the object only if not a new user.
	 *
	 * @return	boolean True on success.
	 */
	public function save($updateOnly = false)
	{
		// Create the user table object
		$table = $this->getTable ();
		$ignore = array('name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate');
		$table->bind ( $this->getProperties (), $ignore );
		$table->exists ( $this->_exists );

		// Check and store the object.
		if (!$table->check ())
		{
			$this->setError ( $table->getError () );
			return false;
		}

		//are we creating a new user
		$isnew = !$this->_exists;

		// If we aren't allowed to create new users return
		if (!$this->userid || ($isnew && $updateOnly))
		{
			return true;
		}

		//Store the user data in the database
		if (!$result = $table->store ())
		{
			$this->setError ( $table->getError () );
		}

		$access = KunenaAccess::getInstance();
		$access->clearCache();

		// Set the id for the KunenaUser object in case we created a new user.
		if ($result && $isnew)
		{
			$this->load ($table->get('userid'));
			//self::$_instances [$table->get ( 'id' )] = $this;
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaUser object from the database.
	 *
	 * @return	boolean	True on success.
	 */
	public function delete()
	{
		// Delete user table object
		$table = $this->getTable ();

		$result = $table->delete ( $this->userid );
		if (!$result)
		{
			$this->setError ( $table->getError () );
		}

		$access = KunenaAccess::getInstance();
		$access->clearCache();

		return $result;

	}

	/**
	 * @param bool   $yes
	 * @param string $no
	 *
	 * @return string
	 */
	public function isOnline($yes = false, $no = 'offline')
	{
		return KunenaUserHelper::isOnline($this->userid, $yes, $no);
	}

	/**
	* @return int
	 */
	public function getStatus()
	{
		return KunenaUserHelper::getStatus($this->userid);
 	}

	/**
	 * @return string
	 */
	public function getStatusText()
	{
		return KunenaHtmlParser::parseText($this->status_text);
	}

	/**
	 * @return array
	 */
	public function getAllowedCategories()
	{
		if (!isset($this->_allowed))
		{
			$this->_allowed = KunenaAccess::getInstance()->getAllowedCategories($this->userid);
		}

		return $this->_allowed;
	}

	/**
	 * @return string
	 */
	public function getMessageOrdering()
	{
		static $default;

		if (is_null($default))
		{
			$default = KunenaFactory::getConfig()->get('default_sort') == 'desc' ? 'desc' : 'asc';
		}

		if ( $this->exists() )
		{
			return $this->ordering != '0' ? ($this->ordering == '1' ? 'desc' : 'asc') : $default;
		}
		else
		{
			return $default == 'asc' ? 'asc' : 'desc';
		}
	}

	/**
	 * Checks if user has administrator permissions in the category.
	 *
	 * If no category is given or it doesn't exist, check will be done against global administrator permissions.
	 *
	 * @param KunenaForumCategory $category
	 *
	 * @return bool
	 */
	public function isAdmin(KunenaForumCategory $category = null)
	{
		return KunenaAccess::getInstance()->isAdmin ( $this, $category && $category->exists() ? $category->id : null );
	}

	/**
	 * Checks if user has moderator permissions in the category.
	 *
	 * If no category is given or it doesn't exist, check will be done against global moderator permissions.
	 *
	 * @param KunenaForumCategory $category
	 *
	 * @return bool
	 */
	public function isModerator(KunenaForumCategory $category = null)
	{
		return KunenaAccess::getInstance()->isModerator ( $this, $category && $category->exists() ? $category->id : null );
	}

	/**
	 * @return bool
	 */
	public function isBanned()
	{
		if (!$this->banned)
		{
			return false;
		}

		if ($this->blocked || $this->banned == $this->_db->getNullDate ())
		{
			return true;
		}

		$ban = new JDate ($this->banned);
		$now = new JDate ();

		return ($ban->toUnix () > $now->toUnix ());
	}

	/**
	 * @return bool
	 */
	public function isBlocked()
	{
		if ($this->blocked)
		{
			return true;
		}

		return false;
	}

	/**
	 * @param string $visitorname
	 * @param bool   $escape
	 *
	 * @return string
	 */
	public function getName($visitorname = '', $escape = true)
	{
		if (! $this->userid && !$this->name)
		{
			$name = $visitorname;
		}
		else
		{
			$name = $this->_config->username ? $this->username : $this->name;
		}

		if ($escape)
		{
			$name = htmlspecialchars($name, ENT_COMPAT, 'UTF-8');
		}

		return $name;
	}

	/**
	 * @param string $class
	 * @param string|int $sizex
	 * @param int	$sizey
	 *
	 * @return string
	 */
	public function getAvatarImage($class = '', $sizex = 'thumb', $sizey = 90)
	{
		$avatars = KunenaFactory::getAvatarIntegration ();

		return $avatars->getLink ( $this, $class, $sizex, $sizey );
	}

	/**
	 * @param string|int $sizex
	 * @param int	$sizey
	 *
	 * @return string
	 */
	public function getAvatarURL($sizex = 'thumb', $sizey = 90)
	{
		$avatars = KunenaFactory::getAvatarIntegration ();

		return $avatars->getURL ( $this, $sizex, $sizey );
	}

	/**
	 * @param null|string   $name
	 * @param null|string   $title
	 * @param string $rel
	 * @param string $task
	 * @param string $class
	 *
	 * @return string
	 */
	public function getLink($name = null, $title = null, $rel = 'nofollow', $task = '', $class = null, $catid = 0)
	{
		if (!$name)
		{
			$name = $this->getName();
		}

		$key = "{$name}.{$title}.{$rel}.{$catid}";

		if (empty($this->_link[$key]))
		{
			if (!$title)
			{
				$title = JText::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->getName());
			}

			$class = !is_null($class) ? $class : $this->getType($catid, 'class');
			$link = $this->getURL(true, $task);

			if (! empty ( $link ))
			{
				$this->_link[$key] = "<a class=\"{$class}\" href=\"{$link}\" title=\"{$title}\" rel=\"{$rel}\">{$name}</a>";
			}
			else
			{
				$this->_link[$key] = "<span class=\"{$class}\">{$name}</span>";
			}
		}

		return $this->_link[$key];
	}

	/**
	 * @param bool   $xhtml
	 * @param string $task
	 *
	 * @return mixed
	 */
	public function getURL($xhtml = true, $task = '')
	{
		// Note: We want to link also existing users who have never visited Kunena before.
		if (!$this->userid || !$this->registerDate)
		{
			return;
		}

		$config = KunenaConfig::getInstance();
		$me = KunenaUserHelper::getMyself();
		if (!$config->pubprofile && !$me->exists())
		{
			return false;
		}

		return KunenaFactory::getProfile ()->getProfileURL ( $this->userid, $task, $xhtml );
	}

	/**
	 * Get users type as a string inside the specified category.
	 *
	 * @param int  $catid   Category id or 0 for global.
	 * @param bool $code    True if we want to return the code, otherwise return translation key.
	 *
	 * @return string
	 */
	public function getType($catid = 0, $code = false)
	{
		static $types = array(
			'admin'=>'COM_KUNENA_VIEW_ADMIN',
			'localadmin'=>'COM_KUNENA_VIEW_ADMIN',
			'globalmod'=>'COM_KUNENA_VIEW_GLOBAL_MODERATOR',
			'moderator'=>'COM_KUNENA_VIEW_MODERATOR',
			'user'=>'COM_KUNENA_VIEW_USER',
			'guest'=>'COM_KUNENA_VIEW_VISITOR',
			'banned'=>'COM_KUNENA_VIEW_BANNED',
			'blocked'=>'COM_KUNENA_VIEW_BLOCKED'
		);

		$adminCategories = KunenaAccess::getInstance()->getAdminStatus($this);
		$moderatedCategories = KunenaAccess::getInstance()->getModeratorStatus($this);

		if ($this->userid == 0)
		{
			$type = 'guest';
		}
		elseif ($this->isBlocked())
		{
			$type = 'blocked';
		}
		elseif ($this->isBanned())
		{
			$type = 'banned';
		}
		elseif (!empty($adminCategories[0]))
		{
			$type = 'admin';
		}
		elseif (!empty($adminCategories[$catid]))
		{
			$type = 'localadmin';
		}
		elseif (!empty($moderatedCategories[0]))
		{
			$type = 'globalmod';
		}
		elseif (!empty($moderatedCategories[$catid]))
		{
			$type = 'moderator';
		}
		elseif (!$catid && !empty($moderatedCategories))
		{
			$type = 'moderator';
		}
		else
		{
			$type = 'user';
		}

		// Deprecated in K4.0
		if ($code === 'class')
		{
			$userClasses = KunenaFactory::getTemplate()->getUserClasses();

			return isset($userClasses[$type]) ? $userClasses[$type] : $userClasses[0].$type;
		}

		return $code ? $type : $types[$type];
	}

	/**
	 * @param int        $catid    Category Id for the rank (user can have different rank in different categories).
	 * @param string     $type     Possible values: 'title' | 'image' | false (for object).
	 * @param bool|null  $special  True if special only, false if post count, otherwise combined.
	 *
	 * @return stdClass|string|null
	 */
	public function getRank($catid = 0, $type = null, $special = null)
	{
		$config = KunenaConfig::getInstance();

		if (!$config->showranking)
		{
			return null;
		}

		// Guests do not have post rank, they only have special rank.
		if ($special === false && !$this->userid)
		{
			return null;
		}

		// First run? Initialize ranks.
		if (self::$_ranks === null)
		{
			$this->_db->setQuery("SELECT * FROM #__kunena_ranks");
			self::$_ranks = $this->_db->loadObjectList('rank_id');
			KunenaError::checkDatabaseError();
		}

		$userType = $special !== false ? $this->getType($catid, true) : 'count';

		if (isset(self::$_ranks[$this->rank]) && !in_array($userType, array('guest', 'blocked', 'banned', 'count')))
		{
			// Use rank specified to the user.
			$rank = self::$_ranks[$this->rank];
		}
		else
		{
			// Generate user rank.
			$rank = new stdClass();
			$rank->rank_id = 0;
			$rank->rank_title = JText::_('COM_KUNENA_RANK_USER');
			$rank->rank_min = 0;
			$rank->rank_special = 0;
			$rank->rank_image = 'rank0.gif';

			switch ($userType)
			{
				case 'guest' :
					$rank->rank_title = JText::_('COM_KUNENA_RANK_VISITOR');
					$rank->rank_special = 1;
					foreach (self::$_ranks as $cur)
					{
						if ($cur->rank_special == 1 && strstr($cur->rank_image, 'guest'))
						{
							$rank = $cur;
							break;
						}
					}
					break;

				case 'blocked' :
					$rank->rank_title = JText::_('COM_KUNENA_RANK_BLOCKED');
					$rank->rank_special = 1;
					$rank->rank_image = 'rankdisabled.gif';
					foreach (self::$_ranks as $cur)
					{
						if ($cur->rank_special == 1 && strstr($cur->rank_image, 'disabled'))
						{
							$rank = $cur;
							break;
						}
					}
					break;

				case 'banned' :
					$rank->rank_title = JText::_('COM_KUNENA_RANK_BANNED');
					$rank->rank_special = 1;
					$rank->rank_image = 'rankbanned.gif';
					foreach (self::$_ranks as $cur)
					{
						if ($cur->rank_special == 1 && strstr($cur->rank_image, 'banned'))
						{
							$rank = $cur;
							break;
						}
					}
					break;

				case 'admin' :
				case 'localadmin' :
					$rank->rank_title = JText::_('COM_KUNENA_RANK_ADMINISTRATOR');
					$rank->rank_special = 1;
					$rank->rank_image = 'rankadmin.gif';
					foreach (self::$_ranks as $cur)
					{
						if ($cur->rank_special == 1 && strstr($cur->rank_image, 'admin'))
						{
							$rank = $cur;
							break;
						}
					}
					break;

				case 'globalmod' :
				case 'moderator' :
					$rank->rank_title = JText::_('COM_KUNENA_RANK_MODERATOR');
					$rank->rank_special = 1;
					$rank->rank_image = 'rankmod.gif';
					foreach (self::$_ranks as $cur)
					{
						if ($cur->rank_special == 1
							&& (strstr($cur->rank_image, 'rankmod') || strstr($cur->rank_image, 'moderator')))
						{
							$rank = $cur;
							break;
						}
					}
					break;

				case 'user' :
				case 'count' :
					foreach (self::$_ranks as $cur)
					{
						if ($cur->rank_special == 0 && $cur->rank_min <= $this->posts && $cur->rank_min >= $rank->rank_min)
						{
							$rank = $cur;
						}
					}
					break;
			}
		}

		if ($special === true && !$rank->rank_special)
		{
			return null;
		}

		if ($type == 'title')
		{
			return $rank->rank_title;
		}

		if (!$config->rankimages)
		{
			$rank->rank_image = null;
		}

		if ($type == 'image')
		{
			if (!$rank->rank_image)
			{
				return null;
			}
			$url = KunenaTemplate::getInstance()->getRankPath($rank->rank_image, true);
			return '<img src="' . $url . '" alt="" />';
		}

		return $rank;
	}

	/**
	 * Return local time for the user.
	 *
	 * @return KunenaDate  User time instance.
	 */
	public function getTime()
	{
		if (!isset($this->_time))
		{
			$timezone = JFactory::getApplication()->getCfg('offset', null);

			if ($this->userid)
			{
				$user = JUser::getInstance($this->userid);
				$timezone = $user->getParam('timezone', $timezone);
			}

			$this->_time = new KunenaDate('now', $timezone);

			try
			{
				$offset = new DateTimeZone($timezone);
				$this->_time->setTimezone($offset);
			}
			catch (Exception $e)
			{
				// TODO: log error?
			}
		}

		return $this->_time;
	}

	/**
	 * Return registration date.
	 *
	 * @return KunenaDate
	 */
	public function getRegisterDate()
	{
		return KunenaDate::getInstance($this->registerDate);
	}

	/**
	 * Return last visit date.
	 *
	 * @return KunenaDate
	 */
	public function getLastVisitDate()
	{
		if (!$this->lastvisitDate || $this->lastvisitDate == "0000-00-00 00:00:00")
		{
			$date = KunenaDate::getInstance($this->registerDate);
		}
		else
		{
			$date = KunenaDate::getInstance($this->lastvisitDate);
		}

		return $date;
	}

	/**
	 * @param null|string $layout
	 *
	 * @return string
	 */
	public function getTopicLayout( $layout = null )
	{
		if ($layout == 'default')
		{
			$layout = null;
		}

		if (!$layout)
		{
			$layout = $this->_app->getUserState ( 'com_kunena.topic_layout' );
		}

		if (!$layout)
		{
			$layout = $this->view;
		}

		switch ($layout)
		{
			case 'flat':
			case 'threaded':
			case 'indented':
				break;
			default:
				$layout = $this->_config->topic_layout;
		}

		return $layout;
	}

	/**
	 * @param string $layout
	 */
	public function setTopicLayout($layout = 'default')
	{
		if ($layout != 'default')
		{
			$layout = $this->getTopicLayout($layout);
		}

		$this->_app->setUserState ('com_kunena.topic_layout', $layout);

		if ($this->userid && $this->view != $layout)
		{
			$this->view = $layout;
			$this->save(true);
		}
	}

	/**
	 * Get the URL to private messages
	 *
	 * @return string
	 */
	public function getPrivateMsgURL()
	{
		$private = KunenaFactory::getPrivateMessaging();

		return $private->getInboxURL();
	}

	/**
	 * Get the label for URL to private messages
	 *
	 * @return string
	 */
	public function getPrivateMsgLabel()
	{
		$private = KunenaFactory::getPrivateMessaging();

		if ($this->isMyself())
		{
			$count = $private->getUnreadCount($this->userid);

			if ($count)
			{
				return JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $count);
			}
			else
			{
				return JText::_('COM_KUNENA_PMS_INBOX');
			}
		}
		else
		{
			return JText::_('COM_KUNENA_PM_WRITE');
		}
	}

	/**
	 * Get link to private messages.
	 *
	 * @return string  URL.
	 *
	 * @since  K4.0
	 */
	public function getPrivateMsgLink()
	{
		if (!isset($this->_pm))
		{
			$private = KunenaFactory::getPrivateMessaging();

			if (!$this->userid)
			{
				$this->_pm = '';
			}
			elseif ($this->isMyself())
			{
				$count = $private->getUnreadCount($this->userid);
				$this->_pm = $private->getInboxLink($count
					? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $count)
					: JText::_('COM_KUNENA_PMS_INBOX'));
			}
			else
			{
				$this->_pm = $private->getInboxLink(JText::_('COM_KUNENA_PM_WRITE'));
			}
		}

		return $this->_pm;
	}

	/**
	 * Get email address if current user has permissions to see it.
	 *
	 * @return string  Cloaked email address or empty string.
	 *
	 * @since  K4.0
	 */
	public function getEmailLink()
	{
		if (!isset($this->_email))
		{
			$config = KunenaConfig::getInstance();
			$me = KunenaUserHelper::getMyself();

			$this->_email = '';

			if ($this->email && (($config->showemail && (!$this->hideEmail || $me->isModerator())) || $me->isAdmin()))
			{
				$this->_email = JHtml::_('email.cloak', $this->email);
			}
		}

		return $this->_email;
	}

	/**
	 * Get website URL from the user.
	 *
	 * @return string  URL to the website.
	 *
	 * @since  K4.0
	 */
	public function getWebsiteURL()
	{
		$url = $this->websiteurl;

		if (!preg_match("~^(?:f|ht)tps?://~i", $this->websiteurl))
		{
			$url = 'http://' . $url;
		}

		return $url;
	}

	/**
	 * Get website name from the user.
	 *
	 * @return string  Name to the website or the URL if the name isn't set.
	 *
	 * @since  K4.0
	 */
	public function getWebsiteName()
	{
		$name = trim($this->websitename) ? $this->websitename : $this->websiteurl;

		return $name;
	}

	/**
	 * Get website link from the user.
	 *
	 * @return string  Link to the website.
	 *
	 * @since  K4.0
	 */
	public function getWebsiteLink()
	{
		if (!isset($this->_website) && $this->websiteurl)
		{
			$this->_website = '';

			$url = $this->getWebsiteURL();

			$name = $this->getWebsiteName();

			$this->_website = '<a href="' . $this->escape($url) . '" target="_blank">' . $this->escape($name) . '</a>';
		}

		return (string) $this->_website;
	}

	/**
	 * Output gender.
	 *
	 * @param  bool  $translate
	 *
	 * @return string  One of: male, female or unknown.
	 *
	 * @since  K4.0
	 */
	public function getGender($translate = true)
	{
		switch ($this->gender)
		{
			case 1 :
				$gender = 'male';
				break;
			case 2 :
				$gender = 'female';
				break;
			default :
				$gender = 'unknown';
		}

		return $translate ? JText::_('COM_KUNENA_MYPROFILE_GENDER_' . $gender) : $gender;
	}

	/**
	 * Render personal text.
	 *
	 * @return string
	 *
	 * @since  K4.0
	 */
	public function getPersonalText()
	{
		if (!isset($this->_personalText))
		{
			$this->_personalText = KunenaHtmlParser::parseText($this->personalText);
		}

		return $this->_personalText;
	}

	/**
	 * Render user signature.
	 *
	 * @return string
	 *
	 * @since  K4.0
	 */
	public function getSignature()
	{
		if (!isset($this->_signature))
		{
			$this->_signature = KunenaHtmlParser::parseBBCode($this->signature, $this, KunenaConfig::getInstance()->maxsig);
		}

		return $this->_signature;
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function profileIcon($name)
	{
		switch ($name)
		{
			case 'gender' :
				switch ($this->gender)
				{
					case 1 :
						$gender = 'male';
						break;
					case 2 :
						$gender = 'female';
						break;
					default :
						$gender = 'unknown';
				}
				$title = JText::_ ( 'COM_KUNENA_MYPROFILE_GENDER' ) . ': ' . JText::_ ( 'COM_KUNENA_MYPROFILE_GENDER_' . $gender );
				return '<span class="kicon-profile kicon-profile-gender-' . $gender . '" title="' . $title . '"></span>';
				break;
			case 'birthdate' :
				if ($this->birthdate)
				{
					$date = new KunenaDate($this->birthdate);
					if ($date->format('%Y') < 1902)
					{
						break;
					}
					return '<span class="kicon-profile kicon-profile-birthdate" title="' . JText::_ ( 'COM_KUNENA_MYPROFILE_BIRTHDATE' ) . ': ' . $this->birthdate->toKunena('date', 'GMT') . '"></span>';
				}
				break;
			case 'location' :
				if ($this->location)
				{
					return '<span class="kicon-profile kicon-profile-location" title="' . JText::_ ( 'COM_KUNENA_MYPROFILE_LOCATION' ) . ': ' . $this->escape ( $this->location ) . '"></span>';
				}
				break;
			case 'website' :
				$url = $this->websiteurl;
				if (!preg_match("~^(?:f|ht)tps?://~i", $this->websiteurl))
				{
					$url = 'http://' . $this->websiteurl;
				}
				if (!$this->websitename)
				{
					$websitename = $this->websiteurl;
				}
				else
				{
					$websitename = $this->websitename;
				}
				if ($this->websiteurl)
				{
					return '<a href="' . $this->escape ( $url ) . '" target="_blank"><span class="kicon-profile kicon-profile-website" title="' . JText::_ ( 'COM_KUNENA_MYPROFILE_WEBSITE' ) . ': ' . $this->escape ( $websitename ) . '"></span></a>';
				}
				break;
			case 'private' :
				$pms = KunenaFactory::getPrivateMessaging ();
				return $pms->showIcon ( $this->userid );
				break;
			case 'email' :
				// TODO: show email
				return; // '<span class="email" title="'. JText::_('COM_KUNENA_MYPROFILE_EMAIL').'"></span>';
				break;
			case 'profile' :
				if (! $this->userid)
				{
					return;
				}
				return $this->getLink('<span class="profile" title="' . JText::_ ( 'COM_KUNENA_VIEW_PROFILE' ) . '"></span>');
				break;
		}
	}

	/**
	 * @param string $name
	 * @param bool $gray
	 *
	 * @return string
	 */
	public function socialButton($name, $gray = false)
	{
		$social = array ('twitter' => array ('url' => 'http://twitter.com/##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_TWITTER' ), 'nourl' => '0' ),
			'facebook' => array ('url' => 'http://www.facebook.com/##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_FACEBOOK' ), 'nourl' => '0' ),
			'myspace' => array ('url' => 'http://www.myspace.com/##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_MYSPACE' ), 'nourl' => '0' ),
			'linkedin' => array ('url' => 'http://www.linkedin.com/in/##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_LINKEDIN' ), 'nourl' => '0' ),
			'delicious' => array ('url' => 'http://delicious.com/##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_DELICIOUS' ), 'nourl' => '0' ),
			'friendfeed' => array ('url' => 'http://friendfeed.com/##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_FRIENDFEED' ), 'nourl' => '0' ),
			'digg' => array ('url' => 'http://www.digg.com/users/##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_DIGG' ), 'nourl' => '0' ),
			'skype' => array ('url' => '##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_SKYPE' ), 'nourl' => '1' ),
			'yim' => array ('url' => '##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_YIM' ), 'nourl' => '1' ),
			'aim' => array ('url' => '##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_AIM' ), 'nourl' => '1' ),
			'gtalk' => array ('url' => '##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_GTALK' ), 'nourl' => '1' ),
			'msn' => array ('url' => '##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_MSN' ), 'nourl' => '1' ),
			'icq' => array ('url' => 'http://www.icq.com/people/cmd.php?uin=##VALUE##&action=message', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_ICQ' ), 'nourl' => '0' ),
			'blogspot' => array ('url' => 'http://##VALUE##.blogspot.com/', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_BLOGSPOT' ), 'nourl' => '0' ),
			'flickr' => array ('url' => 'http://www.flickr.com/photos/##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_FLICKR' ), 'nourl' => '0' ),
			'bebo' => array ('url' => 'http://www.bebo.com/Profile.jsp?MemberId=##VALUE##', 'title' => JText::_ ( 'COM_KUNENA_MYPROFILE_BEBO' ), 'nourl' => '0' )
		);

		if (!isset($social [$name]))
		{
			return;
		}

		$title = $social [$name] ['title'];
		$value = $this->escape ( $this->$name );
		$url = strtr ( $social [$name] ['url'], array ('##VALUE##' => $value ) );

		if ($social [$name] ['nourl'] == '0')
		{
			if (!empty($this->$name))
			{
				return '<a href="' . $this->escape ( $url ) . '" class="kTip" target="_blank" title="' . $title . ': ' . $value . '"><span class="kicon-profile kicon-profile-' . $name . '"></span></a>';
			}
		}
		else
		{
			if (!empty($this->$name))
			{
				return '<span class="kicon-profile kicon-profile-' . $name . ' kTip" title="' . $title . ': ' . $value . '"></span>';
			}
		}
		if ($gray)
		{
			return '<span class="kicon-profile kicon-profile-' . $name . '-off"></span>';
		}
		else
		{
			return '';
		}
	}

	/**
	 * @param string $var
	 *
	 * @return string
	 */
	public function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'id':

			return $this->userid;
		}

		$trace = debug_backtrace();
		trigger_error(
			'Undefined property via __get(): ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_NOTICE);

		return null;
	}

	/**
	 * Check if captcha is allowed for guests users or registered users
	 *
	 * @return boolean
	 */
	public function canDoCaptcha()
	{
		$config = KunenaFactory::getConfig();

		if ( !$this->exists() && $config->captcha == 1 )
		{
			return true;
		}

		if ( $this->exists() && !$this->isModerator() && $config->captcha >= 0)
		{
			if ($config->captcha_post_limit > 0 && $this->posts < $config->captcha_post_limit)
			{
				return true;
			}
		}

		if ( $config->captcha == '-1' )
		{
			return false;
		}
	}
}
