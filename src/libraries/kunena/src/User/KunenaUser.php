<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

use DateTimeZone;
use Exception;
use InvalidArgumentException;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Icons\KunenaSvgIcons;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use stdClass;

/**
 * Class \Kunena\Forum\Libraries\User\KunenaUser
 *
 * @property    int     $id
 * @property    int     $userid
 * @property    int     $status
 * @property    string  $status_text
 * @property    string  $name
 * @property    string  $username
 * @property    string  $email
 * @property    int     $blocked
 * @property    string  $registerDate
 * @property    string  $lastvisitDate
 * @property    string  $signature
 * @property    int     $moderator
 * @property    int     $banned
 * @property    int     $ordering
 * @property    int     $posts
 * @property    string  $avatar
 * @property    int     $karma
 * @property    int     $karma_time
 * @property    int     $uhits
 * @property    string  $personalText
 * @property    int     $gender
 * @property    object  $birthdate
 * @property    string  $location
 * @property    string  $websitename
 * @property    string  $websiteurl
 * @property    int     $rank
 * @property    int     $view
 * @property    int     $hideEmail
 * @property    int     $showOnline
 * @property    int     $canSubscribe
 * @property    int     $userListtime
 * @property    string  $icq
 * @property    string  $yim
 * @property    string  $microsoft
 * @property    string  $skype
 * @property    string  $twitter
 * @property    string  $facebook
 * @property    string  $google
 * @property    string  $github
 * @property    string  $myspace
 * @property    string  $linkedin
 * @property    string  $linkedin_company
 * @property    string  $friendfeed
 * @property    string  $digg
 * @property    string  $blogspot
 * @property    string  $flickr
 * @property    string  $bebo
 * @property    int     $thankyou
 * @property    string  $instagram
 * @property    string  $qqsocial
 * @property    string  $qzone
 * @property    string  $weibo
 * @property    string  $wechat
 * @property    string  $apple
 * @property    string  $vk
 * @property    string  $telegram
 * @property    string  $vimeo
 * @property    string  $whatsapp
 * @property    string  $youtube
 * @property    string  $ok
 * @property    int     $socialshare
 * @property    string  $pinterest
 * @property    string  $reddit
 * @property    int     $timestamp
 * @property    boolean $showEmail
 * @since   Kunena 6.0
 */
