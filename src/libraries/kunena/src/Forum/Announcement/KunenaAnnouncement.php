<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Announcement;

\defined('_JEXEC') or die();

use Exception;
use InvalidArgumentException;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class KunenaForumAnnouncement
 *
 * @property int    $id
 * @property string $title
 * @property int    $created_by
 * @property string $sdescription
 * @property string $description
 * @property string $created
 * @property int    $published
 * @property int    $publish_up
 * @property int    $publish_down
 * @property int    $ordering
 * @property int    $showdate
 * @since   Kunena 6.0
 */
class KunenaAnnouncement extends KunenaDatabaseObject
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $actions = [
		'none'   => [],
		'read'   => ['Read'],
		'create' => ['New', 'NotBanned', 'Write'],
		'edit'   => ['Read', 'NotBanned', 'Write'],
		'delete' => ['Read', 'NotBanned', 'Write'],
	];

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_table = 'KunenaAnnouncements';

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_date = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_author = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_authcache = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_authfcache = null;

	/**
	 * @param   mixed  $properties  properties
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function __construct($properties = null)
	{
		if ($properties !== null)
		{
			$this->setProperties($properties);
		}
		else
		{
			$table            = $this->getTable();
			$table->published = 1;
			$table->showdate  = 1;
			$this->setProperties($table->getProperties());
		}

		parent::__construct($properties);
	}

	/**
	 * Returns the global KunenaForumAnnouncement object.
	 *
	 * @param   null  $identifier  Announcement id to load.
	 * @param   bool  $reload      reload
	 *
	 * @return  KunenaAnnouncement
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getInstance($identifier = null, $reload = false): KunenaAnnouncement
	{
		return KunenaAnnouncementHelper::get($identifier, $reload);
	}

	/**
	 * Return URL pointing to the Announcement layout.
	 *
	 * @param   string  $layout  layout
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function getUrl($layout = 'default', $xhtml = true)
	{
		$uri = $this->getUri($layout);

		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * return Joomla\CMS\Uri\Uri object pointing to the Announcement layout.
	 *
	 * @param   string  $layout  layout
	 *
	 * @return  Uri
	 *
	 * @since   Kunena 6.0
	 */
	public function getUri($layout = 'default'): Uri
	{
		$uri = new Uri('index.php?option=com_kunena&view=announcement');

		if ($layout)
		{
			$uri->setVar('layout', $layout);
		}

		if ($this->id)
		{
			$uri->setVar('id', $this->id);
		}

		return $uri;
	}

	/**
	 * Return URL pointing to the Announcement task.
	 *
	 * @param   string  $task   task
	 * @param   bool    $xhtml  xhtml
	 *
	 * @return  boolean
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getTaskUrl($task = null, $xhtml = true): bool
	{
		$uri = $this->getTaskUri($task);

		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * return Joomla\CMS\Uri\Uri object pointing to the Announcement task.
	 *
	 * @param   string  $task  task
	 *
	 * @return  Uri
	 *
	 * @since   Kunena 6.0
	 */
	public function getTaskUri($task = null): Uri
	{
		$uri = new Uri('index.php?option=com_kunena&view=announcement');

		if ($task)
		{
			$uri->setVar('task', $task);
		}

		if ($this->id)
		{
			$uri->setVar('id', $this->id);
		}

		if ($task)
		{
			$uri->setVar(Session::getFormToken(), 1);
		}

		return $uri;
	}

	/**
	 * @param   string  $field  field
	 * @param   null    $mode   mode
	 *
	 * @return  integer|string
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function displayField(string $field, $mode = null)
	{
		switch ($field)
		{
			case 'id':
				return \intval($this->id);
			case 'title':
				return KunenaParser::parseText($this->title, '', 'announcement_title');
			case 'sdescription':
				return KunenaParser::parseBBCode($this->sdescription, '', '', '', 'announcement_sdescription');
			case 'description':
				return KunenaParser::parseBBCode($this->description, '', '', '', 'announcement_description');
			case 'created_by':
				return $this->getAuthor()->getLink();
			case 'publish_up':
			case 'publish_down':
			case 'created':
				if (!$mode)
				{
					$mode = 'date_today';
				}

				return $this->getCreationDate()->toKunena($mode);
		}

		return '';
	}

	/**
	 * @return \Kunena\Forum\Libraries\User\KunenaUser|null
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public function getAuthor(): ?KunenaUser
	{
		if (!$this->_author)
		{
			$this->_author = KunenaUser::getInstance((int) $this->created_by);
		}

		return $this->_author;
	}

	/**
	 * @return \Kunena\Forum\Libraries\Date\KunenaDate|null
	 *
	 * @since   Kunena 6.0
	 */
	public function getCreationDate(): ?KunenaDate
	{
		if (!$this->_date)
		{
			$this->_date = KunenaDate::getInstance($this->created);
		}

		return $this->_date;
	}

	/**
	 * Returns true if user is authorised to do the action.
	 *
	 * @param   string           $action  action
	 * @param   KunenaUser|null  $user    user
	 *
	 * @return  boolean
	 *
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
	 * @param   bool             $throw   trow
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 4.0
	 */
	public function tryAuthorise($action = 'read', KunenaUser $user = null, $throw = true)
	{
		// Special case to ignore authorisation.
		if ($action == 'none')
		{
			return true;
		}

		// Load user if not given.
		if ($user === null)
		{
			$user = KunenaUserHelper::getMyself();
		}

		// Use local authentication cache to speed up the authentication calls.
		if (empty($this->_authcache[$user->userid][$action]))
		{
			// Unknown action - throw invalid argument exception.
			if (!isset(self::$actions[$action]))
			{
				throw new InvalidArgumentException(Text::sprintf('COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action), 500);
			}

			// Do the authorisation.
			$this->_authcache[$user->userid][$action] = null;

			foreach (self::$actions[$action] as $function)
			{
				if (!isset($this->_authfcache[$user->userid][$function]))
				{
					$authFunction                                = 'authorise' . $function;
					$this->_authfcache[$user->userid][$function] = $this->$authFunction($user);
				}

				$error = $this->_authfcache[$user->userid][$function];

				if ($error)
				{
					$this->_authcache[$user->userid][$action] = $error;
					break;
				}
			}
		}

		$exception = $this->_authcache[$user->userid][$action];

		// Throw or return the exception.
		if ($throw && $exception)
		{
			throw $exception;
		}

		return $exception;
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function check(): bool
	{
		return true;
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	protected function saveInternal()
	{
		$cache = Factory::getCache('com_kunena', 'output');
		$cache->remove('announcement', 'global');

		return true;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise
	 * @since   Kunena 6.0
	 */
	protected function authoriseNew(KunenaUser $user): ?KunenaExceptionAuthorise
	{
		if ($this->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		return null;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function authoriseRead(KunenaUser $user)
	{
		if (!$this->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		if ($this->published != 1 && !$user->isModerator())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function authoriseNotBanned(KunenaUser $user)
	{
		$banned = $user->isBanned();

		if ($banned)
		{
			$banned = KunenaBan::getInstanceByUserid($user->userid, true);

			if (!$banned->isLifetime())
			{
				return new KunenaExceptionAuthorise(Text::sprintf('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY', KunenaDate::getInstance($banned->expiration)->toKunena()), 403);
			}
			else
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS'), 403);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function authoriseWrite(KunenaUser $user): ?KunenaExceptionAuthorise
	{
		// Check that user is global moderator
		if (!$user->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_NOT_MODERATOR'), 401);
		}

		if (!$user->isModerator())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_NOT_MODERATOR'), 403);
		}

		return null;
	}
}
