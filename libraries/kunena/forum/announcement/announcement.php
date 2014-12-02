<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Announcement
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
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
class KunenaForumAnnouncement extends KunenaDatabaseObject {
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
	public function __construct($properties = null) {
		if ($properties !== null) {
			$this->setProperties($properties);
		} else {
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
	static public function getInstance($identifier = null, $reload = false) {
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
	public function getUrl($layout = 'default', $xhtml = true) {
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
	public function getUri($layout = 'default') {
		$uri = new JUri('index.php?option=com_kunena&view=announcement');
		if ($layout) $uri->setVar('layout', $layout);
		if ($this->id) $uri->setVar('id', $this->id);
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
	public function getTaskUrl($task = null, $xhtml = true) {
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
	public function getTaskUri($task = null) {
		$uri = new JUri('index.php?option=com_kunena&view=announcement');
		if ($task) $uri->setVar('task', $task);
		if ($this->id) $uri->setVar('id', $this->id);
		if ($task) $uri->setVar(JSession::getFormToken(), 1);
		return $uri;
	}

	/**
	 * @param string $field
	 * @param string $mode
	 *
	 * @return int|string
	 */
	public function displayField($field, $mode=null) {
		switch ($field) {
			case 'id':
				return intval($this->id);
			case 'title':
				return KunenaHtmlParser::parseText($this->title);
			case 'sdescription':
				return KunenaHtmlParser::parseBBCode($this->sdescription);
			case 'description':
				return KunenaHtmlParser::parseBBCode($this->description ? $this->description : $this->sdescription);
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
	public function getAuthor() {
		if (!$this->_author)
			$this->_author = KunenaUser::getInstance((int)$this->created_by);
		return $this->_author;
	}

	/**
	 * @return KunenaDate
	 */
	public function getCreationDate() {
		if (!$this->_date)
			$this->_date = KunenaDate::getInstance($this->created);
		return $this->_date;
	}

	/**
	 * @param string $action
	 * @param mixed  $user
	 * @param bool   $silent
	 *
	 * @return bool
	 */
	public function authorise($action='read', $user=null, $silent=false) {
		if ($action == 'none') return true;
		if ($user === null) {
			$user = KunenaUserHelper::getMyself();
		} elseif (!($user instanceof KunenaUser)) {
			$user = KunenaUserHelper::get($user);
		}

		if (empty($this->_authcache[$user->userid][$action])) {
			if (!isset(self::$actions[$action])) {
				JError::raiseError(500, JText::sprintf ( 'COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action ) );
				return false;
			}

			$this->_authcache[$user->userid][$action] = null;
			foreach (self::$actions[$action] as $function) {
				if (!isset($this->_authfcache[$user->userid][$function])) {
					$authFunction = 'authorise'.$function;
					$this->_authfcache[$user->userid][$function] = $this->$authFunction($user);
				}
				$error = $this->_authfcache[$user->userid][$function];
				if ($error) {
					$this->_authcache[$user->userid][$action] = $error;
					break;
				}
			}
		}
		$error = $this->_authcache[$user->userid][$action];
		if ($silent === false && $error) $this->setError ( $error );

		if ($silent !== null) $error = !$error;
		return $error;
	}

	/**
	 * @return bool
	 */
	public function check() {
		return true;
	}

	// Internal functions

	protected function saveInternal() {
		/** @var JCache|JCacheController $cache */
		$cache = JFactory::getCache('com_kunena', 'output');
		$cache->remove('announcement', 'global');
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseNew(KunenaUser $user) {
		if ($this->exists()) {
			return JText::_ ( 'COM_KUNENA_NO_ACCESS' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseRead(KunenaUser $user) {
		if (!$this->exists() || ($this->published != 1 && !$user->isModerator())) {
			return JText::_ ( 'COM_KUNENA_NO_ACCESS' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseNotBanned(KunenaUser $user) {
		$banned = $user->isBanned();
		if ($banned) {
			$banned = KunenaUserBan::getInstanceByUserid($user->userid, true);
			if (!$banned->isLifetime()) {
				return JText::sprintf ( 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY', KunenaDate::getInstance($banned->expiration)->toKunena());
			} else {
				return JText::_ ( 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS' );
			}
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseWrite(KunenaUser $user) {
		// Check that user is global moderator
		if (!$user->userid || !$user->isModerator()) {
			return JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' );
		}
		return null;
	}
}
