<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Announcement
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumAnnouncement
 *
 * @property int $id
 * @property string $title
 * @property int $created_by
 * @property string $sdescription
 * @property string $description
 * @property string $created
 * @property int $published
 * @property int $ordering
 * @property int $showdate
 */
class KunenaForumAnnouncement extends KunenaDatabaseObject
{
	protected $_table = 'KunenaAnnouncements';
	protected $_date = null;
	protected $_author = null;
	protected $_authcache = null;
	protected $_authfcache = null;

	protected static $actions = array(
			'none'=>array(),
			'read'=>array('Read'),
			'create'=>array('New', 'NotBanned', 'Write'),
			'edit'=>array('Read', 'NotBanned', 'Write'),
			'delete'=>array('Read', 'NotBanned', 'Write'),
	);

	/**
	 * @param mixed $properties
	 */
	public function __construct($properties = null)
	{
		if ($properties !== null)
		{
			$this->setProperties($properties);
		}
		else
		{
			$table = $this->getTable ();
			$table->published = 1;
			$table->showdate = 1;
			$this->setProperties ( $table->getProperties () );
		}
	}

	/**
	 * Returns the global KunenaForumAnnouncement object.
	 *
	 * @param null $identifier	Announcement id to load.
	 * @param bool $reload
	 *
	 * @return KunenaForumAnnouncement
	 */
	static public function getInstance($identifier = null, $reload = false)
	{
		return KunenaForumAnnouncementHelper::get($identifier, $reload);
	}

	/**
	 * Return URL pointing to the Announcement layout.
	 *
	 * @param string $layout
	 * @param bool   $xhtml
	 *
	 * @return string
	 */
	public function getUrl($layout = 'default', $xhtml = true)
	{
		$uri = $this->getUri($layout);

		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * Return JUri object pointing to the Announcement layout.
	 *
	 * @param string $layout
	 *
	 * @return JUri
	 */
	public function getUri($layout = 'default')
	{
		$uri = new JUri('index.php?option=com_kunena&view=announcement');

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
	 * @param string $task
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public function getTaskUrl($task = null, $xhtml = true)
	{
		$uri = $this->getTaskUri($task);

		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * Return JUri object pointing to the Announcement task.
	 *
	 * @param string $task
	 *
	 * @return JUri
	 */
	public function getTaskUri($task = null)
	{
		$uri = new JUri('index.php?option=com_kunena&view=announcement');

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
			$uri->setVar(JSession::getFormToken(), 1);
		}

		return $uri;
	}

	/**
	 * @param string $field
	 * @param string $mode
	 *
	 * @return int|string
	 */
	public function displayField($field, $mode = null)
	{
		switch ($field)
		{
			case 'id':
				return intval($this->id);
			case 'title':
				return KunenaHtmlParser::parseText($this->title);
			case 'sdescription':
				return KunenaHtmlParser::parseBBCode($this->sdescription);
			case 'description':
				return KunenaHtmlParser::parseBBCode($this->description);
			case 'created_by':
				return $this->getAuthor()->getLink();
			case 'created':
				if (!$mode) $mode = 'date_today';
				return $this->getCreationDate()->toKunena($mode);
		}

		return '';
	}

	/**
	 * @return KunenaUser
	 */
	public function getAuthor()
	{
		if (!$this->_author)
		{
			$this->_author = KunenaUser::getInstance((int)$this->created_by);
		}

		return $this->_author;
	}

	/**
	 * @return KunenaDate
	 */
	public function getCreationDate()
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

		// Use local authentication cache to speed up the authentication calls.
		if (empty($this->_authcache[$user->userid][$action]))
		{
			// Unknown action - throw invalid argument exception.
			if (!isset(self::$actions[$action]))
			{
				throw new InvalidArgumentException(JText::sprintf('COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action), 500);
			}

			// Do the authorisation.
			$this->_authcache[$user->userid][$action] = null;

			foreach (self::$actions[$action] as $function)
			{
				if (!isset($this->_authfcache[$user->userid][$function]))
				{
					$authFunction = 'authorise'.$function;
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
	 * @param string $action
	 * @param mixed  $user
	 * @param bool   $silent
	 *
	 * @return bool
	 * @deprecated K4.0
	 */
	public function authorise($action='read', $user = null, $silent = false)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if ($user === null)
		{
			$user = KunenaUserHelper::getMyself();
		}
		elseif (!($user instanceof KunenaUser))
		{
			$user = KunenaUserHelper::get($user);
		}

		$exception = $this->tryAuthorise($action, $user, false);

		if ($silent === false && $exception)
		{
			$this->setError($exception->getMessage());
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if ($silent !== null)
		{
			return !$exception;
		}

		return $exception ? $exception->getMessage() : null;
	}

	/**
	 * @return bool
	 */
	public function check()
	{
		return true;
	}

	// Internal functions

	protected function saveInternal()
	{
		/** @var JCache|JCacheController $cache */
		$cache = JFactory::getCache('com_kunena', 'output');
		$cache->remove('announcement', 'global');
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseNew(KunenaUser $user)
	{
		if ($this->exists())
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 403);
		}

		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseRead(KunenaUser $user)
	{
		if (!$this->exists())
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		if ($this->published != 1 && !$user->isModerator())
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 403);
		}

		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseNotBanned(KunenaUser $user)
	{
		$banned = $user->isBanned();

		if ($banned)
		{
			$banned = KunenaUserBan::getInstanceByUserid($user->userid, true);
			if (!$banned->isLifetime())
			{
				return new KunenaExceptionAuthorise(JText::sprintf('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY', KunenaDate::getInstance($banned->expiration)->toKunena()), 403);
			}
			else
			{
				return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS'), 403);
			}
		}

		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseWrite(KunenaUser $user)
	{
		// Check that user is global moderator
		if (!$user->exists())
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_NOT_MODERATOR'), 401);
		}

		if (!$user->isModerator())
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_NOT_MODERATOR'), 403);
		}

		return null;
	}
}