class KunenaUser extends CMSObject
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected static $_ranks = null;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $registerDate;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $id;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $userid;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $username;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $status;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $status_text;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $name;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $email;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $blocked;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $lastvisitDate;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $signature;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $banned;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $moderator;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $ordering;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $rank;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $websiteurl;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $posts;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $avatar;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $karma;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $karma_time;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $uhits;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $personalText;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $gender;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $birthdate;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $location;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $websitename;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $view;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $hideEmail;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $showOnline;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $canSubscribe;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $userListtime;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $icq;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $yim;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $microsoft;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $skype;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $twitter;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $facebook;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $google;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $github;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $myspace;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $linkedin;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $linkedin_company;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $friendfeed;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $digg;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $blogspot;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $flickr;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $bebo;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $thankyou;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $instagram;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $qqsocial;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $qzone;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $weibo;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $wechat;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $apple;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $vk;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $telegram;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $vimeo;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $whatsapp;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $youtube;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $ok;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $socialshare;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $pinterest;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $reddit;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $timestamp;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_allowed = null;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_link = [];

	/**
	 * @var     mixed
	 * @since   Kunena 6.0
	 */
	protected $_time;

	/**
	 * @var     mixed
	 * @since   Kunena 6.0
	 */
	protected $_pm;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_email;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_website;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_personalText;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_signature;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_exists = false;

	/**
	 * @var     DatabaseDriver|null
	 * @since   Kunena 6.0
	 */
	protected $_db = null;

	/**
	 * @var KunenaConfig
	 * @since version
	 */
	private $_config;

	/**
	 * @var \Joomla\CMS\Application\CMSApplicationInterface|null
	 * @since version
	 */
	private $_app;

	/**
	 * @param   int  $identifier  identifier
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 * @internal
	 */
	public function __construct($identifier = 0)
	{
		// Always load the user -- if user does not exist: fill empty data
		if ($identifier !== false)
		{
			$this->load($identifier);
		}

		if (!isset($this->userid))
		{
			$this->userid = 0;
		}

		$this->_db     = Factory::getContainer()->get('DatabaseDriver');
		$this->_app    = Factory::getApplication();
		$this->_config = KunenaFactory::getConfig();
	}

	/**
	 * Method to load a \Kunena\Forum\Libraries\User\KunenaUser object by userid.
	 *
	 * @param   mixed  $id  The user id of the user to load.
	 *
	 * @return  boolean True on success
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function load($id): bool
	{
		// Create the user table object
		$table = $this->getTable();

		if ($table)
		{
			// Load the KunenaTableUser object based on the user id
			if ($id > 0)
			{
				$this->_exists = $table->load($id);
			}

			// Assuming all is well at this point lets bind the data
			$this->setProperties($table->getProperties());

			// Set showOnline if user doesn't exists (if we will save the user)
			if (!$this->_exists)
			{
				$this->showOnline = 1;
			}

			return $this->_exists;
		}

		return false;
	}

	/**
	 * Method to get the user table object.
	 *
	 * @param   string  $type    The user table name to be used.
	 * @param   string  $prefix  The user table prefix to be used.
	 *
	 * @return  boolean|Table    The user table object.
	 *
	 * @since   Kunena 6.0
	 */
	public function getTable($type = 'Kunena\\Forum\\Libraries\\Tables\\', $prefix = 'KunenaUsers')
	{
		static $tabletype = null;

		// Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name']   = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return Table::getInstance($tabletype ['prefix'], $tabletype ['name']);
	}

	/**
	 * Returns the global \Kunena\Forum\Libraries\User\KunenaUser object, only creating it if it doesn't already exist.
	 *
	 * @param   null|int  $identifier  The user to load - Can be an integer or string - If string, it is converted to ID
	 *                                 automatically.
	 * @param   bool      $reload      Reload user from database.
	 *
	 * @return  KunenaUser
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getInstance($identifier = null, $reload = false): KunenaUser
	{
		return KunenaUserHelper::get($identifier, $reload);
	}

	/**
	 * Returns true if user is authorised to do the action.
	 *
	 * @param   string           $action  action
	 * @param   KunenaUser|null  $user    user
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 4.0
	 */
	public function isAuthorised($action = 'read', KunenaUser $user = null): bool
	{
		return !$this->tryAuthorise($action, $user, false);
	}

	/**
	 * Throws an exception if user isn't authorised to do the action.
	 *
	 * @param   string           $action  action
	 * @param   KunenaUser|null  $user    user
	 * @param   bool             $throw   throw
	 *
	 * @return  KunenaExceptionAuthorise|boolean
	 *
	 * @throws Exception
	 * @since   Kunena 4.0
	 */
	public function tryAuthorise($action = 'read', KunenaUser $user = null, $throw = true)
	{
		// Special case to ignore authorisation.
		if ($action == 'none')
		{
			return false;
		}

		// Load user if not given.
		if ($user === null)
		{
			$user = KunenaUserHelper::getMyself();
		}

		$input     = $this->_app->input;
		$method    = $input->getInt('userid');
		$kuser     = KunenaFactory::getUser($method);
		$config    = KunenaConfig::getInstance();
		$exception = null;

		switch ($action)
		{
			case 'read' :
				if (!isset($this->registerDate) || (!$user->exists() && !$config->pubProfile))
				{
					$exception = new KunenaExceptionAuthorise(Text::_('COM_KUNENA_PROFILEPAGE_NOT_ALLOWED_FOR_GUESTS'), $user->exists() ? 403 : 404);
				}
				break;
			case 'edit' :
				if (!isset($this->registerDate) || (!$this->isMyself() && !$user->isAdmin() && !$user->isModerator()))
				{
					$exception = new KunenaExceptionAuthorise(Text::sprintf('COM_KUNENA_VIEW_USER_EDIT_AUTH_FAILED', $this->getName()), $user->exists() ? 403 : 401);
				}

				if ($user->isModerator() && $kuser->isAdmin() && !$user->isAdmin())
				{
					$exception = new KunenaExceptionAuthorise(Text::sprintf('COM_KUNENA_VIEW_USER_EDIT_AUTH_FAILED', $this->getName()), $user->exists() ? 403 : 401);
				}
				break;
			case 'ban' :
				$banInfo = KunenaBan::getInstanceByUserid($this->userid, true);

				try
				{
					$banInfo->canBan();
				}
				catch (Exception $e)
				{
					$exception = new KunenaExceptionAuthorise($e->getMessage(), $user->exists() ? 403 : 401);
				}
				break;
			default :
				throw new InvalidArgumentException(Text::sprintf('COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action), 500);
		}

		// Throw or return the exception.
		if ($throw && $exception)
		{
			throw new $exception;
		}

		return $exception;
	}

	/**
	 * @param   null|bool  $exists  exists
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function exists($exists = null): bool
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
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function isMyself(): bool
	{
		return KunenaUserHelper::getMyself()->userid == $this->userid;
	}

	/**
	 * Checks if user has administrator permissions in the category.
	 *
	 * If no category is given or it doesn't exist, check will be done against global administrator permissions.
	 *
	 * @param   KunenaCategory|null  $category  category
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function isAdmin(KunenaCategory $category = null): bool
	{
		return KunenaAccess::getInstance()->isAdmin($this, $category && $category->exists() ? $category->id : null);
	}

	/**
	 * Checks if user has moderator permissions in the category.
	 *
	 * If no category is given or it doesn't exist, check will be done against global moderator permissions.
	 *
	 * @param   KunenaCategory|null  $category  category
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function isModerator(KunenaCategory $category = null): bool
	{
		return KunenaAccess::getInstance()->isModerator($this, $category && $category->exists() ? $category->id : null);
	}

	/**
	 * Retrieve the username from integration if it's enabled, it's the integration plugin which give the username
	 *
	 * @param   string  $visitorname  visitor name
	 * @param   bool    $escape       escape
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena 1.6
	 */
	public function getName($visitorname = '', $escape = true): string
	{
		$profile = KunenaFactory::getProfile();

		if ($profile->enabled == false)
		{
			if (!$this->userid && !$this->name)
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

		return $profile->getProfileName($this, $visitorname);
	}

	/**
	 * @param   mixed  $data    data
	 * @param   array  $ignore  ignore
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function bind($data, array $ignore = []): void
	{
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties($data);
	}

	/**
	 * Method to delete the \Kunena\Forum\Libraries\User\KunenaUser object from the database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function delete(): bool
	{
		// Delete user table object
		$table = $this->getTable();

		$result = $table->delete($this->userid);

		if (!$result)
		{
			$this->setError($table->getError());
		}

		$access = KunenaAccess::getInstance();
		$access->clearCache();

		return $result;

	}

	/**
	 * @return  integer
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getStatus(): int
	{
		return KunenaUserHelper::getStatus($this->userid);
	}

	/**
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getStatusText(): string
	{
		return KunenaParser::parseText($this->status_text);
	}

	/**
	 * @return array|null
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public function getAllowedCategories(): ?array
	{
		if (!isset($this->_allowed))
		{
			$this->_allowed = KunenaAccess::getInstance()->getAllowedCategories($this->userid);
		}

		return $this->_allowed;
	}

	/**
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getMessageOrdering(): string
	{
		static $default;

		if (\is_null($default))
		{
			$default = KunenaFactory::getConfig()->get('defaultSort') == 'desc' ? 'desc' : 'asc';
		}

		if ($this->exists())
		{
			return $this->ordering != '0' ? ($this->ordering == '1' ? 'desc' : 'asc') : $default;
		}

		return $default == 'asc' ? 'asc' : 'desc';
	}

	/**
	 * @param   string      $class   class
	 * @param   string|int  $sizex   sizex
	 * @param   int         $sizey   sizey
	 * @param   string      $online  online
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getAvatarImage($class = '', $sizex = 'thumb', $sizey = 90, $online = ''): string
	{
		$avatars = KunenaFactory::getAvatarIntegration();

		if ($avatars)
		{
			$ktemplate     = KunenaFactory::getTemplate();
			$topicicontype = $ktemplate->params->get('topicicontype');

			if (KunenaFactory::getConfig()->avatarType && $avatars->css)
			{
				if ($sizex == 20)
				{
					if ($topicicontype == 'fa')
					{
						return '<i class="fas fa-user-circle status-' . $online . '" aria-hidden="true"></i>';
					}

					return '<span class="rounded-circle status-' . $online . '" aria-hidden="true">' . KunenaSvgIcons::loadsvg('person') . '</span>';
				}
				elseif ($sizex == 'logout' || $sizex == 'profile')
				{
					if ($topicicontype == 'fa')
					{
						return '<i class="fas fa-user-circle fa-7x"></i>';
					}

					if ($topicicontype == 'svg')
					{
						return '<span class="glyphicon glyphicon-user user-circle user-xl b2-7x" aria-hidden="true"></span>';
					}
				}

				if ($topicicontype == 'fa')
				{
					return '<i class="fas fa-user-circle fa-3x"></i>';
				}

				if ($topicicontype == 'svg' && $this->avatar == null)
				{
					return KunenaSvgIcons::loadsvg('person');
				}
			}
		}

		return $avatars->getLink($this, $class, $sizex, $sizey);
	}

	/**
	 * @param   string|int  $sizex  sizex
	 * @param   int         $sizey  sizey
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getAvatarURL($sizex = 'thumb', $sizey = 90): string
	{
		$avatars = KunenaFactory::getAvatarIntegration();

		return $avatars->getURL($this, $sizex, $sizey);
	}

	/**
	 * Get users type as a string inside the specified category.
	 *
	 * @param   string  $class  class
	 *
	 * @param   string  $name   name
	 * @param   string  $title  title
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @internal param int $catid Category id or 0 for global.
	 *
	 * @since    Kunena 5.1.0
	 *
	 * @internal param bool $code True if we want to return the code, otherwise return translation key.
	 */
	public function getLinkNoStyle($name = null, $title = null, $class = null)
	{
		$optional_username = KunenaFactory::getTemplate()->params->get('optional_username');

		if ($optional_username == 0 || !$this->userid)
		{
			return false;
		}

		if (!$name)
		{
			if ($optional_username == 1)
			{
				$name = $this->username;
			}
			elseif ($optional_username == 2)
			{
				$name = $this->name;
			}
		}

		$key = "{$name}.{$title}";

		if (empty($this->_link[$key]))
		{
			if (!$title)
			{
				$title = Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->getName());
			}

			$link = $this->getURL();

			if (!empty($link))
			{
				$this->_link[$key] = "<a class=\"{$class}\" href=\"{$link}\" title=\"{$title}\">{$name}</a>";
			}
			else
			{
				$this->_link[$key] = "<span class=\"{$class}\">{$name}</span>";
			}
		}

		return $this->_link[$key];
	}

	/**
	 * @param   bool         $xhtml  xhtml
	 * @param   string|null  $task   task
	 *
	 * @return  boolean|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getURL($xhtml = true, $task = '')
	{
		// Note: We want to link also existing users who have never visited Kunena before.
		if (!$this->userid || !$this->registerDate)
		{
			return false;
		}

		$me = KunenaUserHelper::getMyself();

		if (!$this->_config->pubProfile && !$me->exists())
		{
			return false;
		}

		return KunenaFactory::getProfile()->getProfileURL($this->userid, $task, $xhtml);
	}

	/**
	 * Return local time for the user.
	 *
	 * @return  KunenaDate  User time instance.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getTime(): KunenaDate
	{
		if (!isset($this->_time))
		{
			$timezone = Factory::getApplication()->get('offset', null);

			if ($this->userid)
			{
				$user     = User::getInstance($this->userid);
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
	 * @return  KunenaDate
	 *
	 * @since   Kunena 6.0
	 */
	public function getRegisterDate(): KunenaDate
	{
		return KunenaDate::getInstance($this->registerDate);
	}

	/**
	 * Return last visit date.
	 *
	 * @return  KunenaDate
	 *
	 * @since   Kunena 6.0
	 */
	public function getLastVisitDate(): KunenaDate
	{
		if (!$this->lastvisitDate || $this->lastvisitDate == "1000-01-01 00:00:00")
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
	 * @param   string  $layout  layout
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function setTopicLayout($layout = 'default'): void
	{
		if ($layout != 'default')
		{
			$layout = $this->getTopicLayout($layout);
		}

		$this->_app->setUserState('com_kunena.topicLayout', $layout);

		if ($this->userid && $this->view != $layout)
		{
			$this->view = $layout;
			$this->save(true);
		}
	}

	/**
	 * @param   null|string  $layout  layout
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getTopicLayout($layout = null)
	{
		if ($layout == 'default')
		{
			$layout = null;
		}

		if (!$layout)
		{
			$layout = $this->_app->getUserState('com_kunena.topicLayout');
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
				$layout = $this->_config->topicLayout;
		}

		return $layout;
	}

	/**
	 * Method to save the \Kunena\Forum\Libraries\User\KunenaUser object to the database.
	 *
	 * @param   boolean  $updateOnly  Save the object only if not a new user.
	 *
	 * @return  boolean True on success.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function save($updateOnly = false): bool
	{
		// Create the user table object
		$table  = $this->getTable();
		$ignore = ['name', 'username', 'email', 'blocked', 'registerDate', 'lastvisitDate'];
		$table->bind($this->getProperties(), $ignore);
		$table->exists($this->_exists);

		// Check and store the object.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Are we creating a new user
		$isnew = !$this->_exists;

		$moderator = KunenaUserHelper::getMyself()->isModerator();
		$my        = Factory::getApplication()->getIdentity();

		if (!$moderator)
		{
			if ($this->userid != $my->id)
			{
				return false;
			}
		}

		// If we aren't allowed to create new users return
		if (!$this->userid || ($isnew && $updateOnly))
		{
			return true;
		}

		// Store the user data in the database
		if (!$result = $table->store())
		{
			$this->setError($table->getError());
		}

		$access = KunenaAccess::getInstance();
		$access->clearCache();

		// Set the id for the \Kunena\Forum\Libraries\User\KunenaUser object in case we created a new user.
		if ($result && $isnew)
		{
			$this->load($table->get('userid'));

			// Self::$_instances [$table->get ( 'id' )] = $this;
		}

		return $result;
	}

	/**
	 * Get the URL to private messages
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getPrivateMsgURL(): string
	{
		$private = KunenaFactory::getPrivateMessaging();

		return $private->getInboxURL();
	}

	/**
	 * Get the label for URL to private messages
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getPrivateMsgLabel(): string
	{
		$private = KunenaFactory::getPrivateMessaging();

		if ($this->isMyself())
		{
			$count = $private->getUnreadCount($this->userid);

			if ($count)
			{
				return Text::sprintf('COM_KUNENA_PMS_INBOX_NEW', $count);
			}

			return Text::_('COM_KUNENA_PMS_INBOX');
		}

		return Text::_('COM_KUNENA_PM_WRITE');
	}

	/**
	 * Get link to private messages.
	 *
	 * @return  string  URL.
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function getPrivateMsgLink(): string
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
				$count     = $private->getUnreadCount($this->userid);
				$this->_pm = $private->getInboxLink(
					$count
						? Text::sprintf('COM_KUNENA_PMS_INBOX_NEW', $count)
						: Text::_('COM_KUNENA_PMS_INBOX')
				);
			}
			else
			{
				$this->_pm = $private->getInboxLink(Text::_('COM_KUNENA_PM_WRITE'));
			}
		}

		return $this->_pm;
	}

	/**
	 * Show email address if current user has permissions to see it.
	 *
	 * @param   mixed  $profile  profile
	 *
	 * @return  boolean Cloaked email address or empty string.
	 *
	 * @throws  Exception
	 * @since   Kunena 5.1
	 */
	public function getEmail($profile): bool
	{
		$me = KunenaUserHelper::getMyself();

		if ($me->isModerator() || $me->isAdmin())
		{
			return true;
		}

		if ($this->_config->showEmail && $profile->email)
		{
			if ($profile->hideEmail == 0)
			{
				return true;
			}

			if ($profile->hideEmail == 2 && $me->exists())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get email address if current user has permissions to see it.
	 *
	 * @return  string  Cloaked email address or empty string.
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function getEmailLink(): string
	{
		if (!isset($this->_email))
		{
			$me = KunenaUserHelper::getMyself();

			$this->_email = '';

			if ($this->email && (($this->_config->showEmail && (!$this->hideEmail || $me->isModerator())) || $me->isAdmin()))
			{
				$this->_email = HTMLHelper::_('email.cloak', $this->email);
			}
		}

		return $this->_email;
	}

	/**
	 * Get website link from the user.
	 *
	 * @return  string  Link to the website.
	 *
	 * @since   Kunena 4.0
	 */
	public function getWebsiteLink(): string
	{
		if (!isset($this->_website) && $this->websiteurl)
		{
			$this->_website = '';

			$url = $this->getWebsiteURL();

			$name = $this->getWebsiteName();

			$this->_website = '<a href="' . $this->escape($url) . '" target="_blank" rel="noopener noreferrer">' . $this->escape($name) . '</a>';
		}

		return (string) $this->_website;
	}

	/**
	 * Get website URL from the user.
	 *
	 * @return  string  URL to the website.
	 *
	 * @since   Kunena 4.0
	 */
	public function getWebsiteURL()
	{
		$url = $this->websiteurl;

		if (!empty($url) && !preg_match("~^(?:f|ht)tps?://~i", $this->websiteurl))
		{
			$url = 'http://' . $url;
		}

		return $url;
	}

	/**
	 * Get website name from the user.
	 *
	 * @return  string  Name to the website or the URL if the name isn't set.
	 *
	 * @since   Kunena 4.0
	 */
	public function getWebsiteName()
	{
		if (!empty($this->websitename))
		{
			return trim($this->websitename);
		}
		else
		{
			return $this->websiteurl;
		}
	}

	/**
	 * @param   string  $var  var
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function escape(string $var): string
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Output gender.
	 *
	 * @param   bool  $translate  translate
	 *
	 * @return  string  One of: male, female or unknown.
	 *
	 * @since   Kunena 4.0
	 */
	public function getGender($translate = true): string
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

		return $translate ? Text::_('COM_KUNENA_MYPROFILE_GENDER_' . $gender) : $gender;
	}

	/**
	 * Render user signature.
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function getSignature()
	{
		if (!$this->_config->signature)
		{
			return false;
		}

		if (!isset($this->_signature))
		{
			$this->_signature = KunenaParser::parseBBCode((string) $this->signature, $this, $this->_config->maxSig);
		}

		return $this->_signature;
	}

	/**
	 * Check if user can see karma.
	 *
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function canSeeKarma(): bool
	{
		if ($this->userid)
		{
			$me = KunenaUserHelper::getMyself();

			if ($this->_config->showKarma && $me->userid && $me->userid != $this->userid)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Render user sidebar.
	 *
	 * @param   KunenaLayout  $layout  layout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 5.0
	 */
	public function getSideProfile(KunenaLayout $layout)
	{
		$view                  = clone $layout;
		$view->config          = $this->_config;
		$view->userkarma_title = $view->userkarma_minus = $view->userkarma_plus = '';

		if ($view->config->showKarma && $this->userid)
		{
			$view->userkarma_title = Text::_('COM_KUNENA_KARMA') . ': ' . $this->karma;

			if ($view->me->userid && $view->me->userid != $this->userid)
			{
				$topicicontype = KunenaFactory::getTemplate()->params->get('topicicontype');

				if ($topicicontype == 'svg')
				{
					$karmaMinusIcon = '<span class="glyphicon-karma glyphicon glyphicon-minus-sign text-danger" title="' . Text::_('COM_KUNENA_KARMA_SMITE') . '"></span>';
					$karmaPlusIcon  = '<span class="glyphicon-karma glyphicon glyphicon-plus-sign text-success" title="' . Text::_('COM_KUNENA_KARMA_APPLAUD') . '"></span>';
				}
				elseif ($topicicontype == 'fa')
				{
					$karmaMinusIcon = '<i class="fa fa-minus-circle" title="' . Text::_('COM_KUNENA_KARMA_SMITE') . '"></i>';
					$karmaPlusIcon  = '<i class="fa fa-plus-circle" title="' . Text::_('COM_KUNENA_KARMA_APPLAUD') . '"></i>';
				}
				else
				{
					$karmaMinusIcon = '<span class="kicon-profile kicon-profile-minus" title="' . Text::_('COM_KUNENA_KARMA_SMITE') . '"></span>';
					$karmaPlusIcon  = '<span class="kicon-profile kicon-profile-plus" title="' . Text::_('COM_KUNENA_KARMA_APPLAUD') . '"></span>';
				}

				$view->userkarma_minus = ' ' . HTMLHelper::_('link', 'index.php?option=com_kunena&view=user&task=karmadown&userid=' . $this->userid . '&' . Session::getFormToken() . '=1', $karmaMinusIcon);
				$view->userkarma_plus  = ' ' . HTMLHelper::_('link', 'index.php?option=com_kunena&view=user&task=karmaup&userid=' . $this->userid . '&' . Session::getFormToken() . '=1', $karmaPlusIcon);
			}
		}

		$view->userkarma = "{$view->userkarma_title} {$view->userkarma_minus} {$view->userkarma_plus}";

		if ($view->config->showUserStats)
		{
			$view->userrankimage = $this->getRank($layout->category->id, 'image');
			$view->userranktitle = $this->getRank($layout->category->id, 'title');
			$view->userposts     = $this->posts;
			$view->userthankyou  = $this->thankyou;
			$activityIntegration = KunenaFactory::getActivityIntegration();
			$view->userpoints    = $activityIntegration->getUserPoints($this->userid);
			$view->usermedals    = $activityIntegration->getUserMedals($this->userid);
		}
		else
		{
			$view->userrankimage = null;
			$view->userranktitle = null;
			$view->userposts     = null;
			$view->userthankyou  = null;
			$view->userpoints    = null;
			$view->usermedals    = null;
		}

		$view->personalText = $this->getPersonalText();

		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', $layout->getLayout());

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaSidebar', [$this->userid]);

		return KunenaFactory::getProfile()->showProfile($view, $params);
	}

	/**
	 * @param   int        $catid    Category Id for the rank (user can have different rank in different categories).
	 * @param   string     $type     Possible values: 'title' | 'image' | false (for object).
	 * @param   bool|null  $special  True if special only, false if post count, otherwise combined.
	 *
	 * @return  stdClass|string|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getRank($catid = 0, $type = null, $special = null)
	{
		if (!$this->_config->showRanking)
		{
			return;
		}

		// Guests do not have post rank, they only have special rank.
		if ($special === false && !$this->userid)
		{
			return;
		}

		// First run? Initialize ranks.
		if (self::$_ranks === null)
		{
			$query = $this->_db->getQuery(true);
			$query->select('*');
			$query->from($this->_db->quoteName('#__kunena_ranks'));
			$this->_db->setQuery($query);

			try
			{
				self::$_ranks = $this->_db->loadObjectList('rankId');
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		$userType = $special !== false ? $this->getType($catid, true) : 'count';

		if (isset(self::$_ranks[$this->rank]) && !\in_array($userType, ['guest', 'blocked', 'banned', 'count']))
		{
			// Use rank specified to the user.
			$rank = self::$_ranks[$this->rank];
		}
		else
		{
			// Generate user rank.
			$rank              = new stdClass;
			$rank->rankId      = 0;
			$rank->rankTitle   = Text::_('COM_KUNENA_RANK_USER');
			$rank->rankMin     = 0;
			$rank->rankSpecial = 0;
			$rank->rankImage   = 'rank0.gif';

			switch ($userType)
			{
				case 'guest' :
					$rank->rankTitle   = Text::_('COM_KUNENA_RANK_VISITOR');
					$rank->rankSpecial = 1;

					foreach (self::$_ranks as $cur)
					{
						if ($cur->rankSpecial == 1 && strstr($cur->rankImage, 'guest'))
						{
							$rank = $cur;
							break;
						}
					}
					break;

				case 'blocked' :
					$rank->rankTitle   = Text::_('COM_KUNENA_RANK_BLOCKED');
					$rank->rankSpecial = 1;
					$rank->rankImage   = 'rankdisabled.gif';

					foreach (self::$_ranks as $cur)
					{
						if ($cur->rankSpecial == 1 && strstr($cur->rankImage, 'disabled'))
						{
							$rank = $cur;
							break;
						}
					}
					break;

				case 'banned' :
					$rank->rankTitle   = Text::_('COM_KUNENA_RANK_BANNED');
					$rank->rankSpecial = 1;
					$rank->rankImage   = 'rankbanned.gif';

					foreach (self::$_ranks as $cur)
					{
						if ($cur->rankSpecial == 1 && strstr($cur->rankImage, 'banned'))
						{
							$rank = $cur;
							break;
						}
					}
					break;

				case 'admin' :
				case 'localadmin' :
					$rank->rankTitle   = Text::_('COM_KUNENA_RANK_ADMINISTRATOR');
					$rank->rankSpecial = 1;
					$rank->rankImage   = 'rankadmin.gif';

					foreach (self::$_ranks as $cur)
					{
						if ($cur->rankSpecial == 1 && strstr($cur->rankImage, 'admin'))
						{
							$rank = $cur;
							break;
						}
					}
					break;

				case 'globalmod' :
				case 'moderator' :
					$rank->rankTitle   = Text::_('COM_KUNENA_RANK_MODERATOR');
					$rank->rankSpecial = 1;
					$rank->rankImage   = 'rankmod.gif';

					foreach (self::$_ranks as $cur)
					{
						if ($cur->rankSpecial == 1
							&& (strstr($cur->rankImage, 'rankmod') || strstr($cur->rankImage, 'moderator'))
						)
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
						if ($cur->rankSpecial == 0 && $cur->rankMin <= $this->posts && $cur->rankMin >= $rank->rankMin)
						{
							$rank = $cur;
						}
					}
					break;
			}
		}

		if ($special === true && !$rank->rankSpecial)
		{
			return;
		}

		if ($type == 'title')
		{
			return Text::_($rank->rankTitle);
		}

		if (!$this->_config->rankImages)
		{
			$rank->rankImage = null;
		}

		if ($type == 'image')
		{
			if (!$rank->rankImage)
			{
				return;
			}

			/**
			 *  Rankimages 0 = Text Rank
			 *             1 = Rank Image
			 *             2 = Usergroup
			 *             3 = Both Rank image and Usergroup
			 */
			if ($this->_config->rankImages == 0)
			{
				return false;
			}

			if ($this->_config->rankImages == 1)
			{
				$url             = KunenaTemplate::getInstance()->getRankPath($rank->rankImage, true);
				$location        = JPATH_SITE . '/media/kunena/ranks/' . $rank->rankImage;
				$imageProperties = Image::getImageFileProperties($location);

				return '<img loading=lazy src="' . $url . '" height="' . $imageProperties->height . '" width="' . $imageProperties->width . '" alt="' . Text::_($rank->rankTitle) . '" />';
			}

			if ($this->_config->rankImages == 2)
			{
				return '<span class="ranksusergroups">' . self::getUserGroup($this->userid) . '</span>';
			}

			if ($this->_config->rankImages == 3)
			{
				$url             = KunenaTemplate::getInstance()->getRankPath($rank->rankImage, true);
				$location        = JPATH_SITE . '/media/kunena/ranks/' . $rank->rankImage;
				$imageProperties = Image::getImageFileProperties($location);

				return '<img loading=lazy src="' . $url . '" height="' . $imageProperties->height . '" width="' . $imageProperties->width . '" alt="' . Text::_($rank->rankTitle) . '" /><br>
				<span class="ranksusergroups">' . self::getUserGroup($this->userid) . '</span>';
			}

			if ($this->_config->rankImages == 4)
			{
				return self::rankCss($rank, $catid);
			}
		}

		return $rank;
	}

	/**
	 * Get users type as a string inside the specified category.
	 *
	 * @param   int   $catid  Category id or 0 for global.
	 * @param   bool  $code   True if we want to return the code, otherwise return translation key.
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getType($catid = 0, $code = false): string
	{
		static $types = [
			'admin'      => 'COM_KUNENA_VIEW_ADMIN',
			'localadmin' => 'COM_KUNENA_VIEW_ADMIN',
			'globalmod'  => 'COM_KUNENA_VIEW_GLOBAL_MODERATOR',
			'moderator'  => 'COM_KUNENA_VIEW_MODERATOR',
			'user'       => 'COM_KUNENA_VIEW_USER',
			'guest'      => 'COM_KUNENA_VIEW_VISITOR',
			'banned'     => 'COM_KUNENA_VIEW_BANNED',
			'blocked'    => 'COM_KUNENA_VIEW_BLOCKED',
		];

		$adminCategories     = KunenaAccess::getInstance()->getAdminStatus($this);
		$moderatedCategories = KunenaAccess::getInstance()->getModeratorStatus($this);

		if ($this->userid == 0)
		{
			$type = 'guest';
		}
		elseif ($this->isBlocked() && !$this->isBanned())
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

		if ($code === 'class')
		{
			$userClasses = KunenaFactory::getTemplate()->getUserClasses();

			return isset($userClasses[$type]) ? $userClasses[$type] : $userClasses[0] . $type;
		}

		return $code ? $type : $types[$type];
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function isBlocked(): bool
	{
		if ($this->blocked)
		{
			return true;
		}

		return false;
	}

	/**
	 * Return if the user is banned, there are two cases banned for life (9999-12-31 23:59:59) and banned for a short
	 * time with a date in near future
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function isBanned()
	{
		if (!$this->banned)
		{
			return false;
		}

		if ($this->blocked || $this->banned == '9999-12-31 23:59:59')
		{
			return true;
		}

		$expiration = new Date($this->banned);
		$now        = new Date;

		if ($expiration->toUnix() > $now->toUnix() && $expiration->toUnix() < '9999-12-31 23:59:59')
		{
			return true;
		}
	}

	/**
	 * @param   integer  $userid  userid
	 *
	 * @return string
	 *
	 * @since   Kunena 6.0
	 */
	public function GetUserGroup(int $userid): string
	{
		$groups = Access::getGroupsByUser($userid, false);

		$groupid_list = implode(',', $groups);

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true)
			->select($db->quoteName('title'))
			->from($db->quoteName('#__usergroups'))
			->where($db->quoteName('id') . ' = ' . $db->quote((int) $groupid_list));

		$db->setQuery($query);
		$groupNames = $db->loadResult();
		$groupNames .= '<br/>';

		return $groupNames;
	}

	/**
	 * Get users type as a string inside the specified category.
	 *
	 * @param   object   $rank   Rank
	 * @param   integer  $catid  Catid
	 *
	 * @return string
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 */
	public function rankCss(object $rank, int $catid): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		$stylesecond = '';
		$stylethird  = '';
		$stylefourth = '';
		$stylelast   = '';

		if ($rank->rank_title == 'COM_KUNENA_SAMPLEDATA_RANK1')
		{
			$stylesecond = 'style="color:#e8f7ff;"';
			$stylethird  = 'style="color:#e8f7ff;"';
			$stylefourth = 'style="color:#e8f7ff;"';
			$stylelast   = 'style="color:#e8f7ff;"';
		}
		elseif ($rank->rank_title == 'COM_KUNENA_SAMPLEDATA_RANK2')
		{
			$stylethird  = 'style="color:#e8f7ff;"';
			$stylefourth = 'style="color:#e8f7ff;"';
			$stylelast   = 'style="color:#e8f7ff;"';
		}
		elseif ($rank->rank_title == 'COM_KUNENA_SAMPLEDATA_RANK3')
		{
			$stylefourth = 'style="color:#e8f7ff;"';
			$stylelast   = 'style="color:#e8f7ff;"';
		}
		elseif ($rank->rank_title == 'COM_KUNENA_SAMPLEDATA_RANK4')
		{
			$stylelast = 'style="color:#e8f7ff;"';
		}
		else
		{
			// Nothing to do here
		}

		return KunenaLayout::factory('Widget/Ranks')
			->set('type', $this->getType($catid, true))
			->set('topicicontype', $topicicontype)
			->set('rank', $rank)
			->set('stylesecond', $stylesecond)
			->set('stylethird', $stylethird)
			->set('stylefourth', $stylefourth)
			->set('stylelast', $stylelast);
	}

	/**
	 * Render personal text.
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function getPersonalText()
	{
		if (!$this->_config->personal)
		{
			return false;
		}

		if (!isset($this->_personalText))
		{
			$this->_personalText = KunenaParser::parseText((string) $this->personalText);
		}

		return $this->_personalText;
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function bannedDate(): bool
	{
		$ban = new Date($this->banned);
		$now = new Date;

		return $ban->toUnix() > $now->toUnix();
	}

	/**
	 * @param   string  $name  name
	 *
	 * @return string
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 */
	public function profileIcon(string $name): string
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

				$title = Text::sprintf('COM_KUNENA_MYPROFILE_GENDER_GENDER', Text::_('COM_KUNENA_MYPROFILE_GENDER_' . $gender));

				return '<span class="kicon-profile kicon-profile-gender-' . $gender . '" data-bs-toggle="tooltip" data-placement="right" title="' . $title . '"></span>';
				break;
			case 'birthdate' :
				if (!$this->birthdate)
				{
					return false;
				}

				return '<span class="kicon-profile kicon-profile-birthdate" data-bs-toggle="tooltip" data-placement="right" title="' . Text::sprintf('COM_KUNENA_MYPROFILE_BIRTHDATE_BIRTHDATE', $this->birthdate) . '"></span>';

				break;
			case 'location' :
				if ($this->location)
				{
					return '<span data-bs-toggle="tooltip" data-placement="right" title="' . $this->escape($this->location) . '">' . KunenaIcons::location() . '</span>';
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
					return '<a href="' . $this->escape($url) . '" target="_blank" rel="noopener noreferrer"><span data-bs-toggle="tooltip" data-placement="right" title="' . $websitename . '">' . KunenaIcons::globe() . '</span></a>';
				}
				break;
			case 'private' :
				$pms = KunenaFactory::getPrivateMessaging();

				return '<span data-bs-toggle="tooltip" data-placement="right" title="' . Text::_('COM_KUNENA_VIEW_PMS') . '" >' . $pms->showIcon($this->userid) . '</span>';
				break;
			case 'email' :
				return '<span data-bs-toggle="tooltip" data-placement="right" title="' . $this->email . '">' . KunenaIcons::email() . '</span>';
				break;
			case 'profile' :
				if (!$this->userid)
				{
					return false;
				}

				return $this->getLink('<span class="profile" title="' . Text::_('COM_KUNENA_VIEW_PROFILE') . '"></span>');
				break;
		}
	}

	/**
	 * @param   null|string  $name        name
	 * @param   null|string  $title       title
	 * @param   string       $rel         rel
	 * @param   string       $task        task
	 * @param   string       $class       class
	 * @param   int          $catid       catid
	 * @param   int          $avatarLink  avatar link
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getLink($name = null, $title = null, $rel = 'nofollow', $task = '', $class = null, $catid = 0, $avatarLink = 0): string
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
				$title = Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->getName());
			}

			$class = ($class !== null) ? $class : $this->getType($catid, 'class');

			if (!empty($class))
			{
				if ($class == 'btn')
				{
				}
				elseif ($class == 'btn btn-outline-primary')
				{
				}
				elseif ($class == 'btn pull-right')
				{
				}
				elseif ($class == 'btn btn-outline-primary pull-right')
				{
				}
				else
				{
					$class = $this->getType($catid, 'class');
				}
			}

			if (KunenaTemplate::getInstance()->tooltips())
			{
				$class = $class . ' ' . KunenaTemplate::getInstance()->tooltips();
			}

			if ($this->userid == Factory::getApplication()->getIdentity()->id && $avatarLink)
			{
				$link = KunenaFactory::getProfile()->getEditProfileURL($this->userid);
			}
			else
			{
				$link = $this->getURL(true, $task);
			}

			if (!empty($rel))
			{
				$rels = 'rel="' . $rel . '"';
			}
			else
			{
				$rels = '';
			}

			if ($rels == 'rel="canonical"')
			{
				$componentParams = ComponentHelper::getParams('com_config');
				$robots          = $componentParams->get('robots');

				if ($robots == 'noindex, follow' || $robots == 'noindex, nofollow')
				{
					$rels = 'rel="nofollow"';
				}
				else
				{
					$rels = 'rel="canonical"';
				}
			}

			if (!empty($link))
			{
				$this->_link[$key] = "<a class=\"{$class}\" href=\"{$link}\" title=\"{$title}\" {$rels}>{$name}</a>";
			}
			else
			{
				$this->_link[$key] = "<span class=\"{$class}\">{$name}</span>";
			}
		}

		return $this->_link[$key];
	}

	/**
	 * Prepare social buttons for the template
	 *
	 * @param   string  $name  name
	 * @param   bool    $gray  gray
	 *
	 * @return  string
	 *
	 * @throws Exception
	 * @since   Kunena 5.0
	 */
	public function socialButtonsTemplate(string $name, $gray = false)
	{
		$social = $this->socialButtons();

		if (!isset($social [$name]))
		{
			return false;
		}

		$title = $social [$name] ['title'];
		$value = $this->escape($this->$name);
		$url   = strtr($social [$name] ['url'], ['##VALUE##' => $value]);

		// TODO : move this part in a template

		if ($social [$name] ['nourl'] == '0')
		{
			if (!empty($this->$name))
			{
				return '<a href="' . $this->escape($url) . '" ' . KunenaTemplate::getInstance()->tooltips(true) . ' target="_blank" title="' . $title . ': ' . $value . '"><span class="kicon-profile kicon-profile-' . $name . '"></span></a>';
			}
		}
		else
		{
			if (!empty($this->$name))
			{
				return '<span class="kicon-profile kicon-profile-' . $name . ' ' . KunenaTemplate::getInstance()->tooltips() . '" title="' . $title . ': ' . $value . '"></span>';
			}
		}

		if ($gray)
		{
			return '<span class="kicon-profile kicon-profile-' . $name . '-off"></span>';
		}

		return '';
	}

	/**
	 * Get list of social buttons
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function socialButtons()
	{
		return ['twitter'          => ['url' => 'https://twitter.com/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_TWITTER'), 'nourl' => '0'],
		        'facebook'         => ['url' => 'https://www.facebook.com/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_FACEBOOK'), 'nourl' => '0'],
		        'myspace'          => ['url' => 'https://www.myspace.com/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_MYSPACE'), 'nourl' => '0'],
		        'linkedin'         => ['url' => 'https://www.linkedin.com/in/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_LINKEDIN'), 'nourl' => '0'],
		        'linkedin_company' => ['url' => 'https://www.linkedin.com/company/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_LINKEDIN_COMPANY'), 'nourl' => '0'],
		        'friendfeed'       => ['url' => 'http://friendfeed.com/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_FRIENDFEED'), 'nourl' => '0'],
		        'digg'             => ['url' => 'http://www.digg.com/users/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_DIGG'), 'nourl' => '0'],
		        'skype'            => ['url' => 'skype:##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_SKYPE'), 'nourl' => '0'],
		        'yim'              => ['url' => '##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_YIM'), 'nourl' => '1'],
		        'google'           => ['url' => '##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_GOOGLE'), 'nourl' => '1'],
		        'github'           => ['url' => 'https://www.github.com/+##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_GITHUB'), 'nourl' => '0'],
		        'microsoft'        => ['url' => '##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_MICROSOFT'), 'nourl' => '1'],
		        'icq'              => ['url' => 'https://icq.com/people/cmd.php?uin=##VALUE##&action=message', 'title' => Text::_('COM_KUNENA_MYPROFILE_ICQ'), 'nourl' => '0'],
		        'blogspot'         => ['url' => 'https://##VALUE##.blogspot.com/', 'title' => Text::_('COM_KUNENA_MYPROFILE_BLOGSPOT'), 'nourl' => '0'],
		        'flickr'           => ['url' => 'https://www.flickr.com/photos/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_FLICKR'), 'nourl' => '0'],
		        'bebo'             => ['url' => 'https://www.bebo.com/Profile.jsp?MemberId=##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_BEBO'), 'nourl' => '0'],
		        'instagram'        => ['url' => 'https://www.instagram.com/##VALUE##/', 'title' => Text::_('COM_KUNENA_MYPROFILE_INSTAGRAM'), 'nourl' => '0'],
		        'qqsocial'         => ['url' => '##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_QQSOCIAL'), 'nourl' => '1'],
		        'qzone'            => ['url' => '##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_QZONE'), 'nourl' => '1'],
		        'weibo'            => ['url' => '##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_WEIBO'), 'nourl' => '1'],
		        'wechat'           => ['url' => '##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_WECHAT'), 'nourl' => '1'],
		        'vk'               => ['url' => 'https://vk.com/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_VK'), 'nourl' => '0'],
		        'telegram'         => ['url' => 'https://t.me/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_TELEGRAM'), 'nourl' => '0'],
		        'apple'            => ['url' => '##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_APPLE'), 'nourl' => '1'],
		        'vimeo'            => ['url' => 'https://vimeo.com/user##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_VIMEO'), 'nourl' => '1'],
		        'whatsapp'         => ['url' => '##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_WHATSAPP'), 'nourl' => '1'],
		        'youtube'          => ['url' => 'https://www.youtube-nocookie.com/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_YOUTUBE'), 'nourl' => '0'],
		        'ok'               => ['url' => 'https://ok.ru/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_OK'), 'nourl' => '0'],
		        'pinterest'        => ['url' => 'https://pinterest.com/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_PINTEREST'), 'nourl' => '0'],
		        'reddit'           => ['url' => 'https://www.reddit.com/user/##VALUE##', 'title' => Text::_('COM_KUNENA_MYPROFILE_REDDIT'), 'nourl' => '0'],
		];
	}

	/**
	 * @param   string  $name  name
	 *
	 * @return  integer|string|void
	 *
	 * @since   Kunena 6.0
	 */
	public function __get(string $name)
	{
		switch ($name)
		{
			case 'id':
			case 'userid':
				return $this->userid;
			case 'status':
				return $this->status;
			case 'status_text':
				return $this->status_text;
			case 'name':
				return $this->name;
			case 'username':
				return $this->username;
			case 'email':
				return $this->email;
			case 'blocked':
				return $this->blocked;
			case 'registerDate':
				return $this->registerDate;
			case 'lastvisitDate':
				return $this->lastvisitDate;
			case 'signature':
				return $this->signature;
			case 'moderator':
				return $this->moderator;
			case 'banned':
				return $this->banned;
			case 'ordering':
				return $this->ordering;
			case 'posts':
				return $this->posts;
			case 'avatar':
				return $this->avatar;
			case 'karma':
				return $this->karma;
			case 'karma_time':
				return $this->karma_time;
			case 'uhits':
				return $this->uhits;
			case 'personalText':
				return $this->personalText;
			case 'gender':
				return $this->gender;
			case 'birthdate':
				return $this->birthdate;
			case 'location':
				return $this->location;
			case 'websitename':
				return $this->websitename;
			case 'websiteurl':
				return $this->websiteurl;
			case 'rank':
				return $this->rank;
			case 'view':
				return $this->view;
			case 'hideEmail':
				return $this->hideEmail;
			case 'showOnline':
				return $this->showOnline;
			case 'canSubscribe':
				return $this->canSubscribe;
			case 'userListtime':
				return $this->userListtime;
			case 'icq':
				return $this->icq;
			case 'yim':
				return $this->yim;
			case 'microsoft':
				return $this->microsoft;
			case 'skype':
				return $this->skype;
			case 'twitter':
				return $this->twitter;
			case 'facebook':
				return $this->facebook;
			case 'google':
				return $this->google;
			case 'github':
				return $this->github;
			case 'myspace':
				return $this->myspace;
			case 'linkedin':
				return $this->linkedin;
			case 'linkedin_company':
				return $this->linkedin_company;
			case 'friendfeed':
				return $this->friendfeed;
			case 'digg':
				return $this->digg;
			case 'blogspot':
				return $this->blogspot;
			case 'flickr':
				return $this->flickr;
			case 'bebo':
				return $this->bebo;
			case 'thankyou':
				return $this->thankyou;
			case 'instagram':
				return $this->instagram;
			case 'qqsocial':
				return $this->qqsocial;
			case 'qzone':
				return $this->qzone;
			case 'weibo':
				return $this->weibo;
			case 'wechat':
				return $this->wechat;
			case 'apple':
				return $this->apple;
			case 'vk':
				return $this->vk;
			case 'telegram':
				return $this->telegram;
			case 'vimeo':
				return $this->vimeo;
			case 'whatsapp':
				return $this->whatsapp;
			case 'youtube':
				return $this->youtube;
			case 'ok':
				return $this->ok;
			case 'pinterest':
				return $this->pinterest;
			case 'reddit':
				return $this->reddit;
			case 'timestamp':
				return $this->timestamp;
			default:
				return;
		}
	}

	/**
	 * Check if captcha is allowed for guests users or registered users
	 *
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function canDoCaptcha()
	{
		if (!$this->exists() && $this->_config->captcha == 1)
		{
			return true;
		}

		if ($this->exists() && !$this->isModerator() && $this->_config->captcha >= 0)
		{
			if ($this->_config->captchaPostLimit > 0 && $this->posts < $this->_config->captchaPostLimit)
			{
				return true;
			}
		}

		if ($this->_config->captcha == '-1')
		{
			return false;
		}
	}

	/**
	 * Method to check if the email symbol can be displayed depending on configuration and on user type
	 *
	 * @param $profile
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena 5.1.18
	 */
	public function canDisplayEmail($profile)
	{
		if (KunenaUserHelper::getMyself()->isModerator())
		{
			return true;
		}

		if ($this->_config->showEmail)
		{
			if ($profile->userid == $this->userid)
			{
				return true;
			}

			if ($profile->email)
			{
				if ($profile->hideEmail == 0 || $profile->hideEmail == 2 && KunenaUserHelper::getMyself()->exists())
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check if the urls and images should be removed in message or signature
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena 5.2.0
	 */
	public function checkUserAllowedLinksImages()
	{
		if ($this->isModerator() || $this->isAdmin())
		{
			return false;
		}

		if ($this->_config->new_users_prevent_post_url_images && $this->posts <= $this->_config->minimal_user_posts_add_url_image)
		{
			return true;
		}
	}
}
